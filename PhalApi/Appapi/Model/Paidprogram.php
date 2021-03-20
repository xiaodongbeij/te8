<?php

class Model_Paidprogram extends PhalApi_Model_NotORM {
	/* 引导页 */
	public function getApplyStatus($uid) {
		
		$apply_status='';
        $info=DI()->notorm->paidprogram_apply->where("uid=?",$uid)->fetchOne();
        if(!$info){
        	$apply_status='-2';	 //没有申请
        	
        }else{
        	$apply_status=$info['status'];
			
			if($apply_status=='-1'){
				$now=time();
				$configpub=getConfigPub();
				$cha=$now-$info['addtime'];
				$payment_time=$configpub['payment_time'];
				if($payment_time&&($cha>$payment_time*24*60*60)){//判断有没有超过申请时间限制
					$apply_status='-2';	 //改为没有申请,判断有没有超过申请时间限制
				}
			}
        }

        

        return $apply_status;

	}

	//申请付费内容
	public function apply($uid){
		$info=DI()->notorm->paidprogram_apply->where("uid=?",$uid)->fetchOne();

		$now=time();

		$configpub=getConfigPub();


		if($info){
			$status=$info['status'];
			if($status==1){
				return 1001;
			}else if($status==0){
				return 1002;
			}else if($status==-1){

				
				$cha=$now-$info['addtime'];
				
				$payment_time=$configpub['payment_time'];

				if($payment_time&&($cha<$payment_time*24*60*60)){
					
					return 1003;
					

				}

				$res=DI()->notorm->paidprogram_apply->where("uid=?",$uid)->update(array('status'=>0));
				
			}
		}else{

			$data=array(
				'uid'=>$uid,
				'status'=>0,
				'addtime'=>$now,
				'percent'=>$configpub['payment_percent']
			);
			$res=DI()->notorm->paidprogram_apply->insert($data);

		}

		if(!$res){
			return 1004;
		}

		return 1;
	}

	//获取付费内容分类列表
	public function getPaidprogramClassList(){
		$list=DI()->notorm->paidprogram_class
            ->select("id,name")
            ->where('status=1')
            ->order("list_order asc,id desc")
            ->fetchAll();

        return $list;
	}

	//添加付费项目
	public function addPaidProgram($data){

		//判断用户是否审核通过
		$apply_status=$this->getApplyStatus($data['uid']);
		if($apply_status!=1){
			return 1001;
		}

		$res=DI()->notorm->paidprogram->insert($data);
		return $res;
	}

	//获取付费项目详情
	public function getPaidProgramInfo($uid,$object_id){
		$info=DI()->notorm->paidprogram
			->where("id=?",$object_id)
			->fetchOne();

		if(!$info){
			return 1001;
		}

		$info['thumb']=get_upload_path($info['thumb']);
		$video_arr=json_decode($info['videos'],true);

		foreach ($video_arr as $k => $v) {
			$video_arr[$k]['video_url']=get_upload_path($v['video_url']);
			$video_arr[$k]['video_length_format']=getSeconds($v['video_length']);
		}

		$info['videos']=$video_arr;
		$info['video_num']="共".count($video_arr)."集";
		if($info['evaluate_nums']>0){
			$info['evaluate_point']=(string)floor($info['evaluate_total']/$info['evaluate_nums']); //观众评价星级
		}else{
			$info['evaluate_point']='0';

		}

		$user_info=getUserInfo($info['uid']);
		$userinfo['avatar']=$user_info['avatar'];
		$userinfo['user_nicename']=$user_info['user_nicename'];
		$info['userinfo']=$userinfo;

		$can_buy='1';
		$is_buy='0';
		$can_comment='1';

		//判断用户是否可以购买
		if($info['uid']==$uid){ //作者本人
			$can_buy='0';
			$is_buy='1';
		}else{ //判断用户是否购买过
			
			$isbuy=$this->checkIsBuy($uid,$object_id);


			if($isbuy){
				$can_buy='0';
				$is_buy='1';
			}
			
		}

		$info['can_buy']=$can_buy;
		$info['is_buy']=$is_buy;


		//判断用户是否可评价
		if($info['uid']==$uid){
			$can_comment='0';
		}else{

			$isbuy=$this->checkIsBuy($uid,$object_id);
			if($isbuy){
				$iscomment=$this->checkIsComment($uid,$object_id);
				if($iscomment){
					$can_comment='0';
				}
			}else{
				$can_comment='0';
			}
		}

		$info['can_comment']=$can_comment;

		return $info;
	}

	//获取我上传的付费项目
	public function getMyPaidProgram($uid,$p){
		if($p<1){
			$p=1;
		}

		$pnums=50;
		$start=($p-1)*$pnums;

		$list=DI()->notorm->paidprogram
			->select("id,title,thumb,type,videos,sale_nums,status,money")
			->where("uid={$uid}")
			->order("addtime desc")
			->limit($start,$pnums)
			->fetchAll();
		foreach ($list as $k => $v) {

			$list[$k]['thumb_format']=get_upload_path($v['thumb']);
			if($v['type']==0){
				$list[$k]['video_num']='共1集';
			}else{
				$video_arr=json_decode($v['videos'],true);
				$list[$k]['video_num']='共'.count($video_arr).'集';
			}

			$list[$k]['money']='￥'.$v['money'];

			unset($list[$k]['thumb']);
			unset($list[$k]['type']);
			unset($list[$k]['videos']);
		}

		return $list;
	}


	//创建付费项目订单
	public function getOrderId($orderinfo){

		//获取项目信息
		$info=DI()->notorm->paidprogram
				->where("id=?",$orderinfo['object_id'])
				->fetchOne();

		if(!$info){
			return 1001;
		}

		if($orderinfo['uid']==$info['uid']){
			return 1002;
		}

		$status=$info['status'];
		if($status!=1){
			return 1003;
		}

		//判断用户是否购买过该项目
		$isbuy=$this->checkIsBuy($orderinfo['uid'],$orderinfo['object_id']);

		if($isbuy){
			return 1004;
		}

		//删除用户此付费项目未付款的订单
		$this->delUnpaidprogram($orderinfo['uid'],$orderinfo['object_id']);

		$orderinfo['touid']=$info['uid'];
		$orderinfo['money']=$info['money'];

		$result=DI()->notorm->paidprogram_order->insert($orderinfo);



		return $result;

	}


	//用户余额支付付费项目
	public function balancePay($uid,$orderinfo){
		//获取项目信息
		$info=DI()->notorm->paidprogram
				->where("id=?",$orderinfo['object_id'])
				->fetchOne();

		if(!$info){
			return 1001;
		}

		if($uid==$info['uid']){
			return 1002;
		}

		$status=$info['status'];
		if($status!=1){
			return 1003;
		}

		//判断用户是否购买过该项目
		$isbuy=$this->checkIsBuy($uid,$orderinfo['object_id']);

		if($isbuy){
			return 1005;
		}

		//获取用户的余额
		$user_balance=getUserShopBalance($uid);
		if($user_balance['balance']<$info['money']){
			return 1004;
		}

		//扣除用户余额
		$res=setUserBalance($uid,0,$info['money']);

		if(!$res){
			return -1;
		}

		//写入订单
		$orderinfo['touid']=$info['uid'];
		$orderinfo['money']=$info['money'];

		$result=DI()->notorm->paidprogram_order->insert($orderinfo);

		if(!$result){

			//返还用户余额
			setUserBalance($uid,1,$info['money']);
			return -1;
		}

		//删除用户此付费项目未付款的订单
		$this->delUnpaidprogram($uid,$orderinfo['object_id']);

		//写入余额操作记录
		$data=array(
			'uid'=>$uid,
			'touid'=>$info['uid'],
			'balance'=>$info['money'],
			'type'=>0,
			'action'=>7, //用户使用余额购买付费项目
			'orderid'=>$result['id'],
			'addtime'=>time()
		);

		addBalanceRecord($data);

		$money=$info['money'];

		//获取用户的抽水比例
		$apply_info=DI()->notorm->paidprogram_apply->where("uid=?",$info['uid'])->fetchOne();
		$percent=$apply_info['percent'];

		if($percent>0){
			$money=$money*(100-$percent)/100;
			$money=round($money,2);
		}

		//给发布者增加余额
		setUserBalance($info['uid'],1,$money);


		$data1=array(
			'uid'=>$info['uid'],
			'touid'=>$uid,
			'balance'=>$money,
			'type'=>1,
			'action'=>8, //付费项目收入
			'orderid'=>$result['id'],
			'addtime'=>time()
		);

		addBalanceRecord($data1);

		//修改付费内容的销量
		DI()->notorm->paidprogram->where("id={$result['object_id']}")->update(array('sale_nums' => new NotORM_Literal("sale_nums + 1")));

		return 1;

	}

	//判断是否购买过付费项目
	public function checkIsBuy($uid,$object_id){

		$info=DI()->notorm->paidprogram_order->where("uid=? and object_id=? and status=1",$uid,$object_id)->fetchOne();
		if(!$info){
			return 0;
		}

		return 1;
	}

	//删除用户该付费项目未支付的订单
	public function delUnpaidprogram($uid,$object_id){
		
		return 1;

		$res=DI()->notorm->paidprogram_order->where("uid=? and object_id=? and status=0",$uid,$object_id)->delete();
		return $res;
	}

	//获取购买的付费项目列表
	public function getPaidProgramList($uid,$p){
		if($p<1){
			$p=1;
		}


		$pnums=50;
		$start=($p-1)*$pnums;
		$list=DI()->notorm->paidprogram_order
				->select("object_id")
				->where("uid=? and status=1 and isdel=0",$uid)
				->order("addtime desc")
				->limit($start,$pnums)
				->fetchAll();

		foreach ($list as $k => $v) {
			//获取付费项目详情
			$info=DI()->notorm->paidprogram->select("id,uid,title,thumb,type,videos")->where("id=?",$v['object_id'])->fetchOne();
			$list[$k]['thumb']=get_upload_path($info['thumb']);
			$list[$k]['title']=$info['title'];
			$user_info=getUserInfo($v['touid']);
			$userinfo['avatar']=$user_info['avatar'];
			$userinfo['user_nicename']=$user_info['user_nicename'];
			$list[$k]['userinfo']=$userinfo;
			$list[$k]['avatar']=$userinfo['avatar']; //android专用
			$list[$k]['user_nicename']=$userinfo['user_nicename']; //android专用

			if($info['type']==0){
				$list[$k]['video_num']='付费视频|共1集';
			}else{
				$video_arr=json_decode($info['videos'],true);
				$count=count($video_arr);
				$list[$k]['video_num']='付费视频|共'.$count.'集';
			}
		}

		return $list;

	}

	//付费内容发布评价
	public function setComment($uid,$object_id,$grade){
		//是否购买
		$isbuy=$this->checkIsBuy($uid,$object_id);
		if(!$isbuy){
			return 1001;
		}

		//是否已评价
		$iscomment=$this->checkIsComment($uid,$object_id);
		if($iscomment){
			return 1002;
		}

		$info=DI()->notorm->paidprogram
				->where("id=?",$object_id)
				->fetchOne();

		if(!$info){
			return 1003;
		}

		if($info['uid']==$uid){
			return 1004;
		}

		if($info['status']!=1){
			return 1005;
		}

		$data=array(
			'uid'=>$uid,
			'touid'=>$info['uid'],
			'object_id'=>$object_id,
			'grade'=>$grade,
			'addtime'=>time()
		);

		$res=DI()->notorm->paidprogram_comment->insert($data);

		if($res){

			DI()->notorm->paidprogram
					->where("id=?",$object_id)
					->update(
						array(
							'evaluate_nums' => new NotORM_Literal("evaluate_nums + 1"),
							'evaluate_total' => new NotORM_Literal("evaluate_total + {$grade}"),
						)
					);
		}

		return $res;

	}

	//判断是否评价过付费项目
	public function checkIsComment($uid,$object_id){

		$info=DI()->notorm->paidprogram_comment->where("uid=? and object_id=?",$uid,$object_id)->fetchOne();
		if(!$info){
			return 0;
		}

		return 1;
	}

	//其他类获取付费项目详情
	public function getPaidProgram($where){
		$info=[];
		$info=DI()->notorm->paidprogram
			->where($where)
			->fetchOne();

		return $info;
	}

	//发布视频时搜索付费内容
	public function searchPaidProgram($uid,$keywords,$p){
		if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;

        $where="uid={$uid} and status=1";

        if($keywords!=''){
            $where.=" and title like '%".$keywords."%'";
        }

        $list=DI()->notorm->paidprogram
        		->select("id,title,thumb,type,money,videos")
        		->where($where)
        		->order("addtime desc")
        		->limit($start,$pnums)
        		->fetchAll();

        foreach ($list as $k => $v) {
        	$list[$k]['thumb']=get_upload_path($v['thumb']);
        	if($v['type']==0){
        		$list[$k]['video_num']='共1集';
        	}else{
        		$video_arr=json_decode($v['videos'],true);
        		$list[$k]['video_num']='共'.count($video_arr).'集';
        	}

        	$list[$k]['price']=$v['money'];
        	$list[$k]['name']=$v['title'];

        	unset($list[$k]['videos']);
        	unset($list[$k]['type']);
        	unset($list[$k]['title']);
        }

        return $list;

	}


}
