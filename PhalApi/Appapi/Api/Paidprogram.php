<?php
/**
 * 动态管理
 */
class Api_Paidprogram extends PhalApi_Api {

	public function getRules() {
		return array(

			'getApplyStatus'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
            'apply' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),

			'getPaidprogramClassList'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),
			
			'addPaidProgram'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'classid' => array('name' => 'classid', 'type' => 'int', 'desc' => '付费内容分类ID'),
				'title' => array('name' => 'title', 'type' => 'string', 'desc' => '付费内容标题'),
				'thumb' => array('name' => 'thumb', 'type' => 'string', 'desc' => '付费内容封面'),
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '付费内容简介'),
				'personal_desc' => array('name' => 'personal_desc', 'type' => 'string', 'desc' => '付费内容个人介绍'),
				'money' => array('name' => 'money', 'type' => 'string', 'desc' => '付费内容价格'),
				'type' => array('name' => 'type', 'type' => 'string', 'desc' => '付费内容类型 0 单视频 1 多视频'),
				'videos' => array('name' => 'videos', 'type' => 'string', 'desc' => '付费内容视频列表json串'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),

			),

			'getPaidProgramInfo'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'object_id'=>array('name' => 'object_id', 'type' => 'int', 'desc' => '项目ID'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),

			'getMyPaidProgram'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '分页数'),
			),

			'getBalance' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getAliOrder' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'object_id'=>array('name' => 'object_id', 'type' => 'int', 'desc' => '项目ID'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getWxOrder' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'object_id'=>array('name' => 'object_id', 'type' => 'int', 'desc' => '项目ID'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'balancePay'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'object_id' => array('name' => 'object_id', 'type' => 'string', 'desc' => '付费项目ID'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getPaidProgramList'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '分页数'),
			),

			'setComment'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'object_id' => array('name' => 'object_id', 'type' => 'string', 'desc' => '付费项目ID'),
				'grade' => array('name' => 'grade', 'type' => 'int', 'desc' => '评论等级 1-5之间的整数'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),

			'searchPaidProgram'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'keywords' => array('name' => 'keywords', 'type' => 'string', 'desc' => '关键词'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
			),
            
		);
	}

	/**
	 * 获取付费项目的申请状态
	 * @desc 用于申请开通付费项目
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isauth 用户是否认证
	 * @return string info[0].auth_msg 用户认证提示信息
	 * @return string info[0].apply_status 用户申请状态 -2 没有申请 -1 拒绝 0 审核中 1同意
	 * @return string info[0].title 用户申请时弹窗标题
	 * @return string info[0].desc 用户申请时弹窗描述内容
	 * @return string info[0].payment_title 用户申请时付费内容协议名称
	 * @return string msg 提示信息
	 */
	public function getApplyStatus(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$time=checkNull($this->time);
        $sign=checkNull($this->sign);
		
		if($uid<1 || $token==''){
            $rs['code'] = 1001;
			$rs['msg'] = '信息错误';
			return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
     
        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs;
        }

//        $rs['info'][0]['isauth']='1';
//        $rs['info'][0]['auth_msg']='申请开播前，请先进行认证。';

        //判断用户身份认证是否成功
        $configpri=getConfigPri();
		if($configpri['auth_islimit']==1){
//			$isauth=isAuth($uid);
            $status = DI()->notorm->user_auth
                ->select("status")
                ->where('uid=?', $uid)
                ->fetchOne();
            if ($status) {
                $rs['info'][0]['isauth']=$status['status'];
                if ($status['status'] == 1){
                    $rs['info'][0]['auth_msg']='直播认证通过';
                }elseif($status['status'] == 2){
                    $rs['info'][0]['auth_msg']='直播认证失败';
                }else{
                    $rs['info'][0]['auth_msg']='申请开播前，请先进行认证。';
                }
            }else{
                $rs['info'][0]['isauth']='0';
                $rs['info'][0]['auth_msg']='申请开播前，请先进行认证。';
            }
		}

		$domain=new Domain_Paidprogram();
		$apply_status=$domain->getApplyStatus($uid);

		$title='';
		$desc='';
		$payment_title='';
		$configpub=getConfigPub();

		switch ($apply_status) {

			case '-2': //没申请
				$configpub=getConfigPub();
				$title='申请说明';
				$desc=$configpub['payment_des'];
				$payment_title='《平台付费内容管理规范协议说明》';
				break;

			case '-1':
				$title='申请未通过';
				$desc='您的申请被拒,'.$configpub['payment_time'].'日后可再次申请,如有疑问可咨询平台客服';
				break;

			case '0':
				$title='申请已提交';
				$desc='审核通过后即可上传付费内容';
				break;
			
			case '1':
				$title='申请已通过';
				$desc='审核通过,可上传付费内容';
				break;
		}

		$rs['info'][0]['apply_status']=$apply_status;
		$rs['info'][0]['title']=$title;
		$rs['info'][0]['desc']=$desc;
		$rs['info'][0]['payment_title']=$payment_title;
		return $rs;
	}
	
	/**
	 * 申请开通付费项目
	 * @desc 用于申请开通付费项目
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function apply() {
		$rs = array('code' => 0, 'msg' => '申请成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$time=checkNull($this->time);
        $sign=checkNull($this->sign);
		
		if($uid<1 || $token==''){
            $rs['code'] = 1001;
			$rs['msg'] = '信息错误';
			return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
     
        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断用户身份认证是否成功
		$isauth=isAuth($uid);
		if(!$isauth){

			$rs['code']=1002;
			$rs['msg']='申请开通付费内容权限需要先进行实名认证';
			return $rs;
		}
		
		$domain = new Domain_Paidprogram();
		$res = $domain->apply($uid);

		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='申请已通过,不可重复申请';
			return $rs;
		}else if($res==1002){
			$rs['code']=1002;
			$rs['msg']='正在审核中,请耐心等待';
			return $rs;
		}else if($res==1003){
			$rs['code']=1003;
			$rs['msg']='申请太频繁,请过段时间再试';
			return $rs;
		}else if($res==1004){
			$rs['code']=1004;
			$rs['msg']='申请失败,请重试';
			return $rs;
		}
		
		return $rs;
	}		
	
   	/**
	 * 获取付费项目分类列表
	 * @desc 用于获取付费项目分类列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 分类ID
	 * @return string info[].name 分类名称
	 * @return string msg 提示信息
	 */
	public function getPaidprogramClassList(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain=new Domain_Paidprogram();
		$res=$domain->getPaidprogramClassList();

		$rs['info']=$res;
		return $rs;

	}
	
	/**
	 * 上传付费项目
	 * @desc 用于上传付费项目
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function addPaidProgram(){
		$rs = array('code' => 0, 'msg' => '付费内容发布成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$classid=checkNull($this->classid);
		$title=checkNull($this->title);
		$thumb=checkNull($this->thumb);
		$content=checkNull($this->content);
		$personal_desc=checkNull($this->personal_desc);
		$money=checkNull($this->money);
		$type=checkNull($this->type);
		$videos=$this->videos;
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);

		if($uid<1||$token==''||$classid<1||!in_array($type, ['0','1'])||!$time||!$sign){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}


		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$now=time();
		if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'classid'=>$classid,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }


		$title=trim($title);
		if($title==''){
			$rs['code']=1001;
			$rs['msg']='请填写作品标题';
			return $rs;
		}

		if(mb_strlen($title)>15){
			$rs['code']=1001;
			$rs['msg']='作品标题在15字以内';
			return $rs;
		}

		if($thumb==''){
			$rs['code']=1001;
			$rs['msg']='请上传作品封面';
			return $rs;
		}

		$content=trim($content);

		if($content==''){
			$rs['code']=1001;
			$rs['msg']='请填写内容简介';
			return $rs;
		}

		if(mb_strlen($content)>100){
			$rs['code']=1001;
			$rs['msg']='内容简介在100字以内';
			return $rs;
		}

		$personal_desc=trim($personal_desc);

		if($personal_desc==''){
			$rs['code']=1001;
			$rs['msg']='请填写作者简介';
			return $rs;
		}

		if(mb_strlen($personal_desc)>50){
			$rs['code']=1001;
			$rs['msg']='作者简介在50字以内';
			return $rs;
		}

		if(!$money){
			$rs['code']=1001;
			$rs['msg']='请填写作品价格';
			return $rs;
		}

		if($money<1||$money>10000){
			$rs['code']=1001;
			$rs['msg']='填写作品价格在1-10000之间';
			return $rs;
		}

		$videos=trim($videos);
		if(!$videos){
			$rs['code']=1001;
			$rs['msg']='请上传视频';
			return $rs;
		}
		$videos_arr=json_decode($videos,true);
		$count=count($videos_arr);

		if($type==0&&$count>1){
			$rs['code']=1001;
			$rs['msg']='单视频只允许上传一个视频';
			return $rs;
		}

		if($type==1&&$count==1){
			$rs['code']=1001;
			$rs['msg']='多视频不允许上传一个视频';
			return $rs;
		}

		$video_url_false=0;
		$video_title_false=0;

		foreach ($videos_arr as $k => $v) {
			if($v['video_url']==''){
				$video_url_false=1;
				break;
			}

			if($type==1&&$v['video_title']==''){
				$video_title_false=1;
				break;
			}

			if($type==1&&mb_strlen($v['video_title'])>15){
				$video_title_false=1;
				break;
			}


		}

		if($video_url_false){
			$rs['code']=1001;
			$rs['msg']='请上传视频文件';
			return $rs;
		}

		if($video_title_false){
			$rs['code']=1001;
			$rs['msg']='视频标题错误';
			return $rs;
		}

		$data=array(
			'uid'=>$uid,
			'classid'=>$classid,
			'title'=>$title,
			'thumb'=>$thumb,
			'content'=>$content,
			'personal_desc'=>$personal_desc,
			'money'=>$money,
			'type'=>$type,
			'videos'=>$videos,
			'addtime'=>$now
		);

		$domain=new Domain_Paidprogram();
		$res=$domain->addPaidProgram($data);

		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='请先确认申请是否通过';
			return $rs;
		}
		if(!$res){
			$rs['code']=1002;
			$rs['msg']='付费内容发布失败';
			return $rs;
		}

		return $rs;

	}
	

	/**
	 * 获取付费项目详情
	 * @desc 用于获取付费项目详情
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].id 记录ID
	 * @return string msg 提示信息
	 */
	public function getPaidProgramInfo(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$object_id=checkNull($this->object_id);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);

		if($uid<1||$token==''||$object_id<1){
			$rs['code'] = 1001;
			$rs['msg'] = '参数错误';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$now=time();
		if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'object_id'=>$object_id,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Paidprogram();
        $res=$domain->getPaidProgramInfo($uid,$object_id);
        if($res==1001){
        	$rs['code']=1001;
            $rs['msg']='无法获取';
            return $rs; 
        }

        $rs['info'][0]=$res;
        return $rs;

	}
	/**
	 * 获取我上传的付费项目
	 * @desc 用于获取我上传的付费项目
	 * @return int code 状态码 0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 * @return array info[].id 付费项目ID
	 * @return array info[].title 付费项目标题
	 * @return array info[].sale_nums 付费项目购买数量
	 * @return array info[].status 付费项目状态 -1 拒绝 0 审核中 1 同意
	 * @return array info[].thumb_format 付费项目封面
	 * @return array info[].video_num 付费项目视频数量
	 */
	public function getMyPaidProgram(){
		$rs = array('code' => 0, 'msg' => '付费内容发布成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);
		$p=checkNull($this->p);

		if($uid<1||$token==''){
			$rs['code'] = 1001;
			$rs['msg'] = '参数错误';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$now=time();
		if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Paidprogram();
        $res=$domain->getMyPaidProgram($uid,$p);
        $rs['info']=$res;

        return $rs;

	}

	/**
     * 获取用户的余额和支付信息
     * @desc 用于 获取用户的余额和支付信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].balance 用户人民币余额
	 * @return string info[0].aliapp_partner 支付宝合作者身份ID
	 * @return string info[0].aliapp_seller_id 支付宝帐号	
	 * @return string info[0].aliapp_key_android 支付宝安卓密钥
	 * @return string info[0].aliapp_key_ios 支付宝苹果密钥
	 * @return string info[0].wx_appid 开放平台账号AppID
	 * @return string info[0].wx_appsecret 微信应用appsecret
	 * @return string info[0].wx_mchid 微信商户号mchid
	 * @return string info[0].wx_key 微信密钥key
	 * @return string info[0].paylist 支付方式列表
	 * @return string info[0].paylist[].id 支付方式列表项ID
	 * @return string info[0].paylist[].name 支付方式列表项名称
	 * @return string info[0].paylist[].thumb 支付方式列表项图标
	 * @return string info[0].paylist[].href 支付方式列表项链接
     * @return string msg 提示信息
     */
	public function getBalance(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==''||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

		$info=getUserShopBalance($uid);
		$info['balance']='￥'.$info['balance'];
		unset($info['balance_total']);

		$configpri=getConfigPri();

		$paidprogram_aliapp_switch=$configpri['paidprogram_aliapp_switch'];
		$paidprogram_wx_switch=$configpri['paidprogram_wx_switch'];

		$info['aliapp_partner']=$paidprogram_aliapp_switch==1?$configpri['aliapp_partner']:'';
		$info['aliapp_seller_id']=$paidprogram_aliapp_switch==1?$configpri['aliapp_seller_id']:'';
		$info['aliapp_key_android']=$paidprogram_aliapp_switch==1?$configpri['aliapp_key_android']:'';
		$info['aliapp_key_ios']=$paidprogram_aliapp_switch==1?$configpri['aliapp_key_ios']:'';

		$info['wx_appid']=$paidprogram_wx_switch==1?$configpri['wx_appid']:'';
		$info['wx_appsecret']=$paidprogram_wx_switch==1?$configpri['wx_appsecret']:'';
		$info['wx_mchid']=$paidprogram_wx_switch==1?$configpri['wx_mchid']:'';
		$info['wx_key']=$paidprogram_wx_switch==1?$configpri['wx_key']:'';

		$paidprogram_balance_switch=$configpri['paidprogram_balance_switch'];

		$paylist=[];

		if($paidprogram_aliapp_switch){
            $paylist[]=[
                'id'=>'ali',
                'name'=>'支付宝支付',
                'thumb'=>get_upload_path("/static/app/paidprogrampay/ali.png"),
                'href'=>'',
                'type'=>'1' //对应创建订单接口里的type
            ];
        }

        if($paidprogram_wx_switch){
            $paylist[]=[
                'id'=>'wx',
                'name'=>'微信支付',
                'thumb'=>get_upload_path("/static/app/paidprogrampay/wx.png"),
                'href'=>'',
                'type'=>'2'
            ];
        }

        if($paidprogram_balance_switch){
            $paylist[]=[
                'id'=>'balance',
                'name'=>'余额支付',
                'thumb'=>get_upload_path("/static/app/paidprogrampay/balance.png"),
                'href'=>'',
                'type'=>'3'
            ];
        }

        $info['paylist'] =$paylist;

        $rs['info'][0]=$info;
		return $rs;

    }


    /* 获取订单号 */
	protected function getOrderid($uid){
		$orderid=$uid.'_'.date('YmdHis').rand(100,999);
		return $orderid;
	}


    /**
     * 创建支付宝订单
     * @desc 用于创建支付宝订单
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return string info[0].orderid 订单编号
     */
    public function getAliOrder(){
    	$rs = array('code' => 0, 'msg' => '订单创建成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $object_id=checkNull($this->object_id);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==''||$object_id<1||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'object_id'=>$object_id,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $orderid=$this->getOrderid($uid);
		$type=1;

		$orderinfo=array(
			"uid"=>$uid,
			"orderno"=>$orderid,
			"object_id"=>$object_id,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time()
		);

		$domain = new Domain_Paidprogram();
		$res = $domain->getOrderId($orderinfo);

		if($res==1001){
			$rs['code']=1001;
            $rs['msg']='付费项目不存在';
            return $rs;
		}else if($res==1002){
			$rs['code']=1002;
            $rs['msg']='不能购买自己的付费项目';
            return $rs;
		}else if($res==1003){
			$rs['code']=1003;
            $rs['msg']='该付费项目不可购买';
            return $rs;
		}else if($res==1004){
			$rs['code']=1004;
            $rs['msg']='您已购买过该项目';
            return $rs;
		}else if(!$res){
			$rs['code']=1005;
			$rs['msg']='订单生成失败';
		}

		$rs['info'][0]['orderid']=$orderid;
		return $rs;
    }

    /**
     * 创建微信订单
     * @desc 用于创建微信订单
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return string info[0].appid 微信支付appid
     * @return string info[0].partnerid 微信支付商户号
     * @return string info[0].noncestr 微信支付仅适用一次验签字符串
     * @return string info[0].prepayid 微信支付id
     */
    public function getWxOrder(){
    	$rs = array('code' => 0, 'msg' => '订单创建成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $object_id=checkNull($this->object_id);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==''||$object_id<1||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'object_id'=>$object_id,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $configpri = getConfigPri(); 
		$configpub = getConfigPub(); 

		 //配置参数检测
					
		if($configpri['wx_appid']== "" || $configpri['wx_mchid']== "" || $configpri['wx_key']== ""){
			$rs['code'] = 1002;
			$rs['msg'] = '微信未配置';
			return $rs;					 
		}

		$orderid=$this->getOrderid($uid);
		$type=2;

		$orderinfo=array(
			"uid"=>$uid,
			"orderno"=>$orderid,
			"object_id"=>$object_id,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time()
		);

		$domain = new Domain_Paidprogram();
		$res = $domain->getOrderId($orderinfo);

		if($res==1001){
			$rs['code']=1001;
            $rs['msg']='付费项目不存在';
            return $rs;
		}else if($res==1002){
			$rs['code']=1002;
            $rs['msg']='不能购买自己的付费项目';
            return $rs;
		}else if($res==1003){
			$rs['code']=1003;
            $rs['msg']='该付费项目不可购买';
            return $rs;
		}else if($res==1004){
			$rs['code']=1004;
            $rs['msg']='您已购买过该项目';
            return $rs;
		}else if(!$res){
			$rs['code']=1005;
			$rs['msg']='订单生成失败';
		}

		$paidprogram_info=$domain->getPaidProgramInfo($uid,$object_id);

		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$time = time();
			
		$paramarr = array(
			"appid"       =>   $configpri['wx_appid'],
			"body"        =>    "花费".$paidprogram_info['money']."购买".$paidprogram_info['title'],
			"mch_id"      =>    $configpri['wx_mchid'],
			"nonce_str"   =>    $noceStr,
			"notify_url"  =>    $configpub['site'].'/Appapi/Paidprogrampay/notify_wx',
			"out_trade_no"=>    $orderid,
			"total_fee"   =>    $paidprogram_info['money']*100, 
			"trade_type"  =>    "APP"
		);

		$sign = $this -> sign($paramarr,$configpri['wx_key']);//生成签名
		$paramarr['sign'] = $sign;
		$paramXml = "<xml>";
		foreach($paramarr as $k => $v){
			$paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";
		}
		$paramXml .= "</xml>";
			 
		$ch = curl_init ();
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在  
		@curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_POST, 1);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);
		@$resultXmlStr = curl_exec($ch);
		if(curl_errno($ch)){
			//print curl_error($ch);
			file_put_contents('./paidprogrampay.txt',date('y-m-d H:i:s').' 提交参数信息 ch:'.json_encode(curl_error($ch))."\r\n",FILE_APPEND);
		}
		curl_close($ch);

		$result2 = $this->xmlToArray($resultXmlStr);
        
        if($result2['return_code']=='FAIL'){
            $rs['code']=1005;
			$rs['msg']=$result2['return_msg'];
            return $rs;	
        }

        $prepayid = $result2['prepay_id'];
		$sign = "";
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$paramarr2 = array(
			"appid"     =>  $configpri['wx_appid'],
			"noncestr"  =>  $noceStr,
			"package"   =>  "Sign=WXPay",
			"partnerid" =>  $configpri['wx_mchid'],
			"prepayid"  =>  $prepayid,
			"timestamp" =>  $now
		);
		$paramarr2["sign"] = $this -> sign($paramarr2,$configpri['wx_key']);//生成签名
		
		$rs['info'][0]=$paramarr2;
		return $rs;	


    }


    /**
	* sign拼装获取
	*/
	protected function sign($param,$key){
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	/**
	* xml转为数组
	*/
	protected function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}

	/**
	 * 用户使用余额支付付费项目
	 * @desc 用于用户使用余额支付付费项目
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function balancePay(){

		$rs = array('code' => 0, 'msg' => '购买成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $object_id=checkNull($this->object_id);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==''||$object_id<1||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'object_id'=>$object_id,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $orderid=$this->getOrderid($uid);
		$type=3;

        $orderinfo=array(
			"uid"=>$uid,
			"orderno"=>$orderid,
			"object_id"=>$object_id,
			"type"=>$type,
			"status"=>1,
			"addtime"=>time(),
			"edittime"=>time()
		);

        $domain=new Domain_Paidprogram();
        $res=$domain->balancePay($uid,$orderinfo);

        if($res==1001){
			$rs['code']=1001;
            $rs['msg']='付费项目不存在';
            return $rs;
		}else if($res==1002){
			$rs['code']=1002;
            $rs['msg']='不能购买自己的付费项目';
            return $rs;
		}else if($res==1003){
			$rs['code']=1003;
            $rs['msg']='该付费项目不可购买';
            return $rs;
		}else if($res==1004){
			$rs['code']=1004;
			$rs['msg']='余额不足';
		}else if($res==1005){
			$rs['code']=1005;
			$rs['msg']='您已购买过该项目';
		}else if(!$res){
			$rs['code']=1006;
			$rs['msg']='余额购买失败';
		}

		return $rs;
	}

	/**
	 * 用户获取购买的付费项目列表
	 * @desc 用于用户获取购买的付费项目列表
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function getPaidProgramList(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        $p=checkNull($this->p);

        if($uid<0||$token==''||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Paidprogram();
        $res=$domain->getPaidProgramList($uid,$p);
        $rs['info']=$res;

        return $rs;

	}
	
	/**
	 * 用户对付费项目发表评论
	 * @desc 用于用户对付费项目发表评论
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function setComment(){
		$rs = array('code' => 0, 'msg' => '评价成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $object_id=checkNull($this->object_id);
        $grade=checkNull($this->grade);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==''||$object_id<1||!$time||!$sign){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $now=time();
        if($now-$time>300){
        	$rs['code']=1001;
        	$rs['msg']='参数错误';
        	return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'object_id'=>$object_id,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        if($grade<1||$grade>5){
        	$rs['code']=1001;
            $rs['msg']='评价等级应在1-5级之间';
            return $rs;
        }

        if(floor($grade)!=$grade){
        	$rs['code']=1001;
            $rs['msg']='评价等级必须为整数';
            return $rs;
        }

        $domain=new Domain_Paidprogram();

        $res=$domain->setComment($uid,$object_id,$grade);

        if($res==1001){
        	$rs['code']=1001;
            $rs['msg']='请先购买付费项目';
            return $rs;
        }

        if($res==1002){
        	$rs['code']=1002;
            $rs['msg']='已经发表评价';
            return $rs;
        }

        if($res==1003){
        	$rs['code']=1003;
            $rs['msg']='付费项目不存在';
            return $rs;
        }

        if($res==1004){
        	$rs['code']=1004;
            $rs['msg']='不能对自己的付费项目发表评价';
            return $rs;
        }

        if($res==1005){
        	$rs['code']=1005;
            $rs['msg']='不能对未审核通过的付费项目发表评价';
            return $rs;
        }

        if(!$res){
        	$rs['code']=1006;
            $rs['msg']='评价失败';
            return $rs;
        }

        return $rs;

	}

	/**
	 * 用户发布的付费内容搜索
	 * @desc 用于用户发布的付费内容搜索
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 * @return int info[0]['id'] 商品ID
	 * @return string info[0]['name'] 商品名称
	 * @return string info[0]['price'] 商品价格
	 * @return string info[0]['thumb'] 商品封面
	 */
	public function searchPaidProgram(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$keywords=checkNull($this->keywords);
		$p=checkNull($this->p);

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断用户是否开通了付费内容
		$is_paidprogram=checkPaidProgramIsPass($uid);
		if(!$is_paidprogram){
			$rs['info']=[];
			return $rs;
		}

		$domain=new Domain_Paidprogram();
		$res=$domain->searchPaidProgram($uid,$keywords,$p);

		$rs['info']=$res;
		return $rs;
	}

		
	/* 检测文件后缀 */
	protected function checkExt($filename){
		$config=array("jpg","png","jpeg");
		$ext   =   pathinfo(strip_tags($filename), PATHINFO_EXTENSION);
		 
		return empty($config) ? true : in_array(strtolower($ext), $config);
	}	
	
	
	


}
