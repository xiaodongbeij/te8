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
use think\Request;

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
class AdminIndexController extends AdminBaseController{

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
    public function index(){

        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }
        
        $data = $this->request->param();
        $map=[];
        $map[]=['user_type','=',2];


        $is_dai=isset($data['is_dai']) ? $data['is_dai']: '';
        if($is_dai!=''){
            $map[]=['is_dai','=',$is_dai];
        }
        
        $parent_id = isset($data['parent_id']) ? $data['parent_id']: '';
        if($parent_id!=''){
            $invite_level = Db::name('user')->where('id', $parent_id)->value('invite_level');
            $map[]=['invite_level','like',$invite_level . '%'];
        }
        
        $mobile=isset($data['iszombie']) ? $data['iszombie']: '';

        if($mobile !='' ){
           
            $map[] = $mobile == 1 ? ['mobile','<>',''] : ['mobile','=',''];
        }
        
        
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['create_time','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['create_time','<=',strtotime($end_time) + 60*60*24];
        }
        
        // $iszombie=isset($data['iszombie']) ? $data['iszombie']: '';
        // if($iszombie!=''){
        //     $map[]=['iszombie','=',$iszombie];
        // }
        
        $isban=isset($data['isban']) ? $data['isban']: '';
        if($isban!=''){
            if($isban==1){
                $map[]=['user_status','=',0];
            }else{
                $map[]=['user_status','<>',0];
            }
            
        }
        
        $issuper=isset($data['issuper']) ? $data['issuper']: '';
        if($issuper!=''){
            $map[]=['issuper','=',$issuper];
        }
        
        $source=isset($data['source']) ? $data['source']: '';
        if($source!=''){
            $map[]=['source','=',$source];
        }
        
        $ishot=isset($data['ishot']) ? $data['ishot']: '';
        if($ishot!=''){
            $map[]=['ishot','=',$ishot];
        }

        $mobile=isset($data['mobile']) ? $data['mobile']: '';
        if($mobile!=''){
            $map[]=['mobile','like',$mobile.'%'];
        }
        
        // $iszombiep=isset($data['iszombiep']) ? $data['iszombiep']: '';
        // if($iszombiep!=''){
        //     $map[]=['iszombiep','=',$iszombiep];
        // }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['user_login|user_nicename','like','%'.$keyword.'%'];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){

            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['id',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['id','=',$uid];
            }
        }

        $configpub=getConfigPub();
        
        $nums=Db::name("user")->where('iszombiep',0)->where($map)->count();
        
        $list = Db::name("user")
            ->where($map)
            ->where('iszombiep',0)
			->order("id desc")
			->paginate(20);
			
        
        $list->each(function($v,$k){
			
            $v['code']=Db::name("agent_code")->where("uid = {$v['id']}")->value('code');
            $v['user_login']=m_s($v['user_login']);
            $v['mobile']=m_s($v['mobile']);
//            $v['user_email']=m_s($v['user_email']);
            
            $v['avatar']=get_upload_path($v['avatar']);
            
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
    
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $user_login = DB::name('user')->where(["id"=>$id,"user_type"=>2])->value('user_login');
        $rs = DB::name('user')->where(["id"=>$id,"user_type"=>2])->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $action="删除会员：{$id} - {$user_login}";
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
        $dynamicids=DB::name("dynamic")->where("uid='{$id}'")->column('id');
        DB::name("dynamic")->where("uid='{$id}'")->delete();
        
        DB::name("dynamic_comments")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_comments_like")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_like")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_report")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_report")->where('touid','=',$id)->delete();
        /* 删除动态 相关*/
        
        /* 删除反馈 */
        DB::name("feedback")->where('uid','=',$id)->delete();
        
        /* 删除守护 */
        DB::name("guard_user")->where('uid','=',$id)->delete();
        DB::name("guard_user")->where('liveuid','=',$id)->delete();
        
        /* 删除靓号 */
        DB::name("liang")->where('uid','=',$id)->delete();
        
        /* 删除踢人 */
        DB::name("live_kick")->where('uid','=',$id)->delete();
        
        /* 删除踢人 */
        DB::name("live_kick")->where('uid','=',$id)->delete();
        
        /* 删除禁言 */
        DB::name("live_shut")->where('uid','=',$id)->delete();
        
        /* 删除音乐收藏 */
        DB::name("music_collection")->where('uid','=',$id)->delete();
        
        /* 删除举报 */
        DB::name("report")->where('touid','=',$id)->delete();
        
      
     
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
        $videoids=DB::name("video")->where("uid='{$id}'")->column('id');
        DB::name("video")->where("uid='{$id}'")->delete();
        
        DB::name("video_black")->where('videoid','in',$videoids)->delete();
        DB::name("video_black")->where('uid','=',$id)->delete();
        
        DB::name("video_comments")->where('videoid','in',$videoids)->delete();
        DB::name("video_comments_like")->where('videoid','in',$videoids)->delete();
        
        DB::name("video_like")->where('videoid','in',$videoids)->delete();
        DB::name("video_like")->where('uid','=',$id)->delete();
        
        DB::name("video_step")->where('videoid','in',$videoids)->delete();
        DB::name("video_step")->where('uid','=',$id)->delete();
        
        DB::name("video_report")->where('videoid','in',$videoids)->delete();
        DB::name("video_report")->where('touid','=',$id)->delete();
        /* 删除视频 相关*/
        
        
        /* 删除家族关系 */
        DB::name("family_user")->where("uid='{$id}'")->delete();
        /* 家族长处理 */
        $isexist=DB::name("family")->field("id")->where("uid={$id}")->find();
        if($isexist){
            $data=array(
                'state'=>3,
                'signout'=>2,
                'signout_istip'=>2,
            );
            DB::name("family_user")->where("familyid={$isexist['id']}")->update($data);				
            DB::name("family_profit")->where("familyid={$isexist['id']}")->delete();		
            DB::name("family_profit")->where("id={$isexist['id']}")->delete();		
        }




        //删除用户余额操作记录
        Db::name("user_balance_record")->where("uid={$id}")->delete();

        delcache("userinfo_".$id,"token_".$id);

        //删除极光IM用户id
        delIMUser($id);
        
        $this->success("删除成功！");
            
	}
    
    /* 禁用时间 */
    public function setBan(){
        
        $id = $this->request->param('id', 0, 'intval');
        $reason = $this->request->param('reason');
        $ban_long = $this->request->param('ban_long');
        
        if(!$id){
            $this->error('数据传入失败！');
        }
        
        if($ban_long){
            $ban_long=strtotime($ban_long);
        }else{
            $ban_long=0;
        }
        
        $data=[
            'uid'=>$id,
            'ban_long'=>$ban_long,
            'ban_reason'=>$reason,
            'addtime'=>time(),
        ];
        
        $result = Db::name("user_banrecord")->where(["uid" => $id])->update($data);
        if(!$result){
            $result=Db::name("user_banrecord")->insert($data);
        }
        if(!$result){
            $this->error('操作失败！');
        }
        
        Db::name("user")->where(["id" => $id])->update(['end_bantime'=>$ban_long]);
        
        $action="禁用会员：{$id}";
        setAdminLog($action);
        
        $live=Db::name("live")->field("uid")->where("islive='1'")->select()->toArray();
        foreach($live as $k=>$v){
            hSet($v['uid'] . 'shutup',$id,1);
        }
        
        $this->success("操作成功！");
    }

    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban(){

        $id = $this->request->param('id', 0, 'intval');
        $isdel = $this->request->param('isdel', 0, 'intval'); //直播举报禁用处理
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 0);
            if ($result) {
                
                $live=Db::name("live")->field("uid")->where("islive='1'")->select()->toArray();
                foreach($live as $k=>$v){
                    hSet($v['uid'] . 'shutup',$id,1);
                }

                if($isdel==1){ //直播举报禁用处理
                    $data=[
                        'status'=>1,
                        'uptime'=>time(),
                    ];
                    $rs = DB::name('report')->where("touid={$id}")->update($data);
                }
        
                $action="禁用会员：{$id}";
                setAdminLog($action);
                
                $this->success("会员拉黑成功！");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan(){

        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('end_bantime', 0);
            Db::name("user")->where(["id" => $id, "user_type" => 2])->update(['user_status'=>1,'end_bantime'=>0]);
            
            $action="启用会员：{$id}";
            setAdminLog($action);
                
            $this->success("会员启用成功！");
        } else {
            $this->error('数据传入失败！');
        }
    }
    
    /* 超管 */
    function setsuper(){
        
        $id = $this->request->param('id', 0, 'intval');
        $issuper = $this->request->param('issuper', 0, 'intval');
        
        $rs = DB::name('user')->where("id={$id}")->setField('issuper',$issuper);
        if(!$rs){
            $this->error("操作失败！");
        }
        
        if($issuper==1){
            $action="设置超管会员：{$id}";
            $isexist=DB::name("user_super")->where("uid={$id}")->find();
            if(!$isexist){
                DB::name("user_super")->insert(array("uid"=>$id,'addtime'=>time()));	
            }
            
            hSet('super',$id,'1');
        }else{
            $action="取消超管会员：{$id}";
            
            DB::name("user_super")->where("uid='{$id}'")->delete();
            hDel('super',$id);
        }
        
        setAdminLog($action);
        
        $this->success("操作成功！");
            
	}
    
    /* 热门 */
    function sethot(){
        
        $id = $this->request->param('id', 0, 'intval');
        $ishot = $this->request->param('ishot', 0, 'intval');
        
        $rs = DB::name('user')->where("id={$id}")->setField('ishot',$ishot);
        if(!$rs){
            $this->error("操作失败！");
        }
        DB::name("live")->where(array("uid"=>$id))->setField('ishot',$ishot);
        if($ishot==1){
            $action="设置热门会员：{$id}";
        }else{
            $action="取消热门会员：{$id}";
        }
        
        setAdminLog($action);
        
        $this->success("操作成功！");
            
	}
    
    /* 推荐 */
    function setrecommend(){
        
        $id = $this->request->param('id', 0, 'intval');
        $isrecommend = $this->request->param('isrecommend', 0, 'intval');
        
		$data=[
			'isrecommend'=>$isrecommend,
			'recommend_time'=>time(),
		];

		$rs = DB::name('user')->where("id={$id}")->update($data);
		if(!$rs){
			$this->error("操作失败！");
		}
		DB::name("live")->where(array("uid"=>$id))->update($data);
        if($isrecommend==1){
            $action="设置推荐会员：{$id}";
        }else{
            $action="取消推荐会员：{$id}";
        }
        
        setAdminLog($action);

        $this->success("操作成功！");
            
	}


    function add(){
		return $this->fetch();
	}
	function addPost(){
		if ($this->request->isPost()) {
            
            $data = $this->request->param();
            
			$user_login=$data['user_login'];

			if($user_login==""){
				$this->error("请填写手机号");
			}
            
            if(!checkMobile($user_login)){
                $this->error("请填写正确手机号");
            }
            
            $isexist=DB::name('user')->where(['user_login'=>$user_login])->value('id');
            if($isexist){
                $this->error("该账号已存在，请更换");
            }

            $data['mobile']=$user_login;
            
			$user_pass=$data['user_pass'];
			if($user_pass==""){
				$this->error("请填写密码");
			}
            
            if(!passcheck($user_pass)){
                $this->error("密码为6-20位字母数字组合");
            }
            
            $data['user_pass']=cmf_password($user_pass);
            

			$user_nicename=$data['user_nicename'];
			if($user_nicename==""){
				$this->error("请填写昵称");
			}

			$avatar=$data['avatar'];
			$avatar_thumb=$data['avatar_thumb'];
			if( ($avatar=="" || $avatar_thumb=='' ) && ($avatar!="" || $avatar_thumb!='' )){
                $this->error("请同时上传头像 和 头像小图  或 都不上传");
			}
            
            if($avatar=='' && $avatar_thumb==''){
                $data['avatar']='/default.jpg';
                $data['avatar_thumb']='/default_thumb.jpg';
            }

//            $rule = [
//                'is_dai'  => 'require',
//                'rate'  => 'require',
//            ];
//
//            $msg = [
//                'is_dai.require' => '是否代理',
//                'rate.require' => '返点必须',
//            ];
//            $validate = new Validate($rule, $msg);
//            $result   = $validate->check($data);
//            if(!$result) {
//                unset($data['is_dai']);
//                unset($data['rate']);
//            }else{
//                switch ($data['is_dai']){
//                    case 1:
//                        if($data['rate'] <= 0) $this->error('开启代理返点必须大于0');
//                        break;
//                    case 2:
//                        if($data['rate'] != 0) $this->error('关闭代理返点必须等于0');
//                        break;
//                }
//                $data['rate'] = $data['rate']/1000;
//            }

            $data['user_type']=2;
            $data['create_time']=time();
            $data['invite_code'] = random(8);

			$id = DB::name('user')->insertGetId($data);
            $up = [
                'parent_id'=>0,
                'invite_level'=>$id . '-'
            ];
            $res = Db::name('user')->where('id',$id)->update($up);

            $user_rate = [];
            //返点类型
            $rates = Db::table('cmf_user_rate')
                ->group('platform')
                ->field('platform,remark')
                ->select();
            foreach ($rates as $v){
                $user_rate[] = [
                    'user_id' => $id,
                    'platform' => $v['platform'],
                    'remark' => $v['remark'],
                    'rate' => 0
                ];
            }

            Db::table('cmf_user_rate')->insertAll($user_rate);

            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加会员：{$id}";
            setAdminLog($action);
            
            $this->success("添加成功！");
            
		}
	}
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('user')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['user_login']=m_s($data['user_login']);
        $this->assign('data', $data);
        return $this->fetch();
	}
	
	function editPost(){
		if ($this->request->isPost()) {
            
            $data = $this->request->param();

            //获取用户的状态
            $user_status=Db::name("user")->where("id={$data['id']}")->value('user_status');

            
			$user_pass=$data['user_pass'];
			if($user_pass!=""){
				if(!passcheck($user_pass)){
                    $this->error("密码为6-20位字母数字组合");
                }
                
                $data['user_pass']=cmf_password($user_pass);
			}else{
                unset($data['user_pass']);
            }
            
			$user_nicename=$data['user_nicename'];
			if($user_nicename==""){
				$this->error("请填写昵称");
			}

            if($user_status!=3){
                if(strstr($user_nicename,'已注销')!==false){
                    $this->error("非注销用户昵称不能包含已注销");
                }  
            }

            if(mb_substr($user_nicename, 0,1)=="="){
                $this->error("昵称内容非法");
            }

			$avatar=$data['avatar'];
			$avatar_thumb=$data['avatar_thumb'];
			if( ($avatar=="" || $avatar_thumb=='' ) && ($avatar!="" || $avatar_thumb!='' )){
                $this->error("请同时上传头像 和 头像小图  或 都不上传");
			}
            
            if($avatar=='' && $avatar_thumb==''){
                $data['avatar']='/default.jpg';
                $data['avatar_thumb']='/default_thumb.jpg';
            }
            
            $rs = $this->editParentId($data);
            

            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改会员信息：{$data['id']}";
            setAdminLog($action);

            //查询用户信息存入缓存中
            $info=Db::name("user")
                        ->field('id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,birthday,user_status,issuper')
                        ->where("id={$data['id']} and user_type=2")
                        ->find();


            if($info){
                setcaches("userinfo_".$data['id'],$info);
            }
            
            $this->success("修改成功！");
		}
	}
	
	
	public function delGameAccount()
	{
	    $id = input('id');
	    $key = "game:*:" . $id;
	    $keys = $GLOBALS['redisdb']->Keys($key);
        foreach($keys as $v)
        {
            delcache($v);
        }
        
        return json(['code' => 0, 'message' => '成功']);
	}

	//重置资金密码
    function re_money_pass(Request $request){
        $data = input();
        //判断口令
        if(!cmf_google_token_check(session('google_token'),$data['kou'])) {
            return json(['code'=>0,'msg'=>'口令验证失败']);
        }
        $info = Db::table('cmf_user_info')->where('user_id',$data['id'])->find();
        if ($info){
            $res = Db::table('cmf_user_info')->where('user_id',$data['id'])->update(['money_passwd'=>'']);
            return json(['code'=>1,"msg"=>'重置成功']);
        }else{
            return json(['code'=>0,'msg'=>'用户未设置资金密码']);
        }
        

    }

	function is_dai(Request $request)
    {
        if($request->isPost()){
            $data = input();
            $user_id = $data['id'];
            $is_dai = $data['is_dai'];
            $user = Db::name('user')->where('id', $user_id)->find();
            foreach ($data['rate'] as $v){
                if ($v >= 10){
                    $this->error('返点不能超过10%');
                }
            }
            if($is_dai != 1)
            {
                $this->error("代理必须设置为是");
            }
            if($user['parent_id'] != 0)
            {
                $invite_level = $user['invite_level'];
                $replace = substr($invite_level,0,strpos($invite_level,$user_id));

                if($is_dai == 1)
                {
                    Db::transaction(function () use($user_id,$invite_level,$replace){
                        Db::name('user')->where('id', $user_id)->update(['is_dai' => 1,'parent_id' => 0]);
                        Db::name('user')->where('invite_level','like', $invite_level . '%')->exp('invite_level', "REPLACE(invite_level,'$replace','')")->update();
                    });
                }
            }else{
                Db::name('user')->where('id', $user_id)->update(['is_dai' => 1,'parent_id' => 0]);
            }
//            $temp = Db::name('user')->where('invite_level','like',$user['invite_level'].'%')
//                ->where('id','<>',$user_id)
//                ->field('id')
//                ->select();
//            $down = [];
//            foreach ($temp as $v){
//                $down[] = $v['id'];
//            }
//            $temp2 = Db::name('user_rate')->where('user_id','in',$down)->where('rate','>',0)->find();
//            if ($temp2) $this->error("下级已有返点，无法修改");



            foreach ($data['platform'] as $k => $v){
                $user_rate = UserRate::where('user_id',$user_id)->where('platform',$v)->find();
                if($user_rate) {
                    $user_rate->rate = $data['rate'][$k] / 100;
                    $user_rate->save();
                }else{
                    $remark = '';
                
                    $remark = $data['remark'][$k];
                    $insert = [
                        'user_id' => $user_id,
                        'rate' => $data['rate'][$k] / 100,
                        'platform' => $v,
                        'remark' => $remark
                    ];
                    
                    $res = UserRate::create($insert);
                    if(!$res) $this->error("设置代理失败");
                }
            }

            $this->success("ok");
        }
        $id = input();
        if($id['id']){
            $user = User::field('id,is_dai,rate')->get($id['id']);
            $rate = config('app.rate_plat');
            $user_rate = Db::name('user_rate')
                ->where('user_id', $user['id'])
                ->select();

            $user_rates = [];

            if($user_rate){
                foreach ($user_rate as $k => $v){
                    $user_rates[$v['platform']] = $v['rate'] * 100;
                }
            }

            $rates = [];

            foreach($rate as $k => $v){
                $rates[$k]['platform'] = (string)$v['platform'];
                $rates[$k]['remark'] = $v['remark'];
                $rates[$k]['rate'] = 0;
                if(isset($user_rates[$v['platform']])) $rates[$k]['rate'] = $user_rates[$v['platform']];
            }

            $this->assign('user', $user);
            $this->assign('rates', $rates);
        }
        return $this->fetch();
    }
    
    
    protected function editParentId($data)
    {
        $u = Db::name('user')->where('id', $data['id'])->field('id,invite_level,parent_id')->find();
        $p_level = Db::name('user')->where('id', $data['parent_id'])->value('invite_level');

        if($u['parent_id'] != $data['parent_id'])
        {
           return Db::transaction(function () use($data,$u,$p_level){
                $id = $u['id'];
                $level = $u['invite_level'];
                $parent_id = $data['parent_id'];
                $p_level .=  $id . '-';
                Db::execute("update cmf_user set parent_id = {$parent_id} WHERE id = {$id}");
                Db::execute("update cmf_user_rate set rate = 0 WHERE user_id in (SELECT id FROM cmf_user WHERE invite_level like '{$level}%')");
                Db::execute("update cmf_user set invite_level = REPLACE(invite_level,'$level','$p_level') WHERE invite_level like '{$level}%'");
                DB::name('user')->update($data);
            });
        }else{
           return DB::name('user')->update($data);
        }
        
    }
}
