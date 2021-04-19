<?php


namespace app\appapi\controller;


use app\user\model\User;
use cmf\controller\HomeBaseController;
use think\cache\driver\Redis;
use think\Db;

class RateController extends HomebaseController
{

    //撤销
    /**
     * 撤销彩票订单
     * @desc 撤销彩票订单
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function test_c()
    {
        $site_info = cmf_get_option('site_info');
        dump($site_info);die;

//        $order = Db::table('cmf_game_ticket')->where('status',0)->select();
//        dump($order);die;
        foreach ($order as $v){
//            //开启事务
//            DI()->notorm->beginTransaction('db_appapi');
//            $res1 = user_change_action($order['user_id'],25,$order['money'],'彩票下注撤销');
//            $res2 = DI()->notorm->game_ticket
//                ->where('order_id',$v['id'])
//                ->update(['status'=>2]);
//            if ($res1 && $res2){
//                DI()->notorm->commit('db_appapi');
//            }else{
//                $rs['code'] = 1002;
//                $rs['msg'] = '撤销异常';
//            }

            Db::startTrans();
            try {
                $res1 = user_change_action($order['user_id'],25,$order['money'],'彩票下注撤销');
                $res2 = Db::table('cmf_game_ticket')->where('order_id',$v['id'])->update(['status'=>2]);
                if ($res1 && $res2) {
                    Db::commit();
                    echo '成功';
                } else {
                    Db::rollback();
                    echo '失败';
                }
            } catch (\Exception $e) {
                Db::rollback();
                echo '失败';
            }
        }

        return $rs;
    }

    //彩票返点
    public function ticket_rate()
    {
        $user_model = new User();
//        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
//        $endYesterday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
        $beginYesterday = time() - 1800;
        $endYesterday = time() + 1800;
        //下注记录
        $list = Db::table('cmf_game_ticket')
            ->alias('gt')
            ->where('gt.ok', 'in', [1, 2])
            ->where('gt.rate_status', 2)
            ->where('gt.addtime', '>=', $beginYesterday)
            ->where('gt.addtime', '<', $endYesterday)
            ->group('gt.user_id')
            ->field('gt.id,gt.user_id,sum(gt.money) money,u.invite_level')
            ->join('cmf_user u', 'u.id = gt.user_id')
            ->select()->toArray();
        if (!$list) {
            echo '已结算';
            die;
        }
        $tickets = Db::table('cmf_game_ticket')
            ->alias('gt')
            ->where('gt.ok', 'in', [1, 2])
            ->where('gt.rate_status', 2)
            ->where('gt.addtime', '>=', $beginYesterday)
            ->where('gt.addtime', '<', $endYesterday)
            ->field('gt.id')
            ->select()->toArray();
        $ids = [];      //用于更新游戏记录里返点状态
        foreach ($tickets as $v) {
            $ids[] = $v['id'];
        }
//        dump($list);die;
        $users = [];
        $contact = [];  //下级关系对应

        foreach ($list as $k => $v) {
            $list[$k]['invite_level'] = $temp = explode('-', $v['invite_level']);
            foreach ($temp as $key => $val) {
                if (!in_array($val, $users)) $users[] = $val;
                if (key_exists($key + 1, $temp) && !key_exists($val, $contact)) $contact[$val] = $temp[$key + 1];
            }
        }
        $userinfo = Db::table('cmf_user')
            ->alias('u')
            ->field('u.*,ur.rate')
            ->join('cmf_user_rate ur', 'ur.user_id = u.id')
            ->where('u.id', 'in', $users)
            ->where('ur.platform', '1')
            ->select();
        //id对应rate
        $id_rate = [];
        foreach ($userinfo as $v) {
            $id_rate[$v['id']] = $v['rate'];
        }
//        dump($id_rate);die;
//        dump($list);die;
        foreach ($list as $k => $v) {
            foreach ($v['invite_level'] as $va) {
                foreach ($userinfo as $val) {
                    if ($va == $val['id']) {
                        $temp = ['id' => $val['id']];
                        $temp['coin'] = $val['coin'];
                        $rate = 0;
                        foreach ($v['invite_level'] as $kk => $value){
                            if(empty($value)) continue;
                            if ($val['id'] == $value){
                                $rate = $val['rate'];
                            }elseif($rate>0){
                                $rate -= $id_rate[$value];
                                break;
                            }
                        }
                        $temp['rate'] = $rate;
                        $temp['send_money'] = $v['money'] * $rate;
                        $list[$k]['send'][] = $temp;
                    }
                }
            }
        }
//        dump($list);die;
        //整理数据
        $insert_change = [];    //key->user_id
        $up_users = [];     //key->user_id
        foreach ($list as $v) {
            foreach ($v['send'] as $val) {
                if (array_key_exists($val['id'],$insert_change)){
                    $insert_change[$val['id']]['next_money']+=$val['send_money'];
                    $insert_change[$val['id']]['change_money']+=$val['send_money'];
                }else{
                    $insert = [
                        'user_id' => $val['id'],
                        'change_type' => 7,
                        'money' => $val['coin'],
                        'next_money' => $val['coin'] + $val['send_money'],
                        'change_money' => $val['send_money'],
                        'remark' => '彩票返点',
                        'addtime' => time(),
                        'platform' => '1'
                    ];
                    $insert_change[$val['id']] = $insert;
                }

                if (array_key_exists($val['id'],$up_users)){
                    $up_users[$val['id']]['coin']+=$val['send_money'];
                }else{
                    $up_user = ['id' => $val['id'], 'coin' => $val['coin'] + $val['send_money']];
                    $up_users[$val['id']] = $up_user;
                }
            }
        }
        //开启事务
        Db::startTrans();
        try {
            $res1 = Db::table('cmf_user_change')->insertAll($insert_change);
            $res2 = $user_model->saveAll($up_users);
            $res3 = Db::table('cmf_game_ticket')->where('id', 'in', $ids)->update(['rate_status' => 1]);
            if ($res1 && $res2 && $res3) {
                Db::commit();
                echo '成功';
                die;
            } else {
                Db::rollback();
                echo '失败';
                die;
            }
        } catch (\Exception $e) {
            Db::rollback();
            echo '失败';
            die;
        }
    }


    //游戏返点
    public function game_rate()
    {
        $data = input();
        $platform = $data['plat'];

        $user_model = new User();
        // $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        // $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $beginYesterday = time() - 1800;
        $endYesterday = time() + 1800;

        //下注记录
        $list = Db::table('cmf_game_record')
            ->alias('gr')
            ->where('gr.rate_status', 2)
            ->where('gr.platform_code', $platform)
            ->where('gr.bet_time', '>=', $beginYesterday)
            ->where('gr.bet_time', '<', $endYesterday)
            ->group('gr.user_login')
            ->field('gr.id,gr.user_login,sum(gr.bet_amount) money,u.invite_level')
            ->join('cmf_user u', 'u.user_login = gr.user_login')
            ->select()->toArray();
        if (!$list) {
            echo '已结算';
            die;
        }

        $games = Db::table('cmf_game_record')
            ->alias('gr')
            ->where('gr.rate_status', 2)
            ->where('gr.bet_time', '>=', $beginYesterday)
            ->where('gr.bet_time', '<', $endYesterday)
            ->field('gr.id')
            ->select()->toArray();
        $ids = [];
        foreach ($games as $v) {
            $ids[] = $v['id'];
        }

        $users = [];
        $contact = [];  //下级关系对应
        foreach ($list as $k => $v) {
            $list[$k]['invite_level'] = $temp = explode('-', $v['invite_level']);
            foreach ($temp as $key => $val) {
                if (!in_array($val, $users)) $users[] = $val;
                if (key_exists($key + 1, $temp) && !key_exists($val, $contact)) $contact[$val] = $temp[$key + 1];
            }
        }
        $userinfo = Db::table('cmf_user')
            ->alias('u')
            ->field('u.*,ur.rate')
            ->join('cmf_user_rate ur', 'ur.user_id = u.id')
            ->where('u.id', 'in', $users)
            ->where('ur.platform', $platform)
            ->select();
        //id对应rate
        $id_rate = [];
        foreach ($userinfo as $v) {
            $id_rate[$v['id']] = $v['rate'];
        }

        foreach ($list as $k => $v) {
            foreach ($v['invite_level'] as $va) {
                foreach ($userinfo as $val) {
                    if ($va == $val['id']) {
                        $temp = ['id' => $val['id']];
                        $temp['coin'] = $val['coin'];
                        $rate = 0;
                        foreach ($v['invite_level'] as $kk => $value){
                            if(empty($value)) continue;
                            if ($val['id'] == $value){
                                $rate = $val['rate'];
                            }elseif($rate>0){
                                $rate -= $id_rate[$value];
                                break;
                            }
                        }
                        $temp['rate'] = $rate;
                        $temp['send_money'] = $v['money'] * $rate;
                        $list[$k]['send'][] = $temp;
                    }
                }
            }
        }

        //整理数据
        $insert_change = [];    //账变
        $up_users = []; //用户余额
        foreach ($list as $v) {
            foreach ($v['send'] as $val) {
                if (array_key_exists($val['id'],$insert_change)){
                    $insert_change[$val['id']]['next_money']+=$val['send_money'];
                    $insert_change[$val['id']]['change_money']+=$val['send_money'];
                }else{
                    $insert = [
                        'user_id' => $val['id'],
                        'change_type' => 7,
                        'money' => $val['coin'],
                        'next_money' => $val['coin'] + $val['send_money'],
                        'change_money' => $val['send_money'],
                        'remark' => '游戏返点',
                        'addtime' => time(),
                        'platform' => $platform
                    ];
                    $insert_change[$val['id']] = $insert;
                }
                if (array_key_exists($val['id'],$up_users)){
                    $up_users[$val['id']]['coin']+=$val['send_money'];
                }else{
                    $up_user = ['id' => $val['id'], 'coin' => $val['coin'] + $val['send_money']];
                    $up_users[$val['id']] = $up_user;
                }
            }
        }

        //开启事务
        Db::startTrans();
        try {
            $res1 = Db::table('cmf_user_change')->insertAll($insert_change);
            $res2 = $user_model->saveAll($up_users);
            $res3 = Db::table('cmf_game_record')->where('id', 'in', $ids)->update(['rate_status' => 1]);
            if ($res1 && $res2 && $res3) {
                Db::commit();
                echo '成功';
                die;
            } else {
                Db::rollback();
                echo '失败';
                die;
            }
        } catch (\Exception $e) {
            Db::rollback();
            echo '失败';
            die;
        }
    }

    //直播返点
    public function live_rate()
    {

        $user_model = new User();
        $beginYesterday = time() - 1800;
        $endYesterday = time() + 1800;

        //消费记录
        $list = Db::table('cmf_user_coinrecord')
            ->alias('uc')
            ->where('uc.rate_status', 2)
            ->where('uc.addtime', '>=', $beginYesterday)
            ->where('uc.addtime', '<', $endYesterday)
            ->group('uc.uid')
            ->field('uc.id,uc.uid,sum(uc.totalcoin) money,u.invite_level')
            ->join('cmf_user u', 'u.id = uc.uid')
            ->select()->toArray();
        if (!$list) {
            echo '已结算';
            die;
        }
        $live = Db::table('cmf_user_coinrecord')
            ->alias('uc')
            ->where('uc.rate_status', 2)
            ->where('uc.addtime', '>=', $beginYesterday)
            ->where('uc.addtime', '<', $endYesterday)
            ->field('uc.id')
            ->select()->toArray();
        $ids = [];      //用于更新直播记录里返点状态
        foreach ($live as $v) {
            $ids[] = $v['id'];
        }
        $users = [];
        $contact = [];  //下级关系对应
        foreach ($list as $k => $v) {
            $list[$k]['invite_level'] = $temp = explode('-', $v['invite_level']);
            foreach ($temp as $key => $val) {
                if (!in_array($val, $users)) $users[] = $val;
                if (key_exists($key + 1, $temp) && !key_exists($val, $contact)) $contact[$val] = $temp[$key + 1];
            }
        }

        $userinfo = Db::table('cmf_user')
            ->alias('u')
            ->field('u.*,ur.rate')
            ->join('cmf_user_rate ur', 'ur.user_id = u.id')
            ->where('u.id', 'in', $users)
            ->where('ur.platform', '2')
            ->select();
        //id对应rate
        $id_rate = [];
        foreach ($userinfo as $v) {
            $id_rate[$v['id']] = $v['rate'];
        }

        foreach ($list as $k => $v) {
            foreach ($v['invite_level'] as $va) {
                foreach ($userinfo as $val) {
                    if ($va == $val['id']) {
                        $temp = ['id' => $val['id']];
                        $temp['coin'] = $val['coin'];
                        $rate = 0;
                        foreach ($v['invite_level'] as $kk => $value){
                            if(empty($value)) continue;
                            if ($val['id'] == $value){
                                $rate = $val['rate'];
                            }elseif($rate>0){
                                $rate -= $id_rate[$value];
                                break;
                            }
                        }
                        $temp['rate'] = $rate;
                        $temp['send_money'] = $v['money'] * $rate;
                        $list[$k]['send'][] = $temp;
                    }
                }
            }
        }
        // dump($list);die;
        //整理数据
        $insert_change = [];
        $up_users = [];
//        dump($list);die;
        foreach ($list as $v) {
            if(!isset($v['send'])) continue;
            foreach ($v['send'] as $val) {
                if (array_key_exists($val['id'],$insert_change)){
                    $insert_change[$val['id']]['next_money']+=$val['send_money'];
                    $insert_change[$val['id']]['change_money']+=$val['send_money'];
                }else{
                    $insert = [
                        'user_id' => $val['id'],
                        'change_type' => 7,
                        'money' => $val['coin'],
                        'next_money' => $val['coin'] + $val['send_money'],
                        'change_money' => $val['send_money'],
                        'remark' => '直播返点',
                        'addtime' => time(),
                        'platform' => '2'
                    ];
                    $insert_change[$val['id']] = $insert;
                }
                if (array_key_exists($val['id'],$up_users)){
                    $up_users[$val['id']]['coin']+=$val['send_money'];
                }else{
                    $up_user = ['id' => $val['id'], 'coin' => $val['coin'] + $val['send_money']];
                    $up_users[$val['id']] = $up_user;
                }
            }
        }
        //开启事务
        Db::startTrans();
        try {
            $res1 = Db::table('cmf_user_change')->insertAll($insert_change);
            $res2 = $user_model->saveAll($up_users);
            $res3 = Db::table('cmf_user_coinrecord')->where('id', 'in', $ids)->update(['rate_status' => 1]);
            if ($res1 && $res2 && $res3) {
                Db::commit();
                echo '成功';
                die;
            } else {
                Db::rollback();
                echo '失败';
                die;
            }
        } catch (\Exception $e) {
            Db::rollback();
            echo '失败';
            die;
        }
    }

//    //邀请链接下载页存储ip对应code
//    public function reg(){
//        $invite = input('invite');
//        $key = md5(getIP());
//        $redis = new Redis();
//        $redis->set($key,$invite);
//        $res = $redis->get($key);
//        echo '成功';
//    }

//    public function test_change()
//    {
//        $res = user_change_action(42885,3,10,'测试');
//        var_dump($res);
//    }

    //拉取数据
   public function test()
   {
       $max = Db::table('cmf_game_rule_rate')->field('max(cai_id) cai_id')->select();
//        $id = $max[0]['cai_id'] + 1;
       $id = 281;
       // $id = input('id');
       $cai = Db::table('cmf_game_caizhong')->field('id,short_name')->where('id', $id)->find();
//        dump($cai);die;

       if (!$cai){
           echo 'ok';die;
       }
       $data = [
           'shortName' => $cai['short_name']
       ];
       $return = $this->get_sign($data, 'uHai9bCz');
       $url = 'http://testport1.webuitest.com/ct-data-app/live/wpRuleGroup?' . $return['date'] . '&sign=' . $return['sign'];
       $info = $this->curl($url);
//        dump($info);die;
       $info = json_decode($info,true);
       $list = $info['list'];
//        dump($info);die;
       $insertAll = [];
       foreach ($list as $v){
           $data2 = [
               'shortName' => $cai['short_name'],
               'groupCode' => $v['groupCode']
           ];
           $return2 = $this->get_sign($data2, 'uHai9bCz');
           $url2 = 'http://testport1.webuitest.com/ct-data-app/live/wplottery?' . $return2['date'] . '&sign=' . $return2['sign'];
           $temp = json_decode($this->curl($url2),true);
           $ruleList = $temp['ruleList'];
           foreach ($ruleList as $val){
               foreach ($val['rateList'] as $value){
                   $insert = [
                       'cai_id'=>$id,
                       'rate_name'=>$value['rateName'],
                       'rate_code'=>$value['rateCode'],
                       'rate'=>$value['rate'],
                       'rule_name'=>$val['ruleName'],
                       'rule_code' =>$val['ruleCode']
                   ];
                   $insertAll[] = $insert;
               }
           }
       }
       $res = Db::table('cmf_game_rule_rate')->insertAll($insertAll);
       dump('当前id:'.$id);
       dump('添加数量:'.$res);die;
   }
//////
//    //md5生成签名，返回url参数和签名
//    protected function get_sign($data, $key)
//    {
//        $str = '';
//        ksort($data);
//        foreach ($data as $k => $v) {
//            $str .= $k . '=' . $v . '&';
//        }
//        $data = $str = substr($str, 0, -1);
//        $str .= $key;
//        return [
//            'date' => $data,
//            'sign' => md5($str)
//        ];
//    }
//
//    protected function curl($url, $params = false, $ispost = 0, $https = 0)
//    {
//        $httpInfo = array();
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        if ($https) {
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
//        }
//        if ($ispost) {
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//            curl_setopt($ch, CURLOPT_URL, $url);
//        } else {
//            if ($params) {
//                if (is_array($params)) {
//                    $params = http_build_query($params);
//                }
//                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
//            } else {
//                curl_setopt($ch, CURLOPT_URL, $url);
//            }
//        }
//
//        $response = curl_exec($ch);
//
//        if ($response === FALSE) {
//            //echo "cURL Error: " . curl_error($ch);
//            return false;
//        }
//        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
//        curl_close($ch);
//        return $response;
//    }
    //user_rate1加数据
//    public function insert()
//    {
//        $ids = Db::table('cmf_user')->field('id')->select()->toArray();
//        $user = [];
//        foreach ($ids as $v) {
//            $temp = [
//                'user_id' => $v['id'],
//                'platform' => '0027',
//                'remark' => 'OG游戏'
//            ];
//            $user[] = $temp;
//            $temp = [
//                'user_id' => $v['id'],
//                'platform' => '0022',
//                'remark' => '德胜棋牌'
//            ];
//            $user[] = $temp;
//            $temp = [
//                'user_id' => $v['id'],
//                'platform' => '0002',
//                'remark' => 'PT游戏'
//            ];
//            $user[] = $temp;
//            $temp = [
//                'user_id' => $v['id'],
//                'platform' => '0024',
//                'remark' => '速博体育'
//            ];
//            $user[] = $temp;
//            $temp = [
//                'user_id' => $v['id'],
//                'platform' => '0035',
//                'remark' => '泛亚电竞'
//            ];
//            $user[] = $temp;
//        }
//        $res = Db::table('cmf_user_rate')->insertAll($user);
//    }
}