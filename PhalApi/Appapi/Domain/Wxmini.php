<?php

class Domain_Wxmini {

	public function getAuth($uid){
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->getAuth($uid);

		return $rs;
	}

	public function userAuth($data) {
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->userAuth($data);

		return $rs;
	}
	public function profitList($uid,$p){
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->profitList($uid,$p);

		return $rs;
	}
	public function goodsOrderRefundConsult($uid,$orderid,$user_type){
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->goodsOrderRefundConsult($uid,$orderid,$user_type);

		return $rs;
	}
	public function getOrderExpressInfo($uid,$orderid,$user_type){
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->getOrderExpressInfo($uid,$orderid,$user_type);

		return $rs;
	}
	public function getShopCashRecord($uid,$p){
		$rs = array();

		$model = new Model_Wxmini();
		$rs = $model->getShopCashRecord($uid,$p);

		return $rs;
	}


}
