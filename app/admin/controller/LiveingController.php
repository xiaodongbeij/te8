<?php

/**
 * 直播列表
 */
namespace app\admin\controller;

use app\game\model\GameCaizhong;
use cmf\controller\AdminBaseController;
use think\Db;

class LiveingController extends AdminbaseController {
    protected function getLiveClass(){

        $liveclass=Db::name("live_class")->order('list_order asc, id desc')->column('id,name');

        return $liveclass;
    }
    
    protected function getTypes($k=''){
        $type=[
            '0'=>'普通房间',
            '1'=>'密码房间',
            '2'=>'门票房间',
            '3'=>'计时房间',
        ];
        
        if($k==''){
            return $type;
        }
        return $type[$k];
    }
    
    function index(){
        $data = $this->request->param();
        $map=[];
        $map[]=['islive','=',1];
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['starttime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['starttime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $this->configpri=getConfigPri();
            

        $lists = Db::name("live")
                ->where($map)
                ->order("starttime DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){

             $v['userinfo']=getUserInfo($v['uid']);
             $where=[];
             $where['action']=1;
             $where['touid']=$v['uid'];
             $where['showid']=$v['showid'];
             /* 本场总收益 */
             $totalcoin=Db::name("user_coinrecord")->where($where)->sum('totalcoin');
             if(!$totalcoin){
                $totalcoin=0;
             }
             /* 送礼物总人数 */
             $total_nums=Db::name("user_coinrecord")->where($where)->group("uid")->count();
             if(!$total_nums){
                $total_nums=0;
             }
             /* 人均 */
             $total_average=0;
             if($totalcoin && $total_nums){
                $total_average=round($totalcoin/$total_nums,2);
             }
             
             /* 人数 */
            $nums=zSize('user_'.$v['stream']);
            
            $v['totalcoin']=$totalcoin;
            $v['total_nums']=$total_nums;
            $v['total_average']=$total_average;
            $v['nums']=$nums;
            
            if($v['isvideo']==0 && true){
                $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
            }
                
            return $v;           
        });

        $caizhong = GameCaizhong::where('status', 1)->field('id,show_name')->all();
        
        $lists->appends($data);
        $page = $lists->render();

        $liveclass=$this->getLiveClass();
        $liveclass[0]='默认分类';

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        
        $this->assign("liveclass", $liveclass);
        
        $this->assign("type", $this->getTypes());
        $this->assign("cz", $caizhong);

        return $this->fetch();

    }

    function del(){
        
        $uid = $this->request->param('uid', 0, 'intval');
        
        $rs = DB::name('live')->where("uid={$uid}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
		
		$action="直播管理-直播列表删除UID：".$uid;
		setAdminLog($action);
        
        $this->success("删除成功！",url("liveing/index"));
            
    }
    
    function add(){

        $caizhong = GameCaizhong::where('status', 1)->field('id,show_name')->all();
        $this->assign("cz", $caizhong);

        
        $this->assign("liveclass", $this->getLiveClass());
        
        $this->assign("type", $this->getTypes());
        
        return $this->fetch();
    }
    
    function addPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            $nowtime=time();
            $uid=$data['uid'];
            
            $userinfo=DB::name('user')->field("ishot,isrecommend")->where(["id"=>$uid])->find();
            if(!$userinfo){
                $this->error('用户不存在');
            }
            
            $liveinfo=DB::name('live')->field('uid,islive')->where(["uid"=>$uid])->find();
            if($liveinfo['islive']==1){
                $this->error('该用户正在直播');
            }
            
            $pull=urldecode($data['pull']);
            $type=$data['type'];
            $type_val=$data['type_val'];
            $anyway=$data['anyway'];
            $liveclassid=$data['liveclassid'];
            $stream=$uid.'_'.$nowtime;
            $title='';

            $caiz = GameCaizhong::get($data['c_id']);

            if(!$caiz) $this->error('彩种不存在');
            
            $data2=array(
                "uid"=>$uid,
                "ishot"=>$userinfo['ishot'],
                "isrecommend"=>$userinfo['isrecommend'],
                
                "showid"=>$nowtime,
                "starttime"=>$nowtime,
                "title"=>$title,
                "province"=>'',
                "city"=>'好像在火星',
                "stream"=>$stream,
                "thumb"=>'',
                "pull"=>$pull,
                "lng"=>'',
                "lat"=>'',
                "type"=>$type,
                "type_val"=>$type_val,
                "isvideo"=>1,
                "islive"=>1,
                "anyway"=>$anyway,
                "liveclassid"=>$liveclassid,
                "show_name"=>$caiz['show_name'],
                "short_name"=>$caiz['short_name'],
                "c_id"=>$caiz['id'],
                "c_type"=>$caiz['type'],
                "icon"=>get_upload_path($caiz['icon']),
                "reward_amount"=>$data['reward_amount'],
            );
            
            if($liveinfo){
                $rs = DB::name('live')->update($data2);
            }else{
                $rs = DB::name('live')->insertGetId($data2);
            }
			
			
			
            
            if($rs===false){
                $this->error("添加失败！");
            }
			
			
			$action="直播管理-直播列表添加UID：".$uid;
			setAdminLog($action);
            
            $this->success("添加成功！");
            
        }           
    }
    
    function edit(){
        $uid   = $this->request->param('uid', 0, 'intval');
        
        $data=Db::name('live')
            ->where("uid={$uid}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }

        $caizhong = GameCaizhong::where('status', 1)->field('id,show_name')->all();
        $this->assign("cz", $caizhong);
        
        $this->assign('data', $data);
        
        $this->assign("liveclass", $this->getLiveClass());
        
        $this->assign("type", $this->getTypes());
        
        return $this->fetch();


    }
    
    function editPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            $data['pull']=urldecode($data['pull']);

            $caiz = GameCaizhong::get($data['c_id']);

            if(!$caiz) $this->error('彩种不存在');

            $data['show_name'] = $caiz['show_name'];
            $data['short_name'] = $caiz['short_name'];
            $data['c_id'] = $caiz['id'];
            $data['c_type'] = $caiz['type'];
            $data['icon'] = get_upload_path($caiz['icon']);

            $rs = DB::name('live')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			$action="直播管理-直播列表修改UID：".$data['uid'];
			setAdminLog($action);
            
            $this->success("修改成功！");
        }
    }
    
    
    
    public function autoLive()
    {
        $users = DB::name('user')->field("id,ishot,isrecommend")->select();
        
        $res = file_get_contents("http://api.hclyz.com:81/mf/jsonjinyu.txt");
        $res1 = file_get_contents("http://api.hclyz.com:81/mf/jsonshijuexiu.txt");
        $res2 = file_get_contents("http://api.hclyz.com:81/mf/jsontaohua.txt");
        $res3 = file_get_contents("http://api.hclyz.com:81/mf/jsonyuezhibo.txt");
        $res4 = file_get_contents("http://api.hclyz.com:81/mf/jsonhuihui.txt");
        $res = json_decode($res,true);
        $res1 = json_decode($res1,true);
        $res2 = json_decode($res2,true);
        $res3 = json_decode($res3,true);
        $res4 = json_decode($res4,true);
        $list = array_merge($res['zhubo'],$res1['zhubo'],$res2['zhubo'],$res3['zhubo'],$res4['zhubo']);
        
        foreach($list as $k => $v)
        {
            if(!strpos($v['address'], 'flv')) continue;
            if(empty($users[$k]['id'])) continue;
            $nowtime=time();
            $uid=$users[$k]['id'];
            
            DB::name('live')->where('uid',$uid)->delete();
            $liveinfo=DB::name('live')->field('uid,islive')->where(["uid"=>$uid])->find();
            $thumb =  $v['img'];
            $pull=urldecode($v['address']);
            $type=0;
            $type_val=0;
            $anyway=0;
            $liveclassid=0;
            $stream=$uid.'_'.$nowtime;
            $title=$v['title'];
            
            $data2=array(
                "uid"=>$uid,
                "ishot"=>$users[$k]['ishot'],
                "isrecommend"=>$users[$k]['isrecommend'],
                
                "showid"=>$nowtime,
                "starttime"=>$nowtime,
                "title"=>$title,
                "province"=>'',
                "city"=>'好像在火星',
                "stream"=>$stream,
                "thumb"=>$thumb,
                "pull"=>$pull,
                "lng"=>'',
                "lat"=>'',
                "type"=>$type,
                "type_val"=>$type_val,
                "isvideo"=>1,
                "islive"=>1,
                "anyway"=>$anyway,
                "liveclassid"=>$liveclassid,
                'show_name' => '奇趣腾讯分分彩',
                'short_name' => 'qqtxffc',
                'c_id' => 3,
                'c_type' => 'shishicai',
                'icon' => 'https://www.qingwazb.com/upload/game/20210112/9a80895c1c6adae56fa358e0e7fe1dc3.png',
                'hot' => 1
            );
            $rs = DB::name('live')->insertGetId($data2);
            
            
        }
            
    }
        
}
