<?php
/**
 * 我的明细
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class CdnController extends HomebaseController {

	function index(){       
		
		$url = [
		    'code' => 0,
		    'url' => [
		        'cdn.edison-elvis.com',
		        'cdn.hanrunkeji.com',
		        'cdn.lexiangtc.com',
		        'cdn.hlledsolution.com',
		      //  'cdn.qingxingu916.com'
	        ]
	    ];
	    return json($url);
	}
	

}