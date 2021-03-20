<?php

/**
 * 商品分类列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GoodsclassController extends AdminbaseController {
    
    
    
    function index(){
        
        $lists = Db::name("shop_goods_class")
            ->where("gc_parentid=0")
			->order("gc_sort,gc_id")
			->paginate(5);
        
        $lists->each(function($v,$k){
			
            //获取二级分类
            $two_list=Db::name("shop_goods_class")->field("gc_id,gc_name,gc_isshow,gc_sort,gc_icon,gc_addtime,gc_edittime")->where("gc_parentid={$v['gc_id']}")->order("gc_sort")->select()->toArray();
			
            if($two_list){
                foreach ($two_list as $k1 => $v1) {
                    //查询三级分类
                    $three_list=Db::name("shop_goods_class")->field("gc_id,gc_name,gc_isshow,gc_sort,gc_icon,gc_addtime,gc_edittime")->where("gc_parentid={$v1['gc_id']}")->order("gc_sort")->select()->toArray();

                    if(!$three_list){
                        $three_list=[];
                    }
					

                    $two_list[$k1]['gc_icon']=get_upload_path($v1['gc_icon']);
                    $two_list[$k1]['three_list']=$three_list;
                }
            }
            

            $v['two_list']=$two_list;

            return $v;           
        });


        /*var_dump($lists);
        die;*/

        $page = $lists->render();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page);   
    	return $this->fetch();
    }


    /*
     * 商品分类添加
     *@return lists 商品分类列表
     *@return lists['two_list'] 商品二级分类列表
    */
    function add(){

        //获取后台要用的缓存列表
        $lists=getcaches("adminGoodsClass");
        $one_nums=getcaches("adminGC_oneNums");

		$lists=false;
        if(!$lists){
            $info=$this->getAdminClassList();
            $lists=$info['lists'];
            $one_nums=$info['one_nums'];

            $this->resetAdminCache($lists,$one_nums);
        }
		
	

        $this->assign('lists',$lists);
        $this->assign('one_nums',$one_nums);

        return $this->fetch();
    }

    /*
     * 商品分类添加保存
     *@request gc_parentid 上一级商品分类，作为一级是为0
     *@request gc_name 商品分类名称，30字符以内
    */
    function addPost(){
        $data=$this->request->param();

        $gc_name=$data['gc_name'];
        $gc_parentid=$data['gc_parentid'];
        $gc_sort=$data['gc_sort'];

        if(!$gc_name){
            $this->error("请填写分类名称");
        }

        if(mb_strlen($gc_name)>30){
            $this->error("分类名称在30字符以内");
        }

        //判断分类名称是否重复
        $isexist=Db::name("shop_goods_class")->where("gc_name='{$gc_name}'")->find();
        if($isexist){
            $this->error("分类名称已经存在");
        }

        if($gc_sort==''){
            $this->error("请填写分类排序号");
        }

        if($gc_sort<0){
            $this->error("分类排序号必须是整数");
        }

        if($gc_sort>999999){
            $this->error("分类排序号应是0-999999之间的整数");
        }

        if(floor($gc_sort)!=$gc_sort){
            $this->error("分类排序号应是0-999999之间的整数");
        }

        $data['gc_one_id']=0;
        $data['gc_grade']=1;

        //判断用户选择的上级分类是否是一级

        if($gc_parentid){

            //获取该分类的一级分类
            $two_info=Db::name("shop_goods_class")->where("gc_id={$gc_parentid}")->find();

            if(!$two_info['gc_parentid']){ //添加的为二级分类
			
				//判断有没有上传图标
				$gc_icon=$data['gc_icon'];
				if(empty($gc_icon)){
					$this->error("请填写上传分类图片");
				}
                $data['gc_one_id']=$two_info['gc_id'];
                $data['gc_grade']=2;
            }else{
                $data['gc_one_id']=$two_info['gc_one_id']; 
                $data['gc_grade']=3;
            }
            
        }

        $data['gc_addtime']=time();

        $result=Db::name("shop_goods_class")->insert($data);
        if(!$result){
             $this->error("分类添加失败");
        }

        $action="添加商品分类：{$result}";
        setAdminLog($action);

        $this->resetAdminCache();
        $this->resetCache();
        $this->resetOneCache();
		$this->resetTwoCache();

        $this->success("分类添加成功");
    }



    /*
     * 商品分类编辑
     *@request  上一级商品分类，作为一级是为0
    */
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');

        //获取后台要用的缓存列表
        $lists=getcaches("adminGoodsClass");
        $one_nums=getcaches("adminGC_oneNums");


        if(!$lists){
            $info=$this->getAdminClassList();
            $lists=$info['lists'];
            $one_nums=$info['one_nums'];

            $this->resetAdminCache($lists,$one_nums);
        }


        //获取该分类的信息
        $info=Db::name("shop_goods_class")->where("gc_id={$id}")->find();

        $gc_grade=$info['gc_grade'];


        if($gc_grade==1){
            foreach ($lists as $k => $v) {
                if($v['gc_id']==$id){
                    unset($lists[$k]);
                    break;
                }
            }
        }

        if($gc_grade==2){

            $gc_parentid=$info['gc_parentid'];


            foreach ($lists as $k => $v) {
                if($v['gc_id']==$gc_parentid){
                    foreach ($v['two_list'] as $k1 => $v1) {
                        if($v1['gc_id']==$id){
                            unset($lists[$k]['two_list'][$k1]);
                            break;
                        }
                    }
                }
            }

        }

        $this->assign("lists",$lists);
        $this->assign("one_nums",$one_nums);
        $this->assign("info",$info);

        return $this->fetch();

    }


    /*
     * 商品分类编辑保存
     *@request gc_id 分类id
     *@request gc_parentid 上级分类id
     *@request gc_name 分类名称
     *@request gc_isshow 分类是否显示
     *@request gc_sort 分类排序号
    */
    function editPost(){

        $data = $this->request->param();
        $gc_id=$data['gc_id'];
        $gc_parentid=$data['gc_parentid'];
        $gc_isshow=$data['gc_isshow'];
        $gc_sort=$data['gc_sort'];
        

        //根据id获取信息
        $info=Db::name("shop_goods_class")->where("gc_id={$gc_id}")->find();
        $gc_grade=$info['gc_grade'];

        //选择上级作为一级分类
        if($gc_parentid==0){

            if($gc_grade==2){ //当前分类是二级分类

                //将该分类下的三级分类的一级分类id修改为当前分类id
                Db::name("shop_goods_class")->where("gc_parentid={$gc_id}")->update(array("gc_one_id"=>$gc_id,"gc_grade"=>2,"gc_edittime"=>time()));

            }

            $data['gc_one_id']=0;
            $data['gc_grade']=1;

        }else{

           //判断选择的上级分类信息
            $parent_info=Db::name("shop_goods_class")->where("gc_id={$gc_parentid}")->find();
            $parent_grade=$parent_info['gc_grade']; 

            //判断选择的上级
            switch ($parent_grade) {

                case 1: //上级为一级分类【当前分类设置为二级分类】

                    if($gc_grade==1){ //当前分类为一级
                        //判断该分类下是否有三级
                        $three_info=Db::name("shop_goods_class")->where("gc_grade=3 and gc_one_id={$gc_id}")->find();
                        if($three_info){
                           $this->error("该分类的下级分类超过两级,不可重新指定上级分类"); 
                        }

                        //将该分类下的二级分类更改为三级分类信息
                        Db::name("shop_goods_class")->where("gc_parentid={$gc_id}")->update(array("gc_grade"=>3,"gc_one_id"=>$gc_parentid,"gc_edittime"=>time()));



                    }else if($gc_grade==2){ //当前分类为二级
                        //将该分类下的三级分类信息变更信息
                        Db::name("shop_goods_class")->where("gc_parentid={$gc_id}")->update(array("gc_one_id"=>$gc_parentid,"gc_edittime"=>time()));
                    }

                   

                    
                    $data['gc_grade']=2;
                    $data['gc_one_id']=$gc_parentid;

                    break;

                case 2://上级为二级分类【当前分类设置为三级分类】

                    if($gc_grade==1 || $gc_grade==2){ //当前分类为一级或二级

                        //判断当前分类下是否有分类
                        $info=Db::name("shop_goods_class")->where("gc_parentid={$gc_id}")->find();
                        if($info){
                            $this->error("该分类下有分类,不可重新指定上级分类"); 
                        }

                    }

                    $data['gc_grade']=3;
                    $data['gc_one_id']=$parent_info['gc_one_id'];

                    break;
                
                
            }

        }
		
		if($data['gc_grade']==2){
			//当是二级分类时,判断有没有上传图标
			$gc_icon=$data['gc_icon'];
			if(empty($gc_icon)){
				$this->error("请填写上传分类图片");
			}
                
	
		}

        if($gc_sort==''){
            $this->error("请填写分类排序号");
        }

        if($gc_sort<0){
            $this->error("分类排序号必须是整数");
        }

        if($gc_sort>999999){
            $this->error("分类排序号应是0-999999之间的整数");
        }

        if(floor($gc_sort)!=$gc_sort){
            $this->error("分类排序号应是0-999999之间的整数");
        }

        
        //更新当前分类信息
        $data['gc_edittime']=time();


        $result=Db::name("shop_goods_class")->update($data);
        
        if(!$result){
            $this->error("更新失败"); 
        }

        //将用户经营类目状态更改为不显示

        $status_data=array("status"=>0);

        if($gc_isshow){
            $status_data=array("status"=>1);
        }

        Db::name("seller_goods_class")->where("goods_classid={$gc_id}")->update($status_data);

        $this->resetAdminCache();
        $this->resetCache();
        $this->resetOneCache();
		$this->resetTwoCache();
		
		
		$action="编辑商品分类：{$gc_id}";
        setAdminLog($action);

        $this->success("商品分类更新成功"); 
    }
    
    /*
     * 商品分类排序
     *@request list_orders 排序号
    */
    function listOrder(){
        $ids = $this->request->post("list_orders/a");

        if(!empty($ids)){
            foreach ($ids as $k => $v) {
                $data['gc_sort'] = $v;
                Db::name("shop_goods_class")->where(array("gc_id"=>$k))->update($data);
            }
  
        }

        $action="更新商品分类排序";
        setAdminLog($action);

        $this->resetAdminCache();
        $this->resetCache();
        $this->resetOneCache();
		$this->resetTwoCache();

        $this->success("排序更新成功！");
    }

    /*
     * 商品分类删除
     *@request id 商品分类id
    */
    
    function del(){
        $id = $this->request->param('id', 0, 'intval');

        //判断是否有下级分类
        $info=Db::name("shop_goods_class")->where("gc_parentid={$id}")->find();
        if($info){
            $this->error("请先删除下级分类或将下级分类移至其他分类下再删除");
        }
        
        $rs = DB::name('shop_goods_class')->where("gc_id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }

        //删除用户经营类目
        Db::name("seller_goods_class")->where("goods_classid={$id}")->delete();
        
        $action="删除商品分类：{$id}";
        setAdminLog($action);

        $this->resetAdminCache();
        $this->resetCache();
        $this->resetOneCache();
        $this->resetTwoCache();
                    
        $this->success("删除成功！");
        							  			
    }

    /*
     * 商品分类获取
    */
   protected function getAdminClassList(){

        //获取一级分类
        $lists=Db::name("shop_goods_class")->field("gc_id,gc_name,gc_grade")->where("gc_parentid=0")->order("gc_sort")->select()->toArray();

        $one_nums=0;

        foreach ($lists as $k => $v) {
            //获取二级分类

            $two_list=Db::name("shop_goods_class")->field("gc_id,gc_name,gc_grade")->where("gc_parentid={$v['gc_id']}")->order("gc_sort")->select()->toArray();

            if(!$two_list){
                $two_list=[];
               
            }
            $lists[$k]['two_list']=$two_list;

            $one_nums++;
        }


        return array(
            "lists"=>$lists,
            "one_nums"=>$one_nums
        );

    }

    /*
     * 将后台的商品分类缓存列表存入redis
    */
    protected function resetAdminCache($lists=array(),$one_nums=''){
        
        if((!$lists) || (!$one_nums)){

            $data=$this->getAdminClassList();
            $lists=$data['lists'];
            $one_nums=$data['one_nums'];

        }
        setcaches("adminGoodsClass",$lists);
        setcaches("adminGC_oneNums",$one_nums);
    }

    /*
     * 将商品分类缓存列表存入redis【只查询显示的分类】
    */
    protected function resetCache(){

        //获取一级分类
        $lists=Db::name("shop_goods_class")->field("gc_id,gc_name")->where("gc_parentid=0 and gc_isshow=1")->order("gc_sort")->select()->toArray();

        foreach ($lists as $k => $v) {
            //获取二级分类

            $two_list=Db::name("shop_goods_class")->field("gc_id,gc_name")->where("gc_parentid={$v['gc_id']} and gc_isshow=1")->order("gc_sort")->select()->toArray();

            if($two_list){
                foreach ($two_list as $k1 => $v1) {
                    //查询三级分类
                    $three_list=Db::name("shop_goods_class")->field("gc_id,gc_name")->where("gc_parentid={$v1['gc_id']} and gc_isshow=1")->order("gc_sort")->select()->toArray();

                    if(!$three_list){
                        $three_list=[];
                    }

                    $two_list[$k1]['three_list']=$three_list;
                }
            }
            
            $lists[$k]['two_list']=$two_list;

        }

        setcaches("goodsClass",$lists);

    }

    /*
     * 将商品一级分类缓存列表存入redis【只查询有三级分类 且状态为显示的分类】
    */
    public function resetOneCache(){

        $list1=Db::name("shop_goods_class")->field("gc_one_id")->where("gc_grade=3")->select()->toArray();

        if(!$list1){
            return !1;
        }

        $gc_one_ids=array_column($list1,"gc_one_id");

        $ids=array_unique($gc_one_ids);

        $map['gc_isshow']=1;

        $list=Db::name("shop_goods_class")->field("gc_id,gc_name,gc_isshow")->where($map)->where('gc_id','in',$ids)->order("gc_sort")->select()->toArray();

        setcaches("oneGoodsClass",$list);
    }
	
	
	//商品二级分类缓存列表存入redis--用于app商城模块展示
	public function resetTwoCache(){
		
		$map['gc_isshow']=1;
		$map['gc_grade']=2;
		$list=Db::name("shop_goods_class")
			->field("gc_id,gc_name,gc_icon")
			->where($map)
			->order("gc_sort")
			->select()
			->toArray();
		foreach($list as $k=>$v){
			$key2="threeGoodsClass_".$v['gc_id'];
			$list2=Db::name("shop_goods_class")
				->field("gc_id,gc_name")
				->where("gc_isshow=1 and gc_grade=3 and gc_parentid={$v['gc_id']}")
				->order("gc_sort")
				->select()
				->toArray();
			if($list2){
                setcaches($key2,$list2);
            }
		}

        setcaches("twoGoodsClass",$list);
		
	}
    
}
