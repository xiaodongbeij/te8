<?php


namespace app\user\controller;


use app\user\model\User;
use app\user\model\UserBank;
use app\user\model\UserChange;
use cmf\controller\AdminBaseController;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Request;

class UserChangeController extends AdminbaseController
{
    public function index()
    {
        $data = input();
        $where = [];
        // $where[] = ['change_money','>',0];
        $change_type = isset($data['change_type']) ? $data['change_type']: '';
        if($change_type != ''){
            $where[] = ['change_type', '=', $change_type];
        }
        $iszombie = isset($data['iszombie']) ? $data['iszombie']: '';
        
        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
            $where[] = ['user_id','=',$uid];
        }

        $tid = isset($data['tid']) ? $data['tid']: '';
        if($tid != ''){
            $where[] = ['touid','=',$tid];
        }

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        
        $list = UserChange::withJoin(['iszombie' => function($query)use($iszombie){
            if($iszombie !== '')
            {
                $query->where('iszombie',$iszombie);
            }
        }])->where($where)->order('id desc')->paginate(20);
        
        $usersql = Db::name("user")->where('iszombie',$iszombie)->field('id ids,iszombie')->where('iszombie',$iszombie)->buildSql();

        $list_count = Db::name('user_change')->where($where)->alias('uc')->join([$usersql => 'u'],'u.ids=uc.user_id')->field('count(*) num, sum(change_money) money')->find();
        
        $user_nums = Db::name('user_change')->where($where)->alias('uc')->join([$usersql => 'u'],'u.ids=uc.user_id')->field('count(*) num, sum(change_money) money')->group('user_id')->count();
        // $list_count = UserChange::join()->where($where)->field('count(*) num, sum(change_money) money')->find();
        // $user_nums = UserChange::where($where)->field('id,user_id')->group('user_id')->count();

        $change_type_list = [
            1 => '充值',
            2 => '提现',
            3 => '彩票',
            4 => '补单',
            5 => '会员管理转账',
            6 => '优惠赠送',
            7 => '返水',
            8 => '额度转换',
            9 => '登录奖励',
            10 => '每日任务',
            11 => '赠送礼物',
            12 => '弹幕',
            13 => '购买VIP',
            14 => '购买坐骑',
            15 => '发送红包',
            16 => '抢红包',
            17 => '开通守护',
            18 => '转盘游戏',
            19 => '转盘中奖',
            20 => '游戏下注',
            21 => '直播反水',
            22 => '邀请奖励',
            23 => '游戏存取',
            24 => '提现服务费',
            25 => '彩票下注撤销',
        ];

        $list->appends($data);
        $page = $list->render();

        $this->assign('list', $list);
        $this->assign('list_count', $list_count);
        $this->assign('user_nums', $user_nums);
        $this->assign('type_list', $change_type_list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function gameParagraph()
    {
        $data = input();
        $where = [];
        $where[] = ['change_type', '=', 23];

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
            $where[] = ['user_id','=',$uid];
        }
        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $list = UserChange::where($where)->order('id desc')->paginate(20);
        $list->appends($data);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }

    public function withdrawIndex()
    {
        $data = input();
        $where = [];
        $where[] = ['change_type', '=', 2];
        $withdraw_type = isset($data['withdraw_type']) ? $data['withdraw_type']: '';
        if($withdraw_type != '') $where[] = ['withdraw_type', '=', $withdraw_type];

        $status = isset($data['status']) ? $data['status']: '';
        if($status != '') $where[] = ['status', '=', $status];

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
            $where[] = ['user_id','=',$uid];
        }
        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $list = UserChange::where($where)->order('id desc')->paginate(20);
        $num_money = UserChange::where($where)->field("count(*) num, sum(change_money) money, sum(service_charge) service")->find();
        $user_nums = UserChange::where($where)->field('id,user_id')->group('user_id')->count();
        
        $list->appends($data);
        $page = $list->render();
        createToken();
        $this->assign('num_money', $num_money);
        $this->assign('user_nums', $user_nums);
        $this->assign('list', $list);
        $this->assign('page', $page);

        // 渲染模板输出
        return $this->fetch();
    }
    
    public function audit(Request $request)
    {
        if($request->isPost()){
            $data = input();
            
            if(!cmf_google_token_check(session('google_token'),$data['google_token'])) {
                $this->error('令牌验证失败');
            }
            $res = judgeToken($data['TOKEN']);
            if(!$res) $this->error("请勿重复提交");
            $res = UserChange::where('id', '=', $data['id'])->update(['status' => 1, 'audit_time' => time()]);
            if ($res) {
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
        createToken();
        $this->assign('id', input('id'));
        $this->assign('status', 1);
        return $this->fetch('withdraw_edit');
        
    }

    public function examine(Request $request)
    {
        if($request->isPost()){
            $data = input();

//            if(count($data) > 1) $this->error('禁止批量操作');
            $res = judgeToken($data['TOKEN']);
            if(!$res) return $this->error("请勿重复提交");
            
            if(!cmf_google_token_check(session('google_token'),$data['google_token'])) {
                $this->error('令牌验证失败');
            }
            
            $w = UserChange::where('id', $data['id'])->where('status', 1)->where('change_type',2)->find();
            if($w['status'] != 1) $this->error('请勿此操作');
    
            //开启事务
            Db::startTrans();
    
            $user_info = User::where('id', $w['user_id'])->find();
       
            if(abs($user_info['freeze_money']) < abs($w['change_money'])) $this->error("冻结资金不足");
            $user_info->freeze_money = $user_info['freeze_money'] + $w['change_money'] + $w['service_charge'];
            $user_info->count_Withdrawal -= $w['change_money'];
            $res1 = $user_info->save();
    
            $w->status = 4;
            $w->examine_time = time();
            $res2 = $w->save();
    
            $insert = [
                'user_id' => $user_info['id'],
                'change_type' => 24,
    //            'money' => $info['coin'],
    //            'next_money' => $coin,
                'change_money' => $w['service_charge'],
                'withdraw_id' => $w['id'],
                'remark' => '提现服务费',
                'addtime' => time(),
            ];
            $res3 = UserChange::create($insert);
    
            if ($res1 && $res2 && $res3) {
                Db::commit();
                $this->success('成功');
            }else
            {
                Db::rollback();
                $this->error('失败');
            }
        }
        createToken();
        $this->assign('id', input('id'));
        $this->assign('status', 4);
        return $this->fetch('withdraw_edit');
    }

    public function withdrawRefuse(Request $request)
    {
        if($request->isPost()){
            $id = input('id');
            $remark = input('remark');
            $token = input('TOKEN');
            $res = judgeToken($token);
            if(!$res) $this->error("请勿重复提交");
            $info =  UserChange::where('id', $id)->find();
            if($info['status'] != 2) $this->error("此数据不符合拒绝要求");
            dump(1);die;

            //开启事务
            Db::startTrans();

            $user_info = User::where('id', $info['user_id'])->find();
            if(abs($user_info['freeze_money']) < abs($w['change_money'])) $this->error("冻结资金不足");

            $user_info->coin -= $info['change_money'];
            $user_info->freeze_money += $info['change_money'];
            $res2 = $user_info->save();

            $info->status = 3;
            $info->remark = $remark;
            $res1 = $info->save();

            if ($res1 && $res2) {
                Db::commit();
                $this->success('拒绝成功');
            }else
            {
                Db::rollback();
                $this->error('拒绝失败');
            }

        }
        $id = input('id');
        createToken();
        $this->assign('id', $id);

        // 渲染模板输出
        return $this->fetch();
    }

    function w_export()
    {
        $data = input();
        $where = [];
        $where[] = ['change_type', '=', 2];
        $withdraw_type = isset($data['withdraw_type']) ? $data['withdraw_type']: '';
        if($withdraw_type != '') $where[] = ['withdraw_type', '=', $withdraw_type];

        $status = isset($data['status']) ? $data['status']: '';
        if($status != '') $where[] = ['status', '=', $status];

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
            $where[] = ['user_id','=',$uid];
        }
        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $xlsName  = "提现管理";
        $xlsData = Db::name("user_change")
            ->where($where)
            ->order("id desc")
            ->select()
            ->toArray();

        $withdraw_type_list = [
            1 => '银行卡',
            2 => '微信',
        ];

        $status_list = [
            1 => '已审核',
            2 => '未审核',
            3 => '拒绝',
            4 => '已打款',
        ];


        foreach ($xlsData as $k => $v){
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);
            if($v['audit_time']) $xlsData[$k]['audit_time']=date("Y-m-d H:i:s",$v['audit_time']);
            if($v['examine_time']) $xlsData[$k]['examine_time']=date("Y-m-d H:i:s",$v['examine_time']);
            if($v['withdraw_type']){
                $xlsData[$k]['withdraw_type'] = $withdraw_type_list[$v['withdraw_type']];
            }
            if($v['status']){
                $xlsData[$k]['status'] = $status_list[$v['status']];
            }
        }

        $action="导出提现记录：".Db::name("user_change")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
        $xlsCell  = array(
            array('id','序号'),
            array('user_id','用户ID'),
            array('change_type','变动类型'),
            array('money','变动前金额'),
            array('next_money','变动后金额'),
            array('change_money','变动金额'),
            array('service_charge','手续费'),

            array('withdraw_type','提现类型'),
            array('status','状态'),
            array('bank_name','银行卡名称'),
            array('bank_card','卡号'),
            array('real_name','持卡人真实姓名'),
            array('addtime','生成时间'),
            array('audit_time','审核时间'),
            array('examine_time','打款时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }

    function export(){
        $data = input();
        $where = [];

        $change_type = isset($data['change_type']) ? $data['change_type']: '';
        if($change_type != ''){
            $where[] = ['uc.change_type', '=', $change_type];
        }
        $iszombie = !empty($data['iszombie']) ? $data['iszombie']: 0;

        $uid = isset($data['uid']) ? $data['uid']: '';
        if($uid != ''){
            $where[] = ['uc.user_id','=',$uid];
        }

        $tid = isset($data['tid']) ? $data['tid']: '';
        if($tid != ''){
            $where[] = ['uc.touid','=',$tid];
        }

        $start_time = isset($data['start_time']) ? $data['start_time']: '';
        $end_time = isset($data['end_time']) ? $data['end_time']: '';
        if($start_time != ""){
            $where[] = ['uc.addtime', '>=', strtotime($start_time)];
        }
        if($end_time != ""){
            $where[] = ['uc.addtime', '<=', strtotime($end_time) + 60*60*24];
        }

        $xlsName  = "资金动向";
 

        $change_type_list = [
            1 => '充值',
            2 => '提现',
            3 => '彩票',
            4 => '补单',
            5 => '会员管理转账',
            6 => '优惠赠送',
            7 => '返水',
            8 => '额度转换',
            9 => '登录奖励',
            10 => '每日任务',
            11 => '赠送礼物',
            12 => '弹幕',
            13 => '购买VIP',
            14 => '购买坐骑',
            15 => '发送红包',
            16 => '抢红包',
            17 => '开通守护',
            18 => '转盘游戏',
            19 => '转盘中奖',
            20 => '游戏下注',
            21 => '直播反水',
            22 => '邀请奖励',
            23 => '游戏存取',
            24 => '提现服务费',
            25 => '彩票下注撤销',
        ];

        $withdraw_type_list = [
            1 => '银行卡',
            2 => '微信',
        ];

        $status_list = [
            1 => '已审核',
            2 => '未审核',
            3 => '拒绝',
            4 => '已打款',
        ];
        
        $platform = [
            '1' => '彩票',
            '2' => '直播',
            '0016' => '开元棋牌',
            '0004' => 'AG游戏',
            '0027' => 'OG游戏',
            '0002' => 'PT游戏',
            '0024' => '速博体育',
            '0035' => '泛亚电竞',
        ];
        
        $xlsData = [];
         
        $usersql = Db::name("user")->where('iszombie',$iszombie)->field('id ids,iszombie')->where('iszombie',$iszombie)->buildSql();

        ini_set ("memory_limit","-1");
        set_time_limit(0);
        Db::name('user_change')->where($where)->alias('uc')->join([$usersql => 'u'],'u.ids=uc.user_id')->chunk(10,function($list)use($change_type_list,$withdraw_type_list,$status_list,&$xlsData,$platform){
            foreach ($list as $k => $v){
                if($v['platform'])
                {

                    $v['platform'] = $platform[$v['platform']];
                }
                $v['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
                $v['iszombie'] = $v['iszombie'] == 1 ? '是' : '否' ;
                $v['status'] = $v['change_type'] != 2 ? '' : $status_list[$v['status']];
                if($v['change_type'] == 2){
                    if($v['withdraw_type']){
                        $v['withdraw_type'] = $withdraw_type_list[$v['withdraw_type']];
                    }
    
                }else{
                    $v['change_type'] = $change_type_list[$v['change_type']];
                }
                $xlsData[] = $v;
                
            }
 
        });
   
        
        $action="导出资金动向记录：".Db::name("user_change")->getLastSql();
        setAdminLog($action);
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q');
        $xlsCell  = array(
            array('id','序号'),
            array('user_id','用户ID'),
            array('iszombie','机器人'),
            array('touid','对方ID'),
            array('change_type','变动类型'),
            array('money','变动前金额'),
            array('next_money','变动后金额'),
            array('change_money','变动金额'),
            array('service_charge','手续费'),
            array('remark','备注'),
            array('addtime','生成时间'),
            array('withdraw_type','提现类型'),
            array('status','状态'),
            array('platform','三方游戏平台号'),
            array('bank_name','银行卡名称'),
            array('bank_card','卡号'),
            array('real_name','持卡人真实姓名'),
        );
        
    
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
}