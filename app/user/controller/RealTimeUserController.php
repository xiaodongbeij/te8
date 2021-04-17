<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;
use app\game\model\GameCate;
use app\user\model\User;
use app\user\model\UserRate;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;


class RealTimeUserController extends AdminBaseController{

    public function index(){

        $content = hook_one('user_admin_index_view');
        
        if (!empty($content)) {
            return $content;
        }
        $redis = $GLOBALS['redisdb'];
        $data = input();
        
        $map=[];
        $map[]=['user_type','=',2];
        
        if(!empty($data['uid']))
        {
            $id = getcache('onlineo:' . $data['uid']);
    
            if($id)
            {
                $map[] = ['id', '=', $id];
            }
            
        }else{
            
            $key = $redis->keys('onlineo:*');
           
            $ids = [];
            
            foreach ($key as $v)
            {
                $id = explode(':', $v);
                if(!empty($id[1]))
                {
                    $ids[] = $id[1];
                }
            }
    
            $map[]=['id','in',$ids];
        }
        

        $list = Db::name("user")
            ->where($map)
            ->where('iszombiep',0)
			->order("last_login_time desc")
			->paginate(20);
			
        $num = $list->total();
        $list->each(function($v,$k){
            $v['avatar']=get_upload_path($v['avatar']);
            $v['last_login_time']= date('Y-m-d H:i:s', $v['last_login_time']);
            return $v;           
        });
        
        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('num', $num);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
    
}
