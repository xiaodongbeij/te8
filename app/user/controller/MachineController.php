<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use app\game\model\GameCate;
use app\user\model\User;
use app\user\model\UserRate;
use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;
use think\Request;
use think\Validate;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class MachineController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {

        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }

        $data = $this->request->param();
   

        $configpub = getConfigPub();

        $nums = Db::name("user")->where('iszombiep',1)->count();

        $list = Db::name("user")
            ->where('iszombiep',1)
            ->order("id desc")
            ->paginate(20);


        $list->each(function ($v, $k) {

            $v['code'] = Db::name("agent_code")->where("uid = {$v['id']}")->value('code');
            $v['user_login'] = m_s($v['user_login']);
            $v['mobile'] = m_s($v['mobile']);
//            $v['user_email']=m_s($v['user_email']);

            $v['avatar'] = get_upload_path($v['avatar']);

            return $v;
        });

        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('nowtime', time());
        $this->assign('name_coin', $configpub['name_coin']);
        $this->assign('name_votes', $configpub['name_votes']);
        $this->assign('nums', $nums);
        // 渲染模板输出
        return $this->fetch();
    }

    function del()
    {

        $id = $this->request->param('id', 0, 'intval');

        $user_login = DB::name('user')->where(["id" => $id])->value('user_login');
        $rs = DB::name('user')->where(["id" => $id])->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除会员：{$id} - {$user_login}";
        setAdminLog($action);

        /* 删除认证 */
        DB::name("user_auth")->where("uid='{$id}'")->delete();
        /* 删除直播记录 */
        DB::name("live")->where("uid='{$id}'")->delete();
        DB::name("live_record")->where("uid='{$id}'")->delete();
        /* 删除房间管理员 */
        DB::name("live_manager")->where("uid='{$id}' or liveuid='{$id}'")->delete();

        /*  删除黑名单*/
        DB::name("user_black")->where("uid='{$id}' or touid='{$id}'")->delete();
        /* 删除关注记录 */
        DB::name("user_attention")->where("uid='{$id}' or touid='{$id}'")->delete();

        /* 删除僵尸 */
        DB::name("user_zombie")->where("uid='{$id}'")->delete();
        /* 删除超管 */
        DB::name("user_super")->where("uid='{$id}'")->delete();
        /* 删除会员 */
        DB::name("vip_user")->where("uid='{$id}'")->delete();

        /* 删除分销关系 */
        DB::name("agent")->where("uid='{$id}' or one_uid={$id}")->delete();
        /* 删除分销邀请码 */
        DB::name("agent_code")->where("uid='{$id}'")->delete();
        /* 删除分销收益 */
        DB::name("agent_profit")->where("uid='{$id}'")->delete();
        /* 删除分销收益记录 */
        DB::name("agent_profit_recode")->where("one_uid='{$id}'")->delete();

        /* 删除坐骑 */
        DB::name("car_user")->where("uid='{$id}'")->delete();


        /* 删除推送PUSHID */
        DB::name("user_pushid")->where("uid='{$id}'")->delete();
        /* 删除钱包账号 */
        DB::name("cash_account")->where("uid='{$id}'")->delete();
        /* 删除自己的标签 */
        DB::name("label_user")->where("touid='{$id}'")->delete();

        /* 删除背包 */
        DB::name("backpack")->where("uid='{$id}'")->delete();

        /* 删除动态 相关 */
        $dynamicids = DB::name("dynamic")->where("uid='{$id}'")->column('id');
        DB::name("dynamic")->where("uid='{$id}'")->delete();

        DB::name("dynamic_comments")->where('dynamicid', 'in', $dynamicids)->delete();
        DB::name("dynamic_comments_like")->where('dynamicid', 'in', $dynamicids)->delete();
        DB::name("dynamic_like")->where('dynamicid', 'in', $dynamicids)->delete();
        DB::name("dynamic_report")->where('dynamicid', 'in', $dynamicids)->delete();
        DB::name("dynamic_report")->where('touid', '=', $id)->delete();
        /* 删除动态 相关*/

        /* 删除反馈 */
        DB::name("feedback")->where('uid', '=', $id)->delete();

        /* 删除守护 */
        DB::name("guard_user")->where('uid', '=', $id)->delete();
        DB::name("guard_user")->where('liveuid', '=', $id)->delete();

        /* 删除靓号 */
        DB::name("liang")->where('uid', '=', $id)->delete();

        /* 删除踢人 */
        DB::name("live_kick")->where('uid', '=', $id)->delete();

        /* 删除踢人 */
        DB::name("live_kick")->where('uid', '=', $id)->delete();

        /* 删除禁言 */
        DB::name("live_shut")->where('uid', '=', $id)->delete();

        /* 删除音乐收藏 */
        DB::name("music_collection")->where('uid', '=', $id)->delete();

        /* 删除举报 */
        DB::name("report")->where('touid', '=', $id)->delete();

        /* 删除店铺相关 */


        //$goodsid=DB::name("shop_goods")->where("uid='{$id}'")->column('id');

        /* 删除店铺相关 */

        /* 删除禁用 */
        DB::name("user_banrecord")->where("uid='{$id}'")->delete();

        /* 删除登录 */
        DB::name("user_sign")->where("uid='{$id}'")->delete();

        /* 删除映票记录 */
        DB::name("user_voterecord")->where("uid='{$id}'")->delete();

        /* 删除积分 */
        DB::name("user_scorerecord")->where("touid='{$id}'")->delete();

        /* 删除视频 相关 */
        $videoids = DB::name("video")->where("uid='{$id}'")->column('id');
        DB::name("video")->where("uid='{$id}'")->delete();

        DB::name("video_black")->where('videoid', 'in', $videoids)->delete();
        DB::name("video_black")->where('uid', '=', $id)->delete();

        DB::name("video_comments")->where('videoid', 'in', $videoids)->delete();
        DB::name("video_comments_like")->where('videoid', 'in', $videoids)->delete();

        DB::name("video_like")->where('videoid', 'in', $videoids)->delete();
        DB::name("video_like")->where('uid', '=', $id)->delete();

        DB::name("video_step")->where('videoid', 'in', $videoids)->delete();
        DB::name("video_step")->where('uid', '=', $id)->delete();

        DB::name("video_report")->where('videoid', 'in', $videoids)->delete();
        DB::name("video_report")->where('touid', '=', $id)->delete();
        /* 删除视频 相关*/


        /* 删除家族关系 */
        DB::name("family_user")->where("uid='{$id}'")->delete();
        /* 家族长处理 */
        $isexist = DB::name("family")->field("id")->where("uid={$id}")->find();
        if ($isexist) {
            $data = array(
                'state' => 3,
                'signout' => 2,
                'signout_istip' => 2,
            );
            DB::name("family_user")->where("familyid={$isexist['id']}")->update($data);
            DB::name("family_profit")->where("familyid={$isexist['id']}")->delete();
            DB::name("family_profit")->where("id={$isexist['id']}")->delete();
        }

        //删除用户余额操作记录
        Db::name("user_balance_record")->where("uid={$id}")->delete();



        delcache("userinfo_" . $id, "token_" . $id);

        //删除极光IM用户id
        delIMUser($id);

        $this->success("删除成功！");

    }

 
    /* 一键开启、关闭僵尸粉 */
    function setzombieall()
    {
      
        $iszombie = $this->request->param('iszombie', 0, 'intval');
        if ($iszombie) {
            $num = Db::name('user')->where('iszombiep', 1)->update(['iszombie'=>1]);
            $list = Db::name('user')->where('iszombie', 1)->column('id');
            $ids = [];
            foreach ($list as $v)
            {
                $ids[] = ['uid' => $v];
            }
            Db::name('user_zombie')->insertAll($ids,true);
            $action = "开启" . $num . '个机器人';
        } else {
            
            $num =Db::name('user')->where('iszombiep', 1)->update(['iszombie'=>0]);
            Db::query("truncate cmf_user_zombie");
            $action = "关闭" . $num . '个机器人';
        }
        setAdminLog($action);
        $this->success("操作成功！");

    }
    
    function delMachine()
    {
        $num = Db::name('user')->where('iszombiep', 1)->delete();
        delcache('zombie');

        $action = "刪除" . $num . '个机器人';
        setAdminLog($action);
        $this->success("操作成功！");
    }
    
    function addMachine()
    {
        $num = 500;
        $config = getcaches('getMachineSet');
        if($config['min_grade'] && $config['max_grade'])
        {
            $action = "添加" . $num . '个机器人';
            $ids = [];
            $sadd = [];
            for ($i = 0; $i < $num; $i++) {
                $avatar = 'https://axxa.tv.ddbvtz.cn/tx/'. rand(1,20).'.jpg';
                $u = [
                        'user_nicename' => randNiceName(),
                        'user_type' => 2,
                        'avatar' => $avatar,
                        'avatar_thumb' => $avatar,
                        'user_status' => 1,
                        'mobile' => '9668888' . $i,
                        'consumption' => rand($config['min_grade'], $config['max_grade']),
                        'user_login' => 'robot' . $i,
                        'iszombie' => 1,
                        'iszombiep' => 1,
                        'coin' => 100000,
                    ];
                $uid = Db::name('user')->insertGetId($u);
                $level = rand(1,56);
                $sadd[]  =  json_encode([
                        'id' => $uid,
                        'user_nicename' => $u['user_nicename'],
                        'user_type' => $u['user_type'],
                        'avatar' => $u['avatar'],
                        'avatar_thumb' => $u['avatar_thumb'],
                        'guard_type' => 0,
                        'level' => $level,
                        'coin' => 100000,
                    ]); 
            }
            $GLOBALS['redisdb']->sAdd('zombie',...$sadd);
           
            setAdminLog($action);
            $this->success("操作成功！");
        }
        $this->error("请先去配置机器人等级经验");
        
        
    }

    /* 设置僵尸粉 */
   

    function edit()
    {

        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('user')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $data['user_login'] = m_s($data['user_login']);
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            //获取用户的状态
            $user_status = Db::name("user")->where("id={$data['id']}")->value("user_status");


            $user_pass = $data['user_pass'];
            if ($user_pass != "") {
                if (!passcheck($user_pass)) {
                    $this->error("密码为6-20位字母数字组合");
                }

                $data['user_pass'] = cmf_password($user_pass);
            } else {
                unset($data['user_pass']);
            }

            $user_nicename = $data['user_nicename'];
            if ($user_nicename == "") {
                $this->error("请填写昵称");
            }

            if ($user_status != 3) {
                if (strstr($user_nicename, '已注销') !== false) {
                    $this->error("非注销用户昵称不能包含已注销");
                }
            }

            if (mb_substr($user_nicename, 0, 1) == "=") {
                $this->error("昵称内容非法");
            }

            $avatar = $data['avatar'];
            $avatar_thumb = $data['avatar_thumb'];
            if (($avatar == "" || $avatar_thumb == '') && ($avatar != "" || $avatar_thumb != '')) {
                $this->error("请同时上传头像 和 头像小图  或 都不上传");
            }

            if ($avatar == '' && $avatar_thumb == '') {
                $data['avatar'] = '/default.jpg';
                $data['avatar_thumb'] = '/default_thumb.jpg';
            }

            $rs = DB::name('user')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }

            $action = "修改会员信息：{$data['id']}";
            setAdminLog($action);

            //查询用户信息存入缓存中
            $info = Db::name("user")
                ->field('id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,birthday,user_status,issuper')
                ->where("id={$data['id']} and user_type=2")
                ->find();


            if ($info) {
                setcaches("userinfo_" . $data['id'], $info);
            }

            $this->success("修改成功！");
        }
    }

}
