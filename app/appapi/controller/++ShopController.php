<?php
/**
 * 店铺认证
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class ShopController extends HomebaseController {
	
	function index(){       
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $reset=isset($data['reset']) ? $data['reset']: '0';
        $uid=(int)checkNull($uid);
        $reset=(int)checkNull($reset);
        $token=checkNull($token);
		
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			return $this->fetch(':error');
		} 
		
        if(!$reset){
            $shop=Db::name('shop_apply')->where(['uid'=>$uid])->find();
            
            if($shop){
                /* 已提交 */
                $shop['thumb1']=get_upload_path($shop['thumb']);
                $shop['license1']=get_upload_path($shop['license']);
                $shop['certificate1']=get_upload_path($shop['certificate']);
                $shop['other1']=get_upload_path($shop['other']);
                
                $this->assign("uid",$uid);
                $this->assign("token",$token);
                $this->assign("info",$shop);
                
                if($shop['status']!=1){
                    return $this->fetch('status');
                }
                return $this->fetch('apply');
            }
        }
        
        $fans_ok=0;
        
        $configpri=getConfigPri();
        
        $shop_fans=$configpri['shop_fans'];
        
        $fans=Db::name("user_attention")->where(['touid'=>$uid])->count();
        if($fans>=$shop_fans){
            $fans_ok=1;
        }
        
        $level_ok=0;
        $shop_level=$configpri['shop_level'];
        
        $userinfo=getUserInfo($uid);
        if($userinfo['level_anchor']>=$shop_level){
            $level_ok=1;
        }
        
        $bond_ok=0;
        $shop_bond=$configpri['shop_bond'];
        if($shop_bond<=0){
            $bond_ok=1;
        }else{
           $bond=Db::name("shop_bond")->where(['uid'=>$uid,'status'=>1])->find();
            if($bond){
                $bond_ok=1;
            } 
        }
        
        
        $isapply=0;
        if($fans_ok==1 && $level_ok==1 && $bond_ok==1){
            $isapply=1;
        }

		$this->assign("uid",$uid);
		$this->assign("token",$token);
		$this->assign("shop_fans",$shop_fans);
		$this->assign("fans_ok",$fans_ok);
		$this->assign("shop_level",$shop_level);
		$this->assign("level_ok",$level_ok);
		$this->assign("shop_bond",$shop_bond);
		$this->assign("bond_ok",$bond_ok);
		$this->assign("isapply",$isapply);

		return $this->fetch();
	    
	}
    
    function bond(){
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $info=Db::name("shop_bond")->where(['uid'=>$uid,'status'=>1])->find();
        
        $configpri=getConfigPri();
        
        $shop_bond=$configpri['shop_bond'];
        $this->assign("shop_bond",$shop_bond);
        $this->assign("uid",$uid);
        $this->assign("token",$token);
        $this->assign("info",$info);
        
        return $this->fetch();
    }
    
    function bond_post(){
        
        $rs = array('code' => 0, 'msg' => '缴纳成功', 'info' => array());
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        if(checkToken($uid,$token)==700){
            $rs['code']=700;
            $rs['msg']='您的登陆状态失效，请重新登陆！';
            echo json_encode($rs);
			exit;
		}
        
        
        $bond=Db::name("shop_bond")->where(['uid'=>$uid,'status'=>1])->find();
        if($bond){
            $rs['code']=1002;
            $rs['msg']='已缴纳保证金';
            echo json_encode($rs);
			exit;
        }
        
        
        $configpri=getConfigPri();
        
        $shop_bond=$configpri['shop_bond'];
        
        $where=[
            ['id','=',$uid],
            ['coin','>=',$shop_bond],
        ];
        $isok=Db::name('user')->where($where)->setDec('coin',$shop_bond);
        
        if(!$isok){
            $rs['code']=1001;
            $rs['msg']='余额不足';
            echo json_encode($rs);
			exit;
        }
        
        Db::name("shop_bond")->insert(array("uid"=>$uid,"bond"=>$shop_bond,"status"=>1,"addtime"=>time(),"uptime"=>time() ));
        
        Db::name("user_coinrecord")->insert(array("type"=>'0',"action"=>'14',"uid"=>$uid,"touid"=>$uid,"giftid"=>0,"giftcount"=>1,"totalcoin"=>$shop_bond,"addtime"=>time() ));
        
        echo json_encode($rs);
        exit;
    }

	function apply(){
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $reset=isset($data['reset']) ? $data['reset']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $reset=checkNull($reset);
        
		
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			return $this->fetch(':error');
		} 
        
        $user=[
            'id'=>$uid,
        ];
        session('user',$user);
		$info=[];
        if(!$reset){
            $info=Db::name('shop_apply')->where(['uid'=>$uid])->find();
        
            if($info){
                $info['thumb1']=get_upload_path($info['thumb']);
                $info['license1']=get_upload_path($info['license']);
                $info['certificate1']=get_upload_path($info['certificate']);
                $info['other1']=get_upload_path($info['other']);
                
                if($info['status']!=1){
                    $this->assign("uid",$uid);
                    $this->assign("token",$token);
                    $this->assign("info",$info);
        
                    return $this->fetch('status');
                }
            }
        }
        

		$this->assign("uid",$uid);
		$this->assign("token",$token);
		$this->assign("info",$info);


		return $this->fetch();
	    
	}

	function apply_post(){
        
        $rs = array('code' => 0, 'msg' => '申请提交成功', 'info' => array());
        
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $thumb=isset($data['thumb']) ? $data['thumb']: '';
        $thumb=checkNull($thumb);
        
        $name=isset($data['name']) ? $data['name']: '';
        $name=checkNull($name);
        
        $des=isset($data['des']) ? $data['des']: '';
        $des=checkNull($des);
        
        $tel=isset($data['tel']) ? $data['tel']: '';
        $tel=checkNull($tel);
        
        $certificate=isset($data['certificate']) ? $data['certificate']: '';
        $certificate=checkNull($certificate);
        
        $license=isset($data['license']) ? $data['license']: '';
        $license=checkNull($license);
        
        $other=isset($data['other']) ? $data['other']: '';
        $other=checkNull($other);
        
		
		if(checkToken($uid,$token)==700){
            $rs['code']=700;
            $rs['msg']='您的登陆状态失效，请重新登陆！';
            echo json_encode($rs);
			exit;
		}
        
        $configpri=getConfigPri();
        
        $shop_fans=$configpri['shop_fans'];
        
        $fans=Db::name("user_attention")->where(['touid'=>$uid])->count();
        if($fans < $shop_fans){
            $rs['code']=1003;
            $rs['msg']='申请条件未达成';
            echo json_encode($rs);
            exit;
        }
        
        
        $shop_level=$configpri['shop_level'];
        
        $userinfo=getUserInfo($uid);
        if($userinfo['level_anchor']<$shop_level){
            $rs['code']=1003;
            $rs['msg']='申请条件未达成';
            echo json_encode($rs);
            exit;
        }
        
        $shop_bond=$configpri['shop_bond'];
        $bond=Db::name("shop_bond")->where(['uid'=>$uid,'status'=>1])->find();
        if(!$bond){
            $rs['code']=1003;
            $rs['msg']='申请条件未达成';
            echo json_encode($rs);
            exit;
        }        
        
        $isexist=Db::name('shop_apply')->where(['uid'=>$uid])->find();
		if($isexist){
            if($isexist['status']==0){
                $rs['code']=1001;
                $rs['msg']='申请审核中';
                echo json_encode($rs);
                exit;
            }
        }
        
        if($thumb==''){
            $rs['code']=1002;
            $rs['msg']='请上传店铺图片';
            echo json_encode($rs);
            exit;
        }
        
        if($name==''){
            $rs['code']=1002;
            $rs['msg']='请输入店铺名称';
            echo json_encode($rs);
            exit;
        }
        
        if($des==''){
            $rs['code']=1002;
            $rs['msg']='请输入店铺简介';
            echo json_encode($rs);
            exit;
        }
        
        if($certificate==''){
            $rs['code']=1002;
            $rs['msg']='请上传营业执照';
            echo json_encode($rs);
            exit;
        }
        
        if($license==''){
            $rs['code']=1002;
            $rs['msg']='请上传许可证';
            echo json_encode($rs);
            exit;
        }
        
        $nowtime=time();
        
        $data=[
            'uid'=>$uid,
            'name'=>$name,
            'thumb'=>$thumb,
            'des'=>$des,
            'tel'=>$tel,
            'certificate'=>$certificate,
            'license'=>$license,
            'other'=>$other,
            'addtime'=>$nowtime,
            'uptime'=>$nowtime,
            'status'=>0,
        ];
        
        $configpri=getConfigPri();
        $show_switch=$configpri['show_switch'];
        if($show_switch==0){
            $data['status']=1;
        }
        
        if($isexist){
            Db::name('shop_apply')->where(['uid'=>$uid])->update($data);
        }else{
            Db::name('shop_apply')->insert($data);
        }
        echo json_encode($rs);
        exit;
	    
	}
    
    
	/* 图片上传 */
	public function upload(){
        
        $uploader = new Upload();
        $uploader->setFileType('image');
        $result = $uploader->upload();

        if ($result === false) {
            
            echo json_encode(array("ret"=>0,'file'=>'','msg'=>$uploader->getError()));
            exit;
        }
        
        /* $result=[
            'filepath'    => $arrInfo["file_path"],
            "name"        => $arrInfo["filename"],
            'id'          => $strId,
            'preview_url' => cmf_get_root() . '/upload/' . $arrInfo["file_path"],
            'url'         => cmf_get_root() . '/upload/' . $arrInfo["file_path"],
        ]; */
        
        echo json_encode(array("ret"=>200,'data'=>array("url"=>$result['url']),'msg'=>''));
        exit;
	
	}
}