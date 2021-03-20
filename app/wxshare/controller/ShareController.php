<?php
namespace app\wxshare\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class ShareController extends HomebaseController {
    
    public function index(){
		
//        $list=Db::name('live')->field("uid,title,city,stream,pull,thumb")->where(["islive"=>1])->order("isrecommend desc,starttime desc")->limit(0,20)->select()->toArray();
        $list=Db::name('live')->field("uid,title,stream,pull,thumb")->where(["islive"=>1])->order("isrecommend desc,starttime desc")->limit(0,20)->select()->toArray();

		foreach($list as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['thumb']=get_upload_path($v['thumb']);
			if(!$v['thumb']){
				$v['thumb']=$v['avatar'];
			}
            $list[$k]=$v;
		}
		
		$this->assign('list',$list);
		
		/* session('uid',null);
		session('token',null);
		session('openid',null);
		session('unionid',null);
		session('userinfo',null); */

		return $this->fetch();
    }
	
	public function show(){
        
        $roomnum   = $this->request->param('roomnum', 0, 'intval');
        
		$liveinfo=array();
		$configpri=getConfigPri();
		$this->assign('configpri',$configpri);
		
		$config=getConfigPub();
		$this->assign('config',$config);
        
        $anchor=getUserInfo($roomnum);
        
		$liveinfo=Db::name("live")->field("uid,islive,stream,pull,isvideo,type,goodnum,wy_cid")->where(['uid'=>$roomnum,'islive'=>1])->find();
		if(!$liveinfo){
			$goodnum=getUserLiang($roomnum);
			$liveinfo['uid']=$roomnum;
			$liveinfo['type']=0;
			$liveinfo['goodnum']=$goodnum['name'];
			$liveinfo['islive']='0';
			$liveinfo['pull']='';
			$liveinfo['isvideo']=1;
			$liveinfo['stream']='';
			$liveinfo['wy_cid']='';
		}
        
        if($liveinfo['islive']==0){
            $liveinfo['type']=0;
            $liveinfo['pull']='';
        }
        
        $liveinfo['user_nicename']=$anchor['user_nicename'];
        $liveinfo['avatar']=$anchor['avatar'];
        $liveinfo['avatar_thumb']=$anchor['avatar_thumb'];
        
        if($liveinfo['goodnum']==0){
            $liveinfo['goodnum']=$liveinfo['uid'];
        }
		
		if($liveinfo['isvideo']==1){
			$hls=$liveinfo['pull'] ;
		}else{
			$hls='';
            if($liveinfo['islive'] && $liveinfo['type']==0 ){
                if($configpri['cdn_switch']==5){
                    $hls=$liveinfo['pull'] ;
                }else{
                    $hls=PrivateKeyA('http',$liveinfo['stream'].'.m3u8',0);
                }
                
            }
			
		}
        
        
		$this->assign('livetype',$liveinfo['type']);
		$this->assign('hls',$hls);
		$this->assign('liveinfo',$liveinfo);
		
		$isattention=0;

		//session("uid",'21770');
		//session("token",'df9d994a430aa3179a867ebe92a40b6f21e3464114f9d8a4f4fb05d3abc7be76');
		$uid=(int)session("uid");
        
        if($uid==$anchor['id']){
            $this->assign('reason','不能进入自己的直播间');
            return $this->fetch('error');
            exit;
        }
        $userinfo=[];
		//$uid=12;
		if($uid){
            
            $res=$this->checkShut($uid,$anchor['id']);
            if($res==0){
                $this->assign('reason','您已被踢出房间');
                return $this->fetch('error');
            }
			$userinfo=getUserPrivateInfo($uid);
			
			$isexist=Db::name('user_attention')->where(['uid'=>$uid,'touid'=>$liveinfo['uid']])->find();
			if($isexist){
				$isattention=1;
			}
		}
		$this->assign('isattention',$isattention);
		$this->assign('userinfo',$userinfo);
        if($userinfo){
            $this->assign('userinfoj',json_encode($userinfo));
        }else{
            $this->assign('userinfoj','null');
        }
		
        
        
        /* 等级 */
        $level=getLevelList();
        $levellist=array();
        foreach($level as $k=>$v){
            $levellist[$v['levelid']]=$v;
        }
        $levelanchor=getLevelAnchorList();
        $levelanchorlist=array();
        foreach($levelanchor as $k=>$v){
            $levelanchorlist[$v['levelid']]=$v;
        }

        $this->assign("levellist",$levellist );
        $this->assign("levellistj",json_encode($levellist) );
        $this->assign("levelanchorlist",$levelanchorlist );
        $this->assign("levelanchorlistj",json_encode($levelanchorlist) );
        
        $sensitive_words=str_replace(array("\r\n", "\r", "\n"), "", $configpri['sensitive_words']);
        $words_a=explode(',',$sensitive_words);
        
        $this->assign("words_j",json_encode($words_a) );

		return $this->fetch();
	}
	
	public function wxLogin(){
        
        $roomnum   = $this->request->param('roomnum', 0, 'intval');
        
		$configpri=getConfigPri();
		
		$AppID = $configpri['login_wx_appid'];
		$callback  = get_upload_path('/wxshare/Share/wxLoginCallback?roomnum='.$roomnum); //回调地址
		//微信登录
		if (!session_id()) session_start();
		//-------生成唯一随机串防CSRF攻击
		$state  = md5(uniqid(rand(), TRUE));
		$_SESSION["wx_state"]    = $state; //存到SESSION
		$callback = urlencode($callback);
		//snsapi_base 静默  snsapi_userinfo 授权
		$wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$AppID}&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect ";
		
		header("Location: $wxurl");
	}
	
	public function wxLoginCallback(){
        
        $roomnum   = $this->request->param('roomnum', 0, 'intval');
        $code   = $this->request->param('code');
        
		if($code){
			$configpri=getConfigPri();
		
			$AppID = $configpri['login_wx_appid'];
			$AppSecret = $configpri['login_wx_appsecret'];
			/* 获取token */
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
            
            if(isset($arr['errcode'])){
                $this->assign('reason',$arr['errmsg']);
                return $this->fetch('error');
            }
            
			/* 刷新token 有效期为30天 */
			$url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$AppID}&grant_type=refresh_token&refresh_token={$arr['refresh_token']}";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			
			$url="https://api.weixin.qq.com/sns/userinfo?access_token={$arr['access_token']}&openid={$arr['openid']}&lang=zh_CN";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$wxuser=json_decode($json,1);

			/* 公众号绑定到 开放平台 才有 unionid  否则 用 openid  */
			$openid=$wxuser['unionid'];
			if(!$openid){
                $this->assign('reason','公众号未绑定到开放平台');
                return $this->fetch('error');
			}
            $type='wx';
			$userinfo=DB::name("user")->field("id,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,votestotal,province,city,birthday,user_status,login_type,last_login_time")->where("openid!='' and openid='{$openid}' and login_type='{$type}'")->find();
            $nowtime=time();
			if(empty($userinfo)){	
				if($openid==""){
                    $this->assign('reason','登录错误');
                    return $this->fetch('error');
                }
					
                $user_login=$type.'_'.time().rand(100,999);
                $user_pass=cmf_password('123456');
				
				
				$reg_reward=$configpri['reg_reward'];
                
                $data=array(
                    'openid' 	=>$openid,
                    'user_login'	=> $user_login, 
                    'user_pass'		=>$user_pass,
                    'user_nicename'	=> $wxuser['nickname'],
                    'sex'=> $wxuser['sex'],
                    'avatar'=> $wxuser['headimgurl'],
                    'avatar_thumb'	=> $wxuser['headimgurl'],
                    'login_type'=> $type,
                    'last_login_ip' =>get_client_ip(0,true),
                    'create_time' => $nowtime,
                    'last_login_time' => $nowtime,
                    'coin' => $reg_reward,
                    'user_status' => 1,
                    "user_type"=>2,//会员
                    'signature' =>'这家伙很懒，什么都没留下',
                );	
                $userid=DB::name("user")->insertGetId($data);
                
                $userinfo=DB::name("user")->field("id,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,votestotal,province,city,birthday,user_status,login_type,last_login_time")->where(['id'=>$userid])->find();
				
			} 
            if(!$userinfo){
                $this->assign('reason','登录错误');
                return $this->fetch('error');
            }
            
			$userinfo['level']=getLevel($userinfo['consumption']);

			$token=md5(md5($userinfo['id'].time()));
			$expiretime=time()+60*60*24*300;

            $isok=DB::name("user_token")
			->where("user_id={$userinfo['id']}")
			->update(array("token"=>$token, "expire_time"=>$expiretime , "create_time"=>$nowtime ));
            if(!$isok){
                DB::name("user_token")
                ->insert(array("user_id"=>$userinfo['id'],"token"=>$token, "expire_time"=>$expiretime , "create_time"=>$nowtime ));
            }
            
            if($userinfo['birthday']){
                $userinfo['birthday']=date('Y-m-d',$userinfo['birthday']);   
            }else{
                $userinfo['birthday']='';
            }
            
			$userinfo['token']=$token; 
            
            delcache("token_".$userinfo['id']);


			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('openid',$wxuser['openid']);
			session('unionid',$wxuser['unionid']);
			session('userinfo',$userinfo);
			
			$href='/wxshare/Share/show?roomnum='.$roomnum;
			
		 	header("Location: $href");
			
		}else{
			
			
			
		}
		
	}
	
	
	/* 手机验证码 */
	public function getCode(){
		
		$config=getConfigPri();
        
        $mobile = $this->request->param('mobile');
        
        /* $where="user_login='{$mobile}'";
        
		$checkuser = checkUser($where);	
        
        if($checkuser){
            $rs['errno']=1006;
            $rs['errmsg']='该手机号已注册，请登录';
            echo json_encode($rs);
            exit;
        } */

		if(isset($_SESSION['mobile']) && $_SESSION['mobile']==$mobile && isset($_SESSION['mobile_expiretime']) && $_SESSION['mobile_expiretime']> time() ){
            $rs['errno']=1007;
            $rs['errmsg']='验证码5分钟有效，勿多发';
            echo json_encode($rs);
            exit;
		}


        $limit = ip_limit();	
		if( $limit == 1){
            $rs['errno']=1003;
            $rs['errmsg']='您已当日发送次数过多';
            echo json_encode($rs);
            exit;
		}	
		$mobile_code = random(6,1);

		$result = sendCode($mobile,$mobile_code); 
		if($result['code']===0){
			$_SESSION['mobile'] = $mobile;
			$_SESSION['mobile_code'] = $mobile_code;
			$_SESSION['mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['mobile'] = $mobile;
            $_SESSION['mobile_code'] = $result['msg'];
            $_SESSION['mobile_expiretime'] = time() +60*5;
            
            $rs['errno']=0;
            $rs['errmsg']="验证码为：{$result['msg']}";
            echo json_encode($rs);
            exit;
		}else{
            $rs['errno']=1004;
            $rs['errmsg']=$result['msg'];
            echo json_encode($rs);
            exit;

		}

		$rs=array(
			'errno'=>0,
			'data'=>array(),
			'errmsg'=>'验证码已送',
		);
		
		echo json_encode($rs);
		exit;
	}
	
	/* 登录 */
/* 	$user_login!=$_SESSION['mobile'] */
	public function userLogin(){
        
        $mobile = $this->request->param('mobile');
        $code = $this->request->param('code');
		$user_login=checkNull($mobile);
		$code=checkNull($code);
        
        
		$rs=array('errno'=>0,'data'=>array(),'errmsg'=>'');
        
		if( !isset($_SESSION['mobile']) || !isset($_SESSION['mobile_code']) ){
            $rs['errno']=1120;
            $rs['errmsg']='请先获取验证码';
            echo json_encode($rs);
			exit;	
        }
		
		if($user_login!=$_SESSION['mobile']){	
            $rs['errno']=1120;
            $rs['errmsg']='手机号码不一致';
            echo json_encode($rs);
			exit;					
		}

		if($code!=$_SESSION['mobile_code']){
            $rs['errno']=1120;
            $rs['errmsg']='验证码错误';
            echo json_encode($rs);
			exit;				
			
		}	
        
        $nowtime=time();
        
		$userinfo=Db::name("user")->field("id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,birthday,issuper,user_status")->where("user_login='{$user_login}' and user_type='2'")->find();
		
		if(!$userinfo){
			$pass='yunbaokj';
			$user_pass=cmf_password($pass);
			
			$configpri=getConfigPri();
			$reg_reward=$configpri['reg_reward'];
			
			/* 无信息 进行注册 */
			$data=array(
				'user_login' => $user_login,
//				'user_email' => '',
				'mobile' =>$user_login,
				'user_nicename' =>'请设置昵称',
				'user_pass' =>$user_pass,
				'signature' =>'这家伙很懒，什么都没留下',
				'avatar' =>'/default.jpg',
				'avatar_thumb' =>'/default_thumb.jpg',
				'last_login_ip' =>get_client_ip(0,true),
				'create_time' => $nowtime,
				'last_login_time' => $nowtime,
				'coin' => $reg_reward,
				'user_status' => 1,
				"user_type"=>2,//会员
			);	
			$userid=Db::name("user")->insertGetId($data);	
			$userinfo=array(
				'id' => $userid,
				'user_login' => $data['user_login'],
				'user_nicename' => $data['user_nicename'],
				'avatar' => $data['avatar'],
				'avatar_thumb' => $data['avatar_thumb'],
				'sex' => '2',
				'signature' => $data['signature'],
				'consumption' => 0,
				'votestotal' => 0,
				'province' => '',
				'city' => '',
				'coin' => $reg_reward,
				'votes' => 0,
				'birthday' => '',
				'issuper' => 0,
				'user_status' => 1,
			);
		} 
		
		if($userinfo['user_status']==0){
			$rs['errno']=1002;
			$rs['errmsg']='账号已被禁用';
			echo json_encode($rs);
			exit;	
		}
		$userinfo['level']=getLevel($userinfo['consumption']);
        

        $token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
        $expiretime=time()+60*60*24*300;
        
        $isok=DB::name("user_token")
            ->where("user_id={$userinfo['id']}")
            ->update(array("token"=>$token, "expire_time"=>$expiretime , "create_time"=>$nowtime ));
        if(!$isok){
            DB::name("user_token")
            ->insert(array("user_id"=>$userinfo['id'],"token"=>$token, "expire_time"=>$expiretime , "create_time"=>$nowtime ));
        }
    
        $userinfo['token']=$token;

        

        delcache("token_".$userinfo['id']);


		session('uid',$userinfo['id']);
		session('token',$userinfo['token']);
		session('user',$userinfo);
		
		echo json_encode($rs);
		exit;	
		exit;	
	} 	
	

	/* 用户进入 写缓存 */
	public function setNodeInfo() {

		/* 当前用户信息 */
		$uid=(int)session("uid");
        $token=session("token");
        
        $liveuid = $this->request->param('liveuid', 0, 'intval');
		
		if($uid>0){
			$info=getUserInfo($uid);				
            $info['liveuid']=$liveuid;
			$info['token']=$token;
			$info['contribution']='0';
			
			$carinfo=getUserCar($uid);
			$info['car']=$carinfo;
            $info['usertype']=getIsAdmin($uid,$liveuid);
            
            $guard_type=getUserGuard($uid,$liveuid);
            $info['guard_type']=$guard_type['type'];
            /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
            $info['sign']=$info['contribution'].'.'.($info['level']+100).'1';
		}else{
			/* 游客 */
			$sign= mt_rand(1000,9999);
			$info['id'] = '-'.$sign;
			$info['user_nicename'] = '游客'.$sign;
			$info['avatar'] = '';
			$info['avatar_thumb'] = '';
			$info['sex'] = '0';
			$info['signature'] = '0';
			$info['consumption'] = '0';
			$info['votestotal'] = '0';
			$info['province'] = '';
			$info['city'] = '';
			$info['level'] = '0';
			$info['token']=md5($liveuid.'_'.$sign);
            $info['liveuid']=$liveuid;
			$info['usertype']=30;
			$info['contribution']='0';
			$info['guard_type']='0';
			$info['vip']=array('type'=>'0');
            $info['liang']=array('name'=>'0');
			$info['car']=array(
							'id'=>'0',
							'swf'=>'',
							'swftime'=>'0',
							'words'=>'',
						);
            /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
            $info['sign']=$info['contribution'].'.'.($info['level']+100).'1';
			$token =$info['token'] ;
		}			

		setcaches($token,$info);

		$data=array(
			'error'=>0,
			'userinfo'=>$info,
		 );
		echo  json_encode($data);				
		
	}
	
    protected function checkShut($uid,$liveuid){
        $where=[];
        $where['uid']=$uid;
        $where['liveuid']=$liveuid;
        
        $isexist=Db::name('live_kick')
                ->field("id")
                ->where($where)
                ->find();
        if($isexist){
            return 0;
        }
            
        $isexist=Db::name('live_shut')
                ->where($where)
                ->find();
        if($isexist){
            hSet($liveuid . 'shutup',$uid,1);
        }else{
            hDel($liveuid . 'shutup',$uid);
        }
        
        return 1;
    }
    
    
	public function getGift(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$gift=DB::name('gift')->field("id,type,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")->where('type!=2')->order("list_order asc")->select();
        foreach($gift as $k=>$v){
            $v['gifticon']=get_upload_path($v['gifticon']);
            $gift[$k]=$v;
        }
		$rs['info']=$gift;
		echo json_encode($rs);
		exit;
	}
	
	/* 关注 */
	public function follow(){
        
        $uid=(int)session("uid");
        $touid = $this->request->param('touid', 0, 'intval');
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        if($uid<1 || $touid<1){
            $rs = array(
				'code' => 1001, 
				'msg' => '关注失败', 
				'info' => array()
			);
            echo json_encode($rs);
            exit;
        }
        
		
		$data=array(
			"uid"=>$uid,
			"touid"=>$touid,
		);
		$result=DB::name('user_attention')->insert($data);
		if(!$result){
			$rs = array(
				'code' => 1001, 
				'msg' => '关注失败', 
				'info' => array()
			);
		}
		echo json_encode($rs);
		exit;
	}
	
	/* 送礼物 */
	public function sendGift(){
        
        $uid=(int)session("uid");
        $token = $this->request->param('token');
        $touid = $this->request->param('touid', 0, 'intval');
        $stream = $this->request->param('stream');
        $giftid = $this->request->param('giftid', 0, 'intval');
        
        $token=checkNull($token);
        $touid=checkNull($touid);
        $stream=checkNull($stream);
        $giftid=checkNull($giftid);
		$giftcount=1;

		/* 礼物信息 */
		$giftinfo=Db::name("gift")->field("giftname,gifticon,needcoin,type,mark,swftype,swf,swftime,sticker_id,isplatgift")->where("id='{$giftid}'")->find();		
		if(!$giftinfo){
			echo '{"errno":"1001","data":"","msg":"礼物信息错误"}';
			exit;				
		}
		if($giftinfo['type']==3){
			echo '{"errno":"1001","data":"","msg":"此礼物为手绘礼物,请前去下载app体验~"}';
			exit;				
		}
						   
		$total= $giftinfo['needcoin']*$giftcount;
		$addtime=time();
        
        if($giftinfo['mark']==2){
            /* 守护 */
            $guard_info=getUserGuard($uid,$touid);
            if($guard_info['type']==0){
                echo '{"errno":"1002","data":"","msg":"该礼物是守护专属礼物奥~"}';
                exit;	
            }
        }
        
        $where3['uid']=$touid;
		$liveinfo=Db::name("live")->where("islive=1")->where($where3)->find();
		$showid=0;
		if($liveinfo){
			$showid=$liveinfo['starttime'];
		}
        
        /* 更新用户余额 消费 */
		$ifok=Db::name("user")
                ->where([['id','=',$uid],['coin','>=',$total]])
                ->dec('coin',$total)
                ->inc('consumption',$total)
                ->update();
		if(!$ifok){
            /* 余额不足 */
			echo '{"errno":"1001","data":"","msg":"余额不足"}';
			exit;	
        }
        
        
        $anthor_total=$total;
        /* 幸运礼物分成 */
        if($giftinfo['type']==0 && $giftinfo['mark']==3){
            $jackpotset=getJackpotSet();
            
            $anthor_total=floor($anthor_total*$jackpotset['luck_anchor']*0.01);
        }
        
        /* 家族分成之后的金额 */
		$anthor_total=setFamilyDivide($touid,$anthor_total);
        
		/* 更新直播 映票 累计映票 */
        Db::name("user")
                ->where([['id','=',$touid]])
                ->inc('votes',$anthor_total)
                ->inc('votestotal',$total)
                ->update();
        
        if($anthor_total){
            $insert_votes=[
                'type'=>'1',
                'action'=>'1',
                'uid'=>$touid,
                'fromid'=>$uid,
                'actionid'=>$giftid,
                'nums'=>$giftcount,
                'total'=>$total,
                'showid'=>$showid,
                'votes'=>$anthor_total,
                'addtime'=>time(),
            ];
            Db::name('user_voterecord')->insert($insert_votes); 
        }
        
		/* 更新直播 映票 累计映票 */
        
        Db::name("user_coinrecord")->insert(array("type"=>'0',"action"=>'1',"uid"=>$uid,"touid"=>$touid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"mark"=>$giftinfo['mark'],"addtime"=>$addtime ));

        /* 更新主播热门 */
        if($giftinfo['mark']==1){
            Db::name('live')->where('uid','=',$touid)->setInc('hotvotes',$total);
        }
        
        
        /* PK处理 */
        $key1='LivePK';
        $key2='LivePK_gift';
        
        $ispk='0';
        $pkuid1='0';
        $pkuid2='0';
        $pktotal1='0';
        $pktotal2='0';
        $liveuid=$touid;
        $pkuid= hGet($key1,$liveuid);
        if($pkuid){
            $ispk='1';
            hIncrBy($key2,$liveuid,$total);
            
            $gift_uid=hGet($key2,$liveuid);
            $gift_pkuid=hGet($key2,$pkuid);
            
            $pktotal1=$gift_uid;
            $pktotal2=$gift_pkuid;
            
            $pkuid1=$liveuid;
            $pkuid2=$pkuid;
            
        }
        
        /* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$touid); 
        

        $userinfo3=Db::name('user')->field("votestotal,user_nicename")->where("id='{$touid}'")->find();
        
		$gifttoken=md5(md5('sendGift'.$uid.$touid.$giftid.$giftcount.$total.$showid.$addtime));
        
        $swf=$giftinfo['swf'] ? get_upload_path($giftinfo['swf']):'';

        $ifluck=0;
        $ifup=0;
        $ifwin=0;
        /* 幸运礼物 */
        if($giftinfo['type']==0 && $giftinfo['mark']==3){
            $ifup=1;
            $ifwin=1;
            $list=getLuckRate();
            /* 有中奖配置 才处理 */
            if($list){
                $rateinfo=[];
                foreach($list as $k=>$v){
                    if($v['giftid']==$giftid && $v['nums']==$giftcount){
                        $rateinfo[]=$v;
                    }
                }
                /* 有该礼物、该数量 中奖配置 才处理 */
                if($rateinfo){
                    $ifluck=1;
                }
            }
            
        }
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifluck:'.json_encode($ifluck)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifwin:'.json_encode($ifwin)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
        /* 幸运礼物中奖 */
        $isluck='0';
        $isluckall='0';
        $luckcoin='0';
        $lucktimes='0';
        if($ifluck ==1 ){
            $luckrate=rand(1,100000);
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 luckrate:'.json_encode($luckrate)."\r\n",FILE_APPEND);
            $rate=0;
            foreach($rateinfo as $k=>$v){
                $rate+=floor($v['rate']*1000);
                //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
                if($luckrate <= $rate){
                    /* 中奖 */
                    $isluck='1';
                    $isluckall=$v['isall'];
                    $lucktimes=$v['times'];
                    $luckcoin= $total * $lucktimes;
                    
                    /* 用户加余额  写记录 */
                    Db::name('user')->where('id','=',$uid)->setInc('coin',$luckcoin);
                    
                    $insert=array(
                        "type"=>'1',
                        "action"=>'12',
                        "uid"=>$uid,
                        "touid"=>$uid,
                        "giftid"=>$giftid,
                        "giftcount"=>$lucktimes,
                        "totalcoin"=>$luckcoin,
                        "showid"=>$showid,
                        "mark"=>$giftinfo['mark'],
                        "addtime"=>$addtime 
                    );
                    Db::name('user_coinrecord')->insert($insert);
                    break;
                }
            }
        }
        
        /* 幸运礼物中奖 */
        
        
        /* 奖池升级 */
        $isup='0';
        $uplevel='0';
        $upcoin='0';
        if($ifup == 1 ){
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotset:'.json_encode($jackpotset)."\r\n",FILE_APPEND);
            if($jackpotset['switch']==1 && $jackpotset['luck_jackpot'] > 0){
                /* 开启奖池 */
                $jackpot_up=floor($total * $jackpotset['luck_jackpot'] * 0.01);
                
                //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_up:'.json_encode($jackpot_up)."\r\n",FILE_APPEND);
                if($jackpot_up){
                    
                    Db::name('jackpot')->where('id','=','1')->setInc('total',$jackpot_up);
                    
                    $jackpotinfo=getJackpotInfo();
                    
                    $jackpot_level=getJackpotLevel($jackpotinfo['total']);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_level:'.json_encode($jackpot_level)."\r\n",FILE_APPEND);
                    if($jackpot_level>$jackpotinfo['level']){
                        $isok=Db::name('jackpot')->where("id = 1 and level < {$jackpot_level}") ->update( array('level' => $jackpot_level ));
                        
                        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 isok:'.json_encode($isok)."\r\n",FILE_APPEND);
                        if($isok){
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 isup:'.json_encode($isup)."\r\n",FILE_APPEND);
                            $isup='1';
                            $uplevel=$jackpot_level;
                        }
                    }
                }
            }
        }
        /* 奖池升级 */
        
        /* 奖池中奖 */
        $iswin='0';
        $wincoin='0';
        if($ifwin ==1 ){
            if($jackpotset['switch']==1 ){
               /* 奖池开启 */
               $jackpotinfo=getJackpotInfo();
               //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
               if($jackpotinfo['level']>=1){
                    /* 至少达到第一阶段才能中奖 */
                    
                    $list=getJackpotRate();
                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 list:'.json_encode($list)."\r\n",FILE_APPEND);
                    /* 有奖池中奖配置 才处理 */
                    if($list){
                        $rateinfo=[];
                        foreach($list as $k=>$v){
                            if($v['giftid']==$giftid && $v['nums']==$giftcount){
                                $rateinfo=$v;
                                break;
                            }
                        }
                        //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
                        /* 有该礼物中奖配置 才处理 */
                        if($rateinfo){
                            $winrate=rand(1,100000);
                            
                            $rate_jackpot=json_decode($rateinfo['rate_jackpot'],true);
                            
                            $rate=floor($rate_jackpot[$jackpotinfo['level']] * 1000);
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 winrate:'.json_encode($winrate)."\r\n",FILE_APPEND);
                            //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
                            if($winrate <= $rate){
                                /* 中奖 */
                                $wincoin2=$jackpotinfo['total'];
                                
                                $isok=Db::name('jackpot')
                                        ->where([['id','=','1'],['total','>=',$wincoin2]])
                                        ->dec('total',$wincoin2)
                                        ->update(['level'=>0]);
                                
                                
                                if($isok){
                                    //file_put_contents('./zhifu.txt',date('Y-m-d H:i:s').' 提交参数信息 iswin:'.'1'."\r\n",FILE_APPEND);
                                    $iswin='1';
                                    $wincoin=(string)$wincoin2;
                                    
                                    /* 用户加余额  写记录 */
                                    Db::name('user')->where('id','=',$uid)->setInc('coin',$wincoin2);
                                    
                                    $insert=array(
                                        "type"=>'1',
                                        "action"=>'13',
                                        "uid"=>$uid,
                                        "touid"=>$uid,
                                        "giftid"=>'0',
                                        "giftcount"=>'1',
                                        "totalcoin"=>$wincoin2,
                                        //"showid"=>$showid,
                                        "mark"=>$giftinfo['mark'],
                                        "addtime"=>$addtime 
                                    );
                                    Db::name('user_coinrecord')->insert($insert);
                                }
                            }
                        }
                    }
               }
            }
        }
        /* 奖池中奖 */
        
        $userinfo2=Db::name('user')->field("consumption,coin,votestotal")->where("id='{$uid}'")->find();

		$level=getLevel($userinfo2['consumption']);			
		if($giftinfo['type']!=1){
            $giftinfo['isplatgift']='0';
        }
		$result=array(
            "uid"=>(int)$uid,
            "giftid"=>(int)$giftid,
            "type"=>$giftinfo['type'],
            "giftcount"=>(int)$giftcount,
            "totalcoin"=>$total,
            "giftname"=>$giftinfo['giftname'],
            "gifticon"=>get_upload_path($giftinfo['gifticon']),
            "swftype"=>$giftinfo['swftype'],
            "swftime"=>$giftinfo['swftime'],
            "swf"=>$swf,
            "level"=>$level,
            "votestotal"=>$userinfo3['votestotal'],
            
            "isluck"=>$isluck,
            "isluckall"=>$isluckall,
            "luckcoin"=>$luckcoin,
            "lucktimes"=>$lucktimes,
            
            "isup"=>$isup,
            "uplevel"=>$uplevel,
            "upcoin"=>$upcoin,
            
            "iswin"=>$iswin,
            "wincoin"=>$wincoin,
            
            "ispk"=>$ispk,
            "pkuid"=>$pkuid,
            "pkuid1"=>$pkuid1,
            "pkuid2"=>$pkuid2,
            "pktotal1"=>$pktotal1,
            "pktotal2"=>$pktotal2,
			"isplatgift"=>$giftinfo['isplatgift'],
			"sticker_id"=>$giftinfo['sticker_id'],
        );
        
		setcaches($gifttoken,$result);
        if($liveinfo){
            zIncrBy('user_'.$liveinfo['stream'],$total,$uid);
        }

		echo '{"errno":"0","uid":"'.$uid.'","level":"'.$level.'","type":"'.$giftinfo['type'].'","coin":"'.$userinfo2['coin'].'","gifttoken":"'.$gifttoken.'","livename":"'.$userinfo3['user_nicename'].'","msg":"赠送成功"}';
		exit;	
			
	}

	/* 支付页面  */
	public function pay(){
		
        $uid=(int)session("uid");
        
		$userinfo=Db::name("user")->field("id,user_nicename,avatar_thumb,coin")->where("id='{$uid}'")->find();
		$this->assign('userinfo',$userinfo);
		
		$chargelist=Db::name('charge_rules')->field('id,coin,money,product_id,give')->order('list_order asc')->select();
		
		$this->assign('chargelist',$chargelist);
		
		return $this->fetch();
	}
	/* 获取订单号 */
	public function getOrderId(){
		$uid=(int)session("uid");
        $chargeid = $this->request->param('chargeid', 0, 'intval');
        
		$rs=array(
			'code'=>0,
			'data'=>array(),
			'msg'=>'',
		);
		$charge=Db::name('charge_rules')->where("id={$chargeid}")->find();
		if(!$charge){
            $rs['code']=1002;
			$rs['msg']='订单信息错误';
            echo json_encode($rs);
            exit;
        }
        
        $orderid=$uid.'_'.date('YmdHis').rand(100,999);
        $orderinfo=array(
            "uid"=>$uid,
            "touid"=>$uid,
            "money"=>$charge['money'],
            "coin"=>$charge['coin'],
            "coin_give"=>$charge['give'],
            "orderno"=>$orderid,
            "type"=>'2',
            "ambient"=>'1',
            "status"=>0,
            "addtime"=>time()
        );
        $result=Db::name('charge_user')->insert($orderinfo);
        if(!$result){
            $rs['code']=1001;
            $rs['msg']='订单生成失败';
            echo json_encode($rs);
            exit;
        }
            
        $rs['data']['uid']=$uid;
        $rs['data']['money']=$charge['money'];
        $rs['data']['orderid']=$orderid;

		
		
		echo json_encode($rs);
		exit;
		
	}

	
	
	
}