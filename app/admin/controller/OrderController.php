<?php


namespace app\admin\controller;


use app\admin\model\Channel;
use app\admin\model\Order;
use app\user\model\UserChange;
use cmf\controller\AdminBaseController;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Request;

class OrderController extends AdminBaseController
{
    public function index()
    {
        $data = input();
        $where = [];

        $where[] = ['del_status', '=', 0];
        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['addtime', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['addtime', '<=' ,strtotime($end_time) + 60*60*24];

        $pay_start_time = isset($data['pay_start_time']) ? $data['pay_start_time']: '';
        if($pay_start_time != '') $where[]=['pay_time', '>=' ,strtotime($pay_start_time)];

        $pay_end_time = isset($data['pay_end_time']) ? $data['pay_end_time']: '';
        if($pay_end_time != '') $where[]=['pay_time', '<=', strtotime($pay_end_time) + 60*60*24];

        $order_sn = isset($data['order_sn']) ? $data['order_sn']: '';
        if($order_sn != '') $where[]=['order_sn', '=', $order_sn];

        $third_order_sn = isset($data['third_order_sn']) ? $data['third_order_sn']: '';
        if($third_order_sn != '') $where[]=['third_order_sn', '=', $third_order_sn];

        //层级搜索
        $parent_id = isset($data['parent_id']) ? $data['parent_id']: '';
        if($parent_id != ''){
            $path = Db::table('cmf_user')->where('id',$parent_id)->value('invite_level');
            $user_ids = Db::table('cmf_user')->where('invite_level','like',$path.'%')->field('id')->select();
            $users = [];
            foreach ($user_ids as $v){
                $users[] = $v['id'];
            }
//            dump($user_ids);die;
            $where[]=['user_id', 'in', $users];
        }

        $user_id = isset($data['user_id']) ? $data['user_id']: '';
        if($user_id != '') {
            $where[]=['user_id', '=', $user_id];
        }

        $channel_id = isset($data['channel_id']) ? $data['channel_id']: '';
        if($channel_id != '') $where[]=['channel_id', '=', $channel_id];

        $payway = isset($data['payway']) ? $data['payway']: '';
        if($payway != '') $where[]=['payway', '=', $payway];

        $order_status = isset($data['order_status']) ? $data['order_status']: '';
        if($order_status != '') $where[]=['order_status', '=', $order_status];

        $pay_status = isset($data['pay_status']) ? $data['pay_status']: '';
        if($pay_status != '') $where[]=['pay_status', '=', $pay_status];

        $charge_num = isset($data['charge_num']) ? $data['charge_num']: '';
        if($charge_num != '') $where[]=['charge_num', '=', $charge_num];

        $list = Order::with(['channel'])->where($where)->order('id desc')->paginate(20);
        $list_count = Order::where($where)->count();
        $list_users = Order::where($where)->field('id,user_id')->group('user_id')->count();
        $list_money = Order::where($where)->sum('order_money');
        if(!$list_money) $list_money = 0;
        $list->appends($data);
        $page = $list->render();
        $channels = Channel::where('status', 1)->where('del_status', 0)->field('id,channel_name')->select();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('list_count', $list_count);
        $this->assign('list_users', $list_users);
        $this->assign('list_money', $list_money);

        $this->assign('channels', $channels);

        // 渲染模板输出
        return $this->fetch();
    }

    public function add(Request $request)
    {

    }

    public function edit(Request  $request)
    {

    }

    public function del()
    {
        $ids = input();
        $res = Order::where('id', 'in', $ids)->update(['del_status' => 1]);
        if(!$res) $this->error("error");
        $this->success("ok");
    }


    public function callback(Request $request)
    {
        if($request->isPost()){
            $id = input('id');
            $remark = input('remark');
            $token = input('TOKEN');
            $google_token = input('google_token');
            $res = judgeToken($token);
            if(!$res) $this->error("请勿重复提交");
            if(!cmf_google_token_check(session('google_token'),$google_token)) {
                $this->error('令牌验证失败');
            }
            $this->call($id, $remark);
        }
        $id = input('id');
        createToken();

        $this->assign('id', $id);

        // 渲染模板输出
        return $this->fetch();
    }


    private function call($order_id, $remark)
    {
        $order = Db::table('cmf_order')->where('id', '=', $order_id)->find();
        if(!$order || $order['order_status'] != 1) $this->error('该订单无法回调');

        //开启事务
        Db::startTrans();

        try {
            //更新订单
            $data = [
                'order_status' => 4,
                'pay_status' => 1,
                'pay_time' => time(),
                'pay_money' => $order['order_money'],
                'remark' => $remark,
            ];
            $res1 = Db::table('cmf_order')->where('id', $order_id)->update($data);
            $user = Db::table('cmf_user')->where('id', $order['user_id'])->find();
            //change记录
            $change = [
                'user_id' => $order['user_id'],
                'change_type' => 4,

                'money' => $user['coin'],


                'next_money' => $user['coin'] + $order['order_money'],
                'change_money' => $order['order_money'],
                'addtime' => time()
            ];
            $res2 = UserChange::create($change);

            //更新用户信息
            $userinfo = [
                'coin' => $user['coin'] + $order['order_money'],
                'count_money' => $user['count_money'] + $order['order_money'],
            ];
            $level = $user['level'];
            $user_channel = Db::table('cmf_user_channel')->select();
            foreach ($user_channel as $v) {
                if ($user['count_money'] > $v['min_money']) {
                    $level = $v['id'];
                }
            }
            if ($level != $user['level']) {
                $userinfo['level'] = $level;
            }

            $res3 = Db::table('cmf_user')->where('id', $order['user_id'])->update($userinfo);
            $action = [
                'order_id' => $order_id,
                'action_user' => $user['id'],
                'change_id' => $res2->id,
                'order_status' => 4,
                'pay_status' => 1,
                'remark' => $remark,
                'addtime' => time(),
            ];

            $res4 = Db::table('cmf_order_action')->insert($action);
            if ($res1 && $res2 && $res3 && $res4) {
                Db::commit();
                $this->success('回调成功1');
            }else
            {
                Db::rollback();
                $this->error('回调失败1');
            }
        } catch (\Exception $e) {
            Db::rollback();

            $this->success('回调成功2');
        }
    }

    function export()
    {
         $data = input();
        $where = [];

        $where[] = ['del_status', '=', 0];
        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['addtime', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['addtime', '<=' ,strtotime($end_time) + 60*60*24];

        $pay_start_time = isset($data['pay_start_time']) ? $data['pay_start_time']: '';
        if($pay_start_time != '') $where[]=['pay_time', '>=' ,strtotime($pay_start_time)];

        $pay_end_time = isset($data['pay_end_time']) ? $data['pay_end_time']: '';
        if($pay_end_time != '') $where[]=['pay_time', '<=', strtotime($pay_end_time) + 60*60*24];

        $order_sn = isset($data['order_sn']) ? $data['order_sn']: '';
        if($order_sn != '') $where[]=['order_sn', '=', $order_sn];

        $third_order_sn = isset($data['third_order_sn']) ? $data['third_order_sn']: '';
        if($third_order_sn != '') $where[]=['third_order_sn', '=', $third_order_sn];

        $user_id = isset($data['user_id']) ? $data['user_id']: '';
        if($user_id != '') {
            $where[]=['user_id', '=', $user_id];
        }

        $channel_id = isset($data['channel_id']) ? $data['channel_id']: '';
        if($channel_id != '') $where[]=['channel_id', '=', $channel_id];

        $payway = isset($data['payway']) ? $data['payway']: '';
        if($payway != '') $where[]=['payway', '=', $payway];

        $order_status = isset($data['order_status']) ? $data['order_status']: '';
        if($order_status != '') $where[]=['order_status', '=', $order_status];

        $pay_status = isset($data['pay_status']) ? $data['pay_status']: '';
        if($pay_status != '') $where[]=['pay_status', '=', $pay_status];

        $charge_num = isset($data['charge_num']) ? $data['charge_num']: '';
        if($charge_num != '') $where[]=['charge_num', '=', $charge_num];

        $xlsName  = "订单管理";
        $xlsData = Order::where($where)->order('id desc')->select();

        $order_status = [
            1 => '支付中',
            2 => '取消',
            3 => '无效',
            4 => '完成',
            5 => '退款',
        ];

        $pay_status = [
            0 => '未支付',
            1 => '已支付',
        ];   

        $pay_way = [
            1 => '支付宝',
            2 => '微信',
            3 => '银行卡',
        ];   

        foreach ($xlsData as $k => $v){
            $xlsData[$k]['order_status'] = $order_status[$v['order_status']];
            $xlsData[$k]['pay_status'] = $pay_status[$v['pay_status']];
            $xlsData[$k]['payway'] = $pay_way[$v['payway']];
        }

        $action="导出订单：".Db::name("order")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
        $xlsCell  = array(
            array('id','序号'),
            array('order_sn','订单编号'),
            array('third_order_sn','三方订单号'),
            array('order_status','订单状态'),
            array('pay_status','支付状态'),
            array('user_id','用户ID'),
            array('payway','支付方式'),
            array('channel_id','支付通道'),

            array('order_money','订单金额'),
            array('pay_money','支付金额'),
            array('addtime','下单时间'),
            array('pay_time','支付时间'),
            array('remark','备注'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
}