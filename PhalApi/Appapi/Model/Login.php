<?php

class Model_Login extends PhalApi_Model_NotORM
{

    protected $fields = 'issuper,id,mobile,count_Withdrawal,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,votes,votestotal,birthday,user_status,end_bantime,login_type,last_login_time,qq,bat,wechat';

    /* 主播登录 */
    public function anchorLogin($user_login, $user_pass)
    {

        $user_pass = setPass($user_pass);

        $info = DI()->notorm->user
            ->select($this->fields . ',user_pass,coin,v_up_time')
            ->where('user_login=? and user_type="2"', $user_login)
            ->fetchOne();
        if (!$info || $info['user_pass'] != $user_pass) {
            return 1001;
        }
        unset($info['user_pass']);

        if ($info['user_status'] == '0') {
            return 1003;
        }


        if ($info['end_bantime'] > time()) {
            return 1002;
        }


        if ($info['user_status'] == '3') {
            return 1004;
        }

        if($info['v_up_time'] == null || strtotime(date('Y-m-d', $info['v_up_time'])) != strtotime(date('Y-m-d', time()))){
                $res = DI()->notorm->user
                    ->where("id = ?", $info['id'])
                    ->update([
                        'viewing_num' => 3,
                        'is_share' => 2,
                        'v_up_time' => strtotime(date('Y-m-d', time())),
                    ]);
                if(!$res) return 1005;
            }

        unset($info['user_status']);

        unset($info['end_bantime']);

        $info['isreg'] = '0';


        if ($info['last_login_time'] == 0) {
            $info['isreg'] = '1';
        }

        $info['isagent'] = '0';
        if ($info['isreg'] == 1) {
            $configpri = getConfigPri();
            if ($configpri['agent_switch'] == 1) {
                $info['isagent'] = '1';
            }
        }


        if ($info['birthday']) {
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        } else {
            $info['birthday'] = '';
        }

        $info['level'] = getLevel($info['consumption']);
        $info['level_anchor'] = getLevelAnchor($info['votestotal']);

        $token = md5(md5($info['id'] . $user_login . time()));

        $info['token'] = $token;
        $info['avatar'] = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);

        $temp = DI()->notorm->family
            ->where('uid',$info['id'])
            ->fetchOne();
            
        if ($temp){
            $info['is_family'] = 1;
        }else{
            $info['is_family'] = 0;
        }
        $info['family_id'] = $temp['id'];
        $this->updateToken($info['id'], $token);


        return $info;
    }

    /* 会员登录 */
    public function userLogin($mobile, $code,$ispass)
    {
        $where = 'mobile=? and user_type="2"';
        if($ispass == false)
        {
            $where = 'user_login=? and user_type="2"';
        }
        
        $info = DI()->notorm->user
            ->select($this->fields . ',user_pass,coin,v_up_time')
            ->where($where, $mobile)
            ->fetchOne();
        $reg_mobile_key = 'reg_mobile_' . $mobile;
        $reg_mobile = DI()->redis->Get($reg_mobile_key);
        // var_dump($reg_mobile);die;
        if (!$info && $code && $ispass) {
            if(empty($reg_mobile))
            {
                $rs['code'] = 1001;
                $rs['msg'] = '请先获取验证码';
                return 1001;
            }
            $reg_mobile = json_decode($reg_mobile, true);
            if(!$reg_mobile['mobile'] || !$reg_mobile['mobile_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '请先获取验证码';
                return 1001;
            }


            if($mobile != $reg_mobile['mobile']){
                $rs['code'] = 1001;
                $rs['msg'] = '手机号码不一致';
                return 1006;
            }

            if($code != $reg_mobile['mobile_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '验证码错误';
                return 1007;
            }
            return 1008;
        }
        

        if ($info['user_status'] == '0') {
            return 1003;
        }


        if ($info['end_bantime'] > time()) {
            return 1002;
        }


        if ($info['user_status'] == '3') {
            return 1004;
        }

        unset($info['user_status']);

        // unset($info['end_bantime']);

        $info['isreg'] = '0';


        if ($info['last_login_time'] == 0) {
            $info['isreg'] = '1';
        }

        $info['isagent'] = '0';
        if ($info['isreg'] == 1) {
            $configpri = getConfigPri();
            if ($configpri['agent_switch'] == 1) {
                $info['isagent'] = '1';
            }
        }
       

        if ($info['birthday']) {
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        } else {
            $info['birthday'] = '';
        }

        $family_user = DI()->notorm->family_user->where('uid', $info['id'])->fetchOne();
        $patriarch = DI()->notorm->family->where('uid', $info['id'])->fetchOne();
        if($patriarch){
            $info['family_id'] = $patriarch['id'];
            $info['is_patriarch'] = 1;
        }else if ($family_user && !$patriarch){
            $info['family_id'] = $family_user['familyid'];
            $info['is_patriarch'] = 2;
        }else if (!$family_user && !$patriarch){
            $info['family_id'] = '';
            $info['is_patriarch'] = '';
        }

        if($info['v_up_time'] == null || strtotime(date('Y-m-d', $info['v_up_time'])) != strtotime(date('Y-m-d', time()))){
            $res = DI()->notorm->user
                ->where("id = {$info['id']}")
                ->update([
                    'viewing_num' => 3,
                    'is_share' => 2,
                    'v_up_time' => strtotime(date('Y-m-d', time())),
                ]);
            if(!$res) return 1005;
        }


        $info['level'] = getLevel($info['consumption']);
        $info['level_anchor'] = getLevelAnchor($info['votestotal']);

        $token = md5(md5($info['id'] . $user_login . time()));

        $info['token'] = $token;
        $info['avatar'] = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);

        if ($info['is_dai'] == 2){
            $info['profit'] = 0;
        }else{

            $total = DI()->notorm->user_change
                ->where('user_id',$info['id'])
                ->where('change_type',7)
                ->sum('change_money');

            $info['profit'] = $total;
        }

        $this->updateToken($info['id'], $token);

        return $info;
    }

    public function getUserban($user_login)
    {
        $userinfo = DI()->notorm->user
            ->select('id,end_bantime')
            ->where('mobile=? and user_type="2"', $user_login)
            ->fetchOne();
        return $this->baninfo($userinfo['id'], $userinfo['end_bantime']);

    }

    public function baninfo($uid, $end_bantime)
    {
        $rs = array("ban_long" => 0, "ban_lon1g" => 0, "ban_reason" => "", "end_bantime" => 0, "ban_tip" => '');
        $baninfo = DI()->notorm->user_banrecord
            ->select('*')
            ->where('uid=? ', $uid)
            ->fetchOne();
        if ($baninfo) {
            $rs['ban_long'] = getBanSeconds($baninfo['ban_long'] - time());
            $rs['ban_lon1g'] = $baninfo['ban_long'];
            $rs['ban_reason'] = $baninfo['ban_reason'];
            $rs['end_bantime'] = date("Y-m-d", $end_bantime);
            $rs['ban_tip'] = "本次封禁时间为" . $rs['ban_long'] . "，账号将于" . $rs['end_bantime'] . "解除封禁。";
        }
        return $rs;
    }

    public function getThirdUserban($openid, $type)
    {

        $userinfo = DI()->notorm->user
            ->select('id,end_bantime')
            ->where('openid=? and login_type=? ', $openid, $type)
            ->fetchOne();

        $rs = $this->baninfo($userinfo['id'], $userinfo['end_bantime']);
        return $rs;
    }

    /* 主播注册 */
    public function anchorReg($user_login, $user_pass, $source, $invite)
    {

        $user_pass = setPass($user_pass);

        //判断邀请码
        if ($invite) {
            $parent = DI()->notorm->user->where('invite_code=?', $invite)->fetchOne();
            if (!$parent) {
                return 1008;
            }
        } else {
            $parent = 0;
        }

        //生成邀请码
        do {
            $unique = false;
            $invite_code = $this->createCode();
            $rs_row = DI()->notorm->user
                ->where('invite_code=?', $invite_code)
                ->fetchOne();
            if (!empty($rs_row)) $unique = true;
        } while ($unique);

        $configpri = getConfigPri();
        $reg_reward = $configpri['reg_reward'];
        $avatar = 'https://img.jinchengyibai.cn/avatar/'. rand(1,20).'.jpg';
        $data = array(
            'user_login' => $user_login,
            'mobile' => $user_login,
            'user_nicename' => randNiceName(),
            'user_pass' => $user_pass,
            'signature' => '这家伙很懒，什么都没留下',
            'avatar' => $avatar,
            'avatar_thumb' => $avatar,
            'last_login_ip' => $_SERVER['REMOTE_ADDR'],
            'create_time' => time(),
            'user_status' => 1,
            "user_type" => 2,//会员
            "source" => $source,
            "coin" => $reg_reward,
            "invite_code" => $invite_code
        );

        $isexist = DI()->notorm->user
            ->select('id')
            ->where('user_login=?', $user_login)
            ->fetchOne();
        if ($isexist) {
            return 1006;
        }

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        $rs = DI()->notorm->user->insert($data);
//        if (!$rs) {
//            return 1007;
//        }

        //parent_id和层级
        $user_id = DI()->notorm->user->insert_id();
        if ($parent) {
            $parent_id = $parent['id'];
            $invite_level = $parent['invite_level'] . $user_id . '-';
        } else {
            $parent_id = 0;
            $invite_level = $user_id . '-';
        }

        $rs2 = DI()->notorm->user
            ->where('id=?',$user_id)
            ->update(
                ['parent_id'=>$parent_id,'invite_level'=>$invite_level]
            );
        if ($rs && $rs2){
            DI()->notorm->commit('db_appapi');
        }else{
            DI()->notorm->rollback('db_appapi');
            return 1007;
        }

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
        return 1;
    }

    /* 会员注册 */
    public function userReg($user_login, $user_pass, $source,$invite)
    {

        $user_pass = setPass($user_pass);

        //判断邀请码
        if (strlen($invite) == 6){
            $user_invite = DI()->notorm->user_invite->where('invite_key',$invite)->fetchOne();
            if (!$user_invite){
                $user_invite['uid'] = 1;
            }
            $parent = DI()->notorm->user->where('id',$user_invite['uid'])->fetchOne();
        }else{
            if ($invite) {
                $parent = DI()->notorm->user->where('invite_code=?', $invite)->fetchOne();
                if (!$parent) {
                    return 1008;
                }
            } else {
                    $parent = 0;
            }
        }

        //生成邀请码
        do {
            $unique = false;
            $invite_code = $this->createCode();
            $rs_row = DI()->notorm->user
                ->where('invite_code=?', $invite_code)
                ->fetchOne();
            if (!empty($rs_row)) $unique = true;
        } while ($unique);

        $configpri = getConfigPri();
        $reg_reward = $configpri['reg_reward'];
        $avatar = 'https://img.jinchengyibai.cn/avatar/'. rand(1,20).'.jpg';
     
        // var_dump(NICENAME);die;
        $data = array(
            'user_login' => $user_login,
            'mobile' => '',
            'user_nicename' => randNiceName(),
            'user_pass' => $user_pass,
            'signature' => '这家伙很懒，什么都没留下',
            'avatar' => $avatar,
            'avatar_thumb' => $avatar,
            'last_login_ip' => $_SERVER['REMOTE_ADDR'],
            'create_time' => time(),
            'user_status' => 1,
            "user_type" => 2,//会员
            "is_dai" => 2,
            "source" => $source,
            "coin" => $reg_reward,
            "invite_code" => $invite_code
        );

        $isexist = DI()->notorm->user
            ->select('*')
            ->where('user_login=?', $user_login)
            ->fetchOne();
        if ($isexist) {
            if($isexist['v_up_time'] == null || strtotime(date('Y-m-d', $isexist['v_up_time'])) != strtotime(date('Y-m-d', time()))){
                $res = DI()->notorm->user
                    ->where("id = {$isexist['id']}")
                    ->update([
                        'viewing_num' => 3,
                        'is_share' => 2,
                        'v_up_time' => strtotime(date('Y-m-d', time())),
                    ]);
                if(!$res) return 1005;
            }
            return ['code' => 1006, 'user_pass' => $isexist['user_pass']];
        }

        $user_rate = [];

        //返点类型
//        $rates = DI()->notorm->user_rate
//            ->group('platform')
//            ->select('platform,remark')
//            ->fetchAll();
        $rates = DI()->config->get('app.rate_plat');
        foreach ($rates as $v){
            $user_rate[] = [
                'platform' => $v['platform'],
                'remark' => $v['remark'],
                'rate' => 0
            ];
        }

        if (strlen($invite) == 6){
            $data['is_dai'] = 1;
            $data['invite_key'] = $invite;
//            $data['rate'] = $user_invite['rate'];
            foreach ($user_rate as $k => $v){
                if ($v['platform'] == 1){
                    $user_rate[$k]['rate'] = $user_invite['rate'];
                }
            }
        }

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        $rs = DI()->notorm->user->insert($data);
//        if (!$rs) {
//            return 1007;
//        }

        //parent_id和层级
        $user_id = DI()->notorm->user->insert_id();
        if ($parent) {
            $parent_id = $parent['id'];
            $invite_level = $parent['invite_level'] . $user_id . '-';
        } else {
            $parent_id = 0;
            $invite_level = $user_id . '-';
        }
     
        foreach ($user_rate as $k => $v){
            $user_rate[$k]['user_id'] = $user_id;
        }
//        var_dump($user_rate);die;
        $rs3 = DI()->notorm->user_rate->insert_multi($user_rate);
    
        $rs2 = DI()->notorm->user
            ->where('id=?',$user_id)
            ->update(
                ['parent_id'=>$parent_id,'invite_level'=>$invite_level]
            );
    
        if ($rs && $rs2 && $rs3){
            DI()->notorm->commit('db_appapi');
        }else{
            DI()->notorm->rollback('db_appapi');
            return ['code' => 1007];
        }
            

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
        
        return ['code'=>1];
    }

    /* 找回密码 */
    public function userFindPass($user_login, $user_pass)
    {
        $isexist = DI()->notorm->user
            ->select('id')
            ->where('user_login=? and user_type="2"', $user_login)
            ->fetchOne();
        if (!$isexist) {
            return 1006;
        }
        $user_pass = setPass($user_pass);

        return DI()->notorm->user
            ->where('id=?', $isexist['id'])
            ->update(array('user_pass' => $user_pass));

    }

    /* 第三方会员登录 */
    public function userLoginByThird($openid, $type, $nickname, $avatar, $source)
    {
        $info = DI()->notorm->user
            ->select($this->fields)
            ->where('openid=? and login_type=? ', $openid, $type)
            ->fetchOne();
        $configpri = getConfigPri();
        if (!$info) {
            /* 注册 */
            $user_pass = 'yunbaokeji';
            $user_pass = setPass($user_pass);
            $user_login = $type . '_' . time() . rand(100, 999);

            if (!$nickname) {
                $nickname = $type . '用户-' . substr($openid, -4);
            } else {
                $nickname = urldecode($nickname);
            }
            if (!$avatar) {
                $avatar = '/default.jpg';
                $avatar_thumb = '/default_thumb.jpg';
            } else {
                $avatar = urldecode($avatar);
                // $avatar_a=explode('/',$avatar);
                // $avatar_a_n=count($avatar_a);
                // if($type=='qq'){
                // $avatar_a[$avatar_a_n-1]='100';
                // $avatar_thumb=implode('/',$avatar_a);
                // }else if($type=='wx'){
                // $avatar_a[$avatar_a_n-1]='64';
                // $avatar_thumb=implode('/',$avatar_a);
                // }else{
                $avatar_thumb = $avatar;
                // }

            }
            $reg_reward = $configpri['reg_reward'];
            $data = array(
                'user_login' => $user_login,
                'user_nicename' => $nickname,
                'user_pass' => $user_pass,
                'signature' => '这家伙很懒，什么都没留下',
                'avatar' => $avatar,
                'avatar_thumb' => $avatar_thumb,
                'last_login_ip' => $_SERVER['REMOTE_ADDR'],
                'create_time' => time(),
                'user_status' => 1,
                'openid' => $openid,
                'login_type' => $type,
                "user_type" => 2,//会员
                "source" => $source,
                "coin" => $reg_reward,
            );

            $rs = DI()->notorm->user->insert($data);

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

            $info['id'] = $uid;
            $info['user_nicename'] = $data['user_nicename'];
            $info['avatar'] = get_upload_path($data['avatar']);
            $info['avatar_thumb'] = get_upload_path($data['avatar_thumb']);
            $info['sex'] = '2';
            $info['signature'] = $data['signature'];
            $info['coin'] = '0';
            $info['login_type'] = $data['login_type'];
//            $info['province'] = '';
//            $info['city'] = '';
            $info['birthday'] = '';
            $info['consumption'] = '0';
            $info['user_status'] = 1;
            $info['last_login_time'] = 0;
        } else {
            /* 更新头像 */
            /* if(!$avatar){
                $avatar='/default.jpg';
                $avatar_thumb='/default_thumb.jpg';
            }else{
                $avatar=urldecode($avatar);
                $avatar_thumb=$avatar;
            }

            $info['avatar']=$avatar;
            $info['avatar_thumb']=$avatar_thumb;

            $data=array(
                'avatar' =>$avatar,
                'avatar_thumb' =>$avatar_thumb,
            ); */

        }

        if ($info['user_status'] == '0') {
            return 1003;
        }


        if ($info['end_bantime'] > time()) {
            return 1002;
        }

        //判断用户是否注销
        if ($info['user_status'] == 3) {
            return 1004;
        }

        unset($info['user_status']);

        unset($info['end_bantime']);

        $info['isreg'] = '0';

        if ($info['last_login_time'] == 0) {
            $info['isreg'] = '1';
        }

        $info['isagent'] = '0';
        if ($info['isreg'] == 1) {
            $configpri = getConfigPri();
            if ($configpri['agent_switch'] == 1) {
                $info['isagent'] = '1';
            }
        }

        if ($info['birthday']) {
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        } else {
            $info['birthday'] = '';
        }

        unset($info['last_login_time']);

        $info['level'] = getLevel($info['consumption']);

        $info['level_anchor'] = getLevelAnchor($info['votestotal']);

        $token = md5(md5($info['id'] . $openid . time()));

        $info['token'] = $token;
        $info['avatar'] = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);

        $this->updateToken($info['id'], $token);

        return $info;
    }

    /* 更新token 登陆信息 */
    public function updateToken($uid, $token, $data = array())
    {
        $nowtime = time();
        $expiretime = $nowtime + 60 * 60 * 24 * 300;
        
        DI()->notorm->user
            ->where('id=?', $uid)
            ->update(array('last_login_time' => $nowtime, "last_login_ip" => getIP()));

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

        setcaches("token_" . $uid, $token_info);

        return 1;
    }

    /* 生成邀请码 */
    public function createCode($len = 8, $format = 'ALL2')
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

    /* 更新极光ID */
    public function upUserPush($uid, $pushid)
    {

        $isexist = DI()->notorm->user_pushid
            ->select('*')
            ->where('uid=?', $uid)
            ->fetchOne();
        if (!$isexist) {
            DI()->notorm->user_pushid->insert(array('uid' => $uid, 'pushid' => $pushid));
        } else if ($isexist['pushid'] != $pushid) {
            DI()->notorm->user_pushid->where('uid=?', $uid)->update(array('pushid' => $pushid));
        }
        return 1;
    }

    //获取注销账号条件
    public function getCancelCondition($uid)
    {

        $res = array('list' => array(), 'can_cancel' => '0');

        $list = array(
            '0' => array(
                'title' => '1、账号内无大额未消费或未提现的财产',
                'content' => '你账号内无未结清的欠款、资金和虚拟权益，无正在处理的提现记录；注销后，账户中的虚拟权益等将作废无法恢复。',
                'is_ok' => '0'
            ),
            '1' => array(
                'title' => '2、账号无其它正在进行中的业务及争议纠纷',
                'content' => '本账号内已无其它正在进行中的经营性业务、未完成的交易、无任何未处理完成的纠纷（比如退款申请、退款中、待收货等）',
                'is_ok' => '0'
            )
        );

        //获取用户的映票、钻石、余额
        $userinfo = DI()->notorm->user->where("id=?", $uid)->select("coin,votes,balance")->fetchOne();

        //获取用户映票提现未处理记录
        $votes_cashlist = DI()->notorm->cash_record->where("uid=? and status=0", $uid)->fetchAll();
        //获取余额提现记录未处理记录
        $balance_cashlist = DI()->notorm->user_balance_cashrecord->where("uid=? and status=0", $uid)->fetchAll();

        //钻石小于100，映票小于100，余额为0
        if ($userinfo['coin'] < 100 && $userinfo['votes'] < 100 && $userinfo['balance'] == 0 && !$votes_cashlist && !$balance_cashlist) {
            $list[0]['is_ok'] = '1';
        }

        //获取用户作为买家的交易记录
        $buyer_orderlist = DI()->notorm->shop_order->where("uid=? and (status=0 or status=1 or status=2 or (status=5 and refund_status=0))", $uid)->select("id,status")->fetchAll(); //订单待付款、待发货、待收货、待退款、退款

        //获取用户作为卖家的交易记录
        $seller_orderlist = DI()->notorm->shop_order->where("shop_uid=? and (status=0 or status=1 or status=2 or (status=5 and refund_status=0))", $uid)->select("id,status")->fetchAll();


        if (!$buyer_orderlist && !$seller_orderlist) {
            $list[1]['is_ok'] = '1';
        }

        if ($list[0]['is_ok'] == 1 && $list[1]['is_ok'] == 1) {
            $res['can_cancel'] = '1';
        }

        $res['list'] = $list;

        return $res;

    }

    //注销账号
    public function cancelAccount($uid)
    {


        $condition = $this->getCancelCondition($uid);

        if (!$condition['can_cancel']) {
            return 1001;
        }


        $now = time();

        //审核通过的商品全部下架
        DI()->notorm->shop_goods->where("uid=? and status=1", $uid)->update(array('status' => -2, 'uptime' => $now));
        //付费内容下架
        DI()->notorm->paidprogram->where("uid=?", $uid)->update(array('status' => -1));
        //修改用户昵称
        DI()->notorm->user->where("id=?", $uid)->update(array('user_nicename' => '用户已注销', 'user_status' => 3));
        //未审核的视频改为拒绝
        DI()->notorm->video->where("uid=? and status=0", $uid)->update(array('status' => 2));
        //上架的视频改为下架
        DI()->notorm->video->where("uid=? and status=1 and isdel=0", $uid)->update(array('isdel' => 1));
        //未审核的动态拒绝
        DI()->notorm->dynamic->where("uid=? and status=0", $uid)->update(array('status' => 2));
        //已经审核通过的动态下架
        DI()->notorm->dynamic->where("uid=? and status=1 and isdel=0", $uid)->update(array('isdel' => 1));

        delcache("userinfo_" . $uid);
        return 1;

    }
}
