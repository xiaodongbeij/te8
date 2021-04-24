<?php

namespace app\appapi\job;


use think\queue\Job;
use think\Db;

class Msg 
{

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $to_userid = explode(',', $data['to_userid']);
        foreach ($to_userid as $v)
        {
            Db::name('message')->insert(['userid' => $data['userid'], 'to_userid' => $v, 'content' => $data['content'],'create_time' => time()]);
            echo '发送成功:' . $v . "\n";
        }
        $job->delete();
    }


    public function failed($data)
    {
        
    }



    
  
}