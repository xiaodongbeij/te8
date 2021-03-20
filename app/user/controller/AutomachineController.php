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

use think\Db;

class AutomachineController
{
    public function index()
    {
        
        connectionRedis();
        
        // $res = call_user_func_array([$GLOBALS['redisdb'], 'zadd'],['ceshi', 0, 'we', 2, 'me', 3, 'td', 8, 'go']);
 
        // $live = Db::name('live')->where('islive',1)->column('stream');
        // $list = Db::name('user_zombie')->limit(1)->orderRaw("RAND()")->column('uid');
        // foreach ($live as $v)
        // {
        //     $key = 'user_' . $v;
        //     $arr[] = $key;
            
        //         $userinfo=getUserInfo($uid,1);
        //         $score='0.'.($userinfo['level']+100).'1';
        //         $arr[] = $score;
        //         $arr[] = $uid;
        //         if(rand(1,3) == 2)
        //         {
        //             $GLOBALS['redisdb']->zrem($key, $uid);
        //         }
         
        //     call_user_func_array([$GLOBALS['redisdb'], 'zadd'],$arr);
        // }
        
        // return '成功'; 

    }
}