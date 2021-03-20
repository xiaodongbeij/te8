<?php

class Domain_Shop {

	//获取店铺保证金设置
	public function getBond($uid){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getBond($uid);

		return $rs;
	}

	//缴纳店铺保证金
	public function deductBond($uid,$shop_bond){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->deductBond($uid,$shop_bond);

		return $rs;
	}

	//商品一级分类
	public function getOneGoodsClass(){
		$rs = array();

		$rs=getcaches("oneGoodsClass");

		if(!$rs){
			$model = new Model_Shop();
			$rs = $model->getOneGoodsClass();
			if(!empty($rs)){
				setcaches('oneGoodsClass',$rs);
			}
			
		}

		return $rs;
	}
	
	
	
	//卖家获取店铺申请信息
	public function getShopApplyInfo($uid){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getShopApplyInfo($uid);

		return $rs;
	}
	//申请店铺
	public function shopApply($uid,$data,$apply_status,$classid_arr){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->shopApply($uid,$data,$apply_status,$classid_arr);

		return $rs;
	}

    //获取店铺信息
	public function getShop($uid,$fields='') {
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getShop($uid,$fields);

		return $rs;
	}

	//其他类调用Shop类获取商品信息
	public function getGoods($where=[]) {
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getGoods($where);

		return $rs;
	}

	//商品总数
	public function countGoods($where=[]) {
		$rs = array();
        
		$model = new Model_Shop();
		$rs = $model->countGoods($where);

		return $rs;
	}

	//主播设置商品是否在售
	public function setSale($uid,$goodsid,$issale) {
        
		$rs = array('code' => 0, 'msg' => '操作成功', 'info' => array());

		$model = new Model_Shop();
        
        $where=[];
        $where['id=?']=$goodsid;
        
        $info=$model->getGoods($where);
        if(!$info){
            $rs['code'] = 1001;
			$rs['msg'] = '商品不存在';
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code'] = 1003;
			$rs['msg'] = '无权操作';
			return $rs;
        }
        
        if($info['status']==-2){
            $rs['code'] = 1002;
			$rs['msg'] = '已被管理员下架';
			return $rs;
        }
        
        if($info['status']!=1){
            $rs['code'] = 1002;
			$rs['msg'] = '商品未审核通过';
			return $rs;
        }
        
        $issale= $issale ? 1 : 0;
        $data=[
            'issale'=>$issale,
        ];

        if($issale==0){ //取消在售
        	$data['live_isshow']=0;
        }
        
		$res = $model->upGoods($where,$data);

		return $rs;
	}

	//上下架
	public function upStatus($uid,$goodsid,$status) {
        
		$rs = array('code' => 0, 'msg' => '操作成功', 'info' => array());

		$model = new Model_Shop();
        
        $where=[];
        $where['id=?']=$goodsid;
        
        $info=$model->getGoods($where);
        if(!$info){
            $rs['code'] = 1001;
			$rs['msg'] = '商品不存在';
			return $rs;
        }
        
        
        if($info['uid']!=$uid){
            $rs['code'] = 1003;
			$rs['msg'] = '无权操作';
			return $rs;
        }
        
        if($info['status']==0){
            $rs['code'] = 1002;
			$rs['msg'] = '商品审核中，无权操作';
			return $rs;
        }
        
        if($info['status']==2){
            $rs['code'] = 1002;
			$rs['msg'] = '商品审核未通过';
			return $rs;
        }
        
        if($info['status']==-2){
            $rs['code'] = 1002;
			$rs['msg'] = '已被管理员下架';
			return $rs;
        }
        
        if($status==1){
            $where['status']=-1;
            $data=[
                'status'=>1,
            ];
            $info2['status'] = '1';
        }else{
            $where['status']=1;
            $data=[
                'status'=>-1,
            ];
            $info2['status'] = '-1';
        }

		$res = $model->upGoods($where,$data);
        
        $rs['info'][0]=$info2;
        
		return $rs;
	}

	//获取商品信息
	public function getGoodsInfo($uid,$goodsid){

		$rs = array('code' => 0, 'msg' => '获取成功', 'info' => array());

		$where=[];
        $where['id=?']=$goodsid;

        $model = new Model_Shop();
        
        $info=$model->getGoods($where);

        if(!$info){
        	$rs['code']=1001;
        	$rs['msg']='商品不存在';
        	return $rs;
        }

        $status=$info['status'];

        if($info['uid']!=$uid){ //买家查看商品信息
        	/*if($status==0){
        		$rs['code']=1001;
	        	$rs['msg']='商品不存在';
	        	return $rs;
        	}*/

        	if($status==-1||$status==-2 || $status==0){
        		$info['status']='-1';
        	}
        }

        //商品信息格式化处理
        $info=handleGoods($info);
		$info['iscollect']=isGoodsCollect($uid,$goodsid);  //判断有没有收藏


        //获取卖家的店铺信息
        $shopinfo=$model->getShop($info['uid']);

        //判断用户是否关注了店铺主播
        $isattention=isAttention($uid,$info['uid']);
        $shopinfo['isattention']=$isattention;

        $comment_nums=$model->getGoodsCommentNums($goodsid);
        $info['comment_nums']=NumberFormat($comment_nums);
        $comment_lists=$model->getTopThreeGoodsComments($goodsid);

        $rs['info'][0]['goods_info']=$info;
        $rs['info'][0]['shop_info']=$shopinfo;
        $rs['info'][0]['comment_lists']=$comment_lists;

        return $rs;

	}

	//获取店铺里的在售商品列表
	public function getGoodsList($where,$p){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getGoodsList($where,$p);

		return $rs;
	}


	public function countSale($liveuid) {
		$rs = array();
        
        $where=[];
        $where['uid=?']=$liveuid;
        $where['issale']=1;
        $where['status']=1;
        
		$model = new Model_Shop();
		$rs = $model->countGoods($where);

		return $rs;
	}

	//获取商品评论列表
	public function getGoodsCommentList($uid,$goodsid,$type,$p){
		$rs = array();

		$model = new Model_Shop();
		$rs['comment_lists'] = $model->getGoodsCommentList($uid,$goodsid,$type,$p);

		//获取不同类型下的商品评论个数
		$rs['type_nums']=$model->getGoodsCommentsTypeNums($goodsid,$type);

		return $rs;
	}

	//搜索用户发布的商品列表
	public function searchShopGoods($uid,$keywords,$p){
		$rs = array();

		$model = new Model_Shop();
		$rs=$model->searchShopGoods($uid,$keywords,$p);

		return $rs;
	}
	
	
	//收藏商品
	public function setCollect($uid,$goodsid) {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$model = new Model_Shop();
		$where=[];
        $where['id=?']=$goodsid;
		/* $where['status=?']=1; */

        $info=$model->getGoods($where);
        if(!$info){
            $rs['code'] = 1001;
			$rs['msg'] = '商品不存在';
			return $rs;
        }
		
		$iscollect=$model->setCollect($uid,$goodsid,$info['uid']);

		if($iscollect){
			$rs['msg']='已收藏';	
		}else{
			$rs['msg']='已取消收藏';
		}
		
		$rs['info'][0]['iscollect'] = $iscollect;

		return $rs;
	}
	
	
	//商品收藏列表
	public function getGoodsCollect($uid,$p) {
		$rs = array();
		
		$model = new Model_Shop();
		$rs = $model->getGoodsCollect($uid,$p);
	
		foreach($rs as $k=>$v){
			$where=[];
			$where['id=?']=$v['goodsid'];	
			
			$info=DI()->notorm->shop_goods
					->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow")
					->where($where)
					->fetchOne();

			if(!$info){  //删除收藏商品
				DI()->notorm->user_goods_collect
					->where('uid=? and goodsid=?',$uid,$v['goodsid'])
					->delete();
				
				unset($rs[$k]);
			}else{
	
				$thumb_arr=explode(',',$info['thumbs']);
				$info['thumb']=get_upload_path($thumb_arr[0]);
				if($info['type']==1){ //外链商品
					$info['price']=$info['present_price'];
					
				}else{
					$spec_arr=json_decode($info['specs'],true);
					$info['price']=$spec_arr[0]['price'];
					
				}
				unset($info['specs']);
				unset($info['thumbs']);
				unset($info['present_price']);
				$info['iscollect']=isGoodsCollect($uid,$v['goodsid']);
				$rs[$k]=$info;
			}

		}
		return $rs;
	}
	
	
	//获取正在经营的一级商品分类
	public function getBusinessCategory($uid){
		$rs = array();
		
		$model = new Model_Shop();
		$rs = $model->getBusinessCategory($uid);
			

		return $rs;
	}
	
	
	//获取正在申请的经营类目
	public function getApplyBusinessCategory($uid){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getApplyBusinessCategory($uid);
			
		return $rs;
	}
	
	
	//申请经营类目
	public function applyBusinessCategory($uid,$classid){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->applyBusinessCategory($uid,$classid);
			
		return $rs;
	}
	
	//申请经营类目
	public function getGoodExistence($uid,$goodsid){
		$rs = array();

		$model = new Model_Shop();
		$rs = $model->getGoodExistence($uid,$goodsid);
			
		return $rs;
	}
	
}
