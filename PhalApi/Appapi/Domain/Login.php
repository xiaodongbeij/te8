<?php

class Domain_Login {

    public function anchorLogin($user_login,$user_pass) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->anchorLogin($user_login,$user_pass);

        return $rs;
    }

    public function userLogin($user_login,$user_pass,$is_pass = true) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLogin($user_login,$user_pass,$is_pass);

        return $rs;
    }

    public function anchorReg($user_login,$user_pass,$source,$invite) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->anchorReg($user_login,$user_pass,$source,$invite);

        return $rs;
    }

    public function userReg($user_login,$user_pass,$source,$invite) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userReg($user_login,$user_pass,$source,$invite);

        return $rs;
    }	
	
    public function userFindPass($user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userFindPass($user_login,$user_pass);

        return $rs;
    }	

    public function userLoginByThird($openid,$type,$nickname,$avatar,$source) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLoginByThird($openid,$type,$nickname,$avatar,$source);

        return $rs;
    }

    public function upUserPush($uid,$pushid) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->upUserPush($uid,$pushid);

        return $rs;
    }			
	
	public function getUserban($user_login) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getUserban($user_login);

        return $rs;
    }
	public function getThirdUserban($openid,$type) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getThirdUserban($openid,$type);

        return $rs;
    }

    public function getCancelCondition($uid){
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getCancelCondition($uid);

        return $rs;
    }

    public function cancelAccount($uid){
        $rs = array();

        $model = new Model_Login();
        $rs = $model->cancelAccount($uid);

        return $rs;
    }

}
