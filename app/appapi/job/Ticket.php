<?php

namespace app\appapi\job;

use cmf\controller\HomeBaseController;
use think\queue\Job;
use think\Db;

class Ticket 
{

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
    
        $isJobDone = $this->ticket_notify($data);
        if ($isJobDone === true) {
            $d = $job->delete();
            print("执行成功" . $d);
        } else {
            var_dump($isJobDone . "\n");
            if ($job->attempts() > 2) {
                var_dump("执行失败删除任务 \n");
                $job->delete();
            }
            $num = $job->attempts(1);
            var_dump("重试任务: $num \n");
        }
    }


    public function failed($data)
    {
        
    }


    /**
     * 彩票返奖处理
     */
    private function ticket_notify($data)
    {
        connectionRedis();

        $config = getConfigPri();

        $key = $config['tripartite_game_key'];

        $req = $data;

        $path = CMF_DATA . 'paylog/ticket/'.date('Ym').'/';
        $filename = date('dH').'.txt';
        if(!is_dir($path)){
            $flag = mkdir($path,0777,true);
        }
        if (empty($req)){
            // file_put_contents( $path.$filename,'----------------------回调参数错误----------------------'.PHP_EOL,FILE_APPEND);
            return '回调参数错误';
        }

        // file_put_contents( $path.$filename,'----------------------notify_start----------------------'.PHP_EOL,FILE_APPEND);
        // file_put_contents( $path.$filename,json_encode($req).PHP_EOL,FILE_APPEND);
        $sign = $req['sign'];
        unset($req['sign']);
        //验签
        $sign_res = $this->ver_sign($req,$sign,$key);
        if (!$sign_res){
            // file_put_contents($path.$filename, '验签失败'.PHP_EOL,FILE_APPEND);
            return '签名错误';
        }
        $data = json_decode($req['data'],true);

        $result = true;
        $openCode = true;
        var_dump("开始处理返奖 \n");
        var_dump($data['list']);
        foreach ($data['list'] as $k => $v){
            //查找订单信息
            $order = Db::table('cmf_game_ticket')->where('order_id',$v['billNo'])->find();


            if ($order['status'] == 1 || $order['status'] == 2) continue;
            if ($v['status'] == 3){
                //中奖
                //开启事务
                Db::startTrans();
                try {
                    //用户信息
                    $user = Db::table('cmf_user')->where('id',$order['user_id'])->find();
                    //更新订单
                    $res1 = Db::table('cmf_game_ticket')
                        ->where('order_id',$v['billNo'])
                        ->update([
                            'ok' => 1,
                            'status' => 1,
                            'prize' => $v['prize'],
                            'prize_codes' => $v['openCode']
                        ]);

                    $res2 = user_change_action($order['user_id'],3,$v['prize'],'彩票中奖');

                    if ($res1 && $res2) {
                        Db::commit();

                        $msg = [
                            'msg' => [[
                                '_method_' => 'winning',
                                'ct' =>  $order['show_name'].'中奖' . $v['prize'] . '元',
                                'user_nicename' => $user['user_nicename'],
                                'level' => getLevel($user['consumption']),
                                'money' => $v['prize'],
                                'show_name' => $order['show_name'],
                            ]]
                        ];

                        $msg['msg'][0]['type'] = $v['prize'] > 500 ? 1 : 0;
                        redisPush($msg);

                        // file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新成功'.PHP_EOL,FILE_APPEND);
                    } else {
                        Db::rollback();
                        // file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                        $result = '更新失败0';
                    }
                }catch (\Exception $e) {
                    Db::rollback();
                    // file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                    $result = '更新失败1';
                }
            }else{
                $up = [
                    'ok' => 2,
                    'status' => 1,
                    'prize_codes' => $v['openCode']
                ];
                $res = Db::table('cmf_game_ticket')
                    ->where('order_id',$v['billNo'])
                    ->update($up);
                if ($res){
                    // file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新成功'.PHP_EOL,FILE_APPEND);
                }else{
                    // file_put_contents( $path.$filename,'订单:'. $v['billNo'] .'更新失败'.PHP_EOL,FILE_APPEND);
                    $result = '更新失败2';
                }
            }
        }

        // file_put_contents( $path.$filename,'----------------------notify_end----------------------'.PHP_EOL,FILE_APPEND);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    
    
    //md5验签名
    protected function ver_sign($data, $sign, $key)
    {
        $str = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str = substr($str, 0, -1);
        $str .= $key;
        $temp = md5($str);
        if ($temp == $sign) {
            return true;
        } else {
            return false;
        }
    }
}