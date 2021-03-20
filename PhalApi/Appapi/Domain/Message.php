<?php

class Domain_Message {
	public function getList($uid,$p) {
		$rs = array();

		$model = new Model_Message();
		$rs = $model->getList($uid,$p);

		return $rs;
	}

	public function getShopOrderList($uid,$p){
		$rs = array();

		$model = new Model_Message();
		$rs = $model->getShopOrderList($uid,$p);

		return $rs;
	}
	
}
