<?php

/**
 * 推送管理
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;


class PushController extends AdminbaseController {

    function index(){
        $data = $this->request->param();
        $map=[];
        $map[]=['type','=','0'];
		
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['touid|adminid','like',"%".$keyword."%"];
        }
        
    	$lists = DB::name("pushrecord")
            ->where($map)
            ->order('id desc')
            ->paginate(20);
        
        $lists->each(function($v,$k){
            $v['ip']=long2ip($v['ip']);
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
            $result=DB::name("pushrecord")->delete($id);				
            if($result){
                $action="删除推送信息：{$id}";
                setAdminLog($action);
                
                $this->success('删除成功');
             }else{
                $this->error('删除失败');
             }
        }else{				
            $this->error('数据传入失败！');
        }				
    }	
    
	function add(){
        return $this->fetch();			
	}	
	function addPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$content=$data['content'];
			$touid=$data['touid'];
            
            $content=str_replace("\r","", $content);
            $content=str_replace("\n","", $content);
            
            $touid=str_replace("\r","", $touid);
            $touid=str_replace("\n","", $touid);
            $touid=preg_replace("/,|，/",",", $touid);
            
            if($content==''){
                $this->error('推送内容不能为空');
            }
            
            
            /* 极光推送 */
            $configpri=getConfigPri();
            $app_key = $configpri['jpush_key'];
            $master_secret = $configpri['jpush_secret'];
            
            if(!$app_key || !$master_secret){
                $this->error('请先设置推送配置'); 
            }
            $issuccess=0;
            $error='推送失败';
            if($app_key && $master_secret ){
                 
                require_once CMF_ROOT.'sdk/JPush/autoload.php';

                // 初始化
                $client = new \JPush\Client($app_key, $master_secret,null);
				//file_put_contents(CMF_ROOT.'data/jpush.txt',date('y-m-d h:i:s').'提交参数信息 设备名client2:'.json_encode($client)."\r\n",FILE_APPEND);
				//file_put_contents(CMF_ROOT.'data/jpush.txt',date('y-m-d h:i:s').'提交参数信息 设备名client:'.$client."\r\n",FILE_APPEND);
                $anthorinfo=array();
                
                $map=array();

                if($touid!=''){
                    $uids=preg_split('/,|，/',$touid);
                    $map[]  =['uid','in',$uids];
                }

                $pushids=DB::name("user_pushid")
					->field("pushid")
					->where($map)
					->select()
                    ->toArray();
                    
                $pushids=array_column($pushids,'pushid');
                $pushids=array_filter($pushids);

                $nums=count($pushids);
                
                $apns_production=false;
                if($configpri['jpush_sandbox']){
                    $apns_production=true;
                }
                $title=$content;
                for($i=0;$i<$nums;){
                    $alias=array_slice($pushids,$i,900);
                    $i+=900;
                    try{
                        $result = $client->push()
                                ->setPlatform('all')
                                ->addRegistrationId($alias)
                                ->setNotificationAlert($title)
                                ->iosNotification($title, array(
                                    'sound' => 'sound.caf',
                                    'category' => 'jiguang',
                                    'extras' => array(
                                        'type' => '2',
                                        'userinfo' => $anthorinfo
                                    ),
                                ))
                                ->androidNotification('', array(
                                    'extras' => array(
                                        'type' => '2',
                                        'title' => $title,
                                        'userinfo' => $anthorinfo
                                    ),
                                ))
                                ->options(array(
                                    'sendno' => 100,
                                    'time_to_live' => 0,
                                    'apns_production' =>  $apns_production,
                                ))
                                ->send();
                        if($result['code']==0){
                            $issuccess=1;
                        }else{
                            $error=$result['msg'];
                        }
                    } catch (Exception $e) {   
                        file_put_contents(CMF_ROOT.'data/jpush.txt',date('y-m-d h:i:s').'提交参数信息 设备名:'.json_encode($alias)."\r\n",FILE_APPEND);
                        file_put_contents(CMF_ROOT.'data/jpush.txt',date('y-m-d h:i:s').'提交参数信息:'.$e."\r\n",FILE_APPEND);
                    }					
                }			
            }
            /* 极光推送 */
            
            //写入记录
            $id=addSysytemInfo($touid,$content,0);
            if(!$id){
                $this->error("推送失败！");
            }
            
            $action="推送信息ID：{$id}";
            setAdminLog($action);
            
            $this->success("推送成功！");
		}
        
	}		
    
    function export(){
        
        $data = $this->request->param();
        $map=[];
        $map[]=['type','=','0'];
		
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['touid|adminid','like',"%".$keyword."%"];
        }
        
    	$xlsData = DB::name("pushrecord")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();
            
        foreach ($xlsData as $k => $v)
        {
            if(!$v['touid']){
                $xlsData[$k]['touid']='所有会员';
                
            }  
			$xlsData[$k]['ip']=long2ip($v['ip']);
			$xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);
        }
        
        $action="导出推送信息：".DB::name("pushrecord")->getLastSql();
        setAdminLog($action);
        $xlsName='推送记录';
        $cellName = array('A','B','C','D','E','F');
        $xlsCell  = array(
            array('id','序号'),
            array('admin','管理员'),
            array('ip','IP'),
            array('touid','推送对象'),
            array('content','推送内容'),
            array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    
}
