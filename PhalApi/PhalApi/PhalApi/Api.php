<?php
/**
 * PhalApi_Api 接口服务基类
 *
 * - 实现身份验证、按参数规则解析生成接口参数等操作
 * - 提供给开发人员自宝义的接口服务具体类继承
 *
 * <br>通常地，可以这样继承：<br>
 *
 * ```
 *  class Api_Demo extends PhalApi_Api {
 *
 *      public function getRules() {
 *          return array(
 *              // ...
 *          );
 *      }
 *
 *      public function doSth() {
 *          $rs = array();
 *
 *          // ...
 *
 *          return $rs;
 *      }
 *  }
 * ```
 *
 * @property    mixed $whatever 接口参数
 * @package     PhalApi\Api
 * @license     http://www.phalapi.net/license GPL 协议 GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Api
{

    /**
     * 设置规则解析后的接口参数
     * @param string $name 接口参数名字
     * @param mixed $value 接口参数解析后的值
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * 获取规则解析后的接口参数
     * @param string $name 接口参数名字
     * @return mixed
     * @throws PhalApi_Exception_InternalServerError 获取未设置的接口参数时，返回500
     */
    public function __get($name)
    {
        if (!isset($this->$name) || empty($name)) {
            throw new PhalApi_Exception_InternalServerError(
                T('PhalApi_Api::${name} undefined', array('name' => $name))
            );
        }

        return $this->$name;
    }

    /**
     * 初始化
     *
     * 主要完成的初始化工作有：
     * - 1、[必须]按参数规则解析生成接口参数
     * - 2、[可选]过滤器调用，如：签名验证
     * - 3、[可选]用户身份验证
     *
     * @return null
     * @uses PhalApi_Api::filterCheck()
     * @uses PhalApi_Api::userCheck()
     * @uses PhalApi_Api::createMemberValue()
     */
    public function init()
    {
        $this->createMemberValue();

        $this->filterCheck();

        $this->userCheck();

    }

    /**
     * 按参数规则解析生成接口参数
     *
     * 根据配置的参数规则，解析过滤，并将接口参数存放于类成员变量
     *
     * @uses PhalApi_Api::getApiRules()
     */
    protected function createMemberValue()
    {
        foreach ($this->getApiRules() as $key => $rule) {
            $this->$key = DI()->request->getByRule($rule);
        }
    }

    /**
     * 取接口参数规则
     *
     * 主要包括有：
     * - 1、[固定]系统级的service参数
     * - 2、应用级统一接口参数规则，在app.apiCommonRules中配置
     * - 3、接口级通常参数规则，在子类的*中配置
     * - 4、接口级当前操作参数规则
     *
     * <b>当规则有冲突时，以后面为准。另外，被请求的函数名和配置的下标都转成小写再进行匹配。</b>
     *
     * @return array
     * @uses PhalApi_Api::getRules()
     */
    public function getApiRules()
    {
        $rules = array();

        $allRules = $this->getRules();
        if (!is_array($allRules)) {
            $allRules = array();
        }
        $allRules = array_change_key_case($allRules, CASE_LOWER);

        $action = strtolower(DI()->request->getServiceAction());
        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }

        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        $apiCommonRules = DI()->config->get('app.apiCommonRules', array());
        if (!empty($apiCommonRules) && is_array($apiCommonRules)) {
            $rules = array_merge($apiCommonRules, $rules);
        }

        return $rules;
    }

    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载
     *
     * @return array
     */
    public function getRules()
    {
        return array();
    }

    /**
     * 过滤器调用
     *
     * 可由开发人员根据需要重载，以实现项目拦截处理，需要：
     * - 1、实现PhalApi_Filter::check()接口
     * - 2、注册的过滤器到DI()->filter
     *
     * <br>以下是一个简单的示例：<br>
     * ```
     *    class My_Filter implements PhalApi_Filter {
     *
     *        public function check() {
     *            //TODO
     *        }
     *    }
     *
     *
     *  //在初始化文件 init.php 中注册过滤器
     *  DI()->filter = 'My_Filter';
     * ```
     *
     * @throws PhalApi_Exception_BadRequest 当验证失败时，请抛出此异常，以返回400
     * @see PhalApi_Filter::check()
     */
    protected function filterCheck()
    {
        // 过滤服务白名单
        if ($this->isServiceWhitelist()) {
            return;
        }

        $filter = DI()->get('filter', 'PhalApi_Filter_None');

        if (isset($filter)) {
            if (!($filter instanceof PhalApi_Filter)) {
                throw new PhalApi_Exception_InternalServerError(
                    T('DI()->filter should be instanceof PhalApi_Filter'));
            }

            $filter->check();
        }
    }

    /**
     * 用户身份验证
     *
     * 可由开发人员根据需要重载，此通用操作一般可以使用委托或者放置在应用接口基类
     *
     * @throws PhalApi_Exception_BadRequest 当验证失败时，请抛出此异常，以返回400
     */
    protected function userCheck()
    {
        $service_api = DI()->request->getServiceApi();
        $verify_token = DI()->config->get('app.verify_token');
        if (!empty($verify_token) && in_array($service_api, $verify_token)) {
            $uid = DI()->request->get('uid');
            $token = DI()->request->get('token');
            $token = checkNull($token);
            $checkToken = checkToken($uid, $token);
            if ($checkToken == 700) {
                throw new PhalApi_Exception_BadRequest('用户登陆过期',$checkToken);
            }
        }
        return true;

    }

    /**
     * 是否为白名单的服务
     *
     * @return boolean
     */
    protected function isServiceWhitelist()
    {
        $di = DI();
        $api = $di->request->getServiceApi();
        $action = $di->request->getServiceAction();

        $serviceWhitelist = $di->config->get('app.service_whitelist', array());
        foreach ($serviceWhitelist as $item) {
            $cfgArr = explode('.', $item);
            if (count($cfgArr) < 2) {
                continue;
            }

            // 短路返回
            if ($this->equalOrIngore($api, $cfgArr[0]) && $this->equalOrIngore($action, $cfgArr[1])) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 相等或忽略
     *
     * @param string $str 等判断的字符串
     * @param string $cfg 规则配置，*号表示通配
     * @return boolean
     */
    protected function equalOrIngore($str, $cfg)
    {
        return strcasecmp($str, $cfg) == 0 || $cfg == '*';
    }
    

}
