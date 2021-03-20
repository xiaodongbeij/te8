<?php

/**
 * 音乐管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use cmf\lib\Upload;


class MusicController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '1'=>'管理员',
            '2'=>'用户',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:'';
    }
    
    protected function getDel($k=''){
        $type=array(
            '0'=>'否',
            '1'=>'是',
        );
        if($k==''){
            return $type;
        }
        return isset($type[$k])?$type[$k]:'';
    }
    
    protected function getCat($k=''){
        $list=Db::name("music_classify")
                ->order("list_order asc")
                ->column('title','id');
        
        if($k==''){
            return $list;
        }
        return isset($list[$k])?$list[$k]:'';
    }
	
    function index(){
        $data = $this->request->param();
        $map=[];
        
        $classify_id=isset($data['classify_id']) ? $data['classify_id']: '';
        if($classify_id!=''){
            $map[]=['classify_id','=',$classify_id];
        }
        
        $upload_type=isset($data['upload_type']) ? $data['upload_type']: '';
        if($upload_type!=''){
            $map[]=['upload_type','=',$upload_type];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['title','like','%'.$keyword.'%'];
        }
			

    	$lists = Db::name("music")
                ->where($map)
                ->order("id DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){
			$v['img_url']=get_upload_path($v['img_url']);
			$v['file_url']=get_upload_path($v['file_url']);
			$v['uploader_nicename']=Db::name("user")->where(["id"=>$v['uploader']])->value("user_nicename");
            return $v;           
        });
        
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('classify', $this->getCat());
        
        $this->assign('type', $this->getTypes());
        
        $this->assign('isdel', $this->getDel());
    	
    	return $this->fetch();
    }
    
    /*音乐试听*/
    function listen(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('music')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['file_url']=get_upload_path($data['file_url']);
        
        $this->assign('data', $data);
        return $this->fetch();
    }
    
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        
        $count=DB::name("video")->where(["music_id"=>$id])->count();
        if($count>0){
            $rs=DB::name("music")->where(["id"=>$id])->update(array("isdel"=>1));
        }else{
            $rs=DB::name("music")->where(["id"=>$id])->delete();
        }	
            
        if($rs===false){
            $this->error("删除失败！");
        }
		
		$action="视频管理-删除音乐ID: ".$id;
		setAdminLog($action);
        
        $this->success("删除成功！",url("music/index"));
            
	}
    
    /* 取消删除 */
    function canceldel(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('music')->where("id={$id}")->update(['isdel'=>0]);
        if(!$rs){
            $this->error("取消失败！");
        }
		
		$action="视频管理-取消删除音乐ID: ".$id;
		setAdminLog($action);
        
        $this->success("取消成功！",url("music/index"));
            
	}
    
    /*背景音乐添加*/
    function add(){
    	$this->assign('classify', $this->getCat());
        
    	return $this->fetch();
    }
    
    function addPost(){
		if ($this->request->isPost()) {
            
            $data = $this->request->param();
            
			$title=$data['title'];

			if($title==""){
				$this->error("请填写音乐名称");
			}
            
            $isexist=DB::name('music')->where(["title"=>$title])->find();
			if($isexist){
				$this->error("该音乐已经存在");
			}
            
			$author=$data['author'];
			if($author==""){
				$this->error("请填写演唱者");
			}

			$img_url=$data['img_url'];
			if($img_url==""){
				$this->error("请上传音乐封面");
			}
            
            $file=isset($_FILES['file'])?$_FILES['file']:'';
            if(!$file){
                $this->error("请上传音乐");
            }
            
            $res=$this->upload();
            if($res['ret']==0){
                $this->error($res['msg']);
            }
            
            
            $data['file_url']=$res['data']['url'];

			/* $length=$data['length'];
			if($length==""){
				$this->error("请填写音乐时长");
			}
            
            if(!strpos($length,":")){
				$this->error("请按照格式填写音乐时长");
			}
            
            $length=$data['length'];
			if($length==""){
				$this->error("请填写音乐时长");
			} */
            
            $use_nums=$data['use_nums'];
            if(!is_numeric($use_nums)){
                $this->error("被使用次数必须为数字");
            }

            if($use_nums<0){
                $this->error("被使用次数不能小于0");
            }

            if(floor($use_nums)!=$use_nums){
                $this->error("被使用次数必须为整数");
            }
            
            $data['uploader']=cmf_get_current_admin_id();
            $data['upload_type']=1;
            $data['addtime']=time();
            
			$id = DB::name('music')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
			
			
			$action="视频管理-添加音乐ID: ".$id;
			setAdminLog($action);

            $this->success("添加成功！");
            
		}
	}
    
    /*音乐编辑*/
    function edit(){        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('music')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        
        $this->assign('classify', $this->getCat());
        
        return $this->fetch();
        
    }


    function editPost(){
        
        if ($this->request->isPost()) {
            
            $data = $this->request->param();
            
			$id=$data['id'];
			$title=$data['title'];

			if($title==""){
				$this->error("请填写音乐名称");
			}
            
            $isexist=DB::name('music')->where([['title','=',$title],['id','<>',$id]])->find();
			if($isexist){
				$this->error("该音乐已经存在");
			}
            
			$author=$data['author'];
			if($author==""){
				$this->error("请填写演唱者");
			}

			$img_url=$data['img_url'];
			if($img_url==""){
				$this->error("请上传音乐封面");
			}
            
            $file=isset($_FILES['file'])?$_FILES['file']:'';
            if($file){
                $res=$this->upload();
                if($res['ret']==0){
                    $this->error($res['msg']);
                }
                
                
                $data['file_url']=$res['data']['url'];
            }
            
            

			/* $length=$data['length'];
			if($length==""){
				$this->error("请填写音乐时长");
			}
            
            if(!strpos($length,":")){
				$this->error("请按照格式填写音乐时长");
			}
            
            $length=$data['length'];
			if($length==""){
				$this->error("请填写音乐时长");
			} */

            $use_nums=$data['use_nums'];
            if(!is_numeric($use_nums)){
                $this->error("被使用次数必须为数字");
            }

            if($use_nums<0){
                $this->error("被使用次数不能小于0");
            }

            if(floor($use_nums)!=$use_nums){
                $this->error("被使用次数必须为整数");
            }
            
            $data['updatetime']=time();
            unset($data['file']);
            
			$rs = DB::name('music')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
			
			
			$action="视频管理-修改音乐ID: ".$id;
			setAdminLog($action);

            $this->success("修改成功！");
            
		}

    }
    
    protected function upload()
	{
        
		$uploader = new Upload();
        $uploader->setFileType('audio');
        $result = $uploader->upload();

        if ($result === false) {
            return array("ret"=>0,'file'=>'','msg'=>$uploader->getError());
        }
        
        /* $result=[
            'filepath'    => $arrInfo["file_path"],
            "name"        => $arrInfo["filename"],
            'id'          => $strId,
            'preview_url' => cmf_get_root() . '/upload/' . $arrInfo["file_path"],
            'url'         => cmf_get_root() . '/upload/' . $arrInfo["file_path"],
        ]; */
        
        return array("ret"=>200,'data'=>array("url"=>$result['filepath']),'msg'=>'');

	}
}
