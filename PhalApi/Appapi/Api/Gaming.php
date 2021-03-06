<?php
/**
 * 三方游戏
 */
class Api_Gaming extends PhalApi_Api {
    
    // 三方域名
    protected $domain;
    // 三方游戏账号
    protected $agent;
    protected $t_money;
    // 三方游戏秘钥
    protected $key;
    protected $username;
    protected $param = [];

    protected $uid;
    
    public function __construct()
    {
        
        //getConfigPri;
        $config = getConfigPri();
        $this->t_money = $config['tripartite_money'] ?? 50;
        $this->domain = $config['tripartite_domain'];
        $this->agent = $config['tripartite_agent'];
        $this->key = $config['tripartite_key'];
        $this->uid = checkNull($_POST['uid']);
        $platform = checkNull($_POST['platform']);
        $userinfo=getUserInfo(checkNull($this->uid));
        $this->param=['agent' => $this->agent,'platform' =>$platform,'username' => $userinfo['id']];

        $this->checkCache();
    }
    
    public function getRules() {
        return array(
            'getBalance' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
				'platform' => array('name' => 'platform', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '游戏编号'),
			),
			'getAllBalance' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
			),
			'deposit' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
				'platform' => array('name' => 'platform', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '游戏编号'),
				'money' => array('name' => 'money', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '转账金额'),
			),
			'withdrawals' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
				'platform' => array('name' => 'platform', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '游戏编号'),
				'money' => array('name' => 'money', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '取款金额'),
			),
			'allWithdrawals' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
			),
			'transferGame' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
				'platform' => array('name' => 'platform', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '游戏编号'),
				'gamecode' => array('name' => 'gamecode', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '游戏编码 为空进入大厅'),
			),
        );
    }
    
    /**
     * 获取用户指定游戏余额
     * @desc 获取用户指定游戏余额
     * @return int code 操作码，0表示成功
     * @return array info[0] 
     * @return string info[0].hRet 用户中奖金额
     * @return string info[0].balance 用户余额
     * @return string msg 提示信息
     */
    public function getBalance()
    {
        delcache($this->getAllBalanceKey());
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/GetBalance";
   
        return $this->getHttpQuery($url, $this->param);
    }
    
     /**
     * 获取用户所有平台余额
     * @desc 获取用户所有平台余额
     * @return int ret 操作码，200表示成功
     * @return array data
     * @return string info[0].name 平台名字
     * @return string info[0].icon 平台图标
     * @return string info[0].platform 平台编码
     * @return string info[0].balance 平台余额
     * @return string msg 提示信息
     */
    public function getAllBalance()
    {
        $balance = false;
        // $key = $this->getAllBalanceKey();
        // $balance = DI()->redis->get($key);
        // $balance = json_decode($balance,true);
        if(!$balance)
        {

            $balance['code'] = 0;
            $balance['msg'] = 'successful';
            $balance['info'] = [];
            $game_cate = getGameCate();
            $game_cate = array_splice($game_cate,1);
     
            $urls = [];
            foreach ($game_cate as $k=>$v)
            {
               
                $this->param['platform'] = $v['platform'];
                $url = $this->domain . "/api/{$this->agent}/GetBalance";
                $urls[] = $this->getHttpUrls($url,$this->param);

            }

            $result = getMultiUrlContents($urls);
            $result = array_values($result);
            foreach ($game_cate as $k=>$v)
            {
            
                if(isset($result[$k]))
                {
                    
                    $res = json_decode($result[$k], true);

                    $balance['info'][] = [
                        'name' => $v['name'],
                        'icon' => $v['icon'],
                        'platform' => $v['platform'],
                        'balance' => $res['hRet'] == 1 ? $res['balance'] : 0,
                    ];
                }
                
            }
            DI()->redis->set($key, json_encode($balance),900);

        }
        
        
        return $balance;
    }
    
    
    /**
     * 用户存款
     * @desc 用户存款
     * @return int code 操作码，0表示成功
     * @return array info[0] 
     * @return string msg 提示信息
     */
    public function deposit()
    {
        
        if($this->money < $this->t_money)
        {
            return ['code' => 1, 'msg' => '转存金额不能低于'. $this->t_money . '元', 'remoteMsg' => '转存金额不能低于'. $this->t_money . '元'];
        }
        
        $money = checkNull($this->money);
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/deposit";
        // 订单号
        $this->param['billno'] = date('YmdHis') . random_int(1111, 9999);
        // 转账金额
        $this->param['credit'] = checkNull($money);

           
        return $this->game_in($url, -1 * $money,'游戏存款',$this->param['platform']);
 
    }
    
    /**
     * 用户取款
     * @desc 用户取款
     * @return int code 操作码，0表示成功
     * @return array info[0] 
     * @return string msg 提示信息
     */
    public function withdrawals()
    {
        
        
        // 获取前端传过来的金额
        $money = checkNull($this->money);
    
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/Withdrawals";
        // 订单号
        $this->param['billno'] = date('YmdHis') . random_int(1111, 9999);
        // 取款金额
        $this->param['credit'] = $money;
//        // 取款回来数据要做数据库金额增加
//        return json_decode($this->getHttpQuery($url, $this->param));

        return $this->game_out($url,$money,'游戏取款',$this->param['platform']);
       
        
    }
    
    /**
     * 用户取款
     * @desc 一键回收所有平台存款
     * @return int code 操作码，0表示成功
     * @return array info[0] 
     * @return string msg 提示信息
     */
    public function allWithdrawals()
    {
        $all_balance = $this->getAllBalance();
        foreach($all_balance['info'] as $v)
        {
            if($v['balance'])
            {
                $this->money = floor($v['balance']);
                $this->param['platform'] = $v['platform'];
                $this->withdrawals(); 
            }
        }
        
//        return ['hRet' => 1, 'msg' => 'successful', 'remoteMsg' => '成功'];
        return ['code' => 0, 'msg' => '转入成功', 'remoteMsg' => '成功'];
    }
    
    
    /**
     * 进入游戏
     * @desc 进入游戏
     * @return array info[0] 
     */
    public function TransferGame()
    {
        // 请求地址
        $url = $this->domain . "/web/{$this->agent}/TransferGame";
        // 前端传过来的游戏类型
        $this->param['gametype'] = 1;
        $this->param['gamecode'] = checkNull($this->gamecode);
        
        if($this->param['platform'] == '0035' || $this->param['platform'] == '0027' )
        {
            
            $this->param['gamecode'] = '';
        }
        
       
        // 进入指定房间游戏
        if($this->param['gamecode']){
       
            return $this->PlayGame();
        }
        
    
        // 直接跳转到游戏
        return $this->getHttpQuery($url, $this->param, 2);
    }
    
  
    
    /**
     *  获取请求内容
     * @param string $url
     * @param array $param
     * @param int $location 1=curl请求；2=直接跳转
     * @return bool|string
     */
    protected function getHttpQuery(string $url, array $param, int $location = 1)
    {
        $param = base64_encode(urldecode(http_build_query($param)));
        $key = $this->getKey($param);
        $url = $url . '?' . http_build_query(['param' => $param, 'key' => $key]);
        if ($location == 1) {

            return $this->curlGet($url);
        }

        return ['code' => 0, 'msg' => 'ok', 'info' => ['url' => $url]];

    }
    
    protected function getHttpUrls($url,$param)
    {
        $param = base64_encode(urldecode(http_build_query($param)));
        $key = $this->getKey($param);
        $url = $url . '?' . http_build_query(['param' => $param, 'key' => $key]);
        return $url;
    }
    /**
     *  生成秘钥
     * @param $param
     * @return string
     */
    protected function getKey(string $param)
    {
        return md5($param . $this->key);
    }
    
    /**
     *  发送请求
     * @param string $url
     * @return bool|string
     */
    protected function curlGet(string $url = "")
    {
        
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);

        if($res)
        {
            $rs = ['code' => 0, 'msg' => 'successful', 'info' => ''];
            $info = [];
            $res = json_decode($res, true);

            if ($res['hRet'] != 1){
                $rs = ['code'=>1003,'msg'=>$res['msg'],'info' => ''];
            }
            if(isset($res['balance'])) {
                $info['balance'] = $res['balance'];
                $rs['info'] = $info;
            }
            return $rs;
        }
        return ['code' => 1, 'msg' => '转入失败', 'info' => ''];
    }
    
    /**
     * 进入指定游戏
     * 根据游戏 CODE 进入指定游戏。只对 BG、AG、PT、JL 有效
     * @return bool|string
     */
    protected function PlayGame()
    {
        // 请求地址
        $url = $this->domain . "/web/{$this->agent}/PlayGame";

        // 直接跳转到游戏
        return $this->getHttpQuery($url, $this->param, 2);
    }
    
    protected function getAllBalanceKey(){
        return 'getAllBalancess:'. $this->param['user_login'];
    }
    
    /**
     * 创建账户并写入缓存
     */
    protected function CreateAccount()
    {
        // 请求地址
        $url = $this->domain . "/api/{$this->agent}/CreateAccount";
        $res = $this->getHttpQuery($url, $this->param);
        setcaches($this->createKey(),$res);
    }
    
    /**
     * 检测缓存
     */
    protected function checkCache()
    {
        $res = getcaches($this->createKey());
        
        if(!$res){
            
             $this->CreateAccount();
        }
    }
    
    protected function createKey()
    {

        return 'game:' . $this->param['platform'] . ':' . $this->param['username'];
    }

    //取款
    protected function game_out($url,$money,$remark,$platform){
        $return = $this->getHttpQuery($url, $this->param);
        $return['balance'] = abs($money);
        if ($return['code'] == 0){
            //取款成功
            $return['msg'] ='成功';
            //账变
            $res = $this->game_change($this->uid,23,$money,$remark,$platform);
            return $return;
        }else{
            //取款失败
            return $return;
        }
    }

    //存款
    protected function game_in($url,$money,$remark,$platform){
        //账变
        $res = $this->game_change($this->uid,23,$money,$remark,$platform);
        if ($res === 1){
            $return = $this->getHttpQuery($url, $this->param);
   
            $return['balance'] = abs($money);
            if ($return['code'] == 0){
                $return['msg'] ='成功';
            }else{
                //请求失败,余额退回
//                $money = -1 * $money;
//                $remark = '游戏存款失败，金额退回';
//                $res = $this->game_change($this->uid,23,$money,$remark,$platform);
                $return['msg'] = '请求失败';
            }
            return $return;
        }elseif($res === 2){
            return [
                'code' => 1001,
                'msg' => '余额不足'
            ];
        }else{
            return [
                'code' => 1002,
                'msg' => '转入失败'
            ];
        }
    }

    //存取款
//    protected function money_change($url,$money,$remark,$platform){
//
//        //开启事务
//        DI()->notorm->beginTransaction('db_appapi');
//        //账变
//        $res = user_change_action($this->uid,23,$money,$remark,'','','','',$platform);
//        if ($res === 1){
//
//            $return = $this->getHttpQuery($url, $this->param);
//
//            $return['balance'] = abs($money);
//            if ($return['code'] == 0){
//                $return['msg'] ='成功';
//                //事务提交
//                DI()->notorm->commit('db_appapi');
//            }else{
//                //回滚
//                DI()->notorm->rollback('db_appapi');
//            }
//
//            return $return;
//        }elseif($res === 2){
//
//            return [
////                'hRet' => 1001,
//                'code' => 1001,
//                'msg' => '余额不足'
//            ];
//        }else{
//            //回滚
//            DI()->notorm->rollback('db_appapi');
//
//            return [
////                'hRet' => 1002,
//                'code' => 1002,
//                'msg' => '转入失败'
//            ];
//        }
//    }

    //游戏账变
    protected function game_change($user_id,$type,$money,$remark,$platform){
        //开启事务
        DI()->notorm->beginTransaction('db_appapi');
        $info=DI()->notorm->user
            ->where("id=?",$user_id)
            ->select("id,coin,freeze_money")
            ->fetchOne();

            if ($info['coin'] + $money < 0) return 2;
            $coin = $info['coin'] + $money;
            //资金更新
            $res1 = DI()->notorm->user
                ->where('id=?',$user_id)
                ->update(['coin'=>$coin]);
            //更新记录
            $insert = [
                'user_id' => $user_id,
                'change_type' => $type,
                'money' => $info['coin'],
                'next_money' => $coin,
                'change_money' => $money,
                'remark' => $remark,
                'addtime' => time(),
                'platform' =>$platform
            ];

        $res2 = DI()->notorm->user_change->insert($insert);
        if ($res1 && $res2){
            //事务提交
            DI()->notorm->commit('db_appapi');
            return 1;
        }
        //回滚
        DI()->notorm->rollback('db_appapi');
        return false;
    }
    
}