<?php

/**
 * 直播间系统消息
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SystemController extends AdminbaseController {
    function index(){

		$config=getConfigPri();
			
		$this->assign('config', $config);
			
    	
    	return $this->fetch('edit');
    }
    function send(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$content=$data['content'];

			if($content==''){
				$this->error("内容不能为空！");
			}
            
            $action="发送系统消息：{$content}";
            setAdminLog($action);
            
            $this->success("发送成功！");
		}
	}		
}
