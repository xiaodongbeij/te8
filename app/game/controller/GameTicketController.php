<?php


namespace app\game\controller;


use app\game\model\GameCaizhong;
use app\game\model\GameTicket;
use app\user\model\User;
use cmf\controller\AdminBaseController;
use think\Db;
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

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['addtime', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['addtime', '<=' ,strtotime($end_time) + 60*60*24];

        $list_oks = GameTicket::where($where)->where('ok', '=', 1)->count();
        $list_nos = GameTicket::where($where)->where('ok', '=', 2)->count();

        //层级搜索
        $parent_id = isset($data['parent_id']) ? $data['parent_id']: '';
        if($parent_id != ''){
            $path = Db::table('cmf_user')->where('id',$parent_id)->value('invite_level');
            $user_ids = Db::table('cmf_user')->where('invite_level','like',$path.'%')->field('id')->select();
            $users = [];
            foreach ($user_ids as $v){
                $users[] = $v['id'];
            }
            $where[]=['user_id', 'in', $users];
        }

        $ok = isset($data['ok']) ? $data['ok']: '';
        if($ok != '') {
            $where[] = ['ok', '=', $ok];
        }
        $list = GameTicket::with(['user'])->where($where)->order('addtime desc')->paginate(20);
        $user_nums = GameTicket::where($where)->field('id,user_id')->group('user_id')->count();
        $list_count = GameTicket::where($where)->field('sum(money) money, sum(prize) prize')->find();

        $caizhong = GameCaizhong::field('show_name,short_name')->all();

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('list_oks', $list_oks);
        $this->assign('list_nos', $list_nos);
        $this->assign('user_nums', $user_nums);
        $this->assign('list_count', $list_count);
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
    
    
    public function export()
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

        $list = [];
        
        
        foreach (GameTicket::with(['user'])->where($where)->order('addtime desc')->cursor()  as $v)
        {
            $v->ok = $v->ok == 1? '已中奖' : '未中奖';
            $v->status = $v->status == 1? '已结算' : '未结算';
            
            $list[] = $v->toArray();
        }

        $xlsName  = "下注记录";
        $action="导出下注记录";
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
        $xlsCell  = array(
            array('id','序号'),
            array('order_id','订单号'),
            array('user_id','用户ID	'),
            array('show_name','彩种名称'),
            array('rule_name','玩法名称'),
            array('rate_name','用户下注'),
            array('prize_codes','开奖内容'),
            array('expect','期号'),
            array('money','下注金额'),
            array('money','支付金额'),
            array('prize','奖金金额'),
            array('status','已结算'),
            array('ok','中奖状态'),
            array('addtime','添加时间'),
        );
        exportExcel($xlsName,$xlsCell,$list,$cellName);

    }
}