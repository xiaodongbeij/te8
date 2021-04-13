<?php

class Domain_Daili
{

    //获取开户中心列表
//    public function getOpenCenter($uid, $date_type, $date, $page, $page_size)
    public function getOpenCenter($uid, $page, $page_size)
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
//        $url = DI()->config->get('app.daili_invite');
        $con = getConfigPub();
        $url = $con['app_android'] . '?invite=';
        $list = DI()->notorm->user_invite
            ->where('uid=?', $uid)
            ->limit(($page - 1) * $page_size, $page_size)
            ->fetchAll();
        $list_count = DI()->notorm->user_invite
            ->where('uid=?', $uid)
            ->count();
        if (!$list) {
            $rs['code'] = 1001;
            $rs['msg'] = '暂无信息';
            return $rs;
        }
//        $start = $date;
//        if ($date_type == 'day') {
//            $end = $start + 3600 * 24;
//        } elseif ($date_type == 'week') {
//            $end = $start + 3600 * 24 * 7;
//        } elseif ($date_type == 'month') {
//            $year = date('Y', $date);
//            $month = date('m', $date);
//            $day = getDaysInMonth($year, $month);
//            $end = $start + 3600 * 24 * $day;
//        }
        $start = strtotime(date('Y-m-d', time()));
        $end = strtotime(date('Y-m-d', time())) + 86400;
        foreach ($list as $k => $v) {
            $list[$k]['invite_url'] = $url . $v['invite_key'];

            //返点单位换算
            $list[$k]['rate'] = $v['rate'] * 100 . '';

            //统计人数
            //总人数
            $count = DI()->notorm->user
                ->where('invite_key=?', $v['invite_key'])
                ->count();
            //新增人数
            $new = DI()->notorm->user
                ->where('invite_key=?', $v['invite_key'])
                ->where('create_time >=?', $start)
                ->where('create_time <=?', $end)
                ->count();
            $list[$k]['count'] = $count;
            $list[$k]['new'] = $new;
        }
        $rs['count'] = $list_count;
        $rs['info'] = $list;
        return $rs;
    }

    //生成邀请码
    public function addOpenCenter($uid, $rate)
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
//        $info = DI()->notorm->user
//            ->where('id=?', $uid)
//            ->select('rate')
//            ->fetchOne();

        //彩票
        $info = DI()->notorm->user_rate
                ->where('user_id',$uid)
                ->where('platform',1)
                ->select('rate')
            ->fetchOne();

//        var_dump($info);die;
        if ($rate >= $info['rate'] * 100) {
            $rs['code'] = 1001;
            $rs['msg'] = '返点不能大于自身返点';
            return $rs;
        }
        //生成key
        do {
            $unique = false;
            $key = random();
            $rs_row = DI()->notorm->user_invite
                ->where('invite_key', $key)
                ->fetchOne();
            if (!empty($rs_row)) $unique = true;
        } while ($unique);

        $insert = [
            'uid' => $uid,
            'rate' => $rate / 100,
            'invite_key' => $key,
        ];
        $res = DI()->notorm->user_invite->insert($insert);
        $id = DI()->notorm->user_invite->insert_id();
        if ($res) {
            $rs['msg'] = '生成邀请码成功';
            setAdminLog('生成邀请码:id'.$id,$uid,2);
        } else {
            $rs['code'] = 1002;
            $rs['msg'] = '生成邀请码失败';
        }
        return $rs;
    }

    //删除邀请码
    public function delOpenCenter($uid,$id)
    {
        $res = DI()->notorm->user_invite->where('id=?', $id)->delete();
        if ($res) {
            setAdminLog('删除邀请码：id-'.$id,$uid,2);
            $rs['code'] = 0;
            $rs['msg'] = '删除成功';
        } else {
            $rs['code'] = 1001;
            $rs['msg'] = '删除失败';
        }
        return $rs;
    }

//    //会员列表
//    public function getMemberList($uid, $account, $type, $start, $end,$page,$page_size)
//    {
//
//        $sql = "select u.id,u.user_login,u.is_dai,u.create_time,u.invite_level,u.coin,ur.ticket_rate,ur.live_rate,ur.ag_rate from cmf_user u join cmf_user_rate ur on u.id = ur.user_id where u.invite_level like :invite_level";
//        $params = [':invite_level' => $uid.'-%'];
//        if ($account) {
//            $sql .= ' and u.user_login = :account';
//            $params[':account'] = $account;
//        }
//        if ($type) {
//            $sql .= ' and u.is_dai = :type';
//            $params[':type'] = $type;
//        }
//        if ($start) {
//            $sql .= ' and u.create_time >= :start';
//            $params[':start'] = strtotime($start);
//        }
//        if ($end) {
//            $sql .= ' and u.create_time <= :end';
//            $params[':end'] = strtotime($end);
//        }
//        $temp = DI()->notorm->user->queryAll($sql, $params);
//        $count = count($temp);//总数
//        if ($page && $page_size) {
//            $sql .= ' limit :page,:page_size';
//            $params[':page'] = ($page - 1) * $page_size ;
//            $params[':page_size'] = $page_size;
//        }
//        $list = DI()->notorm->user->queryAll($sql, $params);
//
//        $user = '';
//
//        foreach ($list as $k => $v) {
//            $user .= $v['id'];
////            $list[$k]['ticket_rate'] = number_format($v['ticket_rate'] * 100, 1);
////            $list[$k]['live_rate'] = number_format($v['live_rate'] * 100, 1);
////            $list[$k]['ag_rate'] = number_format($v['ag_rate'] * 100, 1);
//            if ($v['is_dai'] == 1) {
//                $list[$k]['level'] = '代理';
//            } else {
//                $list[$k]['level'] = '会员';
//            }
//            //下级人数
//            $list[$k]['count'] = DI()->notorm->user
//                ->where('invite_level LIKE ?', $v['invite_level'] . '-%')
//                ->count();
//            $list[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
//            unset($list[$k]['invite_level']);
//        }
////        //查找返点
////        $user = substr($user,0,-1);
////        $sql2 = "select * from cmf_user_rate";
//
//        $rs['code'] = 0;
//        $rs['msg'] = '获取成功';
//        $rs['info'] = $list;
//        $rs['count'] = $count;
//        return $rs;
//    }

    //会员列表
    public function getMemberList($uid, $account, $type, $start, $end, $page, $page_size)
    {
    $user = DI()->notorm->user->where('id',$uid)->fetchOne();
    $where = "id <> $uid and invite_level like '".$user['invite_level']."%'";
    if ($account){
        $where .= " and id = $account";
    }
    if ($type){
        $where .= " and is_dai = $type";
    }
    if ($start){
        $start = strtotime($start);
        $where .= " and create_time >= $start";
    }
    if ($end){
        $end = strtotime($end);
        $where .= " and create_time <= $end";
    }
//    var_dump($where);die;
    $temp = DI()->notorm->user->where("$where")->count();
                if (!$temp){
            $rs['code'] = 1001;
            $rs['msg'] = '暂无用户';
            $rs['info'] = [];
            return $rs;
        }
    $count = $temp;//总数
//        var_dump($where);
//        var_dump(($page-1) * $page_size);
//        var_dump($page_size);die;

    $list = DI()->notorm->user
        ->where("$where")
        ->order('id desc')
        ->limit(($page-1) * $page_size,$page_size)
        ->fetchAll();
    if (!$list){
        $rs['code'] = 1001;
        $rs['msg'] = '暂无更多用户';
        $rs['info'] = [];
        return $rs;
    }
////        $sql = "select u.id,u.user_login,u.is_dai,u.create_time,u.invite_level,u.coin,ur.type,ur.rate from cmf_user u right join cmf_user_rate ur on u.id = ur.user_id where u.invite_level like :invite_level";
//        $sql = "select id,user_nicename,user_login,is_dai,create_time,invite_level,coin from cmf_user where parent_id = :parent_id";
////        $params = [':invite_level' => $uid . '-%'];   //所有
//        $params = [':parent_id' => $uid ];  //一级
//        if ($account) {
//            $sql .= ' and user_login = :account';
//            $params[':account'] = $account;
//        }
//        if ($type) {
//            $sql .= ' and is_dai = :type';
//            $params[':type'] = $type;
//        }
//        if ($start) {
//            $sql .= ' and create_time >= :start';
//            $params[':start'] = strtotime($start);
//        }
//        if ($end) {
//            $sql .= ' and create_time <= :end';
//            $params[':end'] = strtotime($end);
//        }
//        $temp = DI()->notorm->user->queryAll($sql, $params);
//        if (!$temp){
//            $rs['code'] = 1001;
//            $rs['msg'] = '暂无用户';
//            $rs['info'] = [];
//            return $rs;
//        }
//        $count = count($temp);//总数
//        if ($page && $page_size) {
//            $sql .= ' limit :page,:page_size';
//            $params[':page'] = ($page - 1) * $page_size;
//            $params[':page_size'] = $page_size;
//        }
////        var_dump($sql);die;
//        $list = DI()->notorm->user->queryAll($sql, $params);
        $user = '';

        foreach ($list as $k => $v) {

            //是否直属
            if ($v['parent_id'] == $uid){
                $list[$k]['is_one'] = 1;
            }else{
                $list[$k]['is_one'] = 0;
            }

            $user .= $v['id'] . ',';
            if ($v['is_dai'] == 1) {
                $list[$k]['level'] = '代理';
            } else {
                $list[$k]['level'] = '会员';
            }
            //下级人数
            $list[$k]['count'] = DI()->notorm->user
                ->where('invite_level LIKE ?', $v['invite_level'] . '-%')
                ->where('id <> ?',$v['id'])
                ->count();
            $list[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            unset($list[$k]['invite_level']);
        }
        $user = substr($user, 0, -1);
        $sql2 = "select * from cmf_user_rate where user_id in (" . $user . ")";
        $user_rate = DI()->notorm->user->queryAll($sql2);

        //获取自身返点
        $rate = DI()->notorm->user_rate->where('user_id',$uid)->fetchAll();

        foreach ($list as $k => $v) {
            foreach ($user_rate as $val) {
                if ($v['id'] == $val['user_id']) {
                    $self_rate = 0;
                    foreach ($rate as $value){
//                        var_dump($value);die;
                        if ($value['platform'] == $val['platform']){
                            $self_rate = $value['rate'] * 100 .'';
                        }
                    }

                    $list[$k]['rates'][] = [
                        'platform' => $val['platform'],
                        'remark' => $val['remark'],
                        'rate' => $val['rate'] * 100 .'',
                        'self_rate' => $self_rate . ''
                    ];

                }
            }
        }

        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $list;
        $rs['count'] = $count;
        return $rs;
    }

    //会员修改
//    public function editMember($uid, $id, $rate,$type)
//    {
//        $info = DI()->notorm->user
//            ->where('id=?', $id)
//            ->where('invite_level LIKE ?',$uid.'-%')
//            ->fetchOne();
//        if (!$info){
//            $rs['code'] = 1001;
//            $rs['msg'] = '会员信息错误';
//            return $rs;
//        }
//        $user = DI()->notorm->user
//            ->where('id=?', $uid)
//            ->select('rate')
//            ->fetchOne();
//        if (!$user){
//            $rs['code'] = 1002;
//            $rs['msg'] = '账号信息错误';
//            return $rs;
//        }
//        if ($rate >= $user['rate'] * 1000){
//            $rs['code'] = 1003;
//            $rs['msg'] = '返点不能大于自身返点';
//            return $rs;
//        }
//        $res = DI()->notorm->user
//            ->where('id=?',$id)
//            ->update(['rate'=>$rate/1000]);
//        if ($res){
//            $rs['code'] = 0;
//            $rs['msg'] = '更新成功';
//        }else{
//            $rs['code'] = 1004;
//            $rs['msg'] = '更新失败';
//        }
//        return $rs;
//    }

    //会员返点修改
    public function editMember($uid, $id, $rate, $platform)
    {
        $info = DI()->notorm->user
            ->where('id=?', $id)
//            ->where('invite_level LIKE ?', $uid . '-%')
                ->where('parent_id',$uid)
            ->fetchOne();
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg'] = '会员信息错误';
            return $rs;
        }
        $user = DI()->notorm->user
            ->where('id=?', $uid)
            ->fetchOne();
        if (!$user) {
            $rs['code'] = 1002;
            $rs['msg'] = '账号信息错误';
            return $rs;
        }

        $user_rate = DI()->notorm->user_rate
            ->where('user_id', $user['id'])
            ->where('platform', $platform)
            ->fetchOne();

        $info_rate = DI()->notorm->user_rate
            ->where('user_id', $id)
            ->where('platform', $platform)
            ->fetchOne();

        if (!$user_rate){
            $rs['code'] = 1001;
            $rs['msg'] = '返点信息不存在';
            return $rs;
        }
        if ($rate >= $user_rate['rate'] * 100) {
            $rs['code'] = 1003;
            $rs['msg'] = '返点不能大于自身返点';
            return $rs;
        }

        if ($rate <= $info_rate['rate'] * 100) {
            $rs['code'] = 1003;
            $rs['msg'] = '修改返点不能小于等于用户当前返点';
            return $rs;
        }
        //判断下级有无返点
        $temp = DI()->notorm->user->where('invite_level like ?',$info['invite_level'].'%')
            ->where('id <> ?',$id)
            ->fetchAll();
        $down = [];
        foreach ($temp as $v){
            $down[] = $v['id'];
        }
        $temp1 = DI()->notorm->user_rate->where('user_id',$down)->where('rate > 0')->fetchOne();
        if ($temp1){
            $rs['code'] = 1005;
            $rs['msg'] = '该用户下级有返点，无法编辑';
            return $rs;
        }
        $res = DI()->notorm->user_rate
            ->where('user_id=?', $id)
            ->where('platform', $platform)
            ->update(['rate' => $rate / 100]);
        if ($res) {
            setAdminLog('修改会员返点:会员id'.$id.'平台'.$platform.'返点设为'.$rate/100,$uid,2);
            $rs['code'] = 0;
            $rs['msg'] = '更新成功';
        } else {
            $rs['code'] = 1004;
            $rs['msg'] = '更新失败';
        }
        return $rs;
    }

    //会员转账
    public function changeMoney($uid, $id, $money, $money_password)
    {
        $info = DI()->notorm->user
            ->where('id=?', $id)
//            ->where('invite_level LIKE ?', '%' . $uid . '-%')
            ->where('parent_id = ?', $uid)
            ->fetchOne();
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg'] = '会员信息错误';
            return $rs;
        }
        $user = DI()->notorm->user
            ->select('coin as user_money')
            ->where('id=?', $uid)
            ->fetchOne();
        if (!$user) {
            $rs['code'] = 1002;
            $rs['msg'] = '账号信息错误';
            return $rs;
        }
        $user_info = DI()->notorm->user_info
            ->where('user_id=?', $uid)
            ->fetchOne();
        if (!$user) {
            $rs['code'] = 1003;
            $rs['msg'] = '账号信息错误';
            return $rs;
        }
        //验证密码
        $pass_res = password_verify($money_password, $user_info['money_passwd']);
        if (!$pass_res) {
            $rs['code'] = 1004;
            $rs['msg'] = '密码错误';
            return $rs;
        }
        //验证余额
//        if ($money > $user['user_money']){
////        if ($money > $user['coin']){
//            $rs['code'] = 1005;
//            $rs['msg'] = '余额不足';
//            return $rs;
//        }

        //资金变动
        //开启事务
        DI()->notorm->beginTransaction('db_appapi');
        $res1 = user_change_action($uid, 5, -1 * $money, '会员管理转账-转出', $id);
        if ($res1 === 2) {
            DI()->notorm->rollback('db_appapi');
            $rs['msg'] = '余额不足';
            $rs['code'] = 1003;
            return $rs;
        }
        $res2 = user_change_action($id, 5, $money, '会员管理转账-转入', $uid);
//        //资金更新
//        $res1 = DI()->notorm->user
//            ->where('id=?',$uid)
//            ->update(['coin'=>$user['user_money'] - $money]);
////            ->update(['coin'=>$user['coin'] - $money]);
//        $res2 = DI()->notorm->user
//            ->where('id=?',$id)
//            ->update(['coin'=>$info['user_money'] + $money]);
////            ->update(['coin'=>$info['coin'] + $money]);
//        //更新记录
//        $insert1 = [
//            'user_id' => $uid,
//            'change_type' => 5,
//            'money' => $user['user_money'],
////            'money' => $user['coin'],
//            'next_money' => $user['user_money'] - $money,
////            'next_money' => $user['coin'] - $money,
//            'change_money' => -1 * $money,
//            'remark' => '会员管理转账-转出',
//            'addtime' => time(),
//            'contact_id' => $id
//        ];
//        $res3 = DI()->notorm->user_change->insert($insert1);
//        $insert2 = [
//            'user_id' => $id,
//            'change_type' => 5,
//            'money' => $info['user_money'],
////            'money' => $info['coin'],
//            'next_money' => $info['user_money'] + $money,
////            'next_money' => $info['coin'] + $money,
//            'change_money' => $money,
//            'remark' => '会员管理转账-转入',
//            'addtime' => time(),
//            'contact_id' => $uid
//        ];
//        $res4 = DI()->notorm->user_change->insert($insert2);
        if ($res1 && $res2) {
            DI()->notorm->commit('db_appapi');
            setAdminLog('会员转账：向会员id'.$id.'转账'.$money.'元',$uid,2);
            $rs['code'] = 0;
            $rs['msg'] = '转账成功';
            return $rs;
        } else {
            DI()->notorm->rollback('db_appapi');
            $rs['code'] = 1006;
            $rs['msg'] = '转账失败';
            return $rs;
        }
    }

    public function teamShow($uid, $platform, $start, $end)
    {
        $info = DI()->notorm->user
            ->where('id', $uid)
            ->fetchOne();

        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg'] = '信息错误';
            return $rs;
        }
        $start = strtotime($start);
        $end = strtotime($end);
        if (!is_int($start) || !is_int($end)) {
            $rs['code'] = 1001;
            $rs['msg'] = '起始日期或结束日期错误';
            return $rs;
        }
        //查找团队用户
        $users = DI()->notorm->user
            ->where('invite_level LIKE ?', $info['invite_level'] . '%')
            ->select('id,coin as user_money,create_time,user_login')
            ->fetchAll();
        $count = count($users) - 1;     //团队人数
        $reg_count = 0;     //注册人数
        $user_str = '';
        $user_login = '';
        foreach ($users as $v) {
            if ($v['create_time'] >= $start && $v['create_time'] <= $end && $users['id'] != $info['id']) {
                $reg_count++;
            }
            $user_str .= $v['id'] . ',';
            $user_login .= $v['user_login'] . ',';
        }
        $user_str = substr($user_str, 0, -1);
        $user_login = substr($user_login, 0, -1);

//        $sql = "SELECT * FROM cmf_user_change WHERE user_id in(". $user_str .")".' and addtime >= '.$start.' and addtime <= '.$end;
//        $change = DI()->notorm->user_change->queryAll($sql);
//        $change = DI()->notorm->user_change
//            ->where('user_id in?',"(".$user_str.")")
//            ->fetchAll();
        if ($platform == 1) {    //官方彩票
            $sql = "SELECT 
            sum(if(change_type=1,change_money,0)) recharge,
            sum(if(change_type=2&&status=4,change_money,0)) withdrawal,
            sum(if(change_type=3&&change_money>0,change_money,0)) zho,
            sum(if(change_type=3&&change_money<0,change_money,0)) tou,
            sum(if(change_type=7&&platform=1,change_money,0)) rate,
            sum(if(change_type=6,change_money,0)) discount,
            sum(if(change_type in (3,6,7),change_money,0)) yin 
            FROM cmf_user_change WHERE user_id in(" . $user_str . ")" . ' and addtime >= ' . $start . ' and addtime <= ' . $end;
            $change = DI()->notorm->user_change->queryAll($sql);
            $info = $change[0];
            $info['count'] = $count;
            $info['reg_count'] = $reg_count;
        } elseif ($platform == 2) {    //天鹅直播
            $sql = "SELECT 
            sum(if(change_type=1,change_money,0)) recharge, 
            sum(if(change_type=2&&status=4,change_money,0)) withdrawal,
            sum(if(change_type=11,change_money,0)) reward,
            sum(if(change_type=7&&platform=2,change_money,0)) yin,
            sum(if(change_type=6,change_money,0)) discount,
            sum(if(change_type in (11,12,13,14,17),change_money,0)) expend
            FROM cmf_user_change WHERE user_id in(" . $user_str . ")" . ' and addtime >= ' . $start . ' and addtime <= ' . $end;
            $change = DI()->notorm->user_change->queryAll($sql);
            $info = $change[0];
            $info['count'] = $count;
        } else {
            //游戏接口
            $sql = "SELECT 
            sum(if(change_type=23&&change_money<0&&platform=0016,change_money,0)) game_in,
            sum(if(change_type=23&&change_money>0&&platform=0016,change_money,0)) game_out,
            sum(if(change_type=6,change_money,0)) discount
            FROM cmf_user_change WHERE user_id in(" . $user_str . ")" . ' and addtime >= ' . $start . ' and addtime <= ' . $end;
            $change = DI()->notorm->user_change->queryAll($sql);
            $info = $change[0];
            $info['count'] = $count;
            //游戏盈亏
            $sql2 = "SELECT 
            sum(profit) yin,
            sum(pay_off) zho,
            sum(bet_amount) expend
            FROM cmf_game_record WHERE user_login in(" . $user_login . ")" . ' and bet_time >= ' . $start . ' and bet_time <= ' . $end . ' and platform_code = ' . $platform;
            $game_info = DI()->notorm->game_record->queryAll($sql2);
            $game_info = $game_info[0];
            $info['yin'] = $game_info['yin'];
            $info['zho'] = $game_info['zho'];
            $info['expend'] = $game_info['expend'];
        }

        foreach ($info as $k => $v){
            if (is_null($v)) $info[$k] = 0;
        }

        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $info;
        return $rs;
    }

    public function gameReport($uid, $platform, $start, $end)
    {

        $user_info = DI()->notorm->user
            ->where('id', $uid)
            ->fetchOne();

        $users = DI()->notorm->user
            ->where('invite_level like ?', $user_info['invite_level'] . '-%')
            ->where('id <> ?',$uid)
            ->select('create_time')
            ->fetchAll();
        $count = count($users);
        $reg_count = 0;
        foreach ($users as $v) {
            if ($v['create_time'] >= $start && $v['create_time'] <= $end) {
                $reg_count++;
            }
        }

        if ($platform == 1) {
            //官方彩票
            $info = DI()->notorm->user_change
                ->where('user_id', $uid)
                ->where('addtime >= ?', $start)
                ->where('addtime <= ?', $end)
                ->select('
            sum(if(change_type=1,change_money,0)) recharge,
            sum(if(change_type=2,change_money,0)) withdrawal,
            sum(if(change_type=3&&change_money>0,change_money,0)) zho,
            sum(if(change_type=3&&change_money<0,change_money,0)) tou,
            sum(if(change_type=7&&platform=1,change_money,0)) rate,
            sum(if(change_type=6,change_money,0)) discount,
            sum(if(change_type in (3,6,7),change_money,0)) yin')
                ->fetchOne();
            $info['count'] = $count;
            $info['reg_count'] = $reg_count;
        } elseif ($platform == 2) {
            //天鹅直播
            $info = DI()->notorm->user_change
                ->where('user_id', $uid)
                ->where('addtime >= ?', $start)
                ->where('addtime <= ?', $end)
                ->select('
            sum(if(change_type=1,change_money,0)) recharge, 
            sum(if(change_type=2,change_money,0)) withdrawal,
            sum(if(change_type=11,change_money,0)) reward,
            sum(if(change_type=21,change_money,0)) yin,
            sum(if(change_type=6,change_money,0)) discount,
            sum(if(change_type in (11,12,13,14,17),change_money,0)) expend')
                ->fetchOne();
            $info['count'] = $count;
        } else {
            //游戏接口
//            var_dump($platform);die;

            $info = DI()->notorm->user_change
                ->where('user_id', $uid)
                ->where('addtime >= ?', $start)
                ->where('addtime <= ?', $end)
                ->select('
            sum(if(change_type=23&&change_money<0&&platform='.$platform.',change_money,0)) game_in,
            sum(if(change_type=23&&change_money>0&&platform='.$platform.',change_money,0)) game_out,
            sum(if(change_type=6,change_money,0)) discount')
                ->fetchOne();
            if (!$info){
                $rs['code'] = 1001;
                $rs['msg'] = '暂无数据';
                $rs['info'] = [];
                return $rs;
            }
            $info['count'] = $count;
            //游戏盈亏
            $game_info = DI()->notorm->game_record
                ->where('user_login', $user_info['user_login'])
                ->where('bet_time >= ?', $start)
                ->where('bet_time <= ?', $end)
                ->where('platform_code = ?', $platform)
                ->select('
                    sum(profit) yin,
                    sum(pay_off) zho,
                    sum(bet_amount) expend')
                ->fetchOne();
            $info['yin'] = $game_info['yin'];
            $info['zho'] = $game_info['zho'];
            $info['expend'] = $game_info['expend'];

        }

        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $info;
        return $rs;
    }

//    public function teamShow($uid,$user_login,$start,$end)
//    {
//        if ($user_login){
//            //个人信息
//            $info = DI()->notorm->user
//                ->where('user_login',$user_login)
//                ->where('invite_level LIKE ?','%'.$uid.'-%')
//                ->fetchOne();
//        }else{
//            $info = DI()->notorm->user
//                ->where('id',$uid)
//                ->fetchOne();
//        }
//        if (!$info){
//            $rs['code'] = 1001;
//            $rs['msg'] = '信息错误';
//            return $rs;
//        }
//        //查找团队用户
//        $users = DI()->notorm->user
//            ->where('invite_level LIKE ?',$info['invite_level'].'%')
//            ->select('id,coin as user_money')
//            ->fetchAll();
//        $count = count($users) - 1;     //团队人数
//        //团队余额
//        $yu = 0;
//        $dai = -1;   //代理人数,$users里包含自身
//        $mem = 0;   //会员人数
//        $user_str = '';
//        foreach ($users as $v){
//            $user_str .= $v['id'] . ',';
//            if ($v['is_dai'] == 1){
//                $dai++;
//            }else{
//                $mem++;
//            }
//            $yu += $v['user_money'];
////            $yu += $v['coin'];
//        }
//        if ($dai < 0) $dai = 0;
//        $user_str = substr($user_str,0,-1);
//
////        $start = $date;
////        if ($date_type == 'day') {
////            $end = $start + 3600 * 24;
////        } elseif ($date_type == 'week') {
////            $end = $start + 3600 * 24 * 7;
////        } elseif ($date_type == 'month') {
////            $year = date('Y', $date);
////            $month = date('m', $date);
////            $day = getDaysInMonth($year, $month);
////            $end = $start + 3600 * 24 * $day;
////        }
//        $start = strtotime($start);
//        $end = strtotime($end);
//        if (!is_int($start) || !is_int($end)) {
//            $rs['code'] = 1001;
//            $rs['msg'] = '起始日期或结束日期错误';
//            return $rs;
//        }
//        $sql = "SELECT * FROM cmf_user_change WHERE user_id in(". $user_str .")".' and addtime >= '.$start.' and addtime <= '.$end;
////        var_dump($sql);die;
//        $change = DI()->notorm->user_change->queryAll($sql);
////        var_dump($change);die;
//        $recharge = 0;  //充值
//        $withdrawal = 0;    //提现
//        $discount = 0;  //优惠
//        $tou = 0;   //有效投注
//        $zho = 0;   //中奖
//        $rate = 0;  //反水
//        foreach ($change as $v){
//            if ($v['change_type'] == 1){
//                $recharge += $v['change_money'];
//            }
//            if ($v['change_type'] == 2){
//                $withdrawal += $v['change_money'];
//            }
//            if ($v['change_type'] == 3){
//                if ($v['change_money'] < 0){
//                    $tou += abs($v['change_money']);
//                }else{
//                    $zho += abs($v['change_money']);
//                }
//            }
//            if ($v['change_type'] == 6){
//                $discount += $v['change_money'];
//            }
//            if ($v['change_type'] == 7){
//                $rate += $v['change_money'];
//            }
//        }
//        //团队盈亏
//        $yin = $zho + $rate - $tou;
//
//        $info = [
//            'yin' => $yin,
//            'tou' => $tou,
//            'zho' => $zho,
//            'rate' => $rate,
//            'yu' => $yu,
//            'recharge' => $recharge,
//            'withdrawal' => $withdrawal,
//            'discount' => $discount,
//            'count' => $count,
//            'dai' => $dai,
//            'mem' => $mem
//        ];
//        $rs['code'] = 0;
//        $rs['msg'] = '获取成功';
//        $rs['info'] = $info;
//        return $rs;
//    }

    public function getReport($uid, $user_login, $start, $end, $platform, $page, $page_size)
    {
        $info = DI()->notorm->user
            ->where('id', $uid)
            ->fetchOne();
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg'] = '信息错误';
            return $rs;
        }
        $start = strtotime($start);
        $end = strtotime($end);
        if (!is_int($start) || !is_int($end)) {
            $rs['code'] = 1001;
            $rs['msg'] = '起始日期或结束日期错误';
            return $rs;
        }
//        echo 1;die;
        //查找所有下级
        $all = DI()->notorm->user
            ->where('invite_level like ?', $info['invite_level'] . '%')
            ->where('id <> ?',$uid)
            ->fetchAll();
//        var_dump($all);die;
        $users = '';
        foreach ($all as $v) {
            $users .= $v['id'] . ',';
        }
        $users = substr($users, 0, -1);

        //查找一级代理或指定用户
        $where = "parent_id = $uid";
        if ($user_login) {
            $where = "user_login = $user_login";
        }
        $list = DI()->notorm->user
            ->where($where)
            ->select('id,user_login,invite_level,is_dai,create_time')
            ->limit(($page - 1) * $page_size, $page_size)
            ->fetchAll();
        if (!$list) {
            $rs['code'] = 1001;
            $rs['msg'] = '暂无数据';
            $rs['info'] = [];
            return $rs;
        }
        //总数
        $count = DI()->notorm->user
            ->where($where)
            ->count();

        //查询返点
        $sql1 = "select * from cmf_user_rate where user_id in (" . $users . ")";
        $rates = DI()->notorm->user_rate->queryAll($sql1);
        $user_rate = [];
        foreach ($rates as $v) {
            $user_rate[$v['user_id']] = $v;
        }

        //一级代理下级对应uid
        $level = [];
        //一级代理对应下级user_login
        $user_logins = [];
        foreach ($list as $k => $v) {
            $temp = [];
            $temp2 = [];
            $reg_count = 0;
            foreach ($all as $val) {
                //如果字符串存在
                if (strstr($val['invite_level'], $v['invite_level']) === false) continue;
                $temp[] = $val['id'];
                $temp2[] = $val['user_login'];
                if ($val['create_time'] >= $start && $val['create_time'] <= $end) $reg_count++;
            }
            $level[$v['id']] = $temp;
            $user_logins[$v['id']] = $temp2;
            if ($reg_count > 0){
                $reg_count--;
            }
            $list[$k]['reg_count'] = $reg_count;    //减去自身
            $list[$k]['count'] = count($level[$v['id']]) - 1;//减去自身
        }

        if ($platform == 1) {
            //统计彩票
            foreach ($list as $k => $v) {
                $user_temp = implode(',',$level[$v['id']]);
                $sql = "SELECT 
                sum(if(change_type=1,change_money,0)) recharge,
                sum(if(change_type=2,change_money,0)) withdrawal,
                sum(if(change_type=3&&change_money>0,change_money,0)) zho,
                sum(if(change_type=3&&change_money<0,change_money,0)) tou,
                sum(if(change_type=7&&platform=1,change_money,0)) rate,
                sum(if(change_type=6,change_money,0)) discount,
                sum(if(change_type in (3,6)||(change_type=7&&platform=1),change_money,0)) yin 
                FROM cmf_user_change WHERE user_id in (" . $user_temp . ') and addtime >= ' . $start . ' and addtime <= ' . $end;
                $change = DI()->notorm->user_change->queryAll($sql);

                $list[$k]['zho'] = $change[0]['zho'];
                $list[$k]['tou'] = $change[0]['tou'];
                $list[$k]['rate'] = $change[0]['rate'];    //彩票返点
                $list[$k]['recharge'] = $change[0]['recharge'];
                $list[$k]['withdrawal'] = $change[0]['withdrawal'];
                $list[$k]['discount'] = $change[0]['discount'];
                $list[$k]['yin'] = $change[0]['yin'];

                if ($v['is_dai'] == 1) {
                    $list[$k]['is_dai'] = '代理';
                } else {
                    $list[$k]['is_dai'] = '会员';
                }
                unset($list[$k]['invite_level']);
                unset($list[$k]['create_time']);
            }
        } elseif ($platform == 2) {
            //统计天鹅
            foreach ($list as $k => $v) {
                $user_temp = implode(',',$level[$v['id']]);

                $sql = "SELECT 
                sum(if(change_type=1,change_money,0)) recharge, 
                sum(if(change_type=2,change_money,0)) withdrawal,
                sum(if(change_type=11,change_money,0)) reward,
                sum(if(change_type=7&&platform=2,change_money,0)) yin,
                sum(if(change_type=6,change_money,0)) discount,
                sum(if(change_type in (11,12,13,14,17),change_money,0)) expend
                FROM cmf_user_change WHERE user_id in (" . $user_temp . ') and addtime >= ' . $start . ' and addtime <= ' . $end;
                $change = DI()->notorm->user_change->queryAll($sql);

                $list[$k]['reward'] = $change[0]['reward'];
                $list[$k]['expend'] = $change[0]['expend'];
                $list[$k]['recharge'] = $change[0]['recharge'];
                $list[$k]['withdrawal'] = $change[0]['withdrawal'];
                $list[$k]['discount'] = $change[0]['discount'];
                $list[$k]['yin'] = $change[0]['yin'];

                if ($v['is_dai'] == 1) {
                    $list[$k]['is_dai'] = '代理';
                } else {
                    $list[$k]['is_dai'] = '会员';
                }
                unset($list[$k]['invite_level']);
                unset($list[$k]['create_time']);
            }
        } else {
            //游戏平台
            foreach ($list as $k => $v) {
                $user_temp = implode(',',$level[$v['id']]);
                $users = implode(',',$user_logins[$v['id']]);

                $sql = "SELECT 
            sum(if(change_type=23&&change_money<0&&platform={$platform},change_money,0)) game_in,
            sum(if(change_type=23&&change_money>0&&platform={$platform},change_money,0)) game_out,
            sum(if(change_type=6,change_money,0)) discount
            FROM cmf_user_change WHERE user_id in(" . $user_temp . ")" . ' and addtime >= ' . $start . ' and addtime <= ' . $end;
                $change = DI()->notorm->user_change->queryAll($sql);
                //游戏盈亏
                $sql2 = "SELECT 
            sum(profit) yin,
            sum(pay_off) zho,
            sum(bet_amount) expend
            FROM cmf_game_record WHERE user_login in(" . $users . ")" . ' and bet_time >= ' . $start . ' and bet_time <= ' . $end . ' and platform_code = ' . $platform;
                $game_info = DI()->notorm->game_record->queryAll($sql2);
                $game_info = $game_info[0];

                $list[$k]['discount'] = $change[0]['discount'];
                $list[$k]['game_out'] = $change[0]['game_out'];
                $list[$k]['game_in'] = $change[0]['game_in'];
                $list[$k]['expend'] = $game_info['expend'];
                $list[$k]['zho'] = $game_info['zho'];
                $list[$k]['yin'] = $game_info['yin'];

                if ($v['is_dai'] == 1) {
                    $list[$k]['is_dai'] = '代理';
                } else {
                    $list[$k]['is_dai'] = '会员';
                }
                unset($list[$k]['invite_level']);
                unset($list[$k]['create_time']);
            }
        }

        foreach ($list as $k => $v){
            foreach ($v as $key => $val){
                if (is_null($val) || $val == 0) $list[$k][$key] = 0;
            }
        }

        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $list;
        $rs['count'] = $count;
        return $rs;

    }

//    public function getReport($uid, $user_login, $start, $end, $platform, $page, $page_size)
//    {
//        $info = DI()->notorm->user
//            ->where('id', $uid)
//            ->fetchOne();
//        if (!$info) {
//            $rs['code'] = 1001;
//            $rs['msg'] = '信息错误';
//            return $rs;
//        }
//        $start = strtotime($start);
//        $end = strtotime($end);
//        if (!is_int($start) || !is_int($end)) {
//            $rs['code'] = 1001;
//            $rs['msg'] = '起始日期或结束日期错误';
//            return $rs;
//        }
//
//        //查找所有下级
//        $all = DI()->notorm->user
//            ->where('invite_level like ?', $info['invite_level'] . '-%')
//            ->fetchAll();
//        $users = '';
//        foreach ($all as $v) {
//            $users .= $v['id'] . ',';
//        }
//        $users = substr($users, 0, -1);
//
//        //查找一级代理或指定用户
//        $where = "parent_id = $uid";
//        if ($user_login) {
//            $where = "user_login = $user_login";
//        }
//        $list = DI()->notorm->user
//            ->where($where)
//            ->select('id,user_login,invite_level,is_dai,create_time')
//            ->limit(($page - 1) * $page_size, $page_size)
//            ->fetchAll();
//        if (!$list) {
//            $rs['code'] = 1001;
//            $rs['msg'] = '暂无数据';
//            $rs['info'] = [];
//            return $rs;
//        }
//        //总数
//        $count = DI()->notorm->user
//            ->where($where)
//            ->count();
//
//        //查询返点
//        $sql1 = "select * from cmf_user_rate where user_id in (" . $users . ")";
//        $rates = DI()->notorm->user_rate->queryAll($sql1);
//        $user_rate = [];
//        foreach ($rates as $v) {
//            $user_rate[$v['user_id']] = $v;
//        }
//
//        //一级代理下级对应uid
//        $level = [];
//        foreach ($list as $k => $v) {
//            $temp = [];
//            $reg_count = 0;
//            foreach ($all as $val) {
//                //如果字符串存在
//                if (strstr($val['invite_level'], $v['invite_level']) === false) continue;
//                $temp[] = $val['id'];
//                if ($val['create_time'] >= $start && $val['create_time'] <= $end) $reg_count++;
//            }
//            $level[$v['id']] = $temp;
//            if ($reg_count > 0){
//                $reg_count--;
//            }
//            $list[$k]['reg_count'] = $reg_count;    //减去自身
//            $list[$k]['count'] = count($level[$v['id']]) - 1;//减去自身
//        }
//
//
//        $sql = "SELECT * FROM cmf_user_change WHERE user_id in (" . $users . ') and addtime >= ' . $start . ' and addtime <= ' . $end;
//        $change = DI()->notorm->user_change->queryAll($sql);
//
//        if ($platform == 1) {
//            //统计彩票
//            foreach ($list as $k => $v) {
//                $user_temp = $level[$v['id']];
//                $list[$k]['zho'] = 0;
//                $list[$k]['tou'] = 0;
//                $list[$k]['rate'] = $user_rate[$v['id']]['ticket_rate'];    //彩票返点
//                $list[$k]['recharge'] = 0;
//                $list[$k]['withdrawal'] = 0;
//                $list[$k]['discount'] = 0;
//                $rate = 0;
//                foreach ($change as $val) {
//                    if (in_array($val['user_id'], $user_temp)) {
//                        if ($val['change_type'] == 1) {
//                            $list[$k]['recharge'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 2) {
//                            $list[$k]['withdrawal'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 3) {
//                            if ($val['change_money'] < 0) {
////                                $list[$k]['tou'] = number_format($list[$k]['tou'] + abs($val['change_money']), 4);
//                                $list[$k]['tou'] = sprintf('%.2f',$list[$k]['tou'] + abs($val['change_money']));
//                            } else {
////                                $list[$k]['zho'] = number_format($list[$k]['zho'] + abs($val['change_money']), 4);
//                                $list[$k]['zho'] = sprintf('%.2f',$list[$k]['zho'] + abs($val['change_money']));
//                            }
//                        }
//                        if ($val['change_type'] == 6) {
//                            $list[$k]['discount'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 7 && $val['platform' == '1']) {
//                            $rate += $val['change_money'];
//                        }
//                    }
//                }
////                $list[$k]['yin'] = number_format($list[$k]['zho'] + $rate - $list[$k]['tou'], 4);
//                $list[$k]['yin'] = sprintf('%.2f',$list[$k]['zho'] + $rate - $list[$k]['tou']);
//                if ($v['is_dai'] == 1) {
//                    $list[$k]['is_dai'] = '代理';
//                } else {
//                    $list[$k]['is_dai'] = '会员';
//                }
//                unset($list[$k]['invite_level']);
//                unset($list[$k]['create_time']);
//            }
//        } elseif ($platform == 2) {
//            //统计天鹅
//            foreach ($list as $k => $v) {
//                $user_temp = $level[$v['id']];
////                $list[$k]['zho'] = 0;
////                $list[$k]['tou'] = 0;
////                $list[$k]['rate'] = $user_rate[$v['id']]['live_rate'];    //直播返点
//                $list[$k]['recharge'] = 0;
//                $list[$k]['withdrawal'] = 0;
//                $list[$k]['discount'] = 0;
//                $list[$k]['reward'] = 0;
//                $list[$k]['expend'] = 0;
//                $list[$k]['yin'] = 0;
//                foreach ($change as $val) {
//                    if (in_array($val['user_id'], $user_temp)) {
//                        if ($val['change_type'] == 1) {
//                            $list[$k]['recharge'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 2) {
//                            $list[$k]['withdrawal'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 6) {
//                            $list[$k]['discount'] += $val['change_money'];
//                        }
//                        if ($val['change_type'] == 21) {
////                            $list[$k]['yin'] = number_format($list[$k]['yin'] + $val['change_money'], 4);
//                            $list[$k]['yin'] = sprintf('%.2f',$list[$k]['yin'] + $val['change_money']);
//                        }
//                        if ($v['change_type'] == 11) {
//                            $list[$k]['reward'] += $v['change_money'];
//                            $list[$k]['expend'] += $v['change_money'];
//                        }
//                        if ($v['change_type'] == 11 || $v['change_type'] == 12 || $v['change_type'] == 13 || $v['change_type'] == 14 || $v['change_type'] == 17) {
//                            $list[$k]['expend'] += $v['change_money'];
//                        }
//                    }
//                }
//                if ($v['is_dai'] == 1) {
//                    $list[$k]['is_dai'] = '代理';
//                } else {
//                    $list[$k]['is_dai'] = '会员';
//                }
//                unset($list[$k]['invite_level']);
//                unset($list[$k]['create_time']);
//            }
//        } else {
//            $list = [];
//        }
//
//
//        $rs['code'] = 0;
//        $rs['msg'] = '获取成功';
//        $rs['info'] = $list;
//        $rs['count'] = $count;
//        return $rs;
//
//    }

//    public function getReportDown($uid)
//    {
//        //个人信息
//        $info = DI()->notorm->user
//            ->where('id', $uid)
//            ->fetchOne();
//        //查找团队用户
//        $users = DI()->notorm->user
//            ->where('invite_level LIKE ?', $info['invite_level'] . '-%')
//            ->select('id,user_login,is_dai')
//            ->fetchAll();
//        $return = [];
//        foreach ($users as $v) {
//            $temp['id'] = $v['id'];
//            $temp['user_login'] = $v['user_login'];
//            $temp['is_dai'] = $v['is_dai'];
//            $change = DI()->notorm->user_change
//                ->where('user_id', $v['id'])
//                ->fetchAll();
//            $temp['tou'] = 0;
//            $temp['rate'] = 0;
//            $temp['yin'] = 0;
//            foreach ($change as $val) {
//                if ($val['change_type'] == 3) {
//                    $temp['yin'] += $val['change_money'];
//                    if ($val['change_money'] < 0) {
////                        $temp['tou'] = number_format($temp['tou'] + $val['change_money'], 4);
//                        $temp['tou'] = sprintf('%.2f',$temp['tou'] + $val['change_money']);
//                    }
//                }
//                if ($val['change_type'] == 7) {
//                    $temp['rate'] += $val['change_money'];
//                }
//            }
////            $temp['yin'] = number_format($temp['yin'] + $temp['rate'], 4);
//            $temp['yin'] = sprintf('%.2f',$temp['yin'] + $temp['rate']);
//            $return[] = $temp;
//        }
//        $rs['code'] = 0;
//        $rs['msg'] = '获取成功';
//        $rs['info'] = $return;
//        return $rs;
//    }
}