<?php

class Model_User extends PhalApi_Model_NotORM
{
    /* 用户全部信息 */
    public function getBaseInfo($uid)
    {
        $info = DI()->notorm->user
            ->select("back_img,viewing_num,count_Withdrawal,invite_level,is_dai,id,coin as user_money,user_nicename,avatar,avatar_thumb,sex,signature,coin,votes,consumption,votestotal,birthday,qq,bat,wechat,issuper,iszombie")
            ->where('id=?  and user_type="2"', $uid)
            ->fetchOne();

        //彩票返点
        $rate = DI()->notorm->user_rate
            ->where('user_id',$uid)
            ->where('platform',1)
            ->select('rate')
            ->fetchOne();
        $info['rate'] = $rate['rate'] * 100 .'';

        if ($info) {
            $info['avatar'] = get_upload_path($info['avatar']);
            $info['back_img'] = get_upload_path($info['avatar']);
            $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);
            $info['level'] = getLevel($info['consumption']);
            $info['level_anchor'] = getLevelAnchor($info['votestotal']);
            $info['lives'] = getLives($uid);
            $info['follows'] = getFollows($uid);
            $info['fans'] = getFans($uid);

            $info['gift_count'] = DI()->notorm->user_coinrecord->where("action = 1 and touid = ?",$uid)->count();
//            $info['profit_count'] = DI()->notorm->user_change
//                ->where("user_id = {$uid} and change_money > 0 and change_type BETWEEN 3 AND 5 AND 6 AND 7")
//                ->sum('change_money');

            $info['vip'] = getUserVip($uid);
            $info['liang'] = getUserLiang($uid);

            if ($info['birthday']) {
                $info['birthday'] = date('Y-m-d', $info['birthday']);
            } else {
                $info['birthday'] = '';
            }
        }

        if ($info['is_dai'] == 2){
            $info['profit'] = 0;
        }else{
            //代理收益
//            //查找所有下级
//            $sql = "select * from cmf_user where invite_level like '".$info['invite_level'] . "-%'";
//            $users = DI()->notorm->user->queryAll($sql);
//            //用户对应返点
//            $user_rate = [];
//            $str_ids = '';
//            foreach ($users as $v){
//                $user_rate[$v['id']] = $v['rate'];
//                $str_ids .= $v['id'] . ',';
//            }
//            $str_ids = substr($str_ids,0,-1);
//            //用户对应该用户返点
//            $rate = [];
//            foreach ($users as $v){
//                $ids = explode('-',$v['invite_level']);
//                $ra = $info['rate'];
//                foreach ($ids as $key => $val){
//                    if ($key == 0) continue;
//                    $ra = $ra - $user_rate[$val];
//                }
//                $rate[$v['id']] = $ra;
//            }
//            //下注记录
//            $time = strtotime(date('Y-m-d'));
//            $total = 0;
//            if($str_ids != ''){
//                $sql2 = "select user_id,sum(money) as total from cmf_game_ticket where user_id in ($str_ids) and addtime > $time group by user_id";
//                $list = DI()->notorm->game_ticket->queryAll($sql2);
//                foreach ($list as $v){
//                    $total += $v['total'] * $rate[$v['user_id']];
//                }
//            }

            $total = DI()->notorm->user_change
                ->where('user_id',$info['id'])
                ->where('change_type',7)
                ->sum('change_money');

            $info['profit'] = $total;
        }

        return $info;
    }

    /* 用户全部信息 */
    public function getUserinfo($uid)
    {
        $info = DI()->notorm->user
            ->select("*")
            ->where('id=?', $uid)
            ->fetchOne();
        return $info;
    }

    /* 判断昵称是否重复 */
    public function checkName($uid, $name)
    {
        $isexist = DI()->notorm->user
            ->select('id')
            ->where('id!=? and user_nicename=?', $uid, $name)
            ->fetchOne();
        if ($isexist) {
            return 0;
        } else {
            return 1;
        }
    }

    /* 修改信息 */
    public function userUpdate($uid, $fields)
    {
        /* 清除缓存 */
        delCache("userinfo_" . $uid);
        
        if (!$fields) {
            return false;
        }
        
       $res3 = DI()->notorm->user
            ->where('id=?', $uid)
            ->update($fields);
        return $res3;
    }

    /* 修改密码 */
    public function updatePass($uid, $oldpass, $pass)
    {
        $userinfo = DI()->notorm->user
            ->select("user_pass")
            ->where('id=?', $uid)
            ->fetchOne();
        $oldpass = setPass($oldpass);
        if ($userinfo['user_pass'] != $oldpass) {
            return 1003;
        }
        $newpass = setPass($pass);
        return DI()->notorm->user
            ->where('id=?', $uid)
            ->update(array("user_pass" => $newpass));
    }

    /* 我的钻石 */
    public function getBalance($uid)
    {
        return DI()->notorm->user
            ->select("coin,score")
            ->where('id=?', $uid)
            ->fetchOne();
    }

    /* 充值规则 */
    public function getChargeRules()
    {

        $rules = DI()->notorm->charge_rules
            ->select('id,coin,coin_ios,money,product_id,give')
            ->order('list_order asc')
            ->fetchAll();

        return $rules;
    }

    /* 我的收益 */
    public function getProfit($uid)
    {
        $info = DI()->notorm->user
            ->select("votes,votestotal")
            ->where('id=?', $uid)
            ->fetchOne();

        $config = getConfigPri();

        //提现比例
        $cash_rate = $config['cash_rate'];
        $cash_start = $config['cash_start'];
        $cash_end = $config['cash_end'];
        $cash_max_times = $config['cash_max_times'];
        $cash_take = $config['cash_take'];
        //剩余票数
        $votes = $info['votes'];

        if (!$cash_rate) {
            $total = '0';
        } else {
            //总可提现数
            $total = (string)(floor($votes / $cash_rate) * (100 - $cash_take) / 100);
        }

        if ($cash_max_times) {
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放，每月只可提现'.$cash_max_times.'次';
            $tips = '每月' . $cash_start . '-' . $cash_end . '号可进行提现申请，每月只可提现' . $cash_max_times . '次';
        } else {
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放';
            $tips = '每月' . $cash_start . '-' . $cash_end . '号可进行提现申请';
        }

        $rs = array(
            "votes" => $votes,
            "votestotal" => $info['votestotal'],
            "total" => $total,
            "cash_rate" => $cash_rate,
            "cash_take" => $cash_take,
            "tips" => $tips,
        );
        return $rs;
    }

    /* 提现  */
    public function setCash($data)
    {

        $nowtime = time();

        $uid = $data['uid'];
        $accountid = $data['accountid'];
        $cashvote = $data['cashvote'];

        $config = getConfigPri();
        $cash_start = $config['cash_start'];
        $cash_end = $config['cash_end'];
        $cash_max_times = $config['cash_max_times'];

        $day = (int)date("d", $nowtime);

        if ($day < $cash_start || $day > $cash_end) {
            return 1005;
        }

        //本月第一天
        $month = date('Y-m-d', strtotime(date("Ym", $nowtime) . '01'));
        $month_start = strtotime(date("Ym", $nowtime) . '01');

        //本月最后一天
        $month_end = strtotime("{$month} +1 month");

        if ($cash_max_times) {
            $isexist = DI()->notorm->cash_record
                ->where('uid=? and addtime > ? and addtime < ?', $uid, $month_start, $month_end)
                ->count();
            if ($isexist >= $cash_max_times) {
                return 1006;
            }
        }

//        $isrz = DI()->notorm->user_auth
//            ->select("status")
//            ->where('uid=?', $uid)
//            ->fetchOne();
//        if (!$isrz || $isrz['status'] != 1) {
//            return 1003;
//        }

        /* 钱包信息 */
        $accountinfo = DI()->notorm->cash_account
            ->select("*")
            ->where('id=? and uid=?', $accountid, $uid)
            ->fetchOne();

        if (!$accountinfo) {

            return 1007;
        }


        //提现比例
        $cash_rate = $config['cash_rate'];

        /*提现抽成比例*/
        $cash_take = $config['cash_take'];

        /* 最低额度 */
        $cash_min = $config['cash_min'];

//        var_dump($cash_start);die;

        //提现钱数
        $money = floor($cashvote / $cash_rate);

        if ($money < $cash_min) {
            return 1004;
        }

        $cashvotes = $money * $cash_rate;


        $ifok = DI()->notorm->user
            ->where('id = ? and votes>=?', $uid, $cashvotes)
            ->update(array('votes' => new NotORM_Literal("votes - {$cashvotes}")));
        if (!$ifok) {
            return 1001;
        }

        //平台抽成后最终的钱数
        $money_take = $money * (1 - $cash_take * 0.01);
        $money = number_format($money_take, 2, ".", "");

        $data = array(
            "uid" => $uid,
            "money" => $money,
            "votes" => $cashvotes,
            "orderno" => $uid . '_' . $nowtime . rand(100, 999),
            "status" => 0,
            "addtime" => $nowtime,
            "uptime" => $nowtime,
            "type" => $accountinfo['type'],
            "account_bank" => $accountinfo['account_bank'],
            "account" => $accountinfo['account'],
            "name" => $accountinfo['name'],
        );

        $rs = DI()->notorm->cash_record->insert($data);
        if (!$rs) {
            return 1002;
        }


        return $rs;
    }

    /* 关注 */
    public function setAttent($uid, $touid)
    {
        $isexist = DI()->notorm->user_attention
            ->select("*")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();
        if ($isexist) {
            DI()->notorm->user_attention
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            return 0;
        } else {
            DI()->notorm->user_black
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            DI()->notorm->user_attention
                ->insert(array("uid" => $uid, "touid" => $touid, "addtime" => time()));
            return 1;
        }
    }

    /* 拉黑 */
    public function setBlack($uid, $touid)
    {
        $isexist = DI()->notorm->user_black
            ->select("*")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();
        if ($isexist) {
            DI()->notorm->user_black
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            return 0;
        } else {
            DI()->notorm->user_attention
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            DI()->notorm->user_black
                ->insert(array("uid" => $uid, "touid" => $touid));

            return 1;
        }
    }

    /* 关注列表 */
    public function getFollowsList($uid, $touid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum = 50;
        $start = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_attention
            ->select("touid")
            ->where('uid=?', $touid)
            ->order("addtime desc")
            ->limit($start, $pnum)
            ->fetchAll();
        foreach ($touids as $k => $v) {
            $userinfo = getUserInfo($v['touid']);
            if ($userinfo) {
                if ($uid == $touid) {
                    $isattent = '1';
                } else {
                    $isattent = isAttention($uid, $v['touid']);
                }
                $userinfo['isattention'] = $isattent;
                $touids[$k] = $userinfo;
            } else {
                DI()->notorm->user_attention->where('uid=? or touid=?', $v['touid'], $v['touid'])->delete();
                unset($touids[$k]);
            }
        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 粉丝列表 */
    public function getFansList($uid, $touid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum = 50;
        $start = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_attention
            ->select("uid")
            ->where('touid=?', $touid)
            ->order("addtime desc")
            ->limit($start, $pnum)
            ->fetchAll();
        foreach ($touids as $k => $v) {
            $userinfo = getUserInfo($v['uid']);
            if ($userinfo) {
                $userinfo['isattention'] = isAttention($uid, $v['uid']);
                $touids[$k] = $userinfo;
            } else {
                DI()->notorm->user_attention->where('uid=? or touid=?', $v['uid'], $v['uid'])->delete();
                unset($touids[$k]);
            }

        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 黑名单列表 */
    public function getBlackList($uid, $touid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum = 50;
        $start = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_black
            ->select("touid")
            ->where('uid=?', $touid)
            ->limit($start, $pnum)
            ->fetchAll();
        foreach ($touids as $k => $v) {
            $userinfo = getUserInfo($v['touid']);
            if ($userinfo) {
                $touids[$k] = $userinfo;
            } else {
                DI()->notorm->user_black->where('uid=? or touid=?', $v['touid'], $v['touid'])->delete();
                unset($touids[$k]);
            }
        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 直播记录 */
    public function getLiverecord($touid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum = 50;
        $start = ($p - 1) * $pnum;
        $record = DI()->notorm->live_record
            ->select("id,uid,nums,starttime,endtime,title")
            ->where('uid=?', $touid)
            ->order("id desc")
            ->limit($start, $pnum)
            ->fetchAll();
        foreach ($record as $k => $v) {
            $record[$k]['datestarttime'] = date("Y.m.d", $v['starttime']);
            $record[$k]['dateendtime'] = date("Y.m.d", $v['endtime']);
            $cha = $v['endtime'] - $v['starttime'];
            $record[$k]['length'] = getSeconds($cha);
        }
        return $record;
    }

    /* 个人主页 */
    public function getUserHome($uid, $touid)
    {
        $info = getUserInfo($touid);

        $user_status = $info['user_status'];


        $info['follows'] = (string)getFollows($touid);
        $info['fans'] = (string)getFans($touid);
        $info['isattention'] = (string)isAttention($uid, $touid);
        $info['isblack'] = (string)isBlack($uid, $touid);
        $info['isblack2'] = (string)isBlack($touid, $uid);

        /* 直播状态 */
        $islive = '0';
        $isexist = DI()->notorm->live
            ->select('uid')
            ->where('uid=? and islive=1', $touid)
            ->fetchOne();
        if ($isexist) {
            $islive = '1';
        }
        $info['islive'] = $islive;

        /* 贡献榜前三 */
        $rs = array();
        $rs = DI()->notorm->user_coinrecord
            ->select("uid,sum(totalcoin) as total")
            ->where('action=1 and touid=?', $touid)
            ->group("uid")
            ->order("total desc")
            ->limit(0, 3)
            ->fetchAll();
        foreach ($rs as $k => $v) {
            $userinfo = getUserInfo($v['uid']);
            $rs[$k]['avatar'] = $userinfo['avatar'];
        }
        $info['contribute'] = $rs;

        /* 视频数 */

        if ($uid == $touid) {  //自己的视频（需要返回视频的状态前台显示）
            $where = " uid={$uid} and isdel='0' and status=1  and is_ad=0";
        } else {  //访问其他人的主页视频
            $videoids_s = getVideoBlack($uid);
            $where = "id not in ({$videoids_s}) and uid={$touid} and isdel='0' and status=1  and is_ad=0";
        }

        $videonums = DI()->notorm->video
            ->where($where)
            ->count();
        if (!$videonums) {
            $videonums = 0;
        }

        $info['videonums'] = (string)$videonums;
        /* 动态数 */

        if ($uid == $touid) {  //自己的动态（需要返回动态的状态前台显示）
            $whered = " uid={$uid} and isdel='0' and status=1";
        } else {  //访问其他人的主页动态
            $whered = " uid={$touid} and isdel='0' and status=1  ";
        }

        $dynamicnums = DI()->notorm->dynamic
            ->where($whered)
            ->count();
        if (!$dynamicnums) {
            $dynamicnums = 0;
        }

        $info['dynamicnums'] = (string)$dynamicnums;
        /* 直播数 */
        $livenums = DI()->notorm->live_record
            ->where('uid=?', $touid)
            ->count();

        $info['livenums'] = $livenums;
        /* 直播记录 */
        $record = array();
        $record = DI()->notorm->live_record
            ->select("id,uid,nums,starttime,endtime,title")
            ->where('uid=?', $touid)
            ->order("id desc")
            ->limit(0, 50)
            ->fetchAll();
        foreach ($record as $k => $v) {
            $record[$k]['datestarttime'] = date("Y.m.d", $v['starttime']);
            $record[$k]['dateendtime'] = date("Y.m.d", $v['endtime']);
            $cha = $v['endtime'] - $v['starttime'];
            $record[$k]['length'] = getSeconds($cha);
        }
        $info['liverecord'] = $record;
        return $info;
    }

    /* 贡献榜 */
    public function getContributeList($touid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum = 50;
        $start = ($p - 1) * $pnum;

        $rs = array();
        $rs = DI()->notorm->user_coinrecord
            ->select("uid,sum(totalcoin) as total")
            ->where('touid=?', $touid)
            ->group("uid")
            ->order("total desc")
            ->limit($start, $pnum)
            ->fetchAll();

        foreach ($rs as $k => $v) {
            $rs[$k]['userinfo'] = getUserInfo($v['uid']);
        }

        return $rs;
    }

    /* 设置分销 */
    public function setDistribut($uid, $code)
    {

        $isexist = DI()->notorm->agent
            ->select("*")
            ->where('uid=?', $uid)
            ->fetchOne();
        if ($isexist) {
            return 1004;
        }

        //获取邀请码用户信息
        $oneinfo = DI()->notorm->agent_code
            ->select("uid")
            ->where('code=? and uid!=?', $code, $uid)
            ->fetchOne();
        if (!$oneinfo) {
            return 1002;
        }

        //获取邀请码用户的邀请信息
        $agentinfo = DI()->notorm->agent
            ->select("*")
            ->where('uid=?', $oneinfo['uid'])
            ->fetchOne();
        if (!$agentinfo) {
            $agentinfo = array(
                'uid' => $oneinfo['uid'],
                'one_uid' => 0,
            );
        }
        // 判断对方是否自己下级
        if ($agentinfo['one_uid'] == $uid) {
            return 1003;
        }

        $data = array(
            'uid' => $uid,
            'one_uid' => $agentinfo['uid'],
            'addtime' => time(),
        );
        DI()->notorm->agent->insert($data);
        return 0;
    }


    /* 印象标签 */
    public function getImpressionLabel()
    {

        $key = "getImpressionLabel";
        $list = getcaches($key);
        if (!$list) {
            $list = DI()->notorm->label
                ->select("*")
                ->order("list_order asc,id desc")
                ->fetchAll();
            if ($list) {
                setcaches($key, $list);
            }

        }

        return $list;
    }

    /* 用户标签 */
    public function getUserLabel($uid, $touid)
    {
        $list = DI()->notorm->label_user
            ->select("label")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();

        return $list;

    }

    /* 设置用户标签 */
    public function setUserLabel($uid, $touid, $labels)
    {
        $nowtime = time();
        $isexist = DI()->notorm->label_user
            ->select("*")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();
        if ($isexist) {
            $rs = DI()->notorm->label_user
                ->where('uid=? and touid=?', $uid, $touid)
                ->update(array('label' => $labels, 'uptime' => $nowtime));

        } else {
            $data = array(
                'uid' => $uid,
                'touid' => $touid,
                'label' => $labels,
                'addtime' => $nowtime,
                'uptime' => $nowtime,
            );
            $rs = DI()->notorm->label_user->insert($data);
        }

        return $rs;

    }

    /* 获取我的标签 */
    public function getMyLabel($uid)
    {
        $rs = array();
        $list = DI()->notorm->label_user
            ->select("label")
            ->where('touid=?', $uid)
            ->fetchAll();
        $label = array();
        foreach ($list as $k => $v) {
            $v_a = preg_split('/,|，/', $v['label']);
            $v_a = array_filter($v_a);
            if ($v_a) {
                $label = array_merge($label, $v_a);
            }

        }

        if (!$label) {
            return $rs;
        }


        $label_nums = array_count_values($label);

        $label_key = array_keys($label_nums);

        $labels = $this->getImpressionLabel();

        $order_nums = array();
        foreach ($labels as $k => $v) {
            if (in_array($v['id'], $label_key)) {
                $v['nums'] = (string)$label_nums[$v['id']];
                $order_nums[] = $v['nums'];
                $rs[] = $v;
            }
        }

        array_multisort($order_nums, SORT_DESC, $rs);

        return $rs;

    }

    /* 获取关于我们列表 */
    public function getPerSetting()
    {
        $rs = array();

        $list = DI()->notorm->portal_post
            ->select("id,post_title")
            ->where("type='2'")
            ->order('list_order asc')
            ->fetchAll();
        foreach ($list as $k => $v) {

            $rs[] = array('id' => '0', 'name' => $v['post_title'], 'thumb' => '', 'href' => get_upload_path("/portal/page/index?id={$v['id']}"));
        }

        return $rs;
    }

    /* 提现账号列表 */
    public function getUserAccountList($uid)
    {

        $list = DI()->notorm->cash_account
            ->select("*")
            ->where('uid=?', $uid)
            ->order("addtime desc")
            ->fetchAll();

        return $list;
    }

    /* 账号信息 */
    public function getUserAccount($where)
    {

        $list = DI()->notorm->cash_account
            ->select("*")
            ->where($where)
            ->order("addtime desc")
            ->fetchAll();

        return $list;
    }

    /* 设置提账号 */
    public function setUserAccount($data)
    {

        $rs = DI()->notorm->cash_account
            ->insert($data);

        return $rs;
    }

    /* 删除提账号 */
    public function delUserAccount($data)
    {

        $rs = DI()->notorm->cash_account
            ->where($data)
            ->delete();

        return $rs;
    }

    /* 登录奖励信息 */
    public function LoginBonus($uid)
    {
        $rs = array(
            'bonus_switch' => '0',
            'bonus_day' => '0',
            'count_day' => '0',
            'bonus_list' => array(),
        );

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        $configpri = getConfigPri();
        if (!$configpri['bonus_switch']) {
            return $rs;
        }
        $rs['bonus_switch'] = $configpri['bonus_switch'];

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 bonus_switch:'."\r\n",FILE_APPEND);
        /* 获取登录设置 */
        $key = 'loginbonus';
        $list = getcaches($key);
        if (!$list) {
            $list = DI()->notorm->loginbonus
                ->select("day,coin")
                ->fetchAll();
            if ($list) {
                setcaches($key, $list);
            }
        }

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 list:'."\r\n",FILE_APPEND);
        $rs['bonus_list'] = $list;
        $bonus_coin = array();
        foreach ($list as $k => $v) {
            $bonus_coin[$v['day']] = $v['coin'];
        }

        /* 登录奖励 */
        $signinfo = DI()->notorm->user_sign
            ->select("bonus_day,bonus_time,count_day")
            ->where('uid=?', $uid)
            ->fetchOne();
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 signinfo:'."\r\n",FILE_APPEND);
        if (!$signinfo) {
            $signinfo = array(
                'bonus_day' => '0',
                'bonus_time' => '0',
                'count_day' => '0',
            );
        }
        $nowtime = time();
        if ($nowtime - $signinfo['bonus_time'] > 60 * 60 * 24) {
            $signinfo['count_day'] = 0;
        }
        $rs['count_day'] = (string)$signinfo['count_day'];

        if ($nowtime > $signinfo['bonus_time']) {
            //更新
            $bonus_time = strtotime(date("Ymd", $nowtime)) + 60 * 60 * 24;
            $bonus_day = $signinfo['bonus_day'];
            if ($bonus_day > 6) {
                $bonus_day = 0;
            }
            $bonus_day++;
            $coin = $bonus_coin[$bonus_day];

            if ($coin) {
                $rs['bonus_day'] = (string)$bonus_day;
            }

        }
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rs:'."\r\n",FILE_APPEND);
        return $rs;
    }

    /* 获取登录奖励 */
    public function getLoginBonus($uid)
    {
        $rs = 0;
        $configpri = getConfigPri();
        if (!$configpri['bonus_switch']) {
            return $rs;
        }

        /* 获取登录设置 */
        $key = 'loginbonus';
        $list = getcaches($key);
        if (!$list) {
            $list = DI()->notorm->loginbonus
                ->select("day,coin")
                ->fetchAll();
            if ($list) {
                setcaches($key, $list);
            }
        }

        $bonus_coin = array();
        foreach ($list as $k => $v) {
            $bonus_coin[$v['day']] = $v['coin'];
        }

        $isadd = 0;
        /* 登录奖励 */
        $signinfo = DI()->notorm->user_sign
            ->select("bonus_day,bonus_time,count_day")
            ->where('uid=?', $uid)
            ->fetchOne();
        if (!$signinfo) {
            $isadd = 1;
            $signinfo = array(
                'bonus_day' => '0',
                'bonus_time' => '0',
                'count_day' => '0',
            );
        }
        $nowtime = time();
        if ($nowtime > $signinfo['bonus_time']) {
            //更新
            $bonus_time = strtotime(date("Ymd", $nowtime)) + 60 * 60 * 24;
            $bonus_day = $signinfo['bonus_day'];
            $count_day = $signinfo['count_day'];
            if ($bonus_day > 6) {
                $bonus_day = 0;
            }
            if ($nowtime - $signinfo['bonus_time'] > 60 * 60 * 24) {
                $count_day = 0;
            }
            $bonus_day++;
            $count_day++;


            if ($isadd) {
                DI()->notorm->user_sign
                    ->insert(array("uid" => $uid, "bonus_time" => $bonus_time, "bonus_day" => $bonus_day, "count_day" => $count_day));
            } else {
                DI()->notorm->user_sign
                    ->where('uid=?', $uid)
                    ->update(array("bonus_time" => $bonus_time, "bonus_day" => $bonus_day, "count_day" => $count_day));
            }

            $coin = $bonus_coin[$bonus_day];

            if ($coin) {
                DI()->notorm->beginTransaction('db_appapi');
//                $res1 = DI()->notorm->user
//                    ->where('id=?', $uid)
//                    ->update(array("coin" => new NotORM_Literal("coin + {$coin}")));
                /* 记录 */
                $insert = array("type" => '1', "action" => '3', "uid" => $uid, "touid" => $uid, "giftid" => $bonus_day, "giftcount" => '0', "totalcoin" => $coin, "showid" => '0', "addtime" => $nowtime);
                $res2 = DI()->notorm->user_coinrecord->insert($insert);

                $res3 = user_change_action($uid,9,$coin,DI()->config->get('app.change_type')[9]);
                if ($res2 && $res3 && $res3 != 2) {
                    DI()->notorm->commit('db_appapi');
                    $rs = 1;
                } else {
                    DI()->notorm->rollback('db_appapi');
                }
            } else {
                $rs = 1;
            }
        }

        return $rs;

    }

    //检测用户是否填写了邀请码
    public function checkIsAgent($uid)
    {
        $info = DI()->notorm->agent->where("uid=?", $uid)->fetchOne();
        if (!$info) {
            return 0;
        }

        return 1;
    }

    //用户商城提现
    public function setShopCash($data)
    {

        $nowtime = time();

        $uid = $data['uid'];
        $accountid = $data['accountid'];
        $money = $data['money'];

        $configpri = getConfigPri();
        $balance_cash_start = $configpri['balance_cash_start'];
        $balance_cash_end = $configpri['balance_cash_end'];
        $balance_cash_max_times = $configpri['balance_cash_max_times'];

        $day = (int)date("d", $nowtime);

        if ($day < $balance_cash_start || $day > $balance_cash_end) {
            return 1005;
        }

        //本月第一天
        $month = date('Y-m-d', strtotime(date("Ym", $nowtime) . '01'));
        $month_start = strtotime(date("Ym", $nowtime) . '01');

        //本月最后一天
        $month_end = strtotime("{$month} +1 month");

        if ($balance_cash_max_times) {
            $count = DI()->notorm->user_balance_cashrecord
                ->where('uid=? and addtime > ? and addtime < ?', $uid, $month_start, $month_end)
                ->count();
            if ($count >= $balance_cash_max_times) {
                return 1006;
            }
        }


        /* 钱包信息 */
        $accountinfo = DI()->notorm->cash_account
            ->select("*")
            ->where('id=? and uid=?', $accountid, $uid)
            ->fetchOne();

        if (!$accountinfo) {
            return 1007;
        }


        /* 最低额度 */
        $balance_cash_min = $configpri['balance_cash_min'];

        if ($money < $balance_cash_min) {
            return 1004;
        }


        $ifok = DI()->notorm->user
            ->where('id = ? and balance>=?', $uid, $money)
            ->update(array('balance' => new NotORM_Literal("balance - {$money}")));

        if (!$ifok) {
            return 1001;
        }


        $data = array(
            "uid" => $uid,
            "money" => $money,
            "orderno" => $uid . '_' . $nowtime . rand(100, 999),
            "status" => 0,
            "addtime" => $nowtime,
            "type" => $accountinfo['type'],
            "account_bank" => $accountinfo['account_bank'],
            "account" => $accountinfo['account'],
            "name" => $accountinfo['name'],
        );

        $rs = DI()->notorm->user_balance_cashrecord->insert($data);
        if (!$rs) {
            return 1002;
        }

        return $rs;
    }

    //获取认证信息
    public function getAuthInfo($uid)
    {
        $info = DI()->notorm->user_auth
            ->where("uid=? and status=1", $uid)
            ->select("real_name,cer_no")
            ->fetchOne();
        return $info;
    }


    //获取每日任务
    public function seeDailyTasks($uid)
    {
        $configpri = getConfigPri();
        $configpub = getConfigPub();
        $name_coin = $configpub['name_coin']; //钻石名称


        $list = [];

        //type 任务类型 1观看直播, 2观看视频, 3直播奖励, 4打赏奖励, 5分享奖励
        $type = ['1' => '观看直播', '2' => '观看视频', '3' => '直播奖励', '4' => '打赏奖励', '5' => '分享奖励'];

        // 当天时间
        $time = strtotime(date("Y-m-d 00:00:00", time()));
        foreach ($type as $k => $v) {
            $data = [
                'id' => '0',
                'type' => (string)$k,
                'title' => $v,
                'tip_m' => '',
                'state' => '0',
            ];

            if ($k == 1) {
                $target = $configpri['watch_live_term'];
                $reward = $configpri['watch_live_coin'];
            } else if ($k == 2) {
                $target = $configpri['watch_video_term'];
                $reward = $configpri['watch_video_coin'];
            } else if ($k == 3) {
                $target = $configpri['open_live_term'];
                $reward = $configpri['open_live_coin'];

            } else if ($k == 4) {
                $target = $configpri['award_live_term'];
                $reward = $configpri['award_live_coin'];
            } else {
                $target = $configpri['share_live_term'];
                $reward = $configpri['share_live_coin'];
            }


            $save = [
                'uid' => $uid,
                'type' => $k,
                'target' => $target,
                'schedule' => '0',
                'reward' => $reward,
                'addtime' => $time,
                'state' => '0',
            ];

            $where = "uid={$uid} and type={$k}";
            //每日任务
            $info = DI()->notorm->user_daily_tasks
                ->where($where)
                ->select("*")
                ->fetchOne();

            if (!$info) {
                $info = DI()->notorm->user_daily_tasks->insert($save);


            } else if ($info['addtime'] != $time) {
                $save['uptime'] = time(); //更新时间
                DI()->notorm->user_daily_tasks->where("id={$info['id']}")->update($save);
            } else {
                $target = $info['target'];
                $reward = $info['reward'];
                $data['state'] = $info['state'];
            }

            //提示标语
            if ($k == 1) {
                $tip_m = "观看直播时长达到{$target}分钟，奖励{$reward}" . $name_coin;
            } else if ($k == 2) {
                $tip_m = "观看视频时长达到{$target}分钟，奖励{$reward}" . $name_coin;
            } else if ($k == 3) {
                $tip_m = "每天开播满足{$target}小时可获得奖励{$reward}" . $name_coin;
            } else if ($k == 4) {
                $tip_m = "打赏主播超过{$target}{$name_coin}，奖励{$reward}" . $name_coin;
            } else {
                $tip_m = "直播间每日分享{$target}次可获得奖励{$reward}" . $name_coin;
            }
            $data['id'] = $info['id'];
            $data['tip_m'] = $tip_m;
            $list[] = $data;
        }
        return $list;
    }


    public function receiveTaskReward($uid, $taskid)
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $where = "id={$taskid} and uid={$uid}";
        //每日任务
        $info = DI()->notorm->user_daily_tasks
            ->where($where)
            ->select("*")
            ->fetchOne();

        if (!$info) {
            $rs['code'] = '1001';
            $rs['msg'] = '系统繁忙,请稍后操作~';
            return $rs;
        }
        if ($info['state'] == 0) {
            $rs['code'] = '1001';
            $rs['msg'] = '任务未达标,请继续加油~';
        } else if ($info['state'] == 2) {
            $rs['code'] = '1001';
            $rs['msg'] = '奖励已送达,不能重复领取!';
        } else {
            $rs['msg'] = '奖励已送放,明天继续加油哦~';


            //更新任务状态
            $issave = DI()->notorm->user_daily_tasks
                ->where("id={$info['id']}")
                ->update(['state' => 2, 'uptime' => time()]);

            if ($issave) {
                $coin = $info['reward'];
                //开启事务
                DI()->notorm->beginTransaction('db_appapi');
//                /* 增加用户钻石 */
//                $isprofit = DI()->notorm->user
//                    ->where('id = ?', $uid)
//                    ->update(array('coin' => new NotORM_Literal("coin + {$coin}")));

                $insert = array(
                    "type" => '1',
                    "action" => '21',
                    "uid" => $uid,
                    "touid" => $uid,
                    "giftid" => '0',
                    "giftcount" => '0',
                    "totalcoin" => $coin,
                    "addtime" => time()
                );
                $res2 = DI()->notorm->user_coinrecord->insert($insert);

                $res3 = user_change_action($uid,10,$coin,DI()->config->get('app.change_type')[10]);

                if ($res2 && $res3 && $res3 != 2) {
                    DI()->notorm->commit('db_appapi');
                } else {
                    DI()->notorm->rollback('db_appapi');
                    $rs['code'] = '1001';
                    $rs['msg'] = '系统繁忙,请稍后操作~';
                }

                //删除用户每日任务数据
                $key = "seeDailyTasks_" . $uid;
                delcache($key);
            }


        }

        return $rs;
    }
    
    
    /* 更新token 登陆信息 */
    public function updateToken($uid, $token, $data = array())
    {
        $nowtime = time();
        $expiretime = $nowtime + 60 * 60 * 24 * 300;

        DI()->notorm->user
            ->where('id=?', $uid)
            ->update(array('last_login_time' => $nowtime, "last_login_ip" => $_SERVER['REMOTE_ADDR']));

        $isok = DI()->notorm->user_token
            ->where('user_id=?', $uid)
            ->update(array("token" => $token, "expire_time" => $expiretime, 'create_time' => $nowtime));
        if (!$isok) {
            DI()->notorm->user_token
                ->insert(array("user_id" => $uid, "token" => $token, "expire_time" => $expiretime, 'create_time' => $nowtime,));
        }

        $token_info = array(
            'uid' => $uid,
            'token' => $token,
            'expire_time' => $expiretime,
        );
        DI()->redis->set("token_" . $uid, json_encode($token_info,JSON_UNESCAPED_UNICODE));
  

        return 1;
    }

}
