<?php


namespace app\game\controller;


use app\game\model\GameCaizhong;
use app\game\model\GameTicket;
use app\user\model\User;
use cmf\controller\AdminBaseController;
use think\Request;


class GameTicketController extends AdminBaseController
{
    public function index()
    {
        $where = [];
        $data = input();

        $order_id = isset($data['order_id']) ? $data['order_id']: '';
        if($order_id != '') $where[] = ['order_id', '=', $order_id];

        $username = isset($data['username']) ? $data['username']: '';
        if($username != '') {
            $where[] = ['user_id', '=', $username];
        }

        $cz = isset($data['cz']) ? $data['cz']: '';
        if($cz != '') {
            $where[] = ['short_name', '=', $cz];
        }

        $expect = isset($data['expect']) ? $data['expect']: '';
        if($expect != '') $where[] = ['expect', '=', $expect];

        $status = isset($data['status']) ? $data['status']: '';
        if($status != '') $where[] = ['status', '=', $status];

        $ok = isset($data['ok']) ? $data['ok']: '';
        if($ok != '') $where[] = ['ok', '=', $ok];

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['addtime', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['addtime', '<=' ,strtotime($end_time) + 60*60*24];


        $list = GameTicket::with(['user'])->where($where)->order('addtime desc')->paginate(20);

        $caizhong = GameCaizhong::field('show_name,short_name')->all();

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('cz', $caizhong);
        $this->assign('page', $page);

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

    }
}