<?php


namespace app\game\controller;


use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class GameDetailController extends AdminBaseController
{
    public function index()
    {
        $where = [];
        $data = input();

        $order_id = isset($data['order_id']) ? $data['order_id'] : '';
        if ($order_id != '') $where[] = ['bet_id', '=', $order_id];

        $username = isset($data['username']) ? $data['username'] : '';
        if ($username != '') {
            $where[] = ['user_login', 'like', '%' . $username . '%'];
        }

        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') $where[] = ['status', '=', $status];

        $rate_status = isset($data['rate_status']) ? $data['rate_status'] : '';
        if ($rate_status != '') $where[] = ['rate_status', '=', $rate_status];

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        if($start_time != '') $where[]=['bet_time', '>=' ,strtotime($start_time)];

        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($end_time != '') $where[]=['bet_time', '<=' ,strtotime($end_time) + 60*60*24];

        $list = Db::table('cmf_game_record')->alias('cr')->leftJoin('cmf_game_cate gc','gc.platform=cr.platform_code')->field('cr.*,FROM_UNIXTIME(cr.bet_time,"%Y-%m-%d %H:%i:%s") as bet_time,gc.name')->where($where)->order('bet_time desc')->paginate(20);

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    //游戏用户盈亏
    public function user_profit()
    {
        $data = input();
        $where = [];

        $user_login = isset($data['user_login']) ? $data['user_login'] : '';
        if ($user_login != '') {
            $where[] = ['gr.user_login', '=', $user_login];
        }

        $short_name = isset($data['short_name']) ? $data['short_name'] : '';
        if ($short_name != '') {
            $where[] = ['platform_code', '=', $short_name];
        }

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time = isset($data['end_time']) ? $data['end_time'] : '';
        if ($start_time != "") {
            $where[] = ['bet_time', '>=', strtotime($start_time)];
        }
        if ($end_time != "") {
            $where[] = ['bet_time', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $cai = Db::table('cmf_game_cate')
            ->field('name,platform')
            ->where('platform', '<>', 1)
            ->select();
        $this->assign('cai', $cai);

        $list = Db::table('cmf_game_record')
            ->alias('gr')
            ->group("gr.user_login,gr.platform_code,FROM_UNIXTIME(gr.bet_time,'%Y-%m-%d')")
//            ->join('cmf_user u', 'u.user_login=gr.user_login')
            ->join('cmf_user u', 'u.id=gr.user_login')
            ->where($where)
            ->order('gr.id desc')
            ->field("u.user_login,FROM_UNIXTIME(gr.bet_time,'%Y-%m-%d') date,gr.game_name,sum(gr.pay_off) bonus,sum(gr.bet_amount) money,sum(gr.profit) yin")
            ->paginate(20);
        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    //游戏综合报表
    public function system_report()
    {
        $data = input();
        $where = [];
        $where2 = [];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time = isset($data['end_time']) ? $data['end_time'] : '';
        if ($start_time != "") {
            $where[] = ['addtime', '>=', strtotime($start_time)];
//            $where2[] = ['bet_time', '>=', strtotime($start_time)];
        }
        if ($end_time != "") {
            $where[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
//            $where2[] = ['bet_time', '<=', strtotime($end_time) + 60*60*24];
        }

        $short_name = isset($data['short_name']) ? $data['short_name'] : '0016';
        if ($short_name != '') {
            $where[] = ['platform', '=', $short_name];
            $where2[] = ['platform_code', '=', $short_name];
        }

        $cai = Db::table('cmf_game_cate')
            ->field('name,platform')
            ->where('platform', '<>', 1)
            ->select();
        $this->assign('cai', $cai);

        $list = Db::table('cmf_user_change')
            ->where($where)
            ->group("FROM_UNIXTIME(addtime,'%Y-%m-%d')")
            ->order('id desc')
            ->field("FROM_UNIXTIME(addtime,'%Y-%m-%d') date,sum(if(change_type=23&&change_money<0,change_money,0)) recharge,sum(if(change_type=23&&change_money>0,change_money,0)) withdrawal,sum(if(change_type=7,change_money,0)) rate,sum(if(change_type=6,change_money,0)) activity")
            ->paginate(20);

        $res = $list->items();

        foreach ($res as $k => $v) {
            $info = Db::table('cmf_game_record')
                ->where($where2)
                ->where("bet_time", '>=', strtotime($v['date']))
                ->where("bet_time", '<', strtotime($v['date']) + 3600 * 24)
                ->field('sum(pay_off) pay_off,sum(bet_amount) bet_amount,sum(profit) profit')
                ->find();
            $res[$k]['pay_off'] = is_null($info['pay_off']) ? 0 : $info['pay_off'];
            $res[$k]['bet_amount'] = is_null($info['bet_amount']) ? 0 : $info['bet_amount'];
            $res[$k]['yin'] = is_null($info['profit']) ? 0 : $info['profit'] + $v['rate'] + $v['activity'];


        }
//
//        $list2 = Db::table('cmf_game_record')
//            ->where($where)
//            ->group("FROM_UNIXTIME(bet_time,'%Y-%m-%d')")
//            ->order('id desc')
//            ->field("sum(pay_off) pay_off,sum(bet_amount) bet_amount,sum(profit) profit")
//            ->paginate(20);

//        $list = Db::table('cmf_user_change')
//            ->alias('uc')
//            ->where($where)
//            ->where($where2)
//            ->group("FROM_UNIXTIME(uc.addtime,'%Y-%m-%d')")
//            ->order('uc.id desc')
//            ->field("FROM_UNIXTIME(uc.addtime,'%Y-%m-%d') date,sum(if(change_type=23&&change_money<0,change_money,0)) recharge,sum(if(uc.change_type=23&&uc.change_money>0,change_money,0)) withdrawal,sum(if(uc.change_type=7,change_money,0)) rate,sum(if(uc.change_type=6,change_money,0)) activity")
//            ->join('cmf_game_record gr',"FROM_UNIXTIME(uc.addtime,'%Y-%m-%d') = FROM_UNIXTIME(gr.bet_time,'%Y-%m-%d')")
//            ->paginate(20);

//        var_dump(Db::table('cmf_user_change')->getLastSql());die;

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $res);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    //彩票综合报表导出
    function export(){
        $data = input();
        $where = [];
        $where2 = [];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time = isset($data['end_time']) ? $data['end_time'] : '';
        if ($start_time != "") {
            $where[] = ['addtime', '>=', strtotime($start_time)];
//            $where2[] = ['bet_time', '>=', strtotime($start_time)];
        }
        if ($end_time != "") {
            $where[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
//            $where2[] = ['bet_time', '<=', strtotime($end_time) + 60*60*24];
        }

        $short_name = isset($data['short_name']) ? $data['short_name'] : '0016';
        if ($short_name != '') {
            $where[] = ['platform', '=', $short_name];
            $where2[] = ['platform_code', '=', $short_name];
        }

        $cai = Db::table('cmf_game_cate')
            ->field('name,platform')
            ->where('platform', '<>', 1)
            ->select();
        $this->assign('cai', $cai);

        $list = Db::table('cmf_user_change')
            ->where($where)
            ->group("FROM_UNIXTIME(addtime,'%Y-%m-%d')")
            ->order('id desc')
            ->field("FROM_UNIXTIME(addtime,'%Y-%m-%d') date,sum(if(change_type=23&&change_money<0,change_money,0)) recharge,sum(if(change_type=23&&change_money>0,change_money,0)) withdrawal,sum(if(change_type=7,change_money,0)) rate,sum(if(change_type=6,change_money,0)) activity")
            ->paginate(20);

        $res = $list->items();

        foreach ($res as $k => $v) {
            $info = Db::table('cmf_game_record')
                ->where($where2)
                ->where("bet_time", '>=', strtotime($v['date']))
                ->where("bet_time", '<', strtotime($v['date']) + 3600 * 24)
                ->field('sum(pay_off) pay_off,sum(bet_amount) bet_amount,sum(profit) profit')
                ->find();
            $res[$k]['pay_off'] = is_null($info['pay_off']) ? 0 : $info['pay_off'];
            $res[$k]['bet_amount'] = is_null($info['bet_amount']) ? 0 : $info['bet_amount'];
            $res[$k]['yin'] = is_null($info['profit']) ? 0 : $info['profit'] + $v['rate'] + $v['activity'];


        }

        $xlsName  = "三方游戏报表";

        $action="三方游戏报表导出：".Db::name("cmf_game_record")->getLastSql();
        setAdminLog($action);

        $cellName = array('A','B','C','D','E','F','G','H');
        $xlsCell  = array(
            array('date','日期'),
            array('recharge','存款'),
            array('withdrawal','取款'),
            array('pay_off','派彩'),
            array('bet_amount','有效投注'),
            array('rate','返点'),
            array('activity','活动'),
            array('yin','盈亏'),
        );
        exportExcel($xlsName,$xlsCell,$res,$cellName);
    }

    //团队结算报表
    public function team_report()
    {
        // 渲染模板输出
        return $this->fetch();
    }

    //团队数据接口(展示团体)
    public function team_data(Request $request)
    {
        $data = input();
        $limit = $data['limit'];
        $page = $data['page'];

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
//            $user = Db::name('user')->where('user_login', $uid)->find();
//            $where[] = ['u.invite_level','like',$user['invite_level'].'%'];
            $where[] = ['u.mobile','=',$uid];
        }else{
//            $where[] = ['u.invite_level','not like','%-%'];
            $where[] = ['u.parent_id','=','0'];
            $where[] = ['u.iszombiep','=','0'];
            $where[] = ['u.iszombie','=','0'];
        }

        $list = Db::table('cmf_user')
            ->alias('u')
            ->field('u.id,u.user_login,u.is_dai,u.invite_level,u.mobile')
            ->group('u.id')
            ->where($where)
            ->paginate($limit,false,['page'=>$page])->items();
//            ->paginate(20)->items();

        foreach ($list as $k => $v){
            $temp = Db::table('cmf_user')->where('invite_level','like',$v['invite_level'].'%')->field('id,user_login')->select();
            $ids=[];
            $users = [];
            $level_count = 0;
            foreach ($temp as $val){
                $ids[] = $val['id'];
                $users[] = $val['user_login'];
                $level_count++;
            }
            $list[$k]['level_count'] = $level_count;
            $change = Db::table('cmf_user_change')
                ->alias('uc')
                ->where('user_id','in',$ids)
                ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
                ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
                ->field('sum(if(uc.change_type=23&&change_money<0,uc.change_money,0)) recharge')
                ->field('sum(if(uc.change_type=23&&change_money>0,uc.change_money,0)) withdrawal')
                ->find();

            $record = Db::table('cmf_game_record')
                ->where('user_login','in',$users)
                ->field('sum(pay_off) bonus,sum(bet_amount) xia,sum(profit) profit')
                ->find();

            $list[$k]['recharge'] = is_null($change['recharge']) ? '0.0000' : $change['recharge'];
            $list[$k]['withdrawal'] = is_null($change['withdrawal']) ? '0.0000' : $change['withdrawal'];
            $list[$k]['bonus'] = is_null($record['bonus']) ? '0.0000' : $record['bonus'];
            $list[$k]['rate'] = is_null($change['rate']) ? '0.0000' : $change['rate'];
            $list[$k]['xia'] = is_null($record['xia']) ? '0.0000' : $record['xia'];
            $list[$k]['activity'] = is_null($change['activity']) ? '0.0000' : $change['activity'];
            $list[$k]['yin'] = $change['rate'] + $change['activity'] + $record['profit'];
            $list[$k]['pin_yin'] = -1 * ($change['rate'] + $change['activity'] + $record['profit']);
        }

        $count = Db::table('cmf_user')
            ->alias('u')
            ->group('u.id')
            ->where($where)
            ->count();

        $return = [
            'code' => 0,
            'msg' => '',
            'count' => $count,
            'data' => $list
        ];
        return json($return);
    }

    //团队结算报表获取下级接口
    public function get_sub()
    {
        $data = input();
        if (!$data['user_id']) return json(['code'=>0,'msg'=>'获取失败']);
        $user_id = $data['user_id'];

        $list = Db::table('cmf_user')
            ->field('u.id,u.user_login,u.is_dai,u.invite_level')
            ->alias('u')
            ->group('u.id')
            ->where('u.parent_id','=',$user_id)
            ->select()->toArray();

        foreach ($list as $k => $v){
            $temp = Db::table('cmf_user')
                ->where('invite_level','like',$v['invite_level'].'%')
                ->field('id,user_login')->select();
            $ids=[];
            $users = [];
            $level_count = 0;
            foreach ($temp as $val){
                $ids[] = $val['id'];
                $users[] = $val['user_login'];
                $level_count++;
            }
            $list[$k]['level_count'] = $level_count;
            $change = Db::table('cmf_user_change')
                ->alias('uc')
                ->where('user_id','in',$ids)
                ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
                ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
                ->field('sum(if(uc.change_type=23&&change_money<0,uc.change_money,0)) recharge')
                ->field('sum(if(uc.change_type=23&&change_money>0,uc.change_money,0)) withdrawal')
                ->find();

            $record = Db::table('cmf_game_record')
                ->where('user_login','in',$users)
                ->field('sum(pay_off) bonus,sum(bet_amount) xia,sum(profit) profit')
                ->find();

            $list[$k]['recharge'] = is_null($change['recharge']) ? '0.0000' : $change['recharge'];
            $list[$k]['withdrawal'] = is_null($change['withdrawal']) ? '0.0000' : $change['withdrawal'];
            $list[$k]['bonus'] = is_null($record['bonus']) ? '0.0000' : $record['bonus'];
            $list[$k]['rate'] = is_null($change['rate']) ? '0.0000' : $change['rate'];
            $list[$k]['xia'] = is_null($record['xia']) ? '0.0000' : $record['xia'];
            $list[$k]['activity'] = is_null($change['activity']) ? '0.0000' : $change['activity'];
            $list[$k]['yin'] = $change['rate'] + $change['activity'] + $record['profit'] . '';
            $list[$k]['pin_yin'] = -1 * ($change['rate'] + $change['activity'] + $record['profit']) . '';
        }
//        dump($list);die;
        if ($list){
            return json(['code'=>1,'msg'=>'获取成功','data'=>$list]);
        }else{
            return json(['code'=>0,'msg'=>'无下级']);
        }

    }
}