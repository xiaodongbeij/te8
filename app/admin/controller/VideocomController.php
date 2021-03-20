<?php

/**
 * 短视频--评论
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class VideocomController extends AdminbaseController {


    public function index(){
    	
        $data = $this->request->param();
        $map=[];
		
        $videoid=isset($data['videoid']) ? $data['videoid']: '';
        if($videoid!=''){
            $map[]=['videoid','=',$videoid];
        }
        
        $lists = DB::name("video_comments")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['userinfo']=getUserInfo($v['uid']);
            return $v;
        });
        
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);
    	$this->assign("page", $page);
    	
    	return $this->fetch();
    }
    
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        if($id){
            $result=DB::name("video_comments")->delete($id);				
            if($result){
                $this->success('删除成功');
             }else{
                $this->error('删除失败');
             }
        }else{				
            $this->error('数据传入失败！');
        }				
    }	
}
