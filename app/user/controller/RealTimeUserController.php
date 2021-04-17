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
        
        $data = $this->request->param();
        $map=[];
        $map[]=['user_type','=',2];
        
        
        $redis = $GLOBALS['redisdb'];
        $key = $redis->keys('onlineo:*');

        $ids = [];
       
        foreach ($key as $v)
        {
            $id = explode(':', $v);
            $ids[] = $id[1];
  
        
        }

        $map[]=['id','in',$ids];

        $list = Db::name("user")
            ->where($map)
            ->where('iszombiep',0)
			->order("id desc")
			->paginate(20);
			
        
        $list->each(function($v,$k){
			
            $v['code']=Db::name("agent_code")->where("uid = {$v['id']}")->value('code');
            $v['user_login']=m_s($v['user_login']);
            $v['mobile']=m_s($v['mobile']);
//            $v['user_email']=m_s($v['user_email']);
            
            $v['avatar']=get_upload_path($v['avatar']);
            
            return $v;           
        });
        
        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
    
}
