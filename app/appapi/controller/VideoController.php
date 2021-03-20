<?php
/**
 * 短视频
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class VideoController extends HomebaseController {

	function index(){       
		$data = $this->request->param();
        $videoid=isset($data['videoid']) ? $data['videoid']: '';
        $videoid=(int)checkNull($videoid);

		if( !$videoid ){
			$this->assign("reason",'信息错误');
			return $this->fetch(':error');
		}

		
		$videoinfo=Db::name('video')->where(["id"=>$videoid])->find();
		
		if(!$videoinfo){
			$this->assign("reason",'视频不存在');
			return $this->fetch(':error');
		}
		
		$liveinfo=getUserInfo($videoinfo['uid']);
		
		$hls=get_upload_path($videoinfo['href']);
		
		$this->assign("hls",$hls);
		$this->assign("videoinfo",$videoinfo);
		$this->assign("liveinfo",$liveinfo);

		return $this->fetch();
	}

}