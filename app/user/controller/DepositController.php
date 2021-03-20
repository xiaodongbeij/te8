<?php


namespace app\user\controller;


use app\admin\model\Order;
use app\user\model\User;
use app\user\model\UserChange;
use cmf\controller\AdminBaseController;
use think\Db;

class DepositController extends AdminBaseController
{
    public function index()
    {
        $data = input();
        $where = [];

        $user_login = isset($data['user_login']) ? $data['user_login']: '';
        if($user_login != '') $where[] = ['user_login', '=', $user_login];

        $user_ids1 = UserChange::where('change_type', 1)->whereOr('change_type', 2)->column('user_id');
        $user_ids2 = Order::where('order_status', 4)->where('pay_status', 1)->column('user_id');
        if($user_ids1 && $user_ids2){
            $user_ids = array_unique(array_merge_recursive($user_ids1, $user_ids2));
            $where[] = ['id', 'in', $user_ids];
        }else{
            if($user_ids1){
                $user_ids = array_unique($user_ids1);
                $where[] = ['id', 'in', $user_ids];
            }
            if($user_ids2){
                $user_ids = array_unique($user_ids2);
                $where[] = ['id', 'in', $user_ids];
            }
        }

        $users = User::where($where)
            ->field('id,user_login,count_money,count_Withdrawal')
            ->order('id desc')
            ->paginate(20);

        $comprehensive = 0.00;
        $count_money = 0.00;
        $count_Withdrawal = 0.00;
        if($users){
            $count_money = User::where($where)->sum('count_money');
            $count_Withdrawal = User::where($where)->sum('count_Withdrawal');
            $comprehensive = $count_money - $count_Withdrawal;
        }

        $users->appends($data);
        $page = $users->render();

        $user_num = $users->count();

        $this->assign('list', $users);
        $this->assign('user_num', $user_num);
        $this->assign('count_money', number_format($count_money,2));
        $this->assign('count_Withdrawal', number_format($count_Withdrawal,2));
        $this->assign('comprehensive', number_format($comprehensive,2));
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    //导出
    function export(){

        $data = input();
        $where = [];

        $user_login = isset($data['user_login']) ? $data['user_login']: '';
        if($user_login != '') $where[] = ['user_login', '=', $user_login];

        $user_ids1 = UserChange::where('change_type', 1)->whereOr('change_type', 2)->column('user_id');
        $user_ids2 = Order::where('order_status', 4)->where('pay_status', 1)->column('user_id');
        if($user_ids1 && $user_ids2){
            $user_ids = array_unique(array_merge_recursive($user_ids1, $user_ids2));
            $where[] = ['id', 'in', $user_ids];
        }else{
            if($user_ids1){
                $user_ids = array_unique($user_ids1);
                $where[] = ['id', 'in', $user_ids];
            }
            if($user_ids2){
                $user_ids = array_unique($user_ids2);
                $where[] = ['id', 'in', $user_ids];
            }
        }

        $xlsName  = "存取款统计";

        $lists = User::where($where)
            ->field('id,user_login,count_money,count_Withdrawal')
            ->order('id desc')
            ->select();

        $action="存取款统计导出：".Db::name("cmf_user_change")->getLastSql();
        setAdminLog($action);

        $cellName = array('A','B','C','D');
        $xlsCell  = array(
            array('id','用户id'),
            array('count_money','存款'),
            array('count_Withdrawal','取款'),
            array('comprehensive','盈亏'),
        );
        exportExcel($xlsName,$xlsCell,$lists,$cellName);
    }

}