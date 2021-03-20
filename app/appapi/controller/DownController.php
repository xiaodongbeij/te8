<?php
/**
 * 下载页面
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class DownController extends HomebaseController {

	function index(){       
		return $this->fetch();
	}

}