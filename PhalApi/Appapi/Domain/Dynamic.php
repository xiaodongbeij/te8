<?php

class Domain_Dynamic {
	public function setDynamic($data) {
		$rs = array();

		$model = new Model_Dynamic();
		$rs = $model->setDynamic($data);

		return $rs;
	}
	
    public function setComment($data) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->setComment($data);

        return $rs;
    }

    public function addLike($uid,$dynamicid) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->addLike($uid,$dynamicid);

        return $rs;
    }

    public function addCommentLike($uid,$commentid) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->addCommentLike($uid,$commentid);

        return $rs;
    }
	public function getAttentionDynamic($uid,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getAttentionDynamic($uid,$p);

        return $rs;
    }
	
	public function getNewDynamic($uid,$lng,$lat,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getNewDynamic($uid,$lng,$lat,$p);

        return $rs;
    }
	
	public function getHomeDynamic($uid,$touid,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getHomeDynamic($uid,$touid,$p);

        return $rs;
    }
	public function getRecommendDynamics($uid,$p){
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getRecommendDynamics($uid,$p);

        return $rs;
    }
	
	
	public function getDynamic($uid,$dynamicid) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getDynamic($uid,$dynamicid);

        return $rs;
    }
	
	
	public function getComments($uid,$dynamicid,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getComments($uid,$dynamicid,$p);

        return $rs;
    }

	public function getReplys($uid,$commentid,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getReplys($uid,$commentid,$p);

        return $rs;
    }

	
	
	public function del($uid,$dynamicid) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->del($uid,$dynamicid);

        return $rs;
    }
 
 
    public function report($data) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->report($data);

        return $rs;
    }

	 public function test() {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->test();

        return $rs;
    }

    public function getReportlist() {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getReportlist();

        return $rs;
    }
	
	
    public function getDynamicLabels($p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getDynamicLabels($p);

        return $rs;
    }
	
    public function getHotDynamicLabels() {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getHotDynamicLabels();

        return $rs;
    }
	
    public function getLabelDynamic($uid,$labelid,$p) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->getLabelDynamic($uid,$labelid,$p);

        return $rs;
    }
	
	public function searchHotLabels() {
        $rs = array();
		
		$model = new Model_Dynamic();
		$list = $model->searchHotLabels();
		
		
        return $list;
    }

	public function searchLabels($key,$p) {
        $rs = array();
		
		$model = new Model_Dynamic();
		$list = $model->searchLabels($key,$p);
		
		
        return $list;
    }
	
	public function delComments($uid,$dynamicid,$commentid,$commentuid) {
        $rs = array();

        $model = new Model_Dynamic();
        $rs = $model->delComments($uid,$dynamicid,$commentid,$commentuid);

        return $rs;
    }

}
