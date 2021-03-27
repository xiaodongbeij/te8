<?php

/* 动态管理 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class dynamicController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=[
            "0"=>"待审核",
            "1"=>"审核通过", 
            "-1"=>"审核拒绝", 
        ];
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
    
    protected function getTypes($k=''){
        $type=[
            '0'=>'纯文字',
            '1'=>'文字+图片',
            '2'=>'文字+视频',
            '3'=>'文字+语音',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k])? $type[$k] : '' ;
    }
    
    protected function getOrdertype($k=''){
        $type=array(
            'comments DESC'=>'评论数排序',
            'likes DESC'=>'点赞数排序',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        $status=isset($data['status'])? $data['status']:'';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
        $isdel=isset($data['isdel'])? $data['isdel']:'';
        if($isdel!=''){
            $map[]=['isdel','=',$isdel];
        }
        
        $type=isset($data['type'])? $data['type']:'';
        if($type!=''){
            $map[]=['type','=',$type];
        }
        
        $start_time=isset($data['start_time'])? $data['start_time']:'';
        $end_time=isset($data['end_time'])? $data['end_time']:'';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $lianguid=getLianguser($uid);
            if($lianguid){
                $map[]=['uid',['=',$uid],['in',$lianguid],'or'];
            }else{
                $map[]=['uid','=',$uid];
            }
        }
        
        $ordertype=isset($data['ordertype']) ? $data['ordertype']: 'id DESC';
        
        
        $list = Db::name('dynamic')
            ->where($map)
            ->order($ordertype)
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           if($v['thumb']){
               $thumbs=preg_split('/;/',$v['thumb']);
               $thumb=[];
               foreach($thumbs as $k1=>$v1){
                   $thumb[]=get_upload_path($v1);
               }
               $v['thumb']=$thumb;
           }
           
           $v['video_thumb']=get_upload_path($v['video_thumb']);
           $v['href']=get_upload_path($v['href']);
           $v['voice']=get_upload_path($v['voice']);

           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        
        $this->assign('status', $this->getStatus());
        $this->assign('type', $this->getTypes());
        $this->assign("ordertype", $this->getOrdertype());
        
        $this->assign('status2', $status);
        $this->assign('isdel2', $isdel);

        return $this->fetch('index');
    }

    public function wait(){
        return $this->index();
    }
    public function nopass(){
        return $this->index();
    }
    
    public function lower(){
        return $this->index();
    }
    
    public function see()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('dynamic')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        if($data['thumb']){
           $thumbs=preg_split('/;/',$data['thumb']);
           $thumb=[];
           foreach($thumbs as $k1=>$v1){
               $thumb[]=get_upload_path($v1);
           }
           $data['thumb']=$thumb;
       }
           
        $data['href']=get_upload_path($data['href']);
        $data['voice']=get_upload_path($data['voice']);
           
        $this->assign('data', $data);
        return $this->fetch();
    }
    
    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $nowtime=time();
        
        $rs=DB::name("dynamic")->where("id={$id}")->update(['status'=>$status]);
        if(!$rs){
            $this->error("操作失败");
        }
		
		
		$status_name=$status==1?'通过':'拒绝';
		$action='动态ID: '.$id.' 审核'.$status_name;
		
		setAdminLog($action);
        
        $this->success("操作成功");        
    }

    public function setrecom()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $recoms = $this->request->param('recoms', 0, 'intval');
        
        $nowtime=time();
        
        $rs=DB::name("dynamic")->where("id={$id}")->update(['recommend_val'=>$recoms]);
        if($rs===false){
            $this->error("操作失败");
        }
		
		
		$action='动态ID: '.$id.' 设置推荐值: '.$recoms;
		setAdminLog($action);
        
        $this->success("操作成功");        
    }
    
    /* 上下架 */
    public function setDel(){
        $id = $this->request->param('id', 0, 'intval');
        $isdel = $this->request->param('isdel', 0, 'intval');
        $reason = $this->request->param('reason');
        if($reason==''){
			$reason='';
		}
        if($id){
            //判断用户是否注销
            $uid=DB::name("dynamic")->where(['id'=>$id])->value("uid");
            if($uid){
                $is_destroy=checkIsDestroy($uid);
                if($is_destroy&&$isdel==0){
                    $this->error("该用户已注销,动态不可上架");
                }
            }
            $result=DB::name("dynamic")->where(['id'=>$id])->update(['isdel'=>$isdel,'xiajia_reason'=>$reason]);
            if($result){
                if($isdel==1){
                    //将视频喜欢列表的状态更改
                    DB::name("dynamic_like")->where("dynamicid={$id}")->setField('status',0);
                    //更新此视频的举报信息
                    $data1=array(
                        'status'=>1,
                        'uptime'=>time()
                    );
                    DB::name("dynamic_report")->where("dynamicid={$id}")->update($data1);
                }
                if($isdel==0){
                    //将视频喜欢列表的状态更改
                    DB::name("dynamic_like")->where("dynamicid={$id}")->setField('status',1);
                }           
            }
			 
		
			$isdel_name=$isdel?'下架':'上架';
			$action='动态'.$isdel_name.'ID: '.$id;
			if($reason!=''){
				$action.=' 原因: '.$reason;
			}
            setAdminLog($action);
			 
			 
			 
			 $this->success('操作成功');
			 
        }else{				
            $this->error('数据传入失败！');
        }
    	
    }
    public function del()
    {
        $data = $this->request->param();
        
        if (isset($data['id'])) {
            $id = $data['id']; //获取删除id

			$info=Db::name('dynamic')
				->where("id={$id}")
				->find();

            $rs = DB::name('dynamic')->where("id={$id}")->delete();
            if(!$rs){
                $this->error("删除失败！");
            }
            
            DB::name("dynamic_comments")->where("dynamicid={$id}")->delete();
            DB::name("dynamic_comments_like")->where("dynamicid={$id}")->delete();
            DB::name("dynamic_like")->where("dynamicid={$id}")->delete();
            DB::name("dynamic_report")->where("dynamicid={$id}")->delete();
			
			
			
			if($info['isdel']==1){
				$action='删除下架动态ID: '.$id;
			}else{
				$action='删除'.$this->getStatus($info['status']).'动态ID: '.$id;
			}

			setAdminLog($action);
        } elseif (isset($data['ids'])) {
            $ids = $data['ids'];
            
            $rs = DB::name('dynamic')->where('id', 'in', $ids)->delete();
            if(!$rs){
                $this->error("删除失败！");
            }
            
            DB::name("dynamic_comments")->where('dynamicid', 'in', $ids)->delete();
            DB::name("dynamic_comments_like")->where('dynamicid', 'in', $ids)->delete();
            DB::name("dynamic_like")->where('dynamicid', 'in', $ids)->delete();
            DB::name("dynamic_report")->where('dynamicid', 'in', $ids)->delete();
			
			$action='删除动态IDS: '.$ids;
			setAdminLog($action);

        }
        
        $this->success("删除成功！");
    }

}