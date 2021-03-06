<?php


namespace app\user\controller;


use app\game\model\GameCate;
use app\user\model\User;
use app\user\model\UserRate;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class LiverController extends AdminbaseController
{
    public function index(){

        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }

        $data = $this->request->param();
        $map=[];
        $map[]=['user_type','=',2];
        $liver_ids = Db::name('live')->column('uid');
        $map[]=['id','in',$liver_ids];

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';

        if($start_time!=""){
            $map[]=['create_time','>=',strtotime($start_time)];
        }

        if($end_time!=""){
            $map[]=['create_time','<=',strtotime($end_time) + 60*60*24];
        }

        $iszombie=isset($data['iszombie']) ? $data['iszombie']: '';
        if($iszombie!=''){
            $map[]=['iszombie','=',$iszombie];
        }

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

        $iszombiep=isset($data['iszombiep']) ? $data['iszombiep']: '';
        if($iszombiep!=''){
            $map[]=['iszombiep','=',$iszombiep];
        }

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

        $nums=Db::name("user")->where($map)->count();

        $list = Db::name("user")
            ->where($map)
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
        // ??????????????????
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('nowtime', time());
        $this->assign('name_coin', $configpub['name_coin']);
        $this->assign('name_votes', $configpub['name_votes']);
        $this->assign('nums', $nums);
        // ??????????????????
        return $this->fetch();
    }

    function del(){

        $id = $this->request->param('id', 0, 'intval');

        $user_login = DB::name('user')->where(["id"=>$id,"user_type"=>2])->value('user_login');
        $rs = DB::name('user')->where(["id"=>$id,"user_type"=>2])->delete();
        if(!$rs){
            $this->error("???????????????");
        }

        $action="???????????????{$id} - {$user_login}";
        setAdminLog($action);

        /* ???????????? */
        DB::name("user_auth")->where("uid='{$id}'")->delete();
        /* ?????????????????? */
        DB::name("live")->where("uid='{$id}'")->delete();
        DB::name("live_record")->where("uid='{$id}'")->delete();
        /* ????????????????????? */
        DB::name("live_manager")->where("uid='{$id}' or liveuid='{$id}'")->delete();

        /*  ???????????????*/
        DB::name("user_black")->where("uid='{$id}' or touid='{$id}'")->delete();
        /* ?????????????????? */
        DB::name("user_attention")->where("uid='{$id}' or touid='{$id}'")->delete();

        /* ???????????? */
        DB::name("user_zombie")->where("uid='{$id}'")->delete();
        /* ???????????? */
        DB::name("user_super")->where("uid='{$id}'")->delete();
        /* ???????????? */
        DB::name("vip_user")->where("uid='{$id}'")->delete();

        /* ?????????????????? */
        DB::name("agent")->where("uid='{$id}' or one_uid={$id}")->delete();
        /* ????????????????????? */
        DB::name("agent_code")->where("uid='{$id}'")->delete();
        /* ?????????????????? */
        DB::name("agent_profit")->where("uid='{$id}'")->delete();
        /* ???????????????????????? */
        DB::name("agent_profit_recode")->where("one_uid='{$id}'")->delete();

        /* ???????????? */
        DB::name("car_user")->where("uid='{$id}'")->delete();


        /* ????????????PUSHID */
        DB::name("user_pushid")->where("uid='{$id}'")->delete();
        /* ?????????????????? */
        DB::name("cash_account")->where("uid='{$id}'")->delete();
        /* ????????????????????? */
        DB::name("label_user")->where("touid='{$id}'")->delete();

        /* ???????????? */
        DB::name("backpack")->where("uid='{$id}'")->delete();

        /* ???????????? ?????? */
        $dynamicids=DB::name("dynamic")->where("uid='{$id}'")->column('id');
        DB::name("dynamic")->where("uid='{$id}'")->delete();

        DB::name("dynamic_comments")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_comments_like")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_like")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_report")->where('dynamicid','in',$dynamicids)->delete();
        DB::name("dynamic_report")->where('touid','=',$id)->delete();
        /* ???????????? ??????*/

        /* ???????????? */
        DB::name("feedback")->where('uid','=',$id)->delete();

        /* ???????????? */
        DB::name("guard_user")->where('uid','=',$id)->delete();
        DB::name("guard_user")->where('liveuid','=',$id)->delete();

        /* ???????????? */
        DB::name("liang")->where('uid','=',$id)->delete();

        /* ???????????? */
        DB::name("live_kick")->where('uid','=',$id)->delete();

        /* ???????????? */
        DB::name("live_kick")->where('uid','=',$id)->delete();

        /* ???????????? */
        DB::name("live_shut")->where('uid','=',$id)->delete();

        /* ?????????????????? */
        DB::name("music_collection")->where('uid','=',$id)->delete();

        /* ???????????? */
        DB::name("report")->where('touid','=',$id)->delete();

        /* ?????????????????? */
        DB::name("shop_apply")->where('uid','=',$id)->delete();

        //$goodsid=DB::name("shop_goods")->where("uid='{$id}'")->column('id');
        DB::name("shop_goods")->where('uid','=',$id)->delete();
        /* ?????????????????? */

        /* ???????????? */
        DB::name("user_banrecord")->where("uid='{$id}'")->delete();

        /* ???????????? */
        DB::name("user_sign")->where("uid='{$id}'")->delete();

        /* ?????????????????? */
        DB::name("user_voterecord")->where("uid='{$id}'")->delete();

        /* ???????????? */
        DB::name("user_scorerecord")->where("touid='{$id}'")->delete();

        /* ???????????? ?????? */
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
        /* ???????????? ??????*/


        /* ?????????????????? */
        DB::name("family_user")->where("uid='{$id}'")->delete();
        /* ??????????????? */
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



        //??????????????????
        Db::name("shop_address")->where("uid={$id}")->delete();

        //??????????????????
        Db::name("seller_goods_class")->where("uid={$id}")->delete();
        //??????????????????
        Db::name("shop_apply")->where("uid={$id}")->delete();
        //???????????????????????????
        Db::name("shop_goods")->where("uid={$id}")->delete();
        //??????????????????????????????
        Db::name("user_balance_record")->where("uid={$id}")->delete();
        //????????????????????????
        Db::name("paidprogram_apply")->where("uid={$id}")->delete();

        //????????????????????????
        Db::name("paidprogram_comment")->where("uid={$id} or touid={$id}")->delete();
        //????????????????????????
        Db::name("paidprogram_order")->where("uid= {$id} or touid={$id}")->update(array('isdel'=>1));



        delcache("userinfo_".$id,"token_".$id);

        //????????????IM??????id
        delIMUser($id);

        $this->success("???????????????");

    }

    /* ???????????? */
    public function setBan(){

        $id = $this->request->param('id', 0, 'intval');
        $reason = $this->request->param('reason');
        $ban_long = $this->request->param('ban_long');

        if(!$id){
            $this->error('?????????????????????');
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
            $this->error('???????????????');
        }

        Db::name("user")->where(["id" => $id])->update(['end_bantime'=>$ban_long]);

        $action="???????????????{$id}";
        setAdminLog($action);

        $live=Db::name("live")->field("uid")->where("islive='1'")->select()->toArray();
        foreach($live as $k=>$v){
            hSet($v['uid'] . 'shutup',$id,1);
        }

        $this->success("???????????????");
    }

    /**
     * ??????????????????
     * @adminMenu(
     *     'name'   => '??????????????????',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '??????????????????',
     *     'param'  => ''
     * )
     */
    public function ban(){

        $id = $this->request->param('id', 0, 'intval');
        $isdel = $this->request->param('isdel', 0, 'intval'); //????????????????????????
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 0);
            if ($result) {

                $live=Db::name("live")->field("uid")->where("islive='1'")->select()->toArray();
                foreach($live as $k=>$v){
                    hSet($v['uid'] . 'shutup',$id,1);
                }

                if($isdel==1){ //????????????????????????
                    $data=[
                        'status'=>1,
                        'uptime'=>time(),
                    ];
                    $rs = DB::name('report')->where("touid={$id}")->update($data);
                }

                $action="???????????????{$id}";
                setAdminLog($action);

                $this->success("?????????????????????");
            } else {
                $this->error('??????????????????,???????????????,?????????????????????');
            }
        } else {
            $this->error('?????????????????????');
        }
    }

    /**
     * ??????????????????
     * @adminMenu(
     *     'name'   => '??????????????????',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '??????????????????',
     *     'param'  => ''
     * )
     */
    public function cancelBan(){

        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('end_bantime', 0);
            Db::name("user")->where(["id" => $id, "user_type" => 2])->update(['user_status'=>1,'end_bantime'=>0]);

            $action="???????????????{$id}";
            setAdminLog($action);

            $this->success("?????????????????????");
        } else {
            $this->error('?????????????????????');
        }
    }

    /* ?????? */
    function setsuper(){

        $id = $this->request->param('id', 0, 'intval');
        $issuper = $this->request->param('issuper', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('issuper',$issuper);
        if(!$rs){
            $this->error("???????????????");
        }

        if($issuper==1){
            $action="?????????????????????{$id}";
            $isexist=DB::name("user_super")->where("uid={$id}")->find();
            if(!$isexist){
                DB::name("user_super")->insert(array("uid"=>$id,'addtime'=>time()));
            }

            hSet('super',$id,'1');
        }else{
            $action="?????????????????????{$id}";

            DB::name("user_super")->where("uid='{$id}'")->delete();
            hDel('super',$id);
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ?????? */
    function sethot(){

        $id = $this->request->param('id', 0, 'intval');
        $ishot = $this->request->param('ishot', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('ishot',$ishot);
        if(!$rs){
            $this->error("???????????????");
        }
        DB::name("live")->where(array("uid"=>$id))->setField('ishot',$ishot);
        if($ishot==1){
            $action="?????????????????????{$id}";
        }else{
            $action="?????????????????????{$id}";
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ?????? */
    function setrecommend(){

        $id = $this->request->param('id', 0, 'intval');
        $isrecommend = $this->request->param('isrecommend', 0, 'intval');

        $data=[
            'isrecommend'=>$isrecommend,
            'recommend_time'=>time(),
        ];

        $rs = DB::name('user')->where("id={$id}")->update($data);
        if(!$rs){
            $this->error("???????????????");
        }
        DB::name("live")->where(array("uid"=>$id))->update($data);
        if($isrecommend==1){
            $action="?????????????????????{$id}";
        }else{
            $action="?????????????????????{$id}";
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ??????????????? */
    function setzombie(){

        $id = $this->request->param('id', 0, 'intval');
        $iszombie = $this->request->param('iszombie', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('iszombie',$iszombie);
        if(!$rs){
            $this->error("???????????????");
        }

        if($iszombie==1){
            $action="????????????????????????{$id}";
        }else{
            $action="????????????????????????{$id}";
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ?????????????????????????????? */
    function setzombieall(){

        $iszombie = $this->request->param('iszombie', 0, 'intval');

        $rs = DB::name('user')->where('user_type=2')->setField('iszombie',$iszombie);
        if(!$rs){
            $this->error("???????????????");
        }

        if($iszombie==1){
            $action="???????????????????????????";
        }else{
            $action="???????????????????????????";
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ??????????????? */
    function setzombiep(){

        $id = $this->request->param('id', 0, 'intval');
        $iszombiep = $this->request->param('iszombiep', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('iszombiep',$iszombiep);
        if(!$rs){
            $this->error("???????????????");
        }

        if($iszombiep==1){
            $action="????????????????????????{$id}";
            $isexist=DB::name("user_zombie")->where("uid={$id}")->find();
            if(!$isexist){
                DB::name("user_zombie")->insert(array("uid"=>$id));
            }
        }else{
            $action="????????????????????????{$id}";

            DB::name("user_zombie")->where("uid='{$id}'")->delete();
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    /* ????????????????????? */
    function setzombiepall(){
        $data = $this->request->param();
        $ids = $data['ids'];
        if(!$ids){
            $this->error("???????????????");
        }

        $tids=join(",",$ids);
        $iszombiep = $this->request->param('iszombiep', 0, 'intval');

        $rs = DB::name('user')->where('id', 'in', $ids)->setField('iszombiep',$iszombiep);
        if(!$rs){
            $this->error("???????????????");
        }

        if($iszombiep==1){
            $action="????????????????????????{$tids}";
            foreach($ids as $k=>$v){
                $isexist=DB::name("user_zombie")->where("uid={$v}")->find();
                if(!$isexist){
                    DB::name("user_zombie")->insert(array("uid"=>$v));
                }
            }

        }else{
            $action="????????????????????????{$tids}";

            DB::name("user_zombie")->where('uid', 'in', $ids)->delete();
        }

        setAdminLog($action);

        $this->success("???????????????");

    }

    function add(){
        return $this->fetch();
    }
    function addPost(){
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $user_login=$data['user_login'];

            if($user_login==""){
                $this->error("??????????????????");
            }

            if(!checkMobile($user_login)){
                $this->error("????????????????????????");
            }

            $isexist=DB::name('user')->where(['user_login'=>$user_login])->value('id');
            if($isexist){
                $this->error("??????????????????????????????");
            }

            $data['mobile']=$user_login;

            $user_pass=$data['user_pass'];
            if($user_pass==""){
                $this->error("???????????????");
            }

            if(!passcheck($user_pass)){
                $this->error("?????????6-20?????????????????????");
            }

            $data['user_pass']=cmf_password($user_pass);


            $user_nicename=$data['user_nicename'];
            if($user_nicename==""){
                $this->error("???????????????");
            }

            $avatar=$data['avatar'];
            $avatar_thumb=$data['avatar_thumb'];
            if( ($avatar=="" || $avatar_thumb=='' ) && ($avatar!="" || $avatar_thumb!='' )){
                $this->error("????????????????????? ??? ????????????  ??? ????????????");
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
//                'is_dai.require' => '????????????',
//                'rate.require' => '????????????',
//            ];
//            $validate = new Validate($rule, $msg);
//            $result   = $validate->check($data);
//            if(!$result) {
//                unset($data['is_dai']);
//                unset($data['rate']);
//            }else{
//                switch ($data['is_dai']){
//                    case 1:
//                        if($data['rate'] <= 0) $this->error('??????????????????????????????0');
//                        break;
//                    case 2:
//                        if($data['rate'] != 0) $this->error('??????????????????????????????0');
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
            //????????????
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
                $this->error("???????????????");
            }

            $action="???????????????{$id}";
            setAdminLog($action);

            $this->success("???????????????");

        }
    }
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');

        $data=Db::name('user')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("????????????");
        }

        $data['user_login']=m_s($data['user_login']);
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost(){
        if ($this->request->isPost()) {

            $data = $this->request->param();

            //?????????????????????
            $user_status=Db::name("user")->where("id={$data['id']}")->value("user_status");


            $user_pass=$data['user_pass'];
            if($user_pass!=""){
                if(!passcheck($user_pass)){
                    $this->error("?????????6-20?????????????????????");
                }

                $data['user_pass']=cmf_password($user_pass);
            }else{
                unset($data['user_pass']);
            }

            $user_nicename=$data['user_nicename'];
            if($user_nicename==""){
                $this->error("???????????????");
            }

            if($user_status!=3){
                if(strstr($user_nicename,'?????????')!==false){
                    $this->error("??????????????????????????????????????????");
                }
            }

            if(mb_substr($user_nicename, 0,1)=="="){
                $this->error("??????????????????");
            }

            $avatar=$data['avatar'];
            $avatar_thumb=$data['avatar_thumb'];
            if( ($avatar=="" || $avatar_thumb=='' ) && ($avatar!="" || $avatar_thumb!='' )){
                $this->error("????????????????????? ??? ????????????  ??? ????????????");
            }

            if($avatar=='' && $avatar_thumb==''){
                $data['avatar']='/default.jpg';
                $data['avatar_thumb']='/default_thumb.jpg';
            }

            $rs = DB::name('user')->update($data);
            if($rs===false){
                $this->error("???????????????");
            }

            $action="?????????????????????{$data['id']}";
            setAdminLog($action);

            //?????????????????????????????????
            $info=Db::name("user")
                ->field('id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,birthday,user_status,issuper,qq,wechat')
                ->where("id={$data['id']} and user_type=2")
                ->find();


            if($info){
                setcaches("userinfo_".$data['id'],$info);
            }

            $this->success("???????????????");
        }
    }

    function is_dai(Request $request)
    {
        if($request->isPost()){
            $data = input();
            $user_id = $data['id'];
            $is_dai = $data['is_dai'];
            $user = Db::name('user')->where('id', $user_id)->find();
            if($is_dai != 1)
            {
                $this->error("????????????????????????");
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
            $temp = Db::name('user')->where('invite_level','like',$user['invite_level'].'%')
                ->where('id','<>',$user_id)
                ->field('id')
                ->select();
            $down = [];
            foreach ($temp as $v){
                $down[] = $v['id'];
            }
            $temp2 = Db::name('user_rate')->where('user_id','in',$down)->where('rate','>',0)->find();
            if ($temp2) $this->error("?????????????????????????????????");



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
                    if(!$res) $this->error("??????????????????");
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
}