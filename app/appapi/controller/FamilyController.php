<?php
/**
 * 家族
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class FamilyController extends HomebaseController {

	/* 家族驻地 */
	function home(){

		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $reset=isset($data['reset']) ? $data['reset']: '';
        $reset=(int)checkNull($reset);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);

		
		if($reset==1){
            return $this->fetch('home_user');
		}
		$type=0;
		
		$familyinfo=Db::name('family')->where(["uid"=>$uid])->find();
		if($familyinfo){
            
            $familyinfo['apply_pos']=get_upload_path($familyinfo['apply_pos']);
            $familyinfo['apply_side']=get_upload_path($familyinfo['apply_side']);
            $familyinfo['badge']=get_upload_path($familyinfo['badge']);
			$this->assign("familyinfo",$familyinfo);
			
			if($familyinfo['state']==0){
                return $this->fetch('apply_wait');
				exit;
			}else if($familyinfo['state']==1){
                if($familyinfo['istip']==1){
					Db::name('family')->where(["uid"=>$uid])->setField("istip",0);
                    return $this->fetch('apply_no');
				}else{
                    return $this->fetch('home_user');
                }
                exit;
			}else if($familyinfo['state']==2){
				if($familyinfo['istip']==1){
					Db::name('family')->where(["uid"=>$uid])->setField("istip",0);
                    return $this->fetch('apply_ok');
					exit; 
				}
				$type=1;
			}else if($familyinfo['state']==3){
				/* 家族解散 */
				Db::name('family')->where(["uid"=>$uid])->delete();
                return $this->fetch('sign_no2');
				exit; 
			}
		}
		
        $userfam=Db::name('family_user')->where(["uid"=>$uid])->find();

		if($userfam){

			$familyinfo=Db::name('family')->where("id={$userfam['familyid']}")->find();
            
            $familyinfo['apply_pos']=get_upload_path($familyinfo['apply_pos']);
            $familyinfo['apply_side']=get_upload_path($familyinfo['apply_side']);
            $familyinfo['badge']=get_upload_path($familyinfo['badge']);

			$this->assign("familyinfo",$familyinfo);
			$this->assign("userfam",$userfam);


			if($userfam['state']==0){
                return $this->fetch('attended_wait');
				exit;
			}else if($userfam['state']==1){
                if($userfam['istip']==1){
                    return $this->fetch('attended_no');
                }else{
                    return $this->fetch('home_user');
                }
				exit;
			}else if($userfam['state']==2){
				if($userfam['istip']==1){
					Db::name('family_user')->where(["uid"=>$uid])->setField("istip",0);
                    return $this->fetch('attended_ok');
					exit;
				}
				if($userfam['signout_istip']==1  ){
					Db::name('family_user')->where(["uid"=>$uid])->setField("signout_istip",0);
                    return $this->fetch('sign_no');
					exit;
				}
			}else if($userfam['state']==3){
				if($userfam['signout_istip']==1  ){
					/* 解除签约通过 */
					Db::name('family_user')->where(["uid"=>$uid])->delete();
                    return $this->fetch('sign_ok');
					exit;
				}else if($userfam['signout_istip']==2 ){
					/* 家族解散 */
					Db::name('family_user')->where(["uid"=>$uid])->delete();
                    return $this->fetch('sign_no2');
					exit;
				}else if($userfam['signout_istip']==3 ){
					/* 踢出 */
					Db::name('family_user')->where(["uid"=>$uid])->delete();
                    return $this->fetch('sign_no3');
					exit;
				}
			}
						
		}
		
		if($familyinfo){
            
            $divide_family=$familyinfo['divide_family'];
            if($userfam && $userfam['divide_family'] > -1){
                $divide_family=$userfam['divide_family'];
            }

            $configpri=getConfigPri();
            $divide_switch=$configpri['family_member_divide_switch'];
			
			$familyinfo['userinfo']=getUserInfo($familyinfo['uid']);
			$this->assign("familyinfo",$familyinfo);
			$this->assign("type",$type);
			$this->assign("divide_switch",$divide_switch);
			$this->assign("divide_family",$divide_family);
            return $this->fetch();
			exit;
		}

        return $this->fetch('home_user');

	}
	/* 设置家族默认分成 */
	public function setdivide()
	{
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $divide=isset($data['divide']) ? $data['divide']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $divide=checkNull($divide);
        
		$rs=array('code'=>0,'info'=>array(),'msg'=>'');

		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}
        
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){

			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}

		$data=array(
			'divide_family'=>$divide,
		);

		$result=Db::name('family')->where(["id"=>$familyid])->update($data);

		if($result!==false)
		{
			$rs['msg']='操作成功';
			echo json_encode($rs);
			exit;
		}
		else
		{
			$rs['code']=1002;
			$rs['msg']='操作失败';
			echo json_encode($rs);
			exit;
		}
	} 	
	/* 家族简介 */
	function setdes(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $familyid=(int)checkNull($familyid);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
		$type=0;
		$familyinfo=Db::name('family')->where(["id"=>$familyid])->find();
		if(!$familyinfo){
			$this->assign("reason",'家族不存在' );
            return $this->fetch(':error');
			exit;
		}
		
		if($familyinfo['uid']==$uid){
			$type=1;
		}

		$this->assign("uid",$uid);
		$this->assign("token",$token);
		$this->assign("type",$type);
		$this->assign("familyinfo",$familyinfo);
        return $this->fetch();
		
	}
	/* 设置家族简介 */
	function setdes_post()
	{
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $des=isset($data['des']) ? $data['des']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $des=checkNull($des);
        
		$rs=array('code'=>0,'info'=>array(),'msg'=>'');

		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){

			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}

		$data=array(
			'briefing'=>$des,
		);

		$result=Db::name('family')->where(["id"=>$familyid])->update($data);

		if($result!==false)
		{
			$rs['msg']='修改成功';
			echo json_encode($rs);
			exit;
		}
		else
		{
			$rs['code']=1002;
			$rs['msg']='修改失败';
			echo json_encode($rs);
			exit;
		}
	} 	
	/* 家族申请页面 */
	function apply(){
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
		
		
		//获取后台插件配置的七牛信息
        $qiniu_plugin=Db::name("plugin")->where("name='Qiniu'")->find();

        if(!$qiniu_plugin){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }
        $qiniu_config=json_decode($qiniu_plugin['config'],true);

        if(!$qiniu_config){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        $protocol=$qiniu_config['protocol']; //协议名称
        $domain=$qiniu_config['domain']; //七牛加速域名
        $zone=$qiniu_config['zone']; //存储区域代号

        if(!$protocol || !$domain || !$zone){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        $upload_url='';

        switch ($zone) {
            case 'z0': //华东
                $upload_url='up.qiniup.com';
                break;
            case 'z1': //华北
                $upload_url='up-z1.qiniup.com';
                break;
            case 'z2': //华南
                $upload_url='up-z2.qiniup.com';
                break;
            case 'na0': //北美
                $upload_url='up-na0.qiniup.com';
                break;
            case 'as0': //东南亚
                $upload_url='up-as0.qiniup.com';
                break;
            
            default:
                $upload_url='up.qiniup.com';
                break;
        }
		
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
        $user=[
            'id'=>$uid,
        ];
        session('user',$user);
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		$this->assign("protocol",$protocol);
        $this->assign("domain",$domain);
        $this->assign("upload_url",$upload_url);
		
        return $this->fetch();
	}
	//name 家族名称 申请人ID apply_side身份证反面 apply_pos身份证正面
	//apply_big 家族大图 apply_map家族小图 briefing家族简介 fullname真实姓名
	/* 家族申请提交 */
	public function add()
	{
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $name=isset($data['name']) ? $data['name']: '';
        $apply_side=isset($data['apply_side']) ? $data['apply_side']: '';
        $apply_pos=isset($data['apply_pos']) ? $data['apply_pos']: '';
        $apply_map=isset($data['apply_map']) ? $data['apply_map']: '';
        $briefing=isset($data['briefing']) ? $data['briefing']: '';
        $carded=isset($data['carded']) ? $data['carded']: '';
        $fullname=isset($data['fullname']) ? $data['fullname']: '';
        $divide_family=isset($data['divide_family']) ? $data['divide_family']: '0';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $name=checkNull($name);
        $apply_side=checkNull($apply_side);
        $apply_pos=checkNull($apply_pos);
        $apply_map=checkNull($apply_map);
        $briefing=checkNull($briefing);
        $carded=checkNull($carded);
        $fullname=checkNull($fullname);
        $divide_family=checkNull($divide_family);
        
		
		if(checkToken($uid,$token)==700){
			echo '{"state":"0","token":"'.$token.'","uid":"'.$uid.'","msg":"'.'您的登陆状态失效，请重新登陆！'.'"}';
			exit;
		} 
        if($fullname==''){
            echo '{"state":"0","msg":"'.'请填写个人姓名'.'"}';
			exit;
        }
        
        if($carded==''){
            echo '{"state":"0","msg":"'.'请填写身份证号'.'"}';
			exit;
        }
        
        if($apply_pos==''){
            echo '{"state":"0","msg":"'.'请上传身份证正面照'.'"}';
			exit;
        }
        
        if($apply_side==''){
            echo '{"state":"0","msg":"'.'请上传身份证背面照'.'"}';
			exit;
        }
        
        if($apply_map==''){
            echo '{"state":"0","msg":"'.'请上传家族图标'.'"}';
			exit;
        }
        
		$data2=array(
			'uid'=>$uid,
			'name'=>$name,
			'badge'=>$apply_map,
			'apply_pos'=>$apply_pos,
			'apply_side'=>$apply_side,
			'briefing'=>$briefing,
			'carded'=>$carded,
			'fullname'=>$fullname,
			'addtime'=>time(),
			'state'=>'0',
			'reason'=>'',
			'divide_family'=>$divide_family
		);
		$family=Db::name('family')->where(["uid"=>$uid])->find();
		if($family){
			if($family['state']==0){
				echo '{"state":"0","token":"'.$token.'","uid":"'.$uid.'","msg":"'.'您提交的家族申请正在审核中...'.'"}';
				exit;
			}
            
            if($family['state']==2){
				echo '{"state":"0","token":"'.$token.'","uid":"'.$uid.'","msg":"'.'家族已创建成功'.'"}';
				exit;
			}
			$res=Db::name('family')->where(["id"=>$family['id']])->update($data2);
			
		}
		else
		{
			$res=Db::name('family')->insert($data2);
		}
		
        /* 删除加入的家族关系 */
		Db::name("family_user")->where(["uid"=>$uid])->delete();

		echo '{"state":"1","name":"'.$name.'","uid":"'.$uid.'","token":"'.$token.'"}';
		exit;
	}
		
	function upload()
	{
        $file=isset($_FILES['file'])?$_FILES['file']:'';
        if($file){
            $name=$file['name'];
            $pathinfo = pathinfo($name);
            if(!isset($pathinfo['extension'])){
                $_FILES['file']['name']=$name.'.jpg';
            }
        }
        
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
	
	/* 撤销申请 */
	function revoke(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
		$rs=array('code'=>0,'info'=>array(),'msg'=>'撤销成功' );
		
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		
		$familyinfo=Db::name('family')->where(["uid"=>$uid])->find();
		if($familyinfo){
			if($familyinfo['state']==1){
				//$rs['code']=1001;
				//$rs['msg']='家族申请已审核通过，不能撤销';
				//echo json_encode($rs);
				//exit;
			}else if($familyinfo['state']==2){
				$rs['code']=1002;
				$rs['msg']='家族申请已审核通过，不能撤销';
				echo json_encode($rs);
				exit;
			}
		}
		$result=Db::name('family')->where(["uid"=>$uid])->delete();
		$rs['info']['uid']=$uid;
		$rs['info']['token']=$token;
		echo json_encode($rs);
		exit;
		
	}
	/* 家族中心 */
	public function index2()
	{
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
		
		$userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
			
		$list=Db::name('family')->where("disable=0 and state=2")->orderRaw("rand()")->limit(0,20)->select()->toArray();
		foreach($list as $k=>$v){
				$count=Db::name('family_user')->where("familyid=".$v['id']." and state=2")->count();
				$list[$k]['count']=$count;
				$isstatus=-1;
				if($userfam['familyid']==$v['id']){
					$isstatus=$userfam['state'];
				}
				$list[$k]['isstatus']=$isstatus;
		}
		$this->assign('list', $list);
        return $this->fetch();
	}
	
	public function attended_reload()
	{
		$rs=array('code'=>0,'info'=>array(),'msg'=>'');
		
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
        
		
		$map=array();
		
		$userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
			
		$list=Db::name('family')->where("disable=0 and state=2")->orderRaw("rand()")->limit(0,20)->select()->toArray();
		foreach($list as $k=>$v){
				$count=Db::name('family_user')->where("familyid=".$v['id']." and state=2")->count();
				$list[$k]['count']=$count;
				$isstatus=-1;
				if($userfam['familyid']==$v['id']){
					$isstatus=$userfam['state'];
				}
				$list[$k]['isstatus']=$isstatus;
		}
		$rs['info']=$list;
		echo json_encode($rs);
		exit;
	}
	
	public function attended_search()
	{
        
		$rs=array('code'=>0,'info'=>array(),'msg'=>'');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $key=isset($data['key']) ? $data['key']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $key=checkNull($key);
        
        if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
        
		if($key==''){
			$rs['code']=1001;
			$rs['msg']='请输入签约家族ID/名称';
			echo json_encode($rs);
			exit;
		}
		$map=[
            ['disable','=','0'],
            ['state','=','2'],
            ['id|name','like','%'.$key.'%'],
        ];
		
		$userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
			
		$list=Db::name('family')->where($map)->select()->toArray();
		foreach($list as $k=>$v){
				$count=Db::name('family_user')->where("familyid={$v['id']} and state=2")->count();
				$list[$k]['count']=$count;
				$isstatus=-1;
				if($userfam['familyid']==$v['id']){
					$isstatus=$userfam['state'];
				}
				$list[$k]['isstatus']=$isstatus;
		}
		$rs['info']=$list;
		echo json_encode($rs);
		exit;
	}
	/* 家族详情 */
	function detail(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		$familyinfo=Db::name('family')->where(["disable"=>0,"state"=>2,"id"=>$familyid])->find();
        if(!$familyinfo){
            $reason='家族不存在';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
        }
		
		$familyinfo['userinfo']=getUserInfo($familyinfo['uid']);
		$familyinfo['count']=Db::name('family_user')->where(["familyid"=>$familyid, "state"=>2])->count();
		
        $userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
        
		$isstatus=-1;
		if($userfam['familyid']==$familyinfo['id']){
			$isstatus=$userfam['state'];
		}
		$familyinfo['isstatus']=$isstatus;
		
		$list=Db::name('family_user')->where(["familyid"=>$familyid, "state"=>2])->order("addtime desc")->limit(0,50)->select()->toArray();
		foreach($list as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}

		$this->assign('familyinfo', $familyinfo);
		$this->assign('list', $list);
        return $this->fetch();
	}
	
	function detail_more(){
        
        $data = $this->request->param();
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $p=isset($data['page']) ? $data['page']: '1';
        $familyid=(int)checkNull($familyid);
        $p=(int)checkNull($p);
        
        if($p<1){
            $p=1;
        }
		$pnums=50;
		$start=($p-1)*$pnums;
        
		$list=Db::name('family_user')->where(["familyid"=>$familyid, "state"=>2])->order("addtime desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}
		
		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}

		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);
	 
		echo json_encode($result);
		exit;		
		
	}
	/* 签约家族 */
	function detail_sign(){
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		$userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
        
		$familyinfo=Db::name('family')->where(["disable"=>0 ,"state"=>2,"id"=>$familyid])->find();
		

		$isstatus=-1;
		if($userfam['familyid']==$familyinfo['id']){
			$isstatus=$userfam['state'];
		}
		$familyinfo['isstatus']=$isstatus;
		

		$this->assign('familyinfo', $familyinfo);
        return $this->fetch();
	}
	/* 申请签约 */
	public function attended_add()
	{

		$rs=array('code'=>0,'info'=>array(),'msg'=>'');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);

		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
        
		$fam=Db::name('family')->where(["uid"=>$uid])->find();
		if($fam)
		{	
			if($fam['state']==0){
				$rs['code']=1001;
				$rs['msg']='你已经拥有一个家族在申请中';
				echo json_encode($rs);
				exit;
			}else if($fam['state']==2){
				$rs['code']=1002;
				$rs['msg']='你已经拥有一个家族';
				echo json_encode($rs);
				exit;
			}
			Db::name('family')->where(["uid"=>$uid])->delete();
			Db::name('family_user')->where(["familyid"=>$fam['id']])->delete();
		} 
		
		
		$userfam=Db::name('family_user')->where(["uid"=>$uid])->find();
		$data=array(
			'uid'=>$uid,
			'familyid'=>$familyid,
			'addtime'=>time(),
			'uptime'=>time(),
			'reason'=>'',
			'state'=>'0',
			'signout'=>'0',
			'divide_family'=>'-1',
		);
		if($userfam)
		{
			
			if($userfam['state']=="2")
			{
				$rs['code']=1003;
				$rs['msg']='你已经加入家族';
				echo json_encode($rs);
				exit;
			}

			$time=time()-(60*60*24*10);
			if($userfam['state']=="0" && $userfam['addtime'] > $time)
			{
				$rs['code']=1004;
				$rs['msg']='您加入家族的申请还在审核中，请耐心等待';
				echo json_encode($rs);
				exit;
			}

			$family=Db::name('family_user')->where('id='.$userfam['id'])->update($data);
		}
		else
		{
			$family=Db::name('family_user')->insert($data);
		}
		
		
		$rs['msg']='申请加入家族提交成功';
		echo json_encode($rs);
		exit;
		
	}
	/* 撤销申请签约 */
	function attended_revoke(){
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'撤销成功' );
		
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

		$familyinfo=Db::name('family_user')->where(["uid"=>$uid])->find();
		if($familyinfo){
			if($familyinfo['state']==1){
				//$rs['code']=1001;
				//$rs['msg']='家族申请已审核通过，不能撤销';
				//echo json_encode($rs);
				//exit;
			}else if($familyinfo['state']==2){
				$rs['code']=1002;
				$rs['msg']='加入家族申请已审核通过，不能撤销';
				echo json_encode($rs);
				exit;
			}
		}
		$result=Db::name('family_user')->where(["uid"=>$uid])->delete();

		echo json_encode($rs);
		exit;
		
	}
	/* 签约审核 */
	public function examine()
	{
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
	
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
            return $this->fetch(':error');
			exit;
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token);
        
		$family=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$family){
			$this->assign("reason",'你不是该家族长，无权操作' );
            return $this->fetch(':error');
			exit;
		}

		$list=Db::name("family_user")->where(["familyid"=>$family['id'], "state"=>0])->select()->toArray();
		foreach($list as $k=>$v)
		{
			$userinfo=getUserInfo($v['uid']);
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}
		$this->assign("list", $list);
		$this->assign('family', $family);
		$this->assign('familyid', $familyid);
        return $this->fetch();
	}
	/* 审核处理 */
	public function examine_edit()
	{
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'操作成功');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        
        $type=isset($data['type']) ? $data['type']: '';
        $type=(int)checkNull($type);
        
        $touid=isset($data['touid']) ? $data['touid']: '';
        $touid=(int)checkNull($touid);
        
        $pass=isset($data['pass']) ? $data['pass']: '';
        $pass=checkNull($pass);
        
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}  
		
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){
			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}
		
		
		
		$isexist2=Db::name('family_user')->where(['familyid'=>$familyid,"uid"=>$touid])->find();
		if(!$isexist2){
			$rs['code']=1003;
			$rs['msg']='该会员不是该家族下的成员，无权操作';
			echo json_encode($rs);
			exit;
		}
		$data=array(
			'state'=>$type,
			'uptime'=>time(),
			'istip'=>'1',
			'reason'=>$pass
		);
		
		$result=Db::name('family_user')->where(["id"=>$isexist2['id']])->update($data);
		if($result===false)
		{
            $rs['code']=1002;
			$rs['msg']='操作失败';
			echo json_encode($rs);
			exit;
		}
        
        echo json_encode($rs);
        exit;
	}
	/* 家族成员 */
	public function member()
	{
		
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
	
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
            return $this->fetch(':error');
		} 
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
        
		$type="0";
		$familyinfo=Db::name("family")->where(["id"=>$familyid])->find();
		if($familyinfo['uid']==$uid)
		{
			$type="1";
		}

		$list=Db::name('family_user')->where(["familyid"=>$familyid,"state"=>"2"])->select()->toArray();
		foreach($list as $k=>$v)
		{
			$userinfo=getUserInfo($v['uid']);

            $userinfo['divide_family']=$familyinfo['divide_family'];
			if($v['divide_family'] > -1){
				$userinfo['divide_family']=$v['divide_family'];
			}
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}
		$this->assign('list', $list);
		$this->assign('type', $type);
		$this->assign('familyid', $familyid);
        return $this->fetch();
	}
	/* 成员独立抽成设置 */
	public function member_setdivide(){
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'操作成功');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $divide=isset($data['divide']) ? $data['divide']: '';
        $touid=isset($data['token']) ? $data['touid']: '';
        
        $uid=(int)checkNull($uid);
        $familyid=(int)checkNull($familyid);
        $touid=(int)checkNull($touid);
        $token=checkNull($token);
        $divide=checkNull($divide);

		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}
        
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){
			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}
		
        $isexist2=Db::name('family_user')->where(["familyid"=>$familyid, "uid"=>$touid, "state"=>"2"])->find();
		if(!$isexist2){

			$rs['code']=1003;
			$rs['msg']='该会员不是该家族下的成员，无权操作';
			echo json_encode($rs);
			exit;
		}

		if($divide<0||$divide>100||floor($divide)!=$divide){
			$rs['code']=1003;
			$rs['msg']='分成比例必须是0-100之间的整数';
			echo json_encode($rs);
			exit;
		}

		$configpri=getConfigPri();
		$family_member_divide_switch=$configpri['family_member_divide_switch'];

		if($family_member_divide_switch){ //需要管理员审核

			//判断管理员审核记录
			$apply_info=Db::name("family_user_divide_apply")->where("familyid={$familyid} and uid={$touid}")->find();

			if(!$apply_info){ //不存在申请记录
				$apply_data=array(
					'familyid'=>$familyid,
					'uid'=>$touid,
					'addtime'=>time(),
					'divide'=>$divide
				);

				$res=Db::name("family_user_divide_apply")->insert($apply_data);

				if($res===false){

		            $rs['code']=1002;
					$rs['msg']='操作失败';
					echo json_encode($rs);
					exit;
					
				}


			}else{

				$apply_data=array(
					'uptime'=>time(),
					'divide'=>$divide,
					'status'=>0
				);

				$res=Db::name("family_user_divide_apply")->where("familyid={$familyid} and uid={$touid}")->update($apply_data);

				if($res===false){

		            $rs['code']=1002;
					$rs['msg']='操作失败';
					echo json_encode($rs);
					exit;
					
				}

			}


			$rs['msg']='修改成功,请等待管理员审核';
			$rs['info']=array('is_apply'=>$family_member_divide_switch);


		}else{

			$data=array(
				'divide_family'=>$divide,
			);

	        $result=Db::name('family_user')->where(["uid"=>$touid])->update($data);

			
			if($result===false){

	            $rs['code']=1002;
				$rs['msg']='操作失败';
				echo json_encode($rs);
				exit;
				
			}

			$rs['info']=array('is_apply'=>$family_member_divide_switch);

		}
        
		echo json_encode($rs);
        exit;
	} 	
	/* 成员踢出 */
	public function member_del()
	{
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'操作成功');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $touid=isset($data['touid']) ? $data['touid']: '';
        $reason=isset($data['reason']) ? $data['reason']: '';
        $familyid=(int)checkNull($familyid);
        $touid=(int)checkNull($touid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $reason=checkNull($reason);
        
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}  
		
		
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){
			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}
		
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid,"uid"=>$touid,"state"=>"2"])->find();
		if(!$isexist2){
			$rs['code']=1003;
			$rs['msg']='该会员不是该家族下的成员，无权操作';
			echo json_encode($rs);
			exit;
		}
		
		$data=array(
			'state'=>3,
			'signout'=>3,
			'signout_istip'=>3,
			'signout_reason'=>$reason,
		);
		
		$result=Db::name('family_user')->where(['familyid'=>$familyid, "uid"=>$touid])->update($data);
		if($result===false)
		{
			$rs['code']=1002;
			$rs['msg']='操作失败';
			echo json_encode($rs);
			exit;
		}
        echo json_encode($rs);
        exit;
	}
	/* 解约申请管理 */
	public function signout()
	{
		
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
	
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		
		$family=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$family){
			$this->assign("reason",'你不是该家族长，无权操作');
            return $this->fetch(':error');
			exit;
		}


		$list=Db::name("family_user")->where(["familyid"=>$familyid, "signout"=>"1"])->select()->toArray();
		foreach($list as $k=>$v)
		{
			$userinfo=getUserInfo($v['uid']);
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}
		$this->assign("list", $list);
		$this->assign('familyid', $familyid);
        return $this->fetch();
	}
	
	/* 解约申请操作 */
	public function signout_post()
	{
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'操作成功');
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $touid=isset($data['touid']) ? $data['touid']: '';
        $type=isset($data['type']) ? $data['type']: '';
        $reason=isset($data['reason']) ? $data['reason']: '';
        $uid=(int)checkNull($uid);
        $familyid=(int)checkNull($familyid);
        $touid=(int)checkNull($touid);
        $type=(int)checkNull($type);
        $token=checkNull($token);
        $reason=checkNull($reason);
        
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}  
		
		
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();
		if(!$isexist){
			$rs['code']=1001;
			$rs['msg']='你不是该家族长，无权操作';
			echo json_encode($rs);
			exit;
		}
		
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid, "uid"=>$touid,"state"=>2])->find();
		if(!$isexist2){
			$rs['code']=1003;
			$rs['msg']='该会员不是该家族下的成员，无权操作';
			echo json_encode($rs);
			exit;
		}
		
		if($type==1){
			$data=array(
				'state'=>3,
				'signout'=>2,
				'signout_istip'=>1,
				'signout_reason'=>$reason,
			);
			$result=Db::name('family_user')->where(['familyid'=>$familyid,'uid'=>$touid])->update($data);
		}else{
			$data=array(
				'signout'=>0,
				'signout_istip'=>1,
				'signout_reason'=>$reason,
			);
			$result=Db::name('family_user')->where(['familyid'=>$familyid,'uid'=>$touid])->update( $data );
		}

		
		if($result===false)
		{
			$rs['msg']='操作失败';
			echo json_encode($rs);
			exit;
		}

        echo json_encode($rs);
        exit;
	}
	/* 解除签约 */
	function relieve(){
		
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
        return $this->fetch();
	}
	/* 解除提交 */
	public function retreat()
	{
		
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
	
		$rs=array('code'=>0,'info'=>array(),'msg'=>'申请成功，请等待家族长审核');
		if(checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		}  
		
		$data=array(
			'signout'=>'1'
		);
		$result=DB::name('family_user')->where(["uid"=>$uid])->update($data);
		if($result===false)
		{
			$rs['code']=1002;
			$rs['msg']='操作失败';
			echo json_encode($rs);
			exit;
			
		}
        echo json_encode($rs);
        exit;
	}
	/* 家族收益 */
	public function profit()
	{
		
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
	
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
		
		$isexist=Db::name('family')->where(["id"=>$familyid,"uid"=>$uid])->find();

		
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid,"uid"=>$uid, "state"=>"2"])->find();
		if(!$isexist && !$isexist2){
			$this->assign("reason",'您不是该家族成员，无权操作' );
            return $this->fetch(':error');
			exit;
		}

		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		

		$list=Db::name('family_profit')->field("time,uid,sum(profit) as profitzong,sum(profit_anthor) as anthor_totoal")->where(["familyid"=>$familyid])->group("uid,time")->order("time desc")->limit("0,50")->select()->toArray();
		foreach($list as $k=>$v)
		{
			$list[$k]['userinfo']=getUserInfo($v['uid']);
			$list[$k]['profitzong']=NumberFormat($v['profitzong']);
			$list[$k]['anthor_totoal']=NumberFormat($v['anthor_totoal']);
		}

		$this->assign('list', $list);
		$this->assign('familyid', $familyid);

        return $this->fetch();
	}
	
	public function profit_more()
	{
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $p=isset($data['page']) ? $data['page']: '';
        $familyid=(int)checkNull($familyid);
        $p=(int)checkNull($p);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
		
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$isexist=Db::name('family')->where(["id"=>$familyid, 'uid'=>$uid])->find();

		
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid, "uid"=>$uid, "state"=>"2"])->find();
		if(!$isexist && !$isexist2){
			echo json_encode($result);
			exit;
		}
		
		if($p<1){
            $p=1;
        }
		$pnums=50;
		$start=($p-1)*$pnums;
		

		$list=Db::name('family_profit')->field("time,uid,sum(profit) as profitzong,sum(profit_anthor) as anthor_totoal")->where(["familyid"=>$familyid])->group("uid,time")->order("time desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v)
		{
			$usreinfo=getUserInfo($v['uid']);
			$list[$k]['usreinfo']=$usreinfo;
			$list[$k]['profitzong']=NumberFormat($v['profitzong']);
			$list[$k]['anthor_totoal']=NumberFormat($v['anthor_totoal']);
		}

		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}
	/* 主播数据 */
	public function long()
	{
		
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			return $this->fetch(':error');
		} 
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
		
		$isexist=Db::name('family')->where(["id"=>$familyid, "uid"=>$uid])->find();
        
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid,"uid"=>$uid, "state"=>"2"])->find();
		if(!$isexist && !$isexist2){
			$this->assign("reason",'您不是该家族成员，无权操作' );
            return $this->fetch(':error');
			exit;
		}
		
		$list=Db::name('live_record l')
			->field("l.uid,l.time,sum(l.endtime-l.starttime) as total")
			->leftJoin('family_user f','l.uid=f.uid')
			->where("f.state=2 and l.starttime > f.uptime and f.familyid={$familyid}")
			->group('uid,time')
            ->order('time desc')
			->limit(0,50)
			->select()
            ->toArray();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
			$list[$k]['total']=getSeconds($v['total'],1);
		}

//        dump($list);die;
		$this->assign('list', $list);
		$this->assign('familyid', $familyid);
		
        return $this->fetch();
	}

	public function long_more()
	{
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $p=isset($data['page']) ? $data['page']: '';
        $familyid=(int)checkNull($familyid);
        $p=(int)checkNull($p);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$isexist=Db::name('family')->where(["id"=>$familyid, "uid"=>$uid])->find();
        
		$isexist2=Db::name('family_user')->where(["familyid"=>$familyid,"uid"=>$uid,"state"=>"2"])->find();
		if(!$isexist && !$isexist2){
			echo json_encode($result);
			exit;
		}
		
		if($p<1){
            $p=1;
        }
		$pnums=50;
		$start=($p-1)*$pnums;
		
		$list=Db::name('live_record l')
			->field("l.uid,l.time,sum(l.endtime-l.starttime) as total")
			->leftJoin('family_user f','l.uid=f.uid')
			->where("f.state=2 and l.starttime > f.uptime and f.familyid={$familyid}")
			->group('uid,time')
            ->order('time desc')
			->limit($start,$pnums)
			->select()
            ->toArray();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
			$list[$k]['total']=getSeconds($v['total'],1);
		}


		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}

	/* 家族成员分成申请列表 */
	public function divide_list(){
		
        
        $data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $familyid=isset($data['familyid']) ? $data['familyid']: '';
        $familyid=(int)checkNull($familyid);
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
	
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
            return $this->fetch(':error');
		} 
		
		$this->assign("uid",$uid);
		$this->assign("token",$token);
        
		$familyinfo=Db::name("family")->where(["id"=>$familyid])->find();

		if($familyinfo['uid']!=$uid){ //非家族长
			$this->assign("reason",'家族长才可以查看');
            return $this->fetch(':error');
		}

		$list=Db::name('family_user_divide_apply')->where(["familyid"=>$familyid])->order("uptime desc,addtime desc")->select()->toArray();

		foreach($list as $k=>$v){

			$userinfo=getUserInfo($v['uid']);

            $userinfo['divide_family']=$v['divide'];
			
			$userinfo['fansnum']=getFansnums($v['uid']);
			$list[$k]['userinfo']=$userinfo;
		}

		$status=array(
			'-1'=>'拒绝',
			'0'=>'审核中',
			'1'=>'通过',
		);

		$this->assign('list', $list);
		$this->assign('status', $status);
		$this->assign('familyid', $familyid);
        return $this->fetch();
	}
	
	//获取上传驱动的token
    public function getuploadtoken(){
        
        $uploader = new Upload();
        $result = $uploader->getuploadtoken();

        if ($result === false) {
            echo json_encode(array("ret"=>0,'file'=>'','msg'=>'获取失败'));
            exit;
        }
 
        echo json_encode(
            array(
                "ret"=>200,
                "token"=>$result['token'],
                'domain'=>$result['domain'],
                'msg'=>''
            )
        );

        exit;
    }

}