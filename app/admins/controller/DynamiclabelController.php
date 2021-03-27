<?php

/**
 * 动态话题标签
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;


class DynamiclabelController extends AdminbaseController {

	//列表
    public function index(){

		$lists = Db::name('dynamic_label')
            ->where(function (Query $query) {
                $data = $this->request->param();
                $keyword=isset($data['keyword']) ? $data['keyword']: '';
                if (!empty($keyword)) {
                    $query->where('name', 'like', "%$keyword%");
                }
            })
            ->order("orderno asc, isrecommend desc, use_nums desc")
            ->paginate(20);

	

        // 获取分页显示
        $page = $lists->render();
		
    	$this->assign('lists', $lists);
    	$this->assign("page", $page);

    	return $this->fetch();
    }
	
	//删除
    public function del(){
        $id=$this->request->param('id');
        if($id){
            $result=Db::name('dynamic_label')->delete($id);				
                if($result){
					
					$action="删除动态话题标签ID：{$id}";
					setAdminLog($action);
					
					
                    $this->success('删除成功');
                 }else{
                    $this->error('删除失败');
                 }			
        }else{				
            $this->error('数据传入失败！');
        }								  
        return $this->fetch();				
    }	
    //排序
    public function listsorders() { 

		$ids=$this->request->param('listsorders');
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            Db::name('dynamic_label')->where(array('id' => $key))->update($data);
        }
				
        $status = true;
        if ($status) {

			$action="更新动态话题标签排序";
			setAdminLog($action);
			
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    
	
	//添加
    public function add(){ 
        return $this->fetch();				
    }
   
    public function add_post(){
        if($this->request->isPost()) {
			$data=$this->request->param();
			
            $name=$data['name'];
            if($name==''){
                $this->error('请填写名称');
            }
             
           
            $isexist=Db::name('dynamic_label')
				->where("name='{$name}'")
				->find();
            if($isexist){
                $this->error('已存在相同名称');
            }
            
            if($data['thumb']==''){
                $this->error('请上传封面');
            }
             
        

            $result=Db::name('dynamic_label')->insertGetId($data); 
			
            if($result){
				
				$action="添加动态话题标签ID: ".$result;
				setAdminLog($action);
				
				
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }			
    }		
	
	
	//编辑
    public function edit(){
        $id=$this->request->param('id');
        if($id){
            $data=Db::name('dynamic_label')->find($id);
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        return $this->fetch();				
    }
    
    public function edit_post(){
		
		 if($this->request->isPost()) {
			$data=$this->request->param();	
            $name=$data['name'];
            if($name==''){
                $this->error('请填写名称');
            }
            
            $isexist=Db::name('dynamic_label')
				->where("name='{$name}' and id!={$data['id']}")
				->find();
            if($isexist){
                $this->error('已存在相同名称');
            }
            
            if($data['thumb']==''){
                $this->error('请上传封面');
            }
             
            $result=Db::name('dynamic_label')->update($data); 
            if($result){
				$key='LabelInfo_'.$data['id'];
				delcache($key);
				
				
				$action="编辑动态话题标签ID: ".$data['id'];
				setAdminLog($action);
				
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }			
			
    }
	
	/*是否推荐*/
    public function setrecommend(){

        $data=$this->request->param();	
		$id=$data['id'];
		$isrecommend=$data['isrecommend'];
        if ($id) {
            Db::name("dynamic_label")->where(["id" => $id])->update(['isrecommend'=>$isrecommend]);
			
			
			$isrecommend_name=$isrecommend==1?'推荐':'取消推荐';
			$action=$isrecommend_name."动态话题标签ID: ".$id;
			setAdminLog($action);


            $this->success("操作成功！");
        } else {
            $this->error('数据传入失败！');
        
		}   
	}
}
