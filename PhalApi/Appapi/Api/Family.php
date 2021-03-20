<?php
/**
 * 家族
 */
class Api_Family extends PhalApi_Api {

    public function getRules() {
        return array(
            'familyAdd' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'user_login' => array('name' => 'user_login', 'type' => 'string','require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
                'user_pass2' => array('name' => 'user_pass2', 'type' => 'string',  'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
            ),
            'familyStatistics' => array(
                'familyid' => array('name' => 'familyid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '家族id'),
                'time' => array('name' => 'time', 'type' => 'int','require' => true, 'desc' => '1：今天，2：昨天，3：近七天'),
            ),

            'familyAnchor' => array(
                'familyid' => array('name' => 'familyid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '家族id'),
                'starttime' => array('name' => 'starttime', 'type' => 'string','require' => true, 'desc' => '开始时间2021-01-02'),
                'endtime' => array('name' => 'endtime', 'type' => 'string','require' => true, 'desc' => '结束时间2021-01-02'),
            ),
            'familyWallet' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),

            ),
            'anchorStatus' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'id'    => array('name' => 'id', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '主播ID'),
                'status' => array('name' => 'status', 'type' => 'int','require' => true, 'min' => 0, 'max'=>1, 'desc' => '状态：1-启用，0-禁用'),
            ),
            'familyFinance' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'int','require' => true, 'desc' => '1：全部2：提现3：转入'),
                'time' => array('name' => 'time', 'type' => 'string','require' => true, 'desc' => '例：2021-01'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
            'editAnchorPass' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'id'    => array('name' => 'id', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '主播ID'),
                'oldpass' => array('name' => 'oldpass', 'type' => 'string', 'require' => true, 'desc' => '家族长密码'),
                'pass' => array('name' => 'pass', 'type' => 'string', 'require' => true, 'desc' => '新密码'),
                'pass2' => array('name' => 'pass2', 'type' => 'string', 'require' => true, 'desc' => '确认密码'),
            ),
            'anchorInfo' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
            ),
            'Member' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'familyid' => array('name' => 'familyid', 'type' => 'int','require' => true, 'desc' => '家族id'),
            ),
            'anchorFinance' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'int','require' => true, 'desc' => '1：全部2：礼物3：订阅4：提现'),
                'time' => array('name' => 'time', 'type' => 'string','require' => true, 'desc' => '例：2021-01'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
            'updatePass' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'id' => array('name' => 'id', 'type' => 'int','require' => true, 'desc' => '主播id'),
                'oldpass' => array('name' => 'oldpass', 'type' => 'string','require' => true, 'desc' => '家族密码'),
                'pass' => array('name' => 'pass', 'type' => 'string', 'require' => true ,'desc' => '新密码'),
                'pass2' => array('name' => 'pass2', 'type' => 'string', 'require' => true ,'desc' => '确认密码'),
            ),
            'PatriarchFinance' => array(
                'aid' => array('name' => 'aid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '主播ID'),
                'uid' => array('name' => 'uid', 'type' => 'int','require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'int','require' => true, 'desc' => '1：全部2：礼物3：订阅4：提现'),
                'time' => array('name' => 'time', 'type' => 'string','require' => true, 'desc' => '例：2021-01'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
        );
    }
    /**
     * 主播财务报表（家族长）
     * @desc 用于 主播财务报表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[shouyi] 总收益
     * @return array info[cash_count] 总支出
     * @return array info[0][action] 类型：1-礼物 6-订阅 3-提现
     * @return string msg 提示信息
     */
    public function PatriarchFinance()
    {
        $aid=$this->aid;
        $uid=$this->uid;
        $token=$this->token;
        $time = checkNull($this->time);
        $type = checkNull($this->type);
        $p = checkNull($this->p);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $linfo = DI()->notorm->family->where('uid', $uid)->fetchOne();
        if(!$linfo) return ['code' => 1, 'msg' => '您还不是家族长'];
        $fuinfo = DI()->notorm->family_user->where('uid = ? and familyid = ?', $aid, $linfo['id'])->fetchOne();
        if(!$fuinfo) return ['code' => 1, 'msg' => '参数错误'];

        $start_time = strtotime($time);
        $time = explode('-', $time);
        $day_num = getDaysInMonth($time[0], $time[1]);
        $end_time = $start_time + (86400 * $day_num);

        if($p < 1){
            $p = 1;
        }
        $pnum = 15;
        $start = ($p-1) * $pnum;

        //礼物,订阅总收益
        $shouyi = DI()->notorm->user_coinrecord->where('touid = ?', $aid)->where('action', [1,6])->sum('totalcoin');
        if(!$shouyi) $shouyi = 0;
        //提现总支出
        $cash_count = DI()->notorm->cash_record->where('uid = ? and status = ?', $aid, 1)->sum('money');
        if(!$cash_count) $cash_count = 0;

        //礼物
        $sql3 = "SELECT A.addtime, A.totalcoin as total, A.action, B.user_nicename as title FROM cmf_user_coinrecord AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.touid = :uids AND A.addtime >= :ssstime AND A.addtime <= :eeetime AND A.action = :tp ORDER BY addtime DESC LIMIT :s,:e;";
        $params3 = array(
            ':uids' => $aid,
            ':ssstime' => $start_time,
            ':eeetime' => $end_time,
            ':tp' => 1,
            ':s' => $start,
            ':e' => $pnum,
        );
        $gift = DI()->notorm->user_coinrecord->queryAll($sql3, $params3);
        foreach ($gift as $key => $value){
            $gift[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //订阅
        $sql1 = "SELECT A.addtime, A.totalcoin as total, A.action, B.user_nicename as title FROM cmf_user_coinrecord AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.touid = :uids AND A.addtime >= :ssstime AND A.addtime <= :eeetime AND A.action = :tp ORDER BY addtime DESC LIMIT :s,:e;";
        $params1 = array(
            ':uids' => $aid,
            ':ssstime' => $start_time,
            ':eeetime' => $end_time,
            ':tp' => 6,
            ':s' => $start,
            ':e' => $pnum,
        );
        $ding = DI()->notorm->user_coinrecord->queryAll($sql1, $params1);
        foreach ($ding as $key => $value){
            $ding[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //提现
        $sql2 = "SELECT A.addtime, A.money as total, B.user_nicename as title FROM cmf_cash_record AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.uid = :uids AND A.addtime >= :sstime AND A.addtime <= :eetime AND A.status = :status ORDER BY addtime DESC LIMIT :s,:e;";
        $params2 = array(
            ':uids' => $aid,
            ':sstime' => $start_time,
            ':eetime' => $end_time,
            ':status' => 1,
            ':s' => $start,
            ':e' => $pnum,
        );
        $cash = DI()->notorm->cash_record->queryAll($sql2, $params2);
        foreach ($cash as $key => $value){
            $cash[$key]['action'] = 3;
            $cash[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //全部
        $all = array_merge($gift,$ding);
        $all_new = array_merge($all,$cash);
        $date = array_column($all_new, 'addtime');
        array_multisort($date,SORT_DESC,$all_new);

        switch ($type){
            //全部
            case 1:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $all_new];
            //礼物
            case 2:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $gift];
            //订阅
            case 3:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $ding];
            //提现
            case 4:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $cash];
        }
    }

    /**
     * 主播财务报表（主播）
     * @desc 用于 主播财务报表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[shouyi] 总收益
     * @return array info[cash_count] 总支出
     * @return array info[0][action] 类型：1-礼物 6-订阅 3-提现
     * @return string msg 提示信息
     */
    public function anchorFinance()
    {
        $uid=$this->uid;
        $token=$this->token;
        $time = checkNull($this->time);
        $type = checkNull($this->type);
        $p = checkNull($this->p);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $start_time = strtotime($time);
        $time = explode('-', $time);
        $day_num = getDaysInMonth($time[0], $time[1]);
        $end_time = $start_time + (86400 * $day_num);

        if($p < 1){
            $p = 1;
        }
        $pnum = 15;
        $start = ($p-1) * $pnum;

        //礼物,订阅总收益
        $shouyi = DI()->notorm->user_coinrecord->where('touid = ?', $uid)->where('action', [1,6])->sum('totalcoin');
        if(!$shouyi) $shouyi = 0;
        //提现总支出
        $cash_count = DI()->notorm->cash_record->where('uid = ? and status = ?', $uid, 1)->sum('money');
        if(!$cash_count) $cash_count = 0;

        //礼物
        $sql3 = "SELECT A.addtime, A.totalcoin as total, A.action, B.user_nicename as title FROM cmf_user_coinrecord AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.touid = :uids AND A.addtime >= :ssstime AND A.addtime <= :eeetime AND A.action = :tp ORDER BY addtime DESC LIMIT :s,:e;";
        $params3 = array(
            ':uids' => $uid,
            ':ssstime' => $start_time,
            ':eeetime' => $end_time,
            ':tp' => 1,
            ':s' => $start,
            ':e' => $pnum,
        );
        $gift = DI()->notorm->user_coinrecord->queryAll($sql3, $params3);
        foreach ($gift as $key => $value){
            $gift[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //订阅
        $sql1 = "SELECT A.addtime, A.totalcoin as total, A.action, B.user_nicename as title FROM cmf_user_coinrecord AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.touid = :uids AND A.addtime >= :ssstime AND A.addtime <= :eeetime AND A.action = :tp ORDER BY addtime DESC LIMIT :s,:e;";
        $params1 = array(
            ':uids' => $uid,
            ':ssstime' => $start_time,
            ':eeetime' => $end_time,
            ':tp' => 6,
            ':s' => $start,
            ':e' => $pnum,
        );
        $ding = DI()->notorm->user_coinrecord->queryAll($sql1, $params1);
        foreach ($ding as $key => $value){
            $ding[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //提现
        $sql2 = "SELECT A.addtime, A.money as total, B.user_nicename as title FROM cmf_cash_record AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.uid = :uids AND A.addtime >= :sstime AND A.addtime <= :eetime AND A.status = :status ORDER BY addtime DESC LIMIT :s,:e;";
        $params2 = array(
            ':uids' => $uid,
            ':sstime' => $start_time,
            ':eetime' => $end_time,
            ':status' => 1,
            ':s' => $start,
            ':e' => $pnum,
        );
        $cash = DI()->notorm->cash_record->queryAll($sql2, $params2);
        foreach ($cash as $key => $value){
            $cash[$key]['action'] = 3;
            $cash[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        }

        //全部
        $all = array_merge($gift,$ding);
        $all_new = array_merge($all,$cash);
        $date = array_column($all_new, 'addtime');
        array_multisort($date,SORT_DESC,$all_new);

        switch ($type){
            //全部
            case 1:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $all_new];
            //礼物
            case 2:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $gift];
            //订阅
            case 3:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $ding];
            //提现
            case 4:
                return ['code' => 0, 'msg' => 'ok', 'shouyi' => $shouyi, 'cash_count' => $cash_count, 'info' => $cash];
        }
    }


    /**
     * 家族成员列表
     * @desc 用于 家族成员列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function Member()
    {
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $familyid=checkNull($this->familyid);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $type="0";
        $familyinfo=DI()->notorm->family->where('id = ?', $familyid)->fetchOne();
        if($familyinfo['uid']==$uid)
        {
            $type="1";
        }

        $list=DI()->notorm->family_user->where('familyid = ? and state = ?', $familyid, 2)->fetchAll();

        foreach($list as $k=>$v)
        {
            $userinfo=getUserInfo($v['uid']);

            $userinfo['divide_family']=$familyinfo['divide_family'];
            if($v['divide_family'] > -1){
                $userinfo['divide_family']=$v['divide_family'];
            }
            $userinfo['fansnum']=DI()->notorm->user_attention->where('touid = ?', $v['uid'])->count();
            $list[$k]['userinfo']=$userinfo;
        }
        $info['type'] = $type;
        $info['list'] = $list;
        $info['familyid'] = $familyid;

        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }

    /**
     * 家族财务
     * @desc 用于 家族财务
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[s_type] 1:转入 2：提现
     * @return array info[total] 金额
     * @return string msg 提示信息
     */
    public function familyFinance()
    {
        $uid=$this->uid;
        $token=$this->token;
        $time = checkNull($this->time);
        $type = checkNull($this->type);
        $p = checkNull($this->p);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $start_time = strtotime($time);
        $time = explode('-', $time);
        $day_num = getDaysInMonth($time[0], $time[1]);
        $end_time = $start_time + (86400 * $day_num);

        $finfo = DI()->notorm->family->where('uid = ?', $uid)->fetchOne();
        if(!$finfo) return ['code' => 1, 'msg' => '你还不是家族长哦'];

        if($p < 1){
            $p = 1;
        }
        $pnum = 15;
        $start = ($p-1) * $pnum;

        //转入数据
        $sql1 = "SELECT A.addtime, A.total, B.user_nicename as title FROM cmf_family_profit AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE (A.familyid = :familyid AND A.total > :num AND A.addtime >= :stime AND A.addtime <= :etime) ORDER BY addtime DESC LIMIT :s,:e;";
        $params1 = array(
            ':familyid' => $finfo['id'],
            ':stime' => $start_time,
            ':etime' => $end_time,
            ':num' => 0,
            ':s' => $start,
            ':e' => $pnum,
        );
        $anchors = DI()->notorm->family_profit->queryAll($sql1, $params1);
        if($anchors){
            foreach ($anchors as $key => $value){
                $anchors[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                $anchors[$key]['s_type'] = 1;
            }
        }

        //提现
        $family_users = DI()->notorm->family_user->where('familyid = ?', $finfo['id'])->select('uid')->fetchAll();
        $family_users_ids = '';
        if($family_users){
            foreach ($family_users as $key => $value) {
                $family_users_ids .= $value['uid'] . ',';
            }
        }
        $family_users_ids = substr($family_users_ids,0,strlen($family_users_ids)-1);
        $sql2 = "SELECT A.addtime, A.money as total, B.user_nicename as title FROM cmf_cash_record AS A LEFT JOIN cmf_user AS B ON A.uid = B.id WHERE A.uid IN (:uids) AND A.addtime >= :sstime AND A.addtime <= :eetime AND A.status = :status ORDER BY addtime DESC LIMIT :s,:e;";
        $params2 = array(
            ':uids' => $family_users_ids,
            ':sstime' => $start_time,
            ':eetime' => $end_time,
            ':status' => 1,
            ':s' => $start,
            ':e' => $pnum,
        );
        $cash = DI()->notorm->cash_record->queryAll($sql2, $params2);
        if($cash){
            foreach ($cash as $key => $value){
                $cash[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                $cash[$key]['s_type'] = 2;
            }
        }

        //全部
        $all = array_merge($anchors,$cash);
        $date = array_column($all, 'addtime');
        array_multisort($date,SORT_DESC,$all);

        //总收入
        $ids = explode(',', $family_users_ids);
        $s_sum = DI()->notorm->family_profit->where('uid', $ids)->sum('total');
        if(!$s_sum) $s_sum = 0;
        //总支出
        $z_sum = DI()->notorm->cash_record->where('uid', $ids)->where('status = 1')->sum('money');
        if(!$z_sum) $z_sum = 0;
        switch ($type){
            case 1:
                return ['code' => 0, 'msg' => 'ok', 's_sum' => $s_sum, 'z_sum' => $z_sum, 'info' => $all];
            case 2:
                return ['code' => 0, 'msg' => 'ok', 's_sum' => $s_sum, 'z_sum' => $z_sum, 'info' => $cash];
            case 3:
                return ['code' => 0, 'msg' => 'ok', 's_sum' => $s_sum, 'z_sum' => $z_sum, 'info' => $anchors];
        }

//        //其他
//        $sql3 = "SELECT A.addtime, A.totalcoin as total, A.action, B.user_nicename as title FROM cmf_user_coinrecord AS A LEFT JOIN cmf_user AS B ON A.touid = B.id WHERE A.touid IN (:uids) AND A.addtime >= :ssstime AND A.addtime <= :eeetime ORDER BY addtime DESC;";
//        $params3 = array(
//            ':uids' => $family_users_ids,
//            ':ssstime' => $start_time,
//            ':eeetime' => $end_time,
//        );
//        $other = DI()->notorm->user_coinrecord->queryAll($sql3, $params3);
    }

    /**
     * 修改主播密码
     * @desc 用于 修改主播密码
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function updatePass() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=$this->uid;
        $id = $this->id;
        $token=$this->token;
        $oldpass=$this->oldpass;
        $pass=$this->pass;
        $pass2=$this->pass2;

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if($pass != $pass2){
            $rs['code'] = 1002;
            $rs['msg'] = '两次新密码不一致';
            return $rs;
        }

        $check = passcheck($pass);
        if(!$check ){
            $rs['code'] = 1004;
            $rs['msg'] = '密码为6-20位字母数字组合';
            return $rs;
        }

//        $domain = new Domain_User();
//        $info = $domain->updatePass($uid,$oldpass,$pass);
        $userinfo = DI()->notorm->user
            ->select("user_pass")
            ->where('id=?', $uid)
            ->fetchOne();
        $oldpass = setPass($oldpass);
        if ($userinfo['user_pass'] != $oldpass) {
            $rs['code'] = 1003;
            $rs['msg'] = '家族密码错误';
            return $rs;
        }
        $newpass = setPass($pass);
        $res = DI()->notorm->user
            ->where('id=?', $id)
            ->update(array("user_pass" => $newpass));
        if ($res){
            $rs['info'][0]['msg']='修改成功';
        }else{
            $rs['info'][0]['msg']='修改失败';
        }

        return $rs;
    }

    /**
     * 主播禁用启用
     * @desc 用于 主播禁用启用
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function anchorStatus()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $id = checkNull($this->id);
        $status = checkNull($this->status);

        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $res = DI()->notorm->user
            ->where('id',$id)
            ->update(['user_status'=>$status]);
        if ($res){
            return ['code' => 0, 'msg' => '成功'];
        }else{
            return ['code' => 1, 'msg' => "未更新"];
        }
    }

    /**
     * 主播报表
     * @desc 用于 主播报表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[id] 用户id
     * @return array info[user_nicename] 用户昵称
     * @return array info[live_time] 时间段内的直播总时长
     * @return string msg 提示信息
     */
    public function familyAnchor()
    {
        $familyid = checkNull($this->familyid);
        $starttime = checkNull($this->starttime);
        $endtime = checkNull($this->endtime);

        $sql = "SELECT A.uid as id, B.user_nicename, B.avatar, B.avatar_thumb FROM cmf_family_user AS A LEFT JOIN cmf_user AS B ON A.id = B.id WHERE (A.familyid = :familyid);";
        $params = array(':familyid' => $familyid);
        $anchors = DI()->notorm->family_user->queryAll($sql, $params);
        $starttime = strtotime($starttime);
        $endtime = strtotime($endtime);
        if($anchors){
            foreach ($anchors as $key => $value){
                $start = DI()->notorm->live_record
                    ->where('starttime >= ? and starttime <= ? and uid = ?', $starttime, $endtime, $value['id'])
                    ->sum('starttime');
                if(!$start) $start = 0;
                $end = DI()->notorm->live_record
                    ->where('starttime >= ? and starttime <= ? and uid = ?', $starttime, $endtime, $value['id'])
                    ->sum('endtime');
                if(!$end) $end = 0;
                $anchors[$key]['live_time'] = getSeconds($end - $start, 1);
            }
        }
        return ['code' => 0, 'msg' => 'ok', 'info' => $anchors];
    }
    /**
     * 主播钱包接口
     * @desc 用于 主播钱包接口
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['add'] 新增粉丝
     * @return array info['other'] 其他
     * @return array info['gift'] 礼物
     * @return array info['atten'] 订阅
     * @return string msg 提示信息
     */
    public function familyWallet()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);

        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        //当月
        $start = strtotime(date("Y-m-01"));
        $end = strtotime(date('Y-m-01',strtotime("+1 month")));
        //订阅
        $info['current_month']['atten'] = DI()->notorm->user_coinrecord
            ->where("action = 6 and addtime >= ? and addtime <= ?", $start, $end)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['current_month']['atten']) $info['current_month']['atten'] = 0;
        //礼物
        $info['current_month']['gift'] = DI()->notorm->user_coinrecord
            ->where("action = 1 and addtime >= ? and addtime <= ?", $start, $end)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['current_month']['gift']) $info['current_month']['gift'] = 0;

        //上月
        $last_start = strtotime(date('Y-m-01',strtotime("-1 month")));
        $last_end = strtotime(date("Y-m-01"));
        //订阅
        $info['last_month']['atten'] = DI()->notorm->user_coinrecord
            ->where("action = 6 and addtime >= ? and addtime <= ?", $last_start, $last_end)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['last_month']['atten']) $info['last_month']['atten'] = 0;
        //礼物
        $info['last_month']['gift'] = DI()->notorm->user_coinrecord
            ->where("action = 1 and addtime >= ? and addtime <= ?", $last_start, $last_end)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['last_month']['gift']) $info['last_month']['gift'] = 0;

        $today = strtotime(date('Y-m-d'));
        $yesterday = strtotime(date('Y-m-d',strtotime('-1 day')));
        //订阅
        $info['today']['atten'] = DI()->notorm->user_coinrecord
            ->where("action = 6 and addtime >= ?", $today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['today']['atten']) $info['today']['atten'] = 0;
        //礼物
        $info['today']['gift'] = DI()->notorm->user_coinrecord
            ->where("action = 1 and addtime >= ?", $today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['today']['gift']) $info['today']['gift'] = 0;
        //其他
        $info['today']['other'] = DI()->notorm->user_coinrecord
            ->where("action != 6 and action != 1 and addtime >= ?", $today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['today']['other']) $info['today']['other'] = 0;
        //新增粉丝
        $info['today']['add'] = DI()->notorm->user_attention
            ->where('touid', $uid)
            ->where("addtime >= ?", $today)
            ->count();
        if(!$info['today']['add']) $info['today']['add'] = 0;
        //昨日
        //订阅
        $info['yesterday']['atten'] = DI()->notorm->user_coinrecord
            ->where("action = 6 and addtime >= ? and addtime <= ?", $yesterday,$today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['yesterday']['atten']) $info['yesterday']['atten'] = 0;
        //礼物
        $info['yesterday']['gift'] = DI()->notorm->user_coinrecord
            ->where("action = 1 and addtime >= ? and addtime <= ?", $yesterday,$today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['yesterday']['gift']) $info['yesterday']['gift'] = 0;
        //其他
        $info['yesterday']['other'] = DI()->notorm->user_coinrecord
            ->where("action != 6 and action != 1 and addtime >= ? and addtime <= ?", $yesterday,$today)
            ->where('touid', $uid)
            ->sum('totalcoin');
        if(!$info['yesterday']['other']) $info['yesterday']['other'] = 0;
        //新增粉丝
        $info['yesterday']['add'] = DI()->notorm->user_attention
            ->where("addtime >= ? and addtime <= ?", $yesterday,$today)
            ->where('touid', $uid)
            ->count();
        if(!$info['yesterday']['add']) $info['yesterday']['add'] = 0;
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }

    /**
     * 家族收入概览
     * @desc 用于 家族收入概览
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['total'] 总收入
     * @return array info['profit'] 返佣
     * @return array info['gift'] 礼物
     * @return array info['atten'] 订阅
     * @return string msg 提示信息
     */
    public function familyStatistics()
    {
        $familyid = checkNull($this->familyid);
        $time = checkNull($this->time);

        if($time != 1 && $time != 2 && $time != 3) return ['code' => 1, 'msg' => '参数错误'];
        $family = DI()->notorm->family->where('id = ?', $familyid)->fetchOne();
        if(!$family) return ['code' => 1, 'msg' => '家族不存在'];

        $family_users = DI()->notorm->family_user->where('familyid = ?', $familyid)->select('uid')->fetchAll();
        $family_users_ids = [];
        if($family_users){
            foreach ($family_users as $key => $value) {
                $family_users_ids[$key] = $value['uid'];
            }
        }

        $today = strtotime(date('Y-m-d', time()));

        switch ($time) {
            //今天
            case 1:
                $start_time = $today;
                $end_time = $today + 86400;
                break;
            //昨天    
            case 2:
                $start_time = $today - 86400;
                $end_time = $today;
                break;
            //近七天    
            case 3:
                $start_time = $today - (86400*6);
                $end_time = $today + 86400;
                break;
        }

        //总收入
        $info['total'] = DI()->notorm->family_profit
            ->where('familyid = ? and addtime >= ? and addtime <= ?', $familyid, $start_time, $end_time)
            ->sum('total');
        if(!$info['total']) $info['total'] = 0;
        //返佣
        $info['profit'] = DI()->notorm->family_profit
            ->where('familyid = ? and addtime >= ? and addtime <= ?', $familyid, $start_time, $end_time)
            ->sum('profit');
        if(!$info['profit']) $info['profit'] = 0;
        //礼物
        $info['gift'] = DI()->notorm->user_coinrecord
            ->where("action = 1 and addtime >= ? and addtime <= ?", $start_time, $end_time)
            ->where('touid', $family_users_ids)
            ->sum('totalcoin');
        if(!$info['gift']) $info['gift'] = 0;
        //订阅
        $info['atten'] = DI()->notorm->user_coinrecord
            ->where("action = 6 and addtime >= ? and addtime <= ?", $start_time, $end_time)
            ->where('touid', $family_users_ids)
            ->sum('totalcoin');   
        if(!$info['atten']) $info['atten'] = 0;

        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }

    /**
     * 家族长添加族员
     * @desc 用于 家族长添加族员
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function familyAdd() {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $user_login = checkNull($this->user_login);
        $user_pass = checkNull($this->user_pass);
        $user_pass2 = checkNull($this->user_pass2);
    
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $finfo = DI()->notorm->family->where('uid = ?', $uid)->fetchOne();
        if(!$finfo) return ['code' => 1, 'msg' => '您还不是家族长哦'];
        if($user_pass2 != $user_pass) return ['code' => 1, 'msg' => '确认密码错误']; 

        $user_pass = setPass($user_pass);

        //生成邀请码
        do {
            $unique = false;
            $invite_code = $this::createCode();
            $rs_row = DI()->notorm->user
                ->where('invite_code=?', $invite_code)
                ->fetchOne();
            if (!empty($rs_row)) $unique = true;
        } while ($unique);

        $configpri = getConfigPri();
        $reg_reward = $configpri['reg_reward'];
        $data = array(
            'user_login' => $user_login,
            'mobile' => $user_login,
            'user_nicename' => '手机用户' . substr($user_login, -4),
            'user_pass' => $user_pass,
            'signature' => '这家伙很懒，什么都没留下',
            'avatar' => '/default.jpg',
            'avatar_thumb' => '/default_thumb.jpg',
            'last_login_ip' => $_SERVER['REMOTE_ADDR'],
            'create_time' => time(),
            'user_status' => 1,
            "user_type" => 2,//会员
            "source" => '家族长手动添加',
            "coin" => $reg_reward,
            "invite_code" => $invite_code
        );

        $isexist = DI()->notorm->user
            ->select('id')
            ->where('user_login=?', $user_login)
            ->fetchOne();
        if ($isexist) {
            $rs['code'] = 1;
            $rs['msg'] = '该账号已存在';
            return $rs;
        }

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        $rs = DI()->notorm->user->insert($data);

        $rs3_data = [
            'uid' => $rs['id'],
            'familyid' => $finfo['id'],
            'state' => 2,
            'addtime' => time(),
            'uptime' => time(),
        ];   

        $rs3 = DI()->notorm->family_user->insert($rs3_data);

        if ($rs && $rs3){
            DI()->notorm->commit('db_appapi');
            $uid = $rs['id'];
            if ($reg_reward > 0) {
                $insert = array("type" => '1', "action" => '11', "uid" => $uid, "touid" => $uid, "giftid" => 0, "giftcount" => 1, "totalcoin" => $reg_reward, "showid" => 0, "addtime" => time());
                DI()->notorm->user_coinrecord->insert($insert);
            }
            $code = $this->createCode();
            $code_info = array('uid' => $uid, 'code' => $code);
            $isexist = DI()->notorm->agent_code
                ->select("*")
                ->where('uid = ?', $uid)
                ->fetchOne();
            if ($isexist) {
                DI()->notorm->agent_code->where('uid = ?', $uid)->update($code_info);
            } else {
                DI()->notorm->agent_code->insert($code_info);
            }
            $rs['code'] = 0;
            $rs['msg'] = '添加成功';
            return $rs;
        }else{
            DI()->notorm->rollback('db_appapi');
            $rs['code'] = 1;
            $rs['msg'] = '添加失败';
            return $rs;
        }
    }

    /* 生成邀请码 */
    protected function createCode($len = 8, $format = 'ALL2')
    {
        $is_abc = $is_numer = 0;
        $password = $tmp = '';
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'ALL2':
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }

        while (strlen($password) < $len) {
            $tmp = substr($chars, (mt_rand() % strlen($chars)), 1);
            if (($is_numer <> 1 && is_numeric($tmp) && $tmp > 0) || $format == 'CHAR') {
                $is_numer = 1;
            }
            if (($is_abc <> 1 && preg_match('/[a-zA-Z]/', $tmp)) || $format == 'NUMBER') {
                $is_abc = 1;
            }
            $password .= $tmp;
        }
        if ($is_numer <> 1 || $is_abc <> 1 || empty($password)) {
            $password = $this->createCode($len, $format);
        }
        if ($password != '') {

            $oneinfo = DI()->notorm->agent_code
                ->select("uid")
                ->where("code=?", $password)
                ->fetchOne();

            if (!$oneinfo) {
                return $password;
            }
        }
        $password = $this->createCode($len, $format);
        return $password;
    }

    /**
     * 主播首页
     * @desc 用于 主播首页
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[sum] 主播今日收益
     * @return array info[time] 今日直播时长
     * @return string msg 提示信息
     */
    public function anchorInfo()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);

        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        //开播时长
        $list = DI()->notorm->live_record
            ->select('starttime,endtime')
            ->where('uid',$uid)
            ->where('starttime >= ?',strtotime(date('Y-m-d')))
            ->fetchAll();
        $times = '00:00:00';
        if ($list){
            $temp = 0;
            foreach ($list as $v){
                $temp += $v['endtime']-$v['starttime'];
            }
            $times = gmdate('H:i:s',$temp);
        }
        $info['time'] = $times;

        //主播收益
        $sum = DI()->notorm->user_coinrecord
            ->where('touid',$uid)
            ->where('addtime >= ?',strtotime(date('Y-m-d')))
            ->where('action',[1,2,6,7,10])
            ->sum('totalcoin');
        if ($sum){
            $info['sum'] = $sum;
        }else{
            $info['sum'] = 0;
        }
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }
}
