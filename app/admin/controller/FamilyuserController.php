<?php

/**
 * 家族成员
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class FamilyuserController extends AdminbaseController {
    
    protected function getState($k=''){
        $status=array(
            '0'=>'未审核',
            '1'=>'审核失败',
            '2'=>'审核通过',
        );
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }

    protected function getApplyStatus($k=''){
        $status=array(
            '0'=>'等待审核',
            '1'=>'审核通过',
            '-1'=>'审核拒绝',
        );
        if($k===''){
            return $status;
        }
        
        return isset($status[$k]) ? $status[$k]: '';
    }
    
    protected function getFamily($k=''){
        $list = Db::name('family')
            ->where('state=2')
            ->order("id desc")
            ->column('*','id');
        
        if($k===''){
            return $list;
        }
        
        return isset($list[$k]) ? $list[$k]: '';
    }

	function index()
	{
        $data = $this->request->param();
        $map=[];
        $map[]=['state','<>',3];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $state=isset($data['state']) ? $data['state']: '';
        if($state!=''){
            $map[]=['state','=',$state];
        }
        
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        if($familyid!=''){
            $map[]=['familyid','=',$familyid];
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
			
        $this->family=$this->getFamily();
        
    	$lists = Db::name("family_user")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
			$v['family']=$this->family[$v['familyid']];
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
    	$this->assign("state", $this->getState());
    	
    	return $this->fetch();
	}
	
    function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('family_user')->where("id={$id}")->find();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $data=array(
            'state'=>3,
            'signout'=>3,
            'signout_istip'=>3,
        );
            
        DB::name("family_user")->where(["id"=>$id])->update($data);	
        
        $action="删除家族成员：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！");
	}
	
	function add()
	{
		return $this->fetch();
	}
	function addPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$uid=$data['uid'];

			if($uid==""){
				$this->error("请填写用户ID");
			}
            
            $isexist=DB::name('user')->where(["id"=>$uid,"user_type"=>2])->value('id');
            if(!$isexist){
                $this->error("该用户不存在");
            }
            
            $isfamily=DB::name('family')->where(["uid"=>$uid])->find();
            if($isfamily){
                $this->error("该用户已是家族长");
            }
            
            $isexist=DB::name('family_user')->where(["uid"=>$uid])->find();
            if($isexist && $isexist['state']==2){
                $this->error('该用户已申请家族');
            }
            
            
			$familyid=$data['familyid'];
			if($familyid==""){
				$this->error("请填写家族ID");
			}
            $family=DB::name("family")->where(["id"=>$familyid])->find();
            if(!$family){
                $this->error('该家族不存在');
            }
			
            if($family['state']!=2){
                $this->error('该家族未通过审核');
            }
            
            $data['state']=2;
            $data['addtime']=time();
            $data['uptime']=time();
            
            if($isexist){
                $id = DB::name('family_user')->where(['uid'=>$uid])->update($data);
            }else{
                $id = DB::name('family_user')->insertGetId($data);
            }
            
            if(!$id){
                $this->error("添加失败！");
            }
            
            $action="添加家族成员：{$uid}";
            setAdminLog($action);
            
            $this->success("添加成功！");
            
		}
	
	}
    
    
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('family_user')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $userinfo=getUserInfo($data['uid']);
        
        $family=Db::name("family")->field("name,divide_family")->where(["id"=>$data['familyid']])->find();
        
        $this->assign('data', $data);
        $this->assign('family', $family);
        $this->assign('userinfo', $userinfo);
        $this->assign('state', $this->getState());
        return $this->fetch();				

	}


	function editPost(){

        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            $data['uptime']=time();
            
			$rs = DB::name('family_user')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            $action="修改家族成员信息：{$data['uid']}";
            setAdminLog($action);
            
            $this->success("修改成功！");
		}
        	
	}

     //家族成员分成申请
    function divideapply(){
        
       $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        if($familyid!=''){
            $map[]=['familyid','=',$familyid];
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
            
        $this->family=$this->getFamily();
        
        $lists = Db::name("family_user_divide_apply")
                ->where($map)
                ->order("addtime DESC")
                ->paginate(20);

 

        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            $v['family']=$this->getFamily($v['familyid']);
            return $v;
        });

        
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        $this->assign("status", $this->getApplyStatus());
        
        return $this->fetch(); 
    }

    //分成申请处理
    function applyedit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('family_user_divide_apply')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $userinfo=getUserInfo($data['uid']);
        
        $family=Db::name("family")->field("name,divide_family")->where(["id"=>$data['familyid']])->find();
        
        $this->assign('data', $data);
        $this->assign('family', $family);
        $this->assign('userinfo', $userinfo);
        $this->assign('status', $this->getApplyStatus());
        return $this->fetch();
    }

    //家族成员分成申请修改提交
    function applyeditPost(){

        if ($this->request->isPost()) {


            //获取后台的审核开关
            $configpri=getConfigPri();
            $family_member_divide_switch=$configpri['family_member_divide_switch'];

            if(!$family_member_divide_switch){
                $this->error("家族长修改成员分成比例管理员审核开关关闭时不可修改");
            }
            
            $data = $this->request->param();

            $divide=$data['divide'];
            $status=$data['status'];

            if(!is_numeric($divide)){
                $this->error("分成比例请填写数字");
            }

            if($divide<0||$divide>100){
                $this->error("分成比例在0-100之间");
            } 

            if(floor($divide)!=$divide){
                $this->error("分成比例必须为整数");
            }
            
            $data['uptime']=time();
            
            $rs = DB::name('family_user_divide_apply')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }

            $action="修改家族成员分成比例：{$data['uid']}";

            if($status==1){ //审核通过

                //修改家族成员分成比例
                $data1=array(
                    'uptime'=>time(),
                    'divide_family'=>$divide

                );

                Db::name("family_user")->where("uid={$data['uid']} and familyid={$data['familyid']}")->update($data1);

                $action.=',成功';
            }else if($status==-1){
                $action.=',拒绝';
            }else{
                $action.=',等待审核';
            }
            
            
            setAdminLog($action);
            
            $this->success("修改成功！",'Familyuser/divideapply');
        }
    }

    //家族分成删除
    function delapply(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('family_user_divide_apply')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
       
        $action="删除家族成员分成申请：{$id}";
        setAdminLog($action);
        
        $this->success("删除成功！");
    }
    
}