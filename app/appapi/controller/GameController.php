<?php


namespace app\appapi\controller;


//use app\BaseController;
use cmf\controller\HomeBaseController;
use think\Db;

class GameController extends HomebaseController
{
    // 三方域名
    protected $domain;
    // 三方游戏账号
    protected $agent;
    // 三方游戏秘钥
    protected $key;
    // 用户账号
    protected $username = '13888888888';
    // 平台编号
    protected $platform = '0016';

    //存储游戏记录参数
    protected $start;   //起始
    protected $end;     //结束

    protected $param = [];

    public function __construct()
    {
        // 获取前端传入数据
        $param = input();
        // 验证签名
        if(!$this->verifySign($param))
        {
            return json([
                'hRet' => 101,
                'msg' => 'Key value error'
            ]);
        }

        $this->start = date("Y-m-d H:i:s",strtotime('-20 minute'));
        $this->end = date("Y-m-d H:i:s");
//        $this->start = date("Y-m-d H:i:s",strtotime('-9 day'));
//        $this->end = date("Y-m-d H:i:s",strtotime('-7 day'));
        connectionRedis();
        $con = getConfigPri();
        $this->domain = $con['tripartite_domain'];
        $this->agent = $con['tripartite_agent'];
        $this->key = $con['tripartite_key'];

        /**
         *  上线查询数据库获取数据，并赋值
         */
        $this->param = array_merge([
            'agent' => $this->agent,
            'platform' => $this->platform,
            'username' => $this->username,
        ],$param);

    }

    /**
     * 存储投注记录**
     * @return bool|string
     */
    public function SaveBettingRecord()
    {

        $platforms = Db::table('cmf_game_cate')->field('platform')->select()->toArray();
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetBettingRecord";
        //去除用户
        unset($this->param['username']);
//        // 查询起始时间
        $this->param['datestart'] = $this->start;
//        // 查询截止时间
        $this->param['dateend'] = $this->end;
        // 排序 1 表示降序 ，0 表示升序
        $this->param['ascordesc'] = 1;
        // 当前第几页
        $this->param['currentpage'] = 1;
        // 每页的记录数
        $this->param['pagesize'] = 100000;

//        $this->param['platform'] = '0027';
//        $res = json_decode($this->getHttpQuery($url, $this->param, 1),true);
//        dump($res);die;

        $insert = [];
        foreach ($platforms as $val){
            if (strlen($val['platform']) != 4) continue;
            $this->param['platform'] = $val['platform'] . '';
//            $this->param['platform'] = $val['platform'];
            $res = json_decode($this->getHttpQuery($url, $this->param, 1),true);
            if ($res['hRet'] !== 1) continue;
            foreach ($res['list'] as $v){

                $temp = [
                    'rec_id' => $v['RecId'],
                    'platform_code' => $v['PlatformCode'],
                    'game_name' => $v['GameName'],
                    'game_type' => $v['GameType'],
                    'user_login' => $v['UserAccount'],
                    'bet_id' => $v['BetId'],
                    'bet_time' => strtotime($v['BetTime']),
                    'update_time' => strtotime($v['UpdateTime']),
                    'bet_amount' => $v['BetAmount'],
                    'pay_off' => $v['PayOff'],
                    'profit' => $v['Profit'],
                    'status' => $v['Status'],
                    'remark' => $v['Remark']
                ];
                $insert[] = $temp;
            }
        }
        dump($insert);die;
        $res = Db::table('cmf_game_record')->insertAll($insert,true);
        var_dump($res);
    }

    /**
     * 检测并创建账户
     */
    public function CreateAccount()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/CreateAccount";
        return $this->getHttpQuery($url, $this->param);
    }

    /**
     *  查询用户余额
     * @return bool|string
     */
    public function GetBalance()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetBalance";
        return $this->getHttpQuery($url, $this->param);
    }

    /**
     *  存款
     * @return bool|string
     * @throws \Exception
     */
    public function Deposit()
    {
        // 获取前端传过来的金额
        $money = 100;
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/deposit";
        // 订单号
        $this->param['billno'] = date('YmdHis') . random_int(1111, 9999);
        // 转账金额
        $this->param['credit'] = $money;
        // 转账成功数据要做数据库金额减少
        return $this->getHttpQuery($url, $this->param);
    }

    public function Withdrawals()
    {
        // 获取前端传过来的金额
        $money = 100;
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/Withdrawals";
        // 订单号
        $this->param['billno'] = date('YmdHis') . random_int(1111, 9999);
        // 取款金额
        $this->param['credit'] = $money;
        // 取款回来数据要做数据库金额增加
        return $this->getHttpQuery($url, $this->param);
    }

    /**
     *  进入游戏
     * @return bool|string
     */
    public function TransferGame()
    {
        // 请求地址
        $url = $this->domain . "/web/{$this->agent}/TransferGame";
        // 前端传过来的游戏类型
        $this->param['gametype'] = 1;
        // 游戏编码
        $this->param['gamecode'] = 620;
        // 直接跳转到游戏
        return $this->getHttpQuery($url, $this->param, 2);
    }

    /**
     * 用户投注日报表
     * @return bool|string
     */
    public function GetBetCtrReport()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetBetCtrReport";
        // 当前页码
        $this->param['currentpage'] = 1;
        // 每页数量
        $this->param['pagesize'] = 10;
        // 获取数据
        return $this->getHttpQuery($url, $this->param, 1);
    }

    /**
     * 不用接
     * 获得游戏类型
     * @return bool|string
     */
    public function GetGameType()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetGameType";
        // 获取数据
        return $this->getHttpQuery($url, $this->param, 1);
    }

    /**
     * 不用接
     * 获取游戏列表
     * @return bool|string
     */
    public function GetGameList()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetGameList";
        $this->param['pagesize'] = 20;
        $this->param['currentpage'] = 1;

        // 获取数据
        return $this->getHttpQuery($url, $this->param, 1);
    }


    /**
     * 进入指定游戏
     * 根据游戏 CODE 进入指定游戏。只对 BG、AG、PT、JL 有效
     * @return bool|string
     */
    public function PlayGame()
    {
        // 请求地址
        $url = $this->domain . "/web/{$this->agent}/PlayGame";
        // 前端传过来的游戏类型
        $this->param['gametype'] = 1;
        // 游戏编码
        $this->param['gamecode'] = 620;
        // 直接跳转到游戏
        return $this->getHttpQuery($url, $this->param, 2);
    }

    /**
     * 投注记录**
     * @return bool|string
     */
    public function GetBettingRecord()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetBettingRecord";
        //去除用户
        unset($this->param['username']);
        // 查询起始时间
        $this->param['datestart'] = date('Y-m-d H:i:s', strtotime("-10 day"));
        // 查询截止时间
        $this->param['dateend'] = date('Y-m-d H:i:s',strtotime("+1 day"));
        // 排序 1 表示降序 ，0 表示升序
        $this->param['ascordesc'] = 1;
        // 当前第几页
        $this->param['currentpage'] = 1;
        // 每页的记录数
        $this->param['pagesize'] = 20;
        
        return $this->getHttpQuery($url, $this->param, 1);
    }


    /**
     *  获取请求内容
     * @param string $url
     * @param array $param
     * @param int $location 1=curl请求；2=直接跳转
     * @return bool|string
     */
    public function getHttpQuery(string $url, array $param, int $location = 1)
    {
        $param = base64_encode(urldecode(http_build_query($param)));
        $key = $this->getKey($param);
        $url = $url . '?' . http_build_query(['param' => $param, 'key' => $key]);
        if ($location == 1) {
            return $this->curlGet($url);
        }
        return redirect($url);

    }
    
    /**
     *  效验的参数
     * @param array $param
     * @return bool
     */
    public function verifySign(array $param)
    {
        if(empty($param)) return ['hRet' => 107, 'msg' => '参数不能为空'];
        if(empty($param['sign'])) return ['hRet' => 101 ,'msg' => '签名错误'];
        if(empty($param['platform'])) return ['hRet' => 107, 'msg' => '平台编号不能为空'];
        
        ksort($param);
        $str = '';
        foreach ($param as $k => $v) {
            if ($k == 'sign') continue;
            $str .= $k . '=' . $v . '&';
        }
        // 设置到配置里面
        $str .= 'uKVFzC4Z1OOBN4oxaz6a';
        
        if(md5($str) !== $param['sign']) return ['hRet' => 101 ,'msg' => '签名错误'];
        
        return true;
    }

    /**
     *  生成秘钥
     * @param $param
     * @return string
     */
    public function getKey(string $param)
    {
        return md5($param . $this->key);
    }


    public function curlGet($url=""){

        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    
        curl_setopt($curl, CURLOPT_URL, $url);
    
        $res = curl_exec($curl);
        curl_close($curl);
    
        return $res;
    }

}