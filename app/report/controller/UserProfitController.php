<?php
namespace app\report\controller;

use cmf\controller\AdminBaseController;
use TencentCloud\Dcdb\V20180411\Models\DBAccount;
use think\Db;
use think\Request;

class UserProfitController extends AdminBaseController
{
    //用户盈亏报表
    public function index(){
        $data = input();
        $where = [];

        $user_login = isset($data['user_login']) ? $data['user_login']: '';
        if($user_login != ''){
            $where[] = ['user_login', '=', $user_login];
        }

        $short_name = isset($data['short_name']) ? $data['short_name']: '';
        if($short_name != ''){
            $where[] = ['short_name', '=', $short_name];
        }

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $cai = Db::table('cmf_game_caizhong')
            ->field('short_name,show_name')
            ->group('short_name')
            ->select();
        $this->assign('cai', $cai);

        $list = Db::table('cmf_game_ticket')
            ->alias('gt')
            ->group("gt.user_id,gt.short_name,FROM_UNIXTIME(gt.addtime,'%Y-%m-%d')")
            ->join('cmf_user u','u.id=gt.user_id')
            ->where($where)
            ->order('gt.id desc')
            ->field("u.id,u.user_login,FROM_UNIXTIME(gt.addtime,'%Y-%m-%d') date,gt.short_name,gt.show_name,sum(gt.prize) bonus,sum(gt.money) money,sum(gt.prize) - sum(gt.money) yin")
            ->paginate(20);
        $list_count = Db::table('cmf_game_ticket')->where($where)->field('sum(money) money, sum(prize) prize, sum(prize) - sum(money) yin')->find();    
        $user_nums = Db::table('cmf_game_ticket')->where($where)->field('id,user_id')->group('user_id')->count();
        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('list_count', $list_count);
        $this->assign('user_nums', $user_nums);
        // 渲染模板输出
        return $this->fetch();
    }

    //系统综合报表
    public function system_report()
    {
        $data = input();
        $where = [];

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $list = Db::table('cmf_user_change')
            ->where($where)
            ->group("FROM_UNIXTIME(addtime,'%Y-%m-%d')")
            ->order('id desc')
            ->field("FROM_UNIXTIME(addtime,'%Y-%m-%d') date,sum(if(change_type=1,change_money,0)) recharge,sum(if(change_type=2,change_money,0)) withdrawal,sum(if(change_type=3&&change_money>0,change_money,0)) bonus,sum(if(change_type=3&&change_money<0,change_money,0)) xia,sum(if(change_type=7,change_money,0)) rate,sum(if(change_type=6,change_money,0)) activity,-1*sum(if(change_type in (3,6,7),change_money,0)) yin")
            ->paginate(20);

        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    function export(){
        $data = input();
        $where = [];

        $user_login = isset($data['user_login']) ? $data['user_login']: '';
        if($user_login != ''){
            $where[] = ['user_login', '=', $user_login];
        }

        $short_name = isset($data['short_name']) ? $data['short_name']: '';
        if($short_name != ''){
            $where[] = ['short_name', '=', $short_name];
        }

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $lists = Db::table('cmf_game_ticket')
            ->alias('gt')
            ->group("gt.user_id,gt.short_name,FROM_UNIXTIME(gt.addtime,'%Y-%m-%d')")
            ->join('cmf_user u','u.id=gt.user_id')
            ->where($where)
            ->order('gt.id desc')
            ->field("u.id,u.user_login,FROM_UNIXTIME(gt.addtime,'%Y-%m-%d') date,gt.short_name,gt.show_name,sum(gt.prize) bonus,sum(gt.money) money,sum(gt.prize) - sum(gt.money) yin")
            ->all();
        $xlsName  = "彩票用户盈亏报表";
        $action="彩票用户盈亏报表导出：".Db::name("cmf_game_ticket")->getLastSql();
        setAdminLog($action);

        $cellName = array('A','B','C','D','E','F');
        $xlsCell  = array(
            array('id','id'),
            array('date','日期'),
            array('show_name','彩种'),
            array('bonus','奖金'),
            array('money','下注'),
            array('yin','盈亏'),
        );
        exportExcel($xlsName,$xlsCell,$lists,$cellName);
    }

    //团队结算报表
//    public function team_report()
//    {
//        $data = input();
//        $where = [];
//
//        $list = Db::table('cmf_user_change')
//            ->alias('uc')
//            ->group('uc.user_id')
//            ->where('u.invite_level','not like','%-%')
//            ->join('cmf_user u','uc.user_id = u.id')
//            ->field('uc.user_id,u.user_login,u.is_dai')
//            ->field('sum(if(uc.change_type=1,uc.change_money,0)) recharge')
//            ->field('sum(if(uc.change_type=2,uc.change_money,0)) withdrawal')
//            ->field('sum(if(uc.change_type=3&&uc.change_money>0,uc.change_money,0)) bonus')
//            ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
//            ->field('sum(if(uc.change_type=3&&uc.change_money<0,uc.change_money,0)) xia')
//            ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
//            ->field('sum(if(uc.change_type in (3,6,7),uc.change_money,0)) yin')
//            ->field('-1*sum(if(uc.change_type in (3,6,7),uc.change_money,0)) pin_yin')
//
//            ->paginate(20);
//
//        $list->appends($data);
//        $page = $list->render();
//        $this->assign('list', $list);
//        $this->assign('page', $page);
//        // 渲染模板输出
//        return $this->fetch();
//    }


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
//        dump($data);die;
        $limit = $data['limit'];
        $page = $data['page'];
//        $limit = 10;
//        $page = 1;

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
//            $user = Db::name('user')->where('user_login', $uid)->find();
//            $where[] = ['u.invite_level','like',$user['invite_level'].'%'];
            $where[] = ['u.id','=',$uid];
        }else{
//            $where[] = ['u.invite_level','not like','%-%'];
            $where[] = ['u.parent_id','=',0];
            $where[] = ['u.iszombiep','=','0'];
            $where[] = ['u.iszombie','=','0'];
        }
//        dump($where);die;
        $map = [];
        $start = isset($data['start']) ? $data['start']: '';
        $end = isset($data['end']) ? $data['end']: '';
        if ($start){
            $start = strtotime($start);
            $map[] = ['uc.addtime','>=',$start];
        }
        if ($end){
            $end = strtotime($end);
            $map[] = ['uc.addtime','<=',$end];
        }
//        dump($map);die;
        $list = Db::table('cmf_user')
            ->alias('u')
            ->field('u.id,u.user_login,u.is_dai,u.invite_level,u.mobile')
            ->group('u.id')
            ->where($where)
            ->paginate($limit,false,['page'=>$page])->items();
//            ->paginate(20)->items();
//        dump($list);die;
        foreach ($list as $k => $v){
            $temp = Db::table('cmf_user')->where('invite_level','like',$v['invite_level'].'%')->field('id')->select();
            $ids=[];
            $level_count = 0;
            foreach ($temp as $val){
                $ids[] = $val['id'];
                $level_count++;
            }
            $list[$k]['level_count'] = $level_count;
            $change = Db::table('cmf_user_change')
                ->alias('uc')
                ->where($map)
                ->where('user_id','in',$ids)
                ->field('sum(if(uc.change_type=1,uc.change_money,0)) recharge')
                ->field('sum(if(uc.change_type=2,uc.change_money,0)) withdrawal')
                ->field('sum(if(uc.change_type=3&&uc.change_money>0,uc.change_money,0)) bonus')
                ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
                ->field('sum(if(uc.change_type=3&&uc.change_money<0,uc.change_money,0)) xia')
                ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
                ->field('sum(if(uc.change_type in (3,6,7),uc.change_money,0)) yin')
                ->field('-1*sum(if(uc.change_type in (3,6,7),uc.change_money,0)) pin_yin')
                ->find();
            $list[$k]['recharge'] = is_null($change['recharge']) ? '0.0000' : $change['recharge'];
            $list[$k]['withdrawal'] = is_null($change['withdrawal']) ? '0.0000' : $change['withdrawal'];
            $list[$k]['bonus'] = is_null($change['bonus']) ? '0.0000' : $change['bonus'];
            $list[$k]['rate'] = is_null($change['rate']) ? '0.0000' : $change['rate'];
            $list[$k]['xia'] = is_null($change['xia']) ? '0.0000' : $change['xia'];
            $list[$k]['activity'] = is_null($change['activity']) ? '0.0000' : $change['activity'];
            $list[$k]['yin'] = is_null($change['yin']) ? '0.0000' : $change['yin'];
            $list[$k]['pin_yin'] = is_null($change['pin_yin']) ? '0.0000' : $change['pin_yin'];
            if ($start){
                $list[$k]['start'] = $start;
            }else{
                $list[$k]['start'] = '';
            }
            if ($end){
                $list[$k]['end'] = $end;
            }else{
                $list[$k]['end'] = '';
            }
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
//        $return = $list;
        return json($return);
    }

    //团队结算报表获取下级接口
    public function get_sub()
    {
        $data = input();
        if (!$data['user_id']) return json(['code'=>0,'msg'=>'获取失败']);
        $user_id = $data['user_id'];

        $map = [];
        $start = isset($data['start']) ? $data['start']: '';
        $end = isset($data['end']) ? $data['end']: '';
        if ($start){
            $start = strtotime($start);
            $map[] = ['uc.addtime','>=',$start];
        }
        if ($end){
            $end = strtotime($end);
            $map[] = ['uc.addtime','<=',$end];
        }

        $list = Db::table('cmf_user')
            ->field('u.id,u.user_login,u.is_dai,u.invite_level')
            ->alias('u')
            ->group('u.id')
            ->where('u.parent_id','=',$user_id)
            ->select()->toArray();

        foreach ($list as $k => $v){
            $temp = Db::table('cmf_user')->where('invite_level','like',$v['invite_level'].'%')->field('id')->select();
            $ids=[];
            $level_count = 0;
            foreach ($temp as $val){
                $ids[] = $val['id'];
                $level_count++;
            }
            $list[$k]['level_count'] = $level_count;
            $change = Db::table('cmf_user_change')
                ->alias('uc')
                ->where($map)
                ->where('user_id','in',$ids)
                ->field('sum(if(uc.change_type=1,uc.change_money,0)) recharge')
                ->field('sum(if(uc.change_type=2,uc.change_money,0)) withdrawal')
                ->field('sum(if(uc.change_type=3&&uc.change_money>0,uc.change_money,0)) bonus')
                ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
                ->field('sum(if(uc.change_type=3&&uc.change_money<0,uc.change_money,0)) xia')
                ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
                ->field('sum(if(uc.change_type in (3,6,7),uc.change_money,0)) yin')
                ->field('-1*sum(if(uc.change_type in (3,6,7),uc.change_money,0)) pin_yin')
                ->find();
//            dump($change);die;
            $list[$k]['recharge'] = is_null($change['recharge']) ? '0.0000' : $change['recharge'];
            $list[$k]['withdrawal'] = is_null($change['withdrawal']) ? '0.0000' : $change['withdrawal'];
            $list[$k]['bonus'] = is_null($change['bonus']) ? '0.0000' : $change['bonus'];
            $list[$k]['rate'] = is_null($change['rate']) ? '0.0000' : $change['rate'];
            $list[$k]['xia'] = is_null($change['xia']) ? '0.0000' : $change['xia'];
            $list[$k]['activity'] = is_null($change['activity']) ? '0.0000' : $change['activity'];
            $list[$k]['yin'] = is_null($change['yin']) ? '0.0000' : $change['yin'];
            $list[$k]['pin_yin'] = is_null($change['pin_yin']) ? '0.0000' : $change['pin_yin'];
            if ($start){
                $list[$k]['start'] = $start;
            }else{
                $list[$k]['start'] = '';
            }
            if ($end){
                $list[$k]['end'] = $end;
            }else{
                $list[$k]['end'] = '';
            }
        }
//        dump($list);die;
        if ($list){
            return json(['code'=>1,'msg'=>'获取成功','data'=>$list]);
        }else{
            return json(['code'=>0,'msg'=>'无下级']);
        }

    }

////    团队数据接口(展示个人)
//    public function team_data(Request $request)
//    {
//        $data = input();
//
//        $uid = isset($data['uid']) ? $data['uid']: '';
//        if($uid != ''){
//            $user = Db::name('user')->where('user_login', $uid)->find();
////            $where[] = ['uc.user_id','=',$user['id']];
//            $where[] = ['u.invite_level','like',$user['invite_level'].'%'];
//        }else{
//            $where[] = ['u.invite_level','not like','%-%'];
//        }
//
//        $list = Db::table('cmf_user_change')
//            ->alias('uc')
//            ->group('uc.user_id')
//            ->where($where)
//            ->join('cmf_user u','uc.user_id = u.id')
//            ->field('u.id,u.user_login,u.is_dai')
//            ->field('sum(if(uc.change_type=1,uc.change_money,0)) recharge')
//            ->field('sum(if(uc.change_type=2,uc.change_money,0)) withdrawal')
//            ->field('sum(if(uc.change_type=3&&uc.change_money>0,uc.change_money,0)) bonus')
//            ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
//            ->field('sum(if(uc.change_type=3&&uc.change_money<0,uc.change_money,0)) xia')
//            ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
//            ->field('sum(if(uc.change_type in (3,6,7),uc.change_money,0)) yin')
//            ->field('-1*sum(if(uc.change_type in (3,6,7),uc.change_money,0)) pin_yin')
//            ->paginate(20);
//
//        $count = Db::table('cmf_user_change')
//            ->alias('uc')
//            ->group('uc.user_id')
//            ->where($where)
//            ->where('u.invite_level','not like','%-%')
//            ->join('cmf_user u','uc.user_id = u.id')
//            ->count();
//
//        $return = [
//            'code' => 0,
//            'msg' => '',
//            'count' => $count,
//            'data' => $list->items()
//        ];
//        return json($return);
//    }
//
//    //团队结算报表获取下级接口
//    public function get_sub()
//    {
//        $data = input();
//        if (!$data['user_id']) return json(['code'=>0,'msg'=>'获取失败']);
//        $user_id = $data['user_id'];
////        $invite_level = Db::table('cmf_user')->where('id',$user_id)->field('invite_level')->find()['invite_level'];
//
//        $list = Db::table('cmf_user')
//            ->alias('u')
//            ->group('u.id')
////            ->where('u.invite_level','like',$invite_level.'-%')
//            ->where('u.parent_id','=',$user_id)
//            ->join('cmf_user_change uc','uc.user_id = u.id','left')
//            ->field('u.id,u.user_login,u.is_dai')
//            ->field('sum(if(uc.change_type=1,uc.change_money,0)) recharge')
//            ->field('sum(if(uc.change_type=2,uc.change_money,0)) withdrawal')
//            ->field('sum(if(uc.change_type=3&&uc.change_money>0,uc.change_money,0)) bonus')
//            ->field('sum(if(uc.change_type=7,uc.change_money,0)) rate')
//            ->field('sum(if(uc.change_type=3&&uc.change_money<0,uc.change_money,0)) xia')
//            ->field('sum(if(uc.change_type=6,uc.change_money,0)) activity')
//            ->field('sum(if(uc.change_type in (3,6,7),uc.change_money,0)) yin')
//            ->field('-1*sum(if(uc.change_type in (3,6,7),uc.change_money,0)) pin_yin')
//            ->paginate(20);
//        $return = $list->items();
//        if ($return){
//            return json(['code'=>1,'msg'=>'获取成功','data'=>$return]);
//        }else{
//            return json(['code'=>0,'msg'=>'无下级']);
//        }
//
//    }
}