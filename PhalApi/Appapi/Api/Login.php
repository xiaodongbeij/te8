<?php
/**
 * 登录、注册
 */
if (!session_id()) session_start();
class Api_Login extends PhalApi_Api {
    public function getRules() {
        return array(
            'anchorLogin' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),

            ),
            'userLogin' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '手机号'),
                'code' => array('name' => 'code', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'10', 'desc' => '验证码'),
            ),
            'anchorReg' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string','require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
                'user_pass2' => array('name' => 'user_pass2', 'type' => 'string',  'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
                'invite' => array('name' => 'invite', 'type' => 'string', 'min' => 8, 'max' => 8,'require' => false,   'desc' => '邀请码(可选)'),
                'source' => array('name' => 'source', 'type' => 'string',  'default'=>'pc', 'desc' => '来源设备'),
                'source_type' => array('name' => 'source_type', 'type' => 'int',  'default'=>'0', 'desc' => '0：直播demo；1：小程序'),
            ),
            'userReg' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string','require' => true,  'min' => '6',  'max'=>'40', 'desc' => '账号或设备IMEI'),
                'source' => array('name' => 'source', 'type' => 'string', 'require' => true,  'default'=>'pc', 'desc' => '来源设备'),
                'invite' => array('name' => 'invite', 'type' => 'string', 'min' => 6, 'max' => 8,'require' => false,   'desc' => '邀请码(可选)'),
//                'type' => array('name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 2,'require' => false, 'desc' => '是否代理,1-代理'),
            ),
            'userFindPass' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
                'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
            ),
           
            'getCode' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
            ),

            'getForgetCode' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
            ),
            

            'logout' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
            ),

            'upUserPush'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'pushid' => array('name' => 'pushid', 'type' => 'string', 'desc' => '极光ID'),
            ),

            'getCancelCondition'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
            ),

            'cancelAccount'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
            ),
        );
    }

    /**
     * 会员登陆 需要密码
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].family_id 家族ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].is_family 是否家族长1-是
     * @return string info[0].service_address 客服地址
     * @return string msg 提示信息
     */
    public function anchorLogin() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $user_login=checkNull($this->user_login);
        $user_pass=checkNull($this->user_pass);

        $domain = new Domain_Login();
        $info = $domain->anchorLogin($user_login,$user_pass);
        
        if($info==1001){
            $rs['code'] = 1001;
            $rs['msg'] = '账号或密码错误';
            return $rs;
        }else if($info==1002){
            $rs['code'] = 1002;
            //禁用信息
            $baninfo=$domain->getUserban($user_login);
            $rs['info'][0] =$baninfo;
            return $rs;
        }else if($info==1003){
            $rs['code'] = 1003;
            $rs['msg'] = '该账号已被禁用';
            return $rs;
        }else if($info==1004){
            $rs['code'] = 1004;
            $rs['msg'] = '该账号已注销';
            return $rs;
        }
        $config = getConfigPub();
        $info['service_address'] = $config['service_address'];


        $rs['info'][0] = $info;




        return $rs;
    }

    /**
     * 会员登陆 手机+短信验证码
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].end_bantime 禁言时间
     * @return string msg 提示信息
     */
    public function userLogin() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $mobile=checkNull($this->mobile);
        $code=checkNull($this->code);

        $domain = new Domain_Login();
        $info = $domain->userLogin($mobile,$code);

        if($info==1001){
            $rs['code'] = 1001;
            $rs['msg'] = '该手机号未注册';
            return $rs;
        }else if($info==1002){
            $rs['code'] = 1002;
            //禁用信息
            $baninfo=$domain->getUserban($user_login);
            $rs['info'][0] =$baninfo;
            return $rs;
        }else if($info==1003){
            $rs['code'] = 1003;
            $rs['msg'] = '该账号已被禁用';
            return $rs;
        }else if($info==1004){
            $rs['code'] = 1004;
            $rs['msg'] = '该账号已注销';
            return $rs;
        }else if($info == 1005){
            $rs['code'] = 1005;
            $rs['msg'] = '该账号登录异常,请联系客服';
            return $rs;
        }else if($info == 1006){
            $rs['code'] = 1006;
            $rs['msg'] = '手机号码不一致';
            return $rs;
        }else if($info == 1007){
            $rs['code'] = 1007;
            $rs['msg'] = '验证码错误';
            return $rs;
        }else if($info == 1008){
            $rs['code'] = 1008;
            $rs['msg'] = '该手机号未注册';
            return $rs;
        }
        
        $reg_mobile_key = 'reg_mobile_' . $mobile;
        DI()->redis->del($reg_mobile_key);

        $rs['info'][0] = $info;

        return $rs;
    }
    /**
     * 会员注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].family_id 所在家族id
     * @return string info[0].is_patriarch 是否是家族长
     * @return string msg 提示信息
     */
    public function userReg() {

        $rs = array('code' => 0, 'msg' => '注册成功', 'info' => array());
        $user_login=checkNull($this->user_login);
        $source=checkNull($this->source);
 
        $invite = checkNull($this->invite);
        
        $user_pass = setPass(time());
        $domain = new Domain_Login();
        $info = $domain->userReg($user_login,$user_pass,$source,$invite);
    
        if($info['code']==1006){
            $rs['msg'] = '登录成功';
            $user_pass = $info['user_pass'];
        }else if($info['code'] ==1007 || $info['code'] == 1005){
            $rs['code'] = 1007;
            $rs['msg'] = '登录失败，请重试';
            return $rs;
        }
    
        $domain = new Domain_Login();
        $info = $domain->userLogin($user_login,$user_pass,false);
  
        $rs['info'][0] = $info;

        if($invite){
            $user_info = DI()->notorm->user->where('invite_code = ?', $invite)->fetchOne();
            $today = strtotime(date('Y-m-d', time()));
            $today_share_sum = DI()->notorm->user->where('parent_id=? and create_time > ?', $user_info['id'], $today)->count();
            $site = getConfigPri();
            if($today_share_sum >= $site['look_video']){
                $res = DI()->notorm->user->where('invite_code=?', $invite)->update(['is_share' => 1]);
                if(!$res) return ['code' => 1, 'msg' => '注册失败'];
            }
        }

        return $rs;
    }
    /**
     * 主播注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function anchorReg() {

        $rs = array('code' => 0, 'msg' => '注册成功', 'info' => array());

        $user_login=checkNull($this->user_login);
        $user_pass=checkNull($this->user_pass);
        $user_pass2=checkNull($this->user_pass2);
        $source=checkNull($this->source);
        $code=checkNull($this->code);
        $source_type=checkNull($this->source_type);
        $invite = checkNull($this->invite);

        if($source_type!='1'){

//            if(!$_SESSION['reg_mobile'] || !$_SESSION['reg_mobile_code']){
//                $rs['code'] = 1001;
//                $rs['msg'] = '请先获取验证码';
//                return $rs;
//            }
//
//            if($user_login!=$_SESSION['reg_mobile']){
//                $rs['code'] = 1001;
//                $rs['msg'] = '手机号码不一致';
//                return $rs;
//            }
//
//            if($code!=$_SESSION['reg_mobile_code']){
//                $rs['code'] = 1002;
//                $rs['msg'] = '验证码错误';
//                return $rs;
//            }

        }

        if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;
        }

        $check = passcheck($user_pass);

        if(!$check){
            $rs['code'] = 1004;
            $rs['msg'] = '密码为6-20位字母数字组合';
            return $rs;
        }

        $domain = new Domain_Login();
        $info = $domain->anchorReg($user_login,$user_pass,$source,$invite);

        if($info==1006){
            $rs['code'] = 1006;
            $rs['msg'] = '该手机号已被注册！';
            return $rs;
        }else if($info==1007){
            $rs['code'] = 1007;
            $rs['msg'] = '注册失败，请重试';
            return $rs;
        }else if ($info==1008){
            $rs['code'] = 1008;
            $rs['msg'] = '邀请码错误';
        }

        $rs['info'][0] = $info;

        $_SESSION['reg_mobile'] = '';
        $_SESSION['reg_mobile_code'] = '';
        $_SESSION['reg_mobile_expiretime'] = '';

        return $rs;
    }
    /**
     * 会员找回密码
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info
     * @return string msg 提示信息
     */
    public function userFindPass() {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $user_login=checkNull($this->user_login);
        $user_pass=checkNull($this->user_pass);
        $user_pass2=checkNull($this->user_pass2);
        $code=checkNull($this->code);
        $source_type=checkNull($this->source_type);//0:直播demo；1：小程序

        if($source_type!='1'){

            if(!$_SESSION['forget_mobile'] || !$_SESSION['forget_mobile_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '请先获取验证码';
                return $rs;
            }

            if($user_login!=$_SESSION['forget_mobile']){
                $rs['code'] = 1001;
                $rs['msg'] = '手机号码不一致';
                return $rs;
            }

            if($code!=$_SESSION['forget_mobile_code']){
                $rs['code'] = 1002;
                $rs['msg'] = '验证码错误';
                return $rs;
            }

        }




        if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;
        }

        $check = passcheck($user_pass);
        if(!$check){
            $rs['code'] = 1004;
            $rs['msg'] = '密码为6-20位字母数字组合';
            return $rs;
        }

        $domain = new Domain_Login();
        $info = $domain->userFindPass($user_login,$user_pass);

        if($info==1006){
            $rs['code'] = 1006;
            $rs['msg'] = '该帐号不存在';
            return $rs;
        }else if($info===false){
            $rs['code'] = 1007;
            $rs['msg'] = '重置失败，请重试';
            return $rs;
        }

        $_SESSION['forget_mobile'] = '';
        $_SESSION['forget_mobile_code'] = '';
        $_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }

 

    /**
     * 获取登录短信验证码
     * @desc 用于注册获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getCode() {
        $rs = array('code' => 0, 'msg' => '发送成功', 'info' => array());
       
        $mobile = checkNull($this->mobile);
        $sign = checkNull($this->sign);
        
        $reg_mobile_key = 'reg_mobile_' . $mobile;
       
        $ismobile=checkMobile($mobile);
        if(!$ismobile){
            $rs['code']=1001;
            $rs['msg']='请输入正确的手机号';
            return $rs;
        }

        $checkdata=array(
            'mobile'=>$mobile
        );
        $reg_mobile = getcache($reg_mobile_key);
        $issign=checkSign($checkdata,$sign);
         if(!$issign){
             $rs['code']=1001;
             $rs['msg']='签名错误';
             return $rs;
         }

        $where="user_login='{$mobile}'";

        
        if($reg_mobile['mobile']==$mobile && $reg_mobile['mobile_expiretime' ]> time() ){
            $rs['code']=1002;
            $rs['msg']='验证码5分钟有效，请勿多次发送';
            return $rs;
        }

        // $limit = ip_limit();
        // if( $limit == 1){
        //     $rs['code']=1003;
        //     $rs['msg']='您已当日发送次数过多';
        //     return $rs;
        // }
        $mobile_code = random(6,1);

        /* 发送验证码 */
        $result=sendsmscode($mobile,$mobile_code);
//        $result = ['code'=>0];
        if($result['code']==0){
//            $rs['info']['code'] = $mobile_code;
            // DI()->redis->set($reg_mobile_key,json_encode(['mobile' => $mobile, 'mobile_code' => $mobile_code, 'mobile_expiretime' => time() +60*5]));
            setcaches($reg_mobile_key,['mobile' => $mobile, 'mobile_code' => $mobile_code, 'mobile_expiretime' => time() +60*5]);
            // $reg_mobile = DI()->redis->Get($reg_mobile_key);
            // var_dump($reg_mobile);die;
        }else{
            $rs['code']=1002;
            $rs['msg']=$result['msg'];
        }
        return $rs;
    }

    /**
     * 获取找回密码短信验证码
     * @desc 用于找回密码获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getForgetCode() {
        $rs = array('code' => 0, 'msg' => '发送成功', 'info' => array(),"verificationcode"=>0);

        $mobile = checkNull($this->mobile);
        $sign = checkNull($this->sign);

        $ismobile=checkMobile($mobile);
        if(!$ismobile){
            $rs['code']=1001;
            $rs['msg']='请输入正确的手机号';
            return $rs;
        }

        $checkdata=array(
            'mobile'=>$mobile
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs;
        }

        $where="user_login='{$mobile}'";
        $checkuser = checkUser($where);

        if(!$checkuser){
            $rs['code']=1004;
            $rs['msg']='该手机号未注册';
            return $rs;
        }

        //判断手机号是否注销
        $is_destroy=checkIsDestroyByLogin($mobile);
        if($is_destroy){
            $rs['code']=1005;
            $rs['msg']='该手机号已注销';
            return $rs;
        }

        if($_SESSION['forget_mobile']==$mobile && $_SESSION['forget_mobile_expiretime']> time() ){
            $rs['code']=1002;
            $rs['msg']='验证码5分钟有效，请勿多次发送';
            return $rs;
        }

        $limit = ip_limit();
        if( $limit == 1){
            $rs['code']=1003;
            $rs['msg']='您已当日发送次数过多';
            return $rs;
        }
        $mobile_code = random(6,1);

        /* 发送验证码 */
        $result=sendsmscode($mobile,$mobile_code);
        if($result['code']==0){
            $rs['verificationcode']=$mobile_code;
            $_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = $mobile_code;
            $_SESSION['forget_mobile_expiretime'] = time() +60*5;
        }else if($result['code']==667){
            $_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = $result['msg'];
            $_SESSION['forget_mobile_expiretime'] = time() +60*5;

            $rs['verificationcode']='123456';
            $rs['code']=1002;
            $rs['msg']='验证码为：'.$result['msg'];
        }else{
            $rs['code']=1002;
            $rs['msg']=$result['msg'];
        }

        return $rs;
    }

   

    /**
     * 退出
     * @desc 用于用户退出 注销极光
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function logout() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid = $this->uid;
        $token=checkNull($this->token);

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }



        $info = userLogout($uid);


        return $rs;
    }


    /**
     * 更新极光pushid
     * @desc 用于更新极光pushid
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function upUserPush(){

        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $pushid=checkNull($this->pushid);

        $domain=new Domain_Login();
        $domain->upUserPush($uid,$pushid);

        return $rs;

    }

    /**
     * 获取注销账号的条件
     * @desc 用于获取注销账号的条件
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return array info[0]['list'] 条件数组
     * @return string info[0]['list'][]['title'] 标题
     * @return string info[0]['list'][]['content'] 内容
     * @return string info[0]['list'][]['is_ok'] 是否满足条件 0 否 1 是
     * @return string info[0]['can_cancel'] 是否可以注销账号 0 否 1 是
     */
    public function getCancelCondition(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $domain=new Domain_Login();
        $res=$domain->getCancelCondition($uid);

        $rs['info'][0]=$res;

        return $rs;
    }

    /**
     * 用户注销账号
     * @desc 用于用户注销账号
     * @return int code 状态码,0表示成功
     * @return string msg 返回提示信息
     * @return array info 返回信息
     */
    public function cancelAccount(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(!$time||!$sign){
            $rs['code'] = 1001;
            $rs['msg'] = '参数错误';
            return $rs;
        }

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }


        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs;
        }

        $domain=new Domain_Login();
        $res=$domain->cancelAccount($uid);

        if($res==1001){
            $rs['code']=1001;
            $rs['msg']='相关内容不符合注销账号条件';
            return $rs;
        }

        $rs['msg']='注销成功,手机号、身份证号等信息已解除';
        return $rs;
    }




}
