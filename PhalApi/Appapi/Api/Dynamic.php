<?php
/**
 * 动态管理
 */
class Api_Dynamic extends PhalApi_Api {

	public function getRules() {
		return array(
            'setDynamic' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'title' => array('name' => 'title', 'type' => 'string', 'desc' => '标题'),
				'thumb' => array('name' => 'thumb', 'type' => 'string', 'desc' => '图片地址集合'),
				'video_thumb' => array('name' => 'video_thumb', 'type' => 'string', 'desc' => '视频封面'),
				'href' => array('name' => 'href', 'type' => 'string', 'desc' => '视频地址'),
				'voice' => array('name' => 'voice', 'type' => 'string', 'desc' => '语音地址'),
				'length' => array('name' => 'length', 'type' => 'int', 'default'=>0, 'desc' => '语音长度'),
				'lat' => array('name' => 'lat', 'type' => 'string',  'desc' => '维度'),
				'lng' => array('name' => 'lng', 'type' => 'string',  'desc' => '经度'),
				'city' => array('name' => 'city', 'type' => 'string',  'desc' => '城市'),
				'address' => array('name' => 'address', 'type' => 'string',  'desc' => '详细地理位置'),
				'type' => array('name' => 'type', 'type' => 'int','default'=>0, 'desc' => '动态类型：0：纯文字；1：文字+图片；2：文字+视频；3：文字+音频'),
				'labelid' => array('name' => 'labelid', 'type' => 'int','default'=>0, 'desc' => '标签ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid   type'),
			),
			
           'setComment' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户Token'),
				'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1,'desc' => '动态ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'default'=>0, 'desc' => '回复的评论UID'),
                'commentid' => array('name' => 'commentid', 'type' => 'int',  'default'=>0,  'desc' => '回复的评论commentid'),
                'parentid' => array('name' => 'parentid', 'type' => 'int',  'default'=>0,  'desc' => '回复的评论ID'),
                'content' => array('name' => 'content', 'type' => 'string',  'default'=>'', 'desc' => '内容'),
                'type'=>array('name'=>'type','type'=>'int','default'=>'0','desc'=>'类型，0文字，1语音'),
                'voice'=>array('name'=>'voice','type'=>'string','desc'=>'语音'),
                'length'=>array('name'=>'length','type'=>'int','desc'=>'时长'),
            ),
			'addLike' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'desc' => '动态ID'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid  dynamicid'),
			),
		
			'addCommentLike' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string','desc' => '用户Token'),
                'commentid' => array('name' => 'commentid', 'type' => 'int', 'min' => 1, 'desc' => '评论/回复 ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid  commentid'),
            ),
			
			 'getAttentionDynamic' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string','desc' => '用户Token'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			 'getNewDynamic' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,'desc' => '用户ID'),
            	'lng' => array('name' => 'lng', 'type' => 'string', 'desc' => '经度值'),
                'lat' => array('name' => 'lat', 'type' => 'string','desc' => '纬度值'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getHomeDynamic' => array(
                'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string','desc' => '用户Token'),
                'touid' => array('name' => 'touid', 'type' => 'int', 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),

         
            'getDynamic' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
                'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '动态ID'),
            ),
			
            'getComments' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
                'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '动态ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getReplys' => array(
				'uid' => array('name' => 'uid', 'type' => 'int',  'require' => true, 'desc' => '用户ID'),
                'commentid' => array('name' => 'commentid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
		
            'del' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '动态ID'),
            ),
			
			
			'report' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => 'token'),
                'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '动态ID'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '举报内容'),
            ),
			
			
			'getCreateNonreusableSignature' => array(
                'imgname' => array('name' => 'imgname', 'type' => 'string', 'desc' => '图片名称'),
                'videoname' => array('name' => 'videoname', 'type' => 'string', 'desc' => '视频名称'),
				'folderimg' => array('name' => 'folderimg', 'type' => 'string','desc' => '图片文件夹'),
				'foldervideo' => array('name' => 'foldervideo', 'type' => 'string', 'desc' => '视频文件夹'),
            ),


            'getRecommendDynamics'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getDynamicLabels' => array(

				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getLabelDynamic' => array(
                'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string','desc' => '用户Token'),
                'labelid' => array('name' => 'labelid', 'type' => 'int', 'require' => true, 'desc' => '话题标签ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			
			'searchLabels' => array(
				'key' => array('name' => 'key', 'type' => 'string', 'default'=>'' ,'desc' => '话题标签关键词'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'delComments' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => false, 'desc' => '用户Token'),
				'dynamicid' => array('name' => 'dynamicid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '动态ID'),
                'commentid' => array('name' => 'commentid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论ID'),
                'commentuid' => array('name' => 'commentuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论者用户ID'),
                
            ),


            
		);
	}
	
	/**
	 * 发布动态
	 * @desc 用于 发布动态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].id 记录ID
	 * @return string msg 提示信息
	 */
	public function setDynamic() {
		$rs = array('code' => 0, 'msg' => '发布成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$title=checkNull($this->title);
		$thumb=checkNull($this->thumb);
		$video_thumb=checkNull($this->video_thumb);
		$href=checkNull($this->href);
		$lat=checkNull($this->lat);
		$lng=checkNull($this->lng);
		$city=checkNull($this->city);
		$address=checkNull($this->address);
		$type=checkNull($this->type);
		$sign=checkNull($this->sign);
		$voice=checkNull($this->voice);
		$length=checkNull($this->length);
		$labelid=checkNull($this->labelid);
		
		if($uid<1 || $token==''){
            $rs['code'] = 1001;
			$rs['msg'] = '信息错误';
			return $rs;
        }
     
        $checkdata=array(
            'uid'=>$uid,
            'type'=>$type,
        );
        
       $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1002;
			$rs['msg']='签名错误';
			return $rs;	
        }
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$sensitivewords=sensitiveField($title);
	
		if($sensitivewords==1001){
			$rs['code'] = 10011;
			$rs['msg'] = '动态标题输入非法，请重新输入';
			return $rs;
		}

		$data=array(
            'uid'=>$uid,
            'title'=>$title,
            'thumb'=>$thumb,
            'video_thumb'=>$video_thumb,
            'href'=>$href,
            'voice'=>$voice,
            'length'=>$length,
            'lat'=>$lat,
            'lng'=>$lng,
            'city'=>$city,
            'address'=>$address,
            'type'=>$type,
            'labelid'=>$labelid,
            "likes"=>0,
			"comments"=>0,
        );

		
		$domain = new Domain_Dynamic();
		$info = $domain->setDynamic($data);
		if($info==1007){
			$rs['code']=1007;
			$rs['msg']='视频分类不存在';
			return $rs;
		}else if($info==1003){
			$rs['code']=1003;
			$rs['msg']='您还未认证或认证还未通过';
			return $rs;
		}else if($info==1004){
			$rs['code']=1004;
			$rs['msg']='发布失败，请重试';
			return $rs;
		}else if(!$info){
			$rs['code']=1001;
			$rs['msg']='发布失败';
			return $rs;
		}
		if($info['status']=='0'){
			$rs['msg']="发布成功，请等待审核";
		}
		$rs['info'][0]=$info;
		/* $rs['info'][0]['id']=$info['id'];
		$rs['info'][0]['thumb_s']=get_upload_path($thumb_s); */
		return $rs;
	}		
	
   	/**
     * 评论/回复
     * @desc 用于用户评论/回复 别人视频
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return int info[0].isattent 对方是否关注我
     * @return int info[0].u2t 我是否拉黑对方
     * @return int info[0].t2u 对方是否拉黑我
     * @return int info[0].comments 评论总数
     * @return int info[0].replys 回复总数
     * @return string msg 提示信息
     */
	public function setComment() {
        $rs = array('code' => 0, 'msg' => '评论成功', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$touid=checkNull($this->touid);
		$dynamicid=$this->dynamicid;
		$commentid=$this->commentid;
		$parentid=$this->parentid;
		$content=checkNull($this->content);
		$type=checkNull($this->type);
		$voice=checkNull($this->voice);
		$length=checkNull($this->length);

		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$info=array(
			'isattent'=>'0',
			'u2t'=>'0',
			't2u'=>'0',
			'comments'=>0,
			'replys'=>0,
		);
		if($touid>0){
			$isattent=isAttention($touid,$uid);
			$u2t = isBlack($uid,$touid);
			$t2u = isBlack($touid,$uid);
			$info['isattent']=(string)$isattent;
			$info['u2t']=(string)$u2t;
			$info['t2u']=(string)$t2u;
			if($t2u==1){
				$rs['code'] = 1000;
				$rs['msg'] = '对方暂时拒绝接收您的消息';
				return $rs;
			}
		}
		
		if($type==1){
            if($voice==''){
                $rs['code'] = 1001;
				$rs['msg'] = '请录入语音';
				return $rs;
            }
        }else{
            if($content==''){
                $rs['code'] = 1002;
				$rs['msg'] = '请输入内容';
				return $rs;
            }
        }
		
		$sensitivewords=sensitiveField($content);
		if($sensitivewords==1001){
			$rs['code'] = 10011;
			$rs['msg'] = '评论内容输入非法，请重新输入';
			return $rs;
		}
		
		if($commentid==0 && $commentid!=$parentid){
			$commentid=$parentid;
		}
		
		$data=array(
			'uid'=>$uid,
			'touid'=>$touid,
			'dynamicid'=>$dynamicid,
			'commentid'=>$commentid,
			'parentid'=>$parentid,
			'content'=>$content,
			'addtime'=>time(),
			'type'=>$type,
			'voice'=>$voice,
			'length'=>$length,
		);

        $domain = new Domain_Dynamic();
        $result = $domain->setComment($data);
		
		$info['comments']=$result['comments'];
		$info['replys']=$result['replys'];
	
		$rs['info'][0]=$info;
		
		if($parentid!=0){
			 $rs['msg']='回复成功';			
		}
        return $rs;
    }	
	
  
   	/**
     * 点赞
     * @desc 用于动态点赞数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].islike 是否点赞 
     * @return string info[0].likes 点赞数量
     * @return string msg 提示信息
     */
	public function addLike() {
        $rs = array('code' => 0, 'msg' => '操作成功', 'info' => array());
        $uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$dynamicid=checkNull($this->dynamicid);
		$sign=checkNull($this->sign);
        
        if($uid<1 || $token=='' || $dynamicid <1 ){
            $rs['code'] = 1001;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'dynamicid'=>$dynamicid,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1002;
			$rs['msg']='签名错误';
			return $rs;	
        }
      
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		
        $domain = new Domain_Dynamic();
        $result = $domain->addLike($uid,$dynamicid);
		if($result==1001){
			$rs['code'] = 1001;
			$rs['msg'] = "动态不存在";
			return $rs;
		}else if($result==1002){
			$rs['code'] = 1002;
			$rs['msg'] = "不能给自己点赞";
			return $rs;
		}
		$rs['info'][0]=$result;
        return $rs;
    }	

   	
	
   	/**
     * 评论/回复 点赞
     * @desc 用于评论/回复 点赞数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].islike 是否点赞 
     * @return string info[0].likes 点赞数量
     * @return string msg 提示信息
     */
	public function addCommentLike() {
        $rs = array('code' => 0, 'msg' => '点赞成功', 'info' => array());

        $uid=$this->uid;
        $token=checkNull($this->token);
        $commentid=$this->commentid;
		$sign=$this->sign;

		if($uid<1 || $token=='' || $commentid <1 ){
            $rs['code'] = 1001;
			$rs['msg'] = '信息错误';
			return $rs;
        }
		$checkdata=array(
            'uid'=>$uid,
            'commentid'=>$commentid,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1002;
			$rs['msg']='签名错误';
			return $rs;	
        }
		

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $domain = new Domain_Dynamic();
         $res= $domain->addCommentLike($uid,$commentid);
         if($res==1001){
         	$rs['code']=1003;
         	$rs['msg']='评论信息不存在';
         	return $rs;
         }
         $rs['info'][0]=$res;

        return $rs;
    }	
	
	/**
     * 获取关注动态
     * @desc 用于获取关注动态
     * @return int code 操作码，0表示成功
     * @return array info 动态列表
     * @return array info[].userinfo 用户信息
     * @return string info[].datetime 格式后的发布时间
	 * @return string info[].islike 是否点赞 
	 * @return string info[].comments 评论总数
     * @return string info[].likes 点赞数
     * @return string msg 提示信息
     */
	public function getAttentionDynamic() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=$this->uid;
		$token=checkNull($this->token);
		$p=$this->p;
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$key='attention_dynamicLists_'.$uid.'_'.$p;
        $info=getcaches($key);

        if(!$info){
        	$domain = new Domain_Dynamic();
        	$info=$domain->getAttentionDynamic($uid,$p);
        	if(!$info){
        		 $rs['code']=0;
                $rs['msg']="暂无动态列表";
                return $rs;
        	}
			setCaches($key,$info,2);
        }
        
        $rs['info'] = $info;

        return $rs;
    }
	/**
     * 获取最新动态
     * @desc 用于 获取最新动态
     * @return int code 操作码，0表示成功
     * @return array info 动态列表
     * @return array info[].userinfo 用户信息
     * @return string info[].datetime 格式后的发布时间
	 * @return string info[].islike 是否点赞 
	 * @return string info[].comments 评论总数
     * @return string info[].likes 点赞数
     * @return string msg 提示信息
     */
	public function getNewDynamic() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=checkNull($this->uid);
		$lng=checkNull($this->lng);
		$lat=checkNull($this->lat);
		$p=checkNull($this->p);
		
		if(!$p){
			$p=1;
		}
		
		$key='new_dynamicLists_'.$p;
        $info=getcaches($key);

        if(!$info){
        	$domain = new Domain_Dynamic();
        	$info=$domain->getNewDynamic($uid,$lng,$lat,$p);
			
        	if(!$info){
        		$rs['code']=0;
                $rs['msg']="暂无动态列表";
                return $rs;
        	}
			setCaches($key,$info,2);
        }
        
        $rs['info'] = $info;

        return $rs;
    }
	
	/**
     * 个人主页动态
     * @desc 用于获取个人主页动态
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getHomeDynamic() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);
		$token=checkNull($this->token);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		

        $domain = new Domain_Dynamic();
        $info = $domain->getHomeDynamic($uid,$touid,$p);
		
		
		$rs['info']=$info;

        return $rs;
    }	
	 /**
     * 获取推荐动态
     * @desc 用户获取推荐动态
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getRecommendDynamics(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
    	$uid=$this->uid;
    	if($uid>0){ //非游客

    		$isBan=isBan($this->uid);
			if($isBan=='0'){
				$rs['code'] = 700;
				$rs['msg'] = '该账号已被禁用';
				return $rs;
			}
    	}

		$p=$this->p;
		$key='dynamicRecommend_'.$p;

		$info=getcaches($key);

		if(!$info){

			$domain=new Domain_Dynamic();
			$info=$domain->getRecommendDynamics($uid,$p);

			if($info==1001){
				$rs['code']=1001;
				$rs['msg']="暂无动态列表";
				return $rs;
			}
			setcaches($key,$info,2);
		}
		$rs['info']=$info;

		return $rs;
    }
	
	
	/**
     * 动态详情
     * @desc 用于获取动态详情
     * @return int code 操作码，0表示成功，1000表示视频不存在
     * @return array info[0] 视频详情
     * @return object info[0].userinfo 用户信息
     * @return string info[0].datetime 格式后的时间差
     * @return string info[0].isattent 是否关注
     * @return string info[0].likes 点赞数
     * @return string info[0].comments 评论数
     * @return string info[0].views 阅读数
     * @return string info[0].steps 踩一踩数量
     * @return string info[0].shares 分享数量
     * @return string info[0].islike 是否点赞
     * @return string info[0].isstep 是否踩
     * @return string msg 提示信息
     */
	public function getDynamic() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Dynamic();
        $result = $domain->getDynamic($this->uid,$this->dynamicid);
		if(!$result){
			$rs['code'] = 1000;
			$rs['msg'] = "动态已删除";
			return $rs;
			
		}
		$rs['info'][0]=$result;

        return $rs;
    }
	/**
     * 动态评论列表
     * @desc 用于获取 动态评论列表
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].comments 评论总数
     * @return array info[0].commentlist 评论列表
     * @return object info[0].commentlist[].userinfo 用户信息
	 * @return string info[0].commentlist[].datetime 格式后的时间差
	 * @return string info[0].commentlist[].replys 回复总数
	 * @return string info[0].commentlist[].likes 点赞数
	 * @return string info[0].commentlist[].islike 是否点赞
	 * @return array info[0].commentlist[].replylist 回复列表
     * @return string msg 提示信息
     */
	public function getComments() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Dynamic();
        $rs['info'][0] = $domain->getComments($this->uid,$this->dynamicid,$this->p);

        return $rs;
    }	
	
	/**
     * 回复列表
     * @desc 用于获取动态回复列表
     * @return int code 操作码，0表示成功
     * @return array info 评论列表
     * @return object info[].userinfo 用户信息
	 * @return string info[].datetime 格式后的时间差
	 * @return object info[].tocommentinfo 回复的评论的信息
	 * @return object info[].tocommentinfo.content 评论内容
	 * @return string info[].likes 点赞数
	 * @return string info[].islike 是否点赞
     * @return string msg 提示信息
     */
	public function getReplys() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Dynamic();
        $rs['info'] = $domain->getReplys($this->uid,$this->commentid,$this->p);

        return $rs;
    }	
	
	
	/**
     * 删除动态
     * @desc 用于删除动态以及相关信息
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function del() {
        $rs = array('code' => 0, 'msg' => '删除成功', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$dynamicid=$this->dynamicid;

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
        $domain = new Domain_Dynamic();
        $info = $domain->del($uid,$dynamicid);

        return $rs;
    }	

	/**
     * 举报动态
     * @desc 用于举报动态
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function report() {
        $rs = array('code' => 0, 'msg' => '举报成功', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$dynamicid=$this->dynamicid;
		$content=checkNull($this->content);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
		$data=array(
			'uid'=>$uid,
			'dynamicid'=>$dynamicid,
			'content'=>$content,
			'addtime'=>time(),
		);
        $domain = new Domain_Dynamic();
        $info = $domain->report($data);
		
		if($info==1000){
			$rs['code'] = 1000;
			$rs['msg'] = '动态不存在';
			return $rs;
		}

        return $rs;
    }	


	
	
	/* 检测文件后缀 */
	protected function checkExt($filename){
		$config=array("jpg","png","jpeg");
		$ext   =   pathinfo(strip_tags($filename), PATHINFO_EXTENSION);
		 
		return empty($config) ? true : in_array(strtolower($ext), $config);
	}	
	
	/**
     * 获取七牛上传Token
     * @desc 用于获取七牛上传Token
     * @return int code 操作码，0表示成功
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getQiniuToken(){
	
	   	$rs = array('code' => 0, 'msg' => '', 'info' =>array());

	   	//获取后台配置的腾讯云存储信息
		//$configPri=getConfigPri();
		//$token = DI()->qiniu->getQiniuToken2($configPri['qiniu_accesskey'],$configPri['qiniu_secretkey'],$configPri['qiniu_bucket']);
		$token = DI()->qiniu->getQiniuToken();
		$rs['info'][0]['token']=$token ; 
		return $rs; 
		
	}
   

	/**
     * 获取动态举报分类列表
     * @desc 获取动态举报分类列表
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
	public function getReportlist() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Dynamic();
        $res = $domain->getReportlist();

        if($res==1001){
        	$rs['code']=1001;
        	$rs['msg']='暂无举报分类列表';
        	return $rs;
        }
        $rs['info']=$res;
        return $rs;
    }

	/**
     * 获取动态话题标签
     * @desc 获取动态话题标签
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
	public function getDynamicLabels() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$p=checkNull($this->p);
        $key='getDynamicLabels_'.$p;
		$list=getcaches($key);
		if(!$list){
			$domain = new Domain_Dynamic();
			$list = $domain->getDynamicLabels($p);
			if($list==1001){
				return $rs;
			}
			setcaches($key,$list,10);
     
		}
        $rs['info']=$list;
        return $rs;
    }
	
	/**
     * 获取热门话题标签
     * @desc 用户app首页动态模块展示前十个热门话题
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
	public function getHotDynamicLabels() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $key='getHotDynamicLabels';
		$list=getcaches($key);
		if(!$list){
			$domain = new Domain_Dynamic();
			$list = $domain->getHotDynamicLabels();
			if($list==1001){
				return $rs;
			}
			setcaches($key,$list,10);
     
		}
        $rs['info']=$list;
        return $rs;
    }
	
	
	/**
     * 获取热门话题下的动态
     * @desc 用于获取热门话题下的动态
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getLabelDynamic() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$labelid=checkNull($this->labelid);
		$p=checkNull($this->p);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
        $domain = new Domain_Dynamic();
        $info = $domain->getLabelDynamic($uid,$labelid,$p);
		
		
		$rs['info']=$info;

        return $rs;
    }	
	
	
	/**
     * 搜索界面推荐的话题标签
     * @desc 用户app首页动态模块展示前5个推荐话题
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
	public function searchHotLabels() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());


		$key='searchHotLabels';
		$list=getcaches($key);
		if(!$list){
			$domain = new Domain_Dynamic();
			$list = $domain->searchHotLabels();
			if($list==1001){
				return $rs;
			}
			setcaches($key,$list,10);
     
		}
        $rs['info']=$list;
        return $rs;
    }
	
	/**
     * 搜索话题标签
     * @desc 用户app首页动态模块搜索话题
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function searchLabels() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

		$key=checkNull($this->key);
		$p=checkNull($this->p);
		
		if($key==''){
			return $rs;
		}

        $domain = new Domain_Dynamic();
        $info = $domain->searchLabels($key,$p);

        $rs['info'] = $info;

        return $rs;
    }	
	
	
	/**
     * 删除评论
     * @desc 用于删除评论以及子级评论
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function delComments() {
        $rs = array('code' => 0, 'msg' => '删除成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$dynamicid=checkNull($this->dynamicid);
		$commentid=checkNull($this->commentid);
		$commentuid=checkNull($this->commentuid);


		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
        $domain = new Domain_Dynamic();
        $info = $domain->delComments($uid,$dynamicid,$commentid,$commentuid);
		
		if($info==1001){
			$rs['code'] = 1001;
			$rs['msg'] = '动态信息错误,请稍后操作~';
		}else if($info==1002){
			$rs['code'] = 1002;
			$rs['msg'] = '您无权进行删除操作~';
		}

        return $rs;
    }
	
	
	

}
