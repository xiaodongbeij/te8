<?php

/**
 * 彩票
 */
class Api_Ticket extends PhalApi_Api
{
    protected $url;
    protected $key;
    protected $curl;

    public function __construct()
    {
        $this->curl = new PhalApi_CUrl(2);
        $config = getConfigPri();

        $this->url = $config['tripartite_game_url'];
        $this->key = $config['tripartite_game_key'];
    }

    public function getRules()
    {
        return array(
            'getTicketsType' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                // 'hot' => array('name' => 'hot', 'type' => 'int', 'min' => 1,  'desc' => '推荐'),
//                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'getTickets' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'game_id' => array('name' => 'game_id', 'type' => 'int', 'min' => 1,  'desc' => '默认为彩票集合'),
                'type' => array('name' => 'type', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '彩票分类,为空查询全部'),
                'hot' => array('name' => 'hot', 'type' => 'int', 'min' => 0,  'desc' => '1=推荐;2=不推荐,为空查询全部'),
 
//                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
//                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'getTicketPlay' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
            'getTicketPlayOdds' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'groupCode' => array('name' => 'groupCode', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '玩法组代码'),
            ),
            'getTicketOpenTime' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
            'getTicketOpenRes' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'expect' => array('name' => 'expect', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '期号'),
            ),
            'getTicketOpenList' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'num' => array('name' => 'num', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '数量'),
            ),
            'choiceTicket' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'shortName' => array('name' => 'shortName', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '彩种代码'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
//                'rateCode' => array('name' => 'rateCode', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '赔率代码'),
//                'codes' => array('name' => 'codes', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '投注号'),
//                'money' => array('name' => 'money', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '下注金额'),
                'list' => array('name' => 'list', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '订单集合'),
//                'ruleCode' => array('name' => 'ruleCode', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '规则code'),
                'expect' => array('name' => 'expect', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '期号'),
            ),
            'getAllTicketOpenList' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
            'getTicketRecord' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'status' => array('name' => 'status', 'type' => 'int', 'min' => 0, 'require' => false, 'desc' => '开奖状态,1-已开奖,2-未开奖,3-已中奖,为空查询全部'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
                'time' => array('name' => 'time', 'type' => 'string', 'min' => 1, 'desc' => '日期筛选（2021-01-01）'),
                'game_cate' => array('name' => 'game_cate', 'type' => 'int', 'min' => 1, 'desc' => '游戏类型')
            ),
            'getGameCate' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
            'getGameCateAddL' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
            'cancelTicket' => array(
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'id' => array('name' => 'id', 'type' => 'string', 'require' => true, 'desc' => '下注记录id'),
            ),
        );
    }

    /**
     * 撤销彩票订单
     * @desc 撤销彩票订单
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function cancelTicket()
    {
        $rs = array('code' => 0, 'msg' => '撤销成功', 'info' => array());

        $id = $this->id;

        $order = DI()->notorm->game_ticket
            ->where('order_id',$id)
            ->where('status',0)
            ->fetchOne();
        if (!$order){
            $rs['code'] = 1001;
            $rs['msg'] = '该笔下注不可撤销';
            return $rs;
        }

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');
        $res1 = user_change_action($order['user_id'],25,$order['money'],'彩票下注撤销');
        $res2 = DI()->notorm->game_ticket
            ->where('order_id',$id)
            ->update(['status'=>2]);
        if ($res1 && $res2){
            DI()->notorm->commit('db_appapi');
        }else{
            $rs['code'] = 1002;
            $rs['msg'] = '撤销异常';
        }

        return $rs;
    }

    /**
     * 获取 游戏列表+直播平台
     * @desc 获取游戏列表+直播平台
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[name] 名称
     * @return string info[platform] 平台号
     * @return string msg 提示信息
     */
    public function getGameCateAddL()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $key = 'getGameCateAddLivePingTaiNew';
        $game_cate = getcaches($key);
        if(!$game_cate)
        {
            $game_cate = DI()->notorm->game_cate
                ->where("del_status = 0")
                ->select('name,platform')
                ->fetchAll();
            $game_cate = array_merge($game_cate, [['name' => '直播', 'platform' => 2]]);
            setcaches($key, $game_cate);
        }

        $rs['info'] = $game_cate;
        return $rs;
    }

    /**
     * 获取游戏列表
     * @desc 获取游戏列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[name] 名称
     * @return string info[icon] 图标
     * @return string msg 提示信息
     */
    public function getGameCate()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $game_cate = getGameCate();
        $rs['info'] = $game_cate;
        return $rs;
    }



    /**
     * 彩票集合类型
     * @desc 获取彩票集合类型
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info 彩种类型
     * @return string msg 提示信息
     * @return string info[0]type 彩种类型
     */
    public function getTicketsType()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $res = getTicketsType();
        $rs['info'] = $res;
        return $rs;
    }

    /**
     * 彩票集合
     * @desc 获取彩票集合
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info 彩种信息
     * @return string msg 提示信息
     * @return int info[0]id
     * @return string info[0]show_name 彩种名称
     * @return string info[0]hot 推荐 1=推荐 2=不推荐
     * @return string info[0]short_name 彩种代码
     * @return string info[0]type 彩种类型
     * @return string info[0]icon 图标
     */
    public function getTickets()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
 
        $type = checkNull($this->type) ? checkNull($this->type) : 0;
        $hot = checkNull($this->hot) ? checkNull($this->hot) : 0;
        $cat_id = checkNull($this->game_id);
        $res = getCameCaizhong($cat_id,$type,$hot);
        $rs['info'] = $res;
        return $rs;
    }

    /**
     * 彩票玩法组
     * @desc 根据猜中代码获取玩法组
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 玩法组信息
     * @return string info[0]groupId 玩法组id
     * @return string info[0]groupCode 玩法组代码
     * @return string info[0]groupName 玩法组名称
     * @return string msg 提示信息
     */
    public function getTicketPlay()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $shortName = checkNull($this->shortName);
        $key = 'getTicketPlay:'. $shortName;
        $getTicketPlay = getcaches($key);
//        $getTicketPlay = false;
        if(!$getTicketPlay)
        {
            //彩票接口路由
            $url_route = 'ct-data-app/live/wpRuleGroup?';
            $data = [
                'shortName' => $shortName
            ];
            $return = $this->get_sign($data, $this->key);
            $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
            $res = $this->curl->get($url, 3000);
            $res = json_decode($res, true);
            $getTicketPlay = $res['list'];
            setcaches($key,$getTicketPlay);
        }
        $rs['info'] = $getTicketPlay;
        return $rs;
    }

    /**
     * 玩法赔率
     * @desc 根据彩种代码玩法组获取赔率
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 赔率信息
     * @return string info[0]ruleCode 规则代码
     * @return string info[0]ruleName 规则名称
     * @return string info[0]sort 排序
     * @return string info[0]rateList 赔率
     * @return string info[0]rateList[rateCode] 赔率代码
     * @return string info[0]rateList[rateName] 赔率名称
     * @return string info[0]rateList[rate] 赔率
     * @return string msg 提示信息
     */
    public function getTicketPlayOdds()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //彩票接口路由
        $url_route = 'ct-data-app/live/wplottery?';

        
        $uid = $this->uid;
        $shortName = checkNull($this->shortName);
        $groupCode = checkNull($this->groupCode);
        $key = 'getTicketPlay:'. $shortName . ":" . $groupCode;
        
        $getTicketPlayOdds= getcaches($key);
//        $getTicketPlayOdds = false;
        if(!$getTicketPlayOdds)
        {
            $data = [
                'shortName' => $shortName,
                'groupCode' => $groupCode
            ];
    
            $return = $this->get_sign($data, $this->key);
            $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
            $res = $this->curl->get($url, 3000);
            if ($res) {
                $res = json_decode($res, true);
                if ($res['code'] == '000000') {
                    setcaches($key,$res['ruleList']);
                    $rs['info'] = $res['ruleList'];
                    return $rs;
                }
            }
        }else{
            $rs['info'] = $getTicketPlayOdds;
            return $rs;
        }
        $rs['code'] = 1001;
        $rs['msg'] = '请求异常';
        return $rs;
    }

    /**
     * 获取开奖时间
     * @desc 根据彩种代码获取开奖时间
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info 开奖时间信息
     * @return string info[lastFullExpect] 上一期期号
     * @return string info[lastExpect] 上一期期序号
     * @return string info[currFullExpect] 当前期号
     * @return string info[currExpect] 当前期序号
     * @return string info[remainTime] 距离开奖时间
     * @return string info[openRemainTime] 已投注时间
     * @return string info[stopRemainTime] 距离封盘时间
     */
    public function getTicketOpenTime()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //彩票接口路由
        $url_route = 'ct-data-app/live/loadOpenTime?';
        $shortName = checkNull($this->shortName);

        $data = [
            'shortName' => $shortName,
        ];

        $return = $this->get_sign($data, $this->key);
        $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
       
        $res = $this->curl->get($url, 3000);

        $path = CMF_DATA . 'ticket_open_time/'.date('Ym').'/';
        $filename = date('Y-m-d H:i:s').'.txt';
        if(!is_dir($path)){
            $flag = mkdir($path,0777,true);
        }

        file_put_contents( $path.$filename,$res.PHP_EOL,FILE_APPEND);
 
        if ($res) {
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                unset($res['startTime']);
                unset($res['endTime']);
                unset($res['times']);
                unset($res['message']);
                unset($res['code']);
                $rs['info'] = $res;
                return $rs;
            }
        }
        $rs['code'] = 1001;
        $rs['msg'] = '请求异常';
        return $rs;
    }

    /**
     * 获取开奖结果
     * @desc 根据彩种代码,期号获取开奖时间
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 支付信息
     * @return string msg 提示信息
     */
    public function getTicketOpenRes()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //彩票接口路由
        $url_route = 'ct-data-app/live/loadOpenCode?';
        $uid = $this->uid;
        $shortName = checkNull($this->shortName);

        $expect = $this->expect;


        $data = [
            'shortName' => $shortName,
            'expect' => $expect
        ];

        $return = $this->get_sign($data, $this->key);
        $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
        $res = $this->curl->get($url, 3000);
        if ($res) {
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                unset($res['code']);
                $rs['info'] = $res;
                return $rs;
            }
        }
        $rs['code'] = 1001;
        $rs['msg'] = '请求异常';
        return $rs;
    }

    /**
     * 获取近期开奖号码
     * @desc 根据彩种代码,数量获取近期开奖号码
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info.expect 期号
     * @return string info.openCode 开奖结果
     * @return string info.openTime 开奖时间
     * @return string msg 提示信息
     */
    public function getTicketOpenList()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //彩票接口路由
        $url_route = 'ct-data-app/live/openCodeList?';

        $uid = $this->uid;
        $shortName = checkNull($this->shortName);

        $num = $this->num;

        $data = [
            'shortName' => $shortName,
            'num' => $num
        ];

        $return = $this->get_sign($data, $this->key);
        $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];

        $res = $this->curl->get($url, 3000);
        if ($res) {
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                unset($res['code']);
                unset($res['message']);
                $caizhong_info = DI()->notorm->game_caizhong->where('short_name = ?', $shortName)->fetchOne();
                foreach ($res['openCodeList'] as &$v)
                {
                    $v['name'] = $caizhong_info['show_name'];
                }
                $rs['info'] = $res['openCodeList'];
                return $rs;
            }
        }
        

        $rs['code'] = 1001;
        $rs['msg'] = '请求异常';

        return $rs;
    }

    /**
     * 获取近一期所有开奖号码
     * @desc 获取近一期期开奖号码
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 开奖信息
     * @return string info[0]shortName 彩种
     * @return string info[0]expect 期号
     * @return string info[0]openCode 开奖号码
     * @return string msg 提示信息
     */
    public function getAllTicketOpenList()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //彩票接口路由
        $url_route = 'ct-data-app/live/lotterysOpenCode?';
        $data = [];
        $return = $this->get_sign($data, $this->key);
        $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
        $res = $this->curl->get($url, 3000);

        if ($res) {
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                unset($res['code']);
                unset($res['message']);
                $list = $res['codeList'];
                $open = DI()->notorm->game_caizhong
                    ->where('status=?', 1)
                    ->select('short_name')
                    ->fetchAll();
                $opens = [];
                foreach ($open as $v) {
                    $opens[] = $v['short_name'];
                }
                foreach ($list as $k => $v) {
                    if (!in_array($v['shortName'], $opens)) unset($list[$k]);
                }

                $rs['info'] = array_values($list);
                return $rs;
            }
        }
        $rs['code'] = 1001;
        $rs['msg'] = '请求异常';
        return $rs;
    }

    /**
     * 用户下注记录
     * @desc 提供用户id，获取用户下注记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 下注信息
     * @return string info[0][codes] 用户下注内容
     * @return string info[0][prize_codes] 中奖内容
     * @return string info[0][expect] 期号
     * @return string info[0][money] 下注金额
     * @return string info[0][prize] 中奖金额
     * @return string info[0][ok] 下注状态
     * @return string info[0][rate] 赔率
     * @return string info[0][show_name] 彩种名称
     * @return string info[0][order_id] 投注单号
     * @return string info[0][rule_name] 玩法名称
     * @return string info[0][rate_name] 下注名称
     * @return string info[0][addtime]   下注时间
     * @return string msg 提示信息
     */
    public function getTicketRecord()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = $this->uid;
        $status = $this->status;
        $page = $this->page;
        $page_size = $this->page_size;
        $time = $this->time;
        $game_cate = $this->game_cate;
        if ($status == 1) {
            $ok = [1, 2];
        } elseif ($status == 2) {
            $ok = [3];
        } elseif ($status ==3) {
            $ok = [1];
        }else {
            $ok = [1, 2, 3];
        }

        $wheretime = [];
        $time = isset($time) ? $time : '';
        if($time != '') {
            $start = strtotime($time);
            $end = strtotime($time) + 86400;
            $wheretime = "addtime BETWEEN $start AND $end ";
        }

        $short_names = [];
        $game_cate = isset($game_cate) ? $game_cate : '';
        $whereshort = [];
        if($game_cate != '') {
            $caizhong_short_name = DI()->notorm->game_caizhong->where("cat_id=?", $game_cate)->select('id')->fetchAll();
           
            if($caizhong_short_name){
                foreach ($caizhong_short_name as $k => $v){
                    $short_names[$k] = $v['id'];
                }
                $short_names = implode(",", $short_names);
                $whereshort = "cz_id IN ({$short_names})";
            }
        }

        $di = DI()->notorm->game_ticket->where('user_id = ?', $uid)->where('ok', $ok)->where($wheretime)->where($whereshort);
        
        $res = $di->select("id,codes,prize_codes,short_name,expect,show_name,rule_name,rate_name,money,ok,addtime,prize,order_id,rate,status")
                ->limit(($page - 1) * $page_size, $page_size)
                ->order('addtime desc')
                ->fetchAll();


        $res_count = $di->count();
        
        if (!$res) {
            $rs['msg'] = '暂无记录';
            $rs['code'] = 1001;
            return $rs;
        }
        foreach ($res as $k => $v){
            $res[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            if ($v['status'] == 2){
                $res[$k]['ok'] = '已撤销';
                continue;
            }
            if ($v['ok'] == 1){
                $res[$k]['ok'] = '已中奖';
            }elseif ($v['ok'] == 2){
                $res[$k]['ok'] = '未中奖';
            }else{
                $res[$k]['ok'] = '未开奖';
                unset($res[$k]['prize']);
            }
        }

        $rs['info'] = $res;
        $rs['count'] = $res_count;
        return $rs;
    }

    //获取开奖结果
    protected function getTicketResult($shortName,$expect)
    {
        //彩票接口路由
        $url_route = 'ct-data-app/live/loadOpenCode?';
        $data = [
            'shortName' => $shortName,
            'expect' => $expect
        ];

        $return = $this->get_sign($data, $this->key);
        $url = $this->url . $url_route . $return['date'] . '&sign=' . $return['sign'];
        $res = $this->curl->get($url, 3000);
        if ($res) {
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 用户下注
     * @desc 提供彩种代码，期号进行下注
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0]
     * @return string msg 提示信息
     */
    public function choiceTicket()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid = $this->uid;
        $shortName = checkNull($this->shortName);

        $expect = checkNull($this->expect);
        $list = json_decode($this->list, true);

        if(empty($list))
        {
            $rs['msg'] = '下注参数错误';
            $rs['code'] = 1001;
            return $rs;
        }
         $info = DI()->notorm->user
            ->where("id=?", $uid)
            ->select("id,coin as user_money,freeze_money,avatar_thumb,level,user_nicename,avatar")
            ->fetchOne();
        

        //彩票接口路由
        $url_route = 'ct-data-app/liveWpBets/buy';
      
        $items = [];
        $total = 0;
        $count = 0;
        
        foreach ($list as $v) {
            $orderModel = [
                'ruleCode' => $v['ruleCode'],
                'rateCode' => $v['rateCode'],
                'codes' => $v['codes'],
                'total' => $v['money'],
                'yjf' => 1
            ];

            $order_id = date('Ymd') . random(5, 1) . '-' . random(8);
            $items[$order_id] = $orderModel;
            $total += $v['money'];
            $count += 1;
        }

        $orderList = [
            'lottery' => $shortName,
            'currExpect' => $expect,
            'total' => $total,
            'orderCount' => $count,
            'items' => $items,
            'currency' => 'rmb'
        ];
        $data1 = [
            'orderList' => json_encode($orderList),
            'userName' => $uid
        ];

       
        $total = -1 * $total;
        //开启事务
        DI()->notorm->beginTransaction('db_appapi');
        $res1 = user_change_action($uid,3,$total,'彩票下注');
        if ($res1 === 2){
            DI()->notorm->rollback('db_appapi');
            $rs['msg'] = '余额不足';
            $rs['code'] = 1003;
            return $rs;
        }
        //本地存储下单记录
        $data = [];
        foreach ($items as $k => $v) {
            $cpinfo = getCpInfo($shortName,$v['rateCode'],$v['ruleCode']);
            $temp = [
                'cz_id' => $cpinfo['id'],
                'order_id' => $k,
                'user_id' => $uid,
                'short_name' => $shortName,
                'show_name' => $cpinfo['show_name'],
                'rule_code' => $v['ruleCode'],
                'rule_name' => $cpinfo['rule_name'],
                'rate_code' => $v['rateCode'],
                'rate_name' => $cpinfo['rate_name'],
                'rate' => $cpinfo['rate'],
                'codes' => $v['codes'],
                'expect' => $expect,
                'money' => abs($v['total']),
                'status' => 0,
                'addtime' => time()
            ];

            $data[] = $temp;
        }
        $res3 = DI()->notorm->game_ticket->insert_multi($data);
        if ($res1 && $res3) {

            $return = $this->get_sign($data1, $this->key);
            $url = $this->url . $url_route;
            $data1['sign'] = $return['sign'];
            $res = $this->curl->post($url, $data1, 3000);
            $res = json_decode($res, true);
            if ($res['code'] == '000000') {
                DI()->notorm->commit('db_appapi');

                $rs['msg'] = $res['message'];
                return $rs;
            } else {
                DI()->notorm->rollback('db_appapi');
                $rs['msg'] = $res['message'];
                $rs['code'] = 1002;
                return $rs;
            }

        } else {
            DI()->notorm->rollback('db_appapi');
            $rs['msg'] = '下注异常';
            $rs['code'] = 1001;
            return $rs;
        }
    }

    //md5生成签名，返回url参数和签名
    protected function get_sign($data, $key)
    {
        $str = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $data = $str = substr($str, 0, -1);
        $str .= $key;
        return [
            'date' => $data,
            'sign' => md5($str)
        ];
    }

}