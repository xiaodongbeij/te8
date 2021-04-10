<?php

class Domain_Home {

    public function getSlide($where) {
        $rs = array();
        $model = new Model_Home();
        $rs = $model->getSlide($where);
        return $rs;
    }
		
	public function getRecommendLive($p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getRecommendLive($p);
				
        return $rs;
    }
	
	public function getHot($p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getHot($p);
				
        return $rs;
    }
		
	public function getFollow($uid,$p) {
        $rs = array();
				
        $model = new Model_Home();
        $rs = $model->getFollow($uid,$p);
				
        return $rs;
    }
		
	public function getCodeRoom($p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getCodeRoom($p);
				
        return $rs;
    }

    public function getNew($p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getNew($p);
                
        return $rs;
    }
		
	public function search($uid,$key,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->search($uid,$key,$p);
				
        return $rs;
    }	
	
	public function getNearby($lng,$lat,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getNearby($lng,$lat,$p);
				
        return $rs;
    }
	
	public function getRecommend() {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getRecommend();
				
        return $rs;
    }
	
	public function attentRecommend($uid,$touid) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->attentRecommend($uid,$touid);
				
        return $rs;
    }

    public function profitList($uid,$type,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->profitList($uid,$type,$p);
                
        return $rs;
    }

    public function consumeList($uid,$type,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->consumeList($uid,$type,$p);
                
        return $rs;
    }

    public function getClassLive($liveclassid,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getClassLive($liveclassid,$p);
                
        return $rs;
    }
	
	
	public function getShopList($p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getShopList($p);
                
        return $rs;
    }
	
	public function getShopClassList($shopclassid,$sell,$price,$isnew,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getShopClassList($shopclassid,$sell,$price,$isnew,$p);
                
        return $rs;
    }
	
	public function searchShop($key,$sell,$price,$isnew,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->searchShop($key,$sell,$price,$isnew,$p);
				
        return $rs;
    }
	

}
