<?php
/**
 * 代理
 */

class Api_Daili extends PhalApi_Api
{

    public function getRules()
    {
        return array(
            'getOpenCenter' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
//                'date_type' => array('name' => 'date_type', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期类型:日-day,周-week,月-month'),
//                'date' => array('name' => 'date', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '日期起始时间戳,例:1608048000,表示2020-12-16'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'addOpenCenter' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'rate' => array('name' => 'rate', 'type' => 'float', 'require' => true, 'desc' => '返点数,百分比'),
            ),
            'delOpenCenter' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'id' => array('name' => 'id', 'type' => 'int','min' => 1, 'require' => true, 'desc' => '邀请码id'),
            ),
            'getMemberList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'account' => array('name' => 'account', 'type' => 'string','min' => 1, 'require' => false, 'desc' => '会员账号'),
                'type' => array('name' => 'type', 'type' => 'string','min' => 1, 'require' => false, 'desc' => '空-全部,1-代理,2-会员'),
                'start' => array('name' => 'start', 'type' => 'string','min' => 1, 'require' => false, 'desc' => '开始日期'),
                'end' => array('name' => 'end', 'type' => 'string','min' => 1, 'require' => false, 'desc' => '结束日期'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'editMember' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'id' => array('name' => 'id', 'type' => 'int','min' => 1, 'require' => true, 'desc' => '会员id'),
                'platform' => array('name' => 'platform', 'type' => 'string', 'require' => true, 'desc' => '平台号'),
                'rate' => array('name' => 'rate', 'type' => 'float','min' => 0,'require' => true, 'desc' => '返点'),
            ),
            'changeMoney' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'id' => array('name' => 'id', 'type' => 'int','min' => 1, 'require' => true, 'desc' => '会员id'),
                'money' => array('name' => 'money', 'type' => 'float','min' => 1,'require' => true, 'desc' => '转账金额'),
                'money_password' => array('name' => 'money_password', 'type' => 'string','min' => 1,'require' => true, 'desc' => '资金密码'),
            ),
            'teamShow' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
//                'user_login' => array('name' => 'user_login', 'type' => 'int','min' => 1, 'require' => false, 'desc' => '代理账号,为空显示当前账号团队统计'),
//                'date_type' => array('name' => 'date_type', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期类型:日-day,周-week,月-month'),
//                'date' => array('name' => 'date', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '日期起始时间戳,例:1608048000,表示2020-12-16'),
                'platform' => array('name' => 'platform', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '平台,1-官方彩票,2-天鹅直播'),
                'start' => array('name' => 'start', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期起始'),
                'end' => array('name' => 'end', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期结束'),
            ),
            'getReport' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => 'id,查询下级id,为空查询当前用户'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'user_login' => array('name' => 'user_login', 'type' => 'int','min' => 0, 'require' => false, 'desc' => '代理账号,为空显示当前账号团队统计'),
                'platform' => array('name' => 'platform', 'type' => 'int','min' => 1, 'require' => true, 'desc' => '平台,1-官方彩票,2-天鹅直播,0016-开元棋牌'),
                'start' => array('name' => 'start', 'type' => 'string', 'require' => false, 'desc' => '日期起始,例:2020-01-01'),
                'end' => array('name' => 'end', 'type' => 'string', 'require' => false, 'desc' => '日期结束,例如:2020-01-02'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
//            'getReportDown' => array(
//                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
//                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
//            ),

            'DaiLiZq' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
            ),

            'InvitationRecord' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'gameReport' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'platform' => array('name' => 'platform', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '平台,1-官方彩票,2-天鹅直播'),
                'start' => array('name' => 'start', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期起始'),
                'end' => array('name' => 'end', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期结束'),

            ),
            'teamDetail' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'type' => array('name' => 'type', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型,1-充值，2-提现'),
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '搜索账号'),
                'start' => array('name' => 'start', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期起始'),
                'end' => array('name' => 'end', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期结束'),
                'cate' => array('name' => 'cate', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型,1-个人，2-下级'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'touDetail' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'type' => array('name' => 'type', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型,1-下注，2-奖金'),
                'plat' => array('name' => 'plat', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '平台号'),
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '搜索账号'),
                'start' => array('name' => 'start', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期起始'),
                'end' => array('name' => 'end', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期结束'),
                'cate' => array('name' => 'cate', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型,1-个人，2-下级'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),
            'ticketDetail' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'type' => array('name' => 'type', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '彩种,为空查询全部'),
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '搜索账号'),
                'start' => array('name' => 'start', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期起始'),
                'end' => array('name' => 'end', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '日期结束'),
                'status' => array('name' => 'status', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '状态,1全部，2未开奖，3已中奖，4未中奖，5已撤单'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            )

        );
    }

    /**
     * 团队彩票明细
     * @desc 用于获取团队彩票明细
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     * @return string list.user_id 用户id
     * @return string list.user_nicename 昵称
     * @return string list.show_name 彩种
     * @return string list.money 下注金额
     * @return string list.prize 奖金
     * @return string list.addtime 时间
     * @return string list.expect 期号
     * @return string list.ok 中奖状态，1-中奖，2-未中奖，3-未开奖
     */
    public function ticketDetail(){
        $uid = $this->uid;
        $type = $this->type;
        $start = $this->start;
        $end = $this->end;
        $id = $this->id;
        $status = $this->status;
        $page = $this->page;
        $page_size = $this->page_size;
        $of = ($page-1) * $page_size;

        //查询列表
        $user = DI()->notorm->user->where('id',$uid)->fetchOne();
        $ids = DI()->notorm->user->where('id <> ?',$uid)->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
        $str = "";
        if(!$ids){
            $rs['code'] = 0;
            $rs['msg'] = '获取成功';
            $rs['info'] = [];
            return $rs;
        }
        foreach ($ids as $v){
            $str .= $v['id'].',';
        }
        $str = substr($str,0,-1);
        $where = " user_id in ($str)";

        if ($start){
            $where .= " and addtime >= ".strtotime($start);
        }
        if ($end){
            $where .= " and addtime <= ".strtotime($end);
        }
        if ($id){
            $where .= " and user_id = $id";
        }
        if ($type){
            $where .= " and short_name = '$type'";
        }
        if ($status == 2){
            $where .= " and status = 0";
        }elseif ($status == 3){
            $where .= " and ok = 1";
        }elseif ($status == 4){
            $where .= " and ok = 2";
        }elseif ($status == 5){
            $where .= " and status = 2";
        }

        $sql = "select gt.user_id,gt.rate_name,u.user_nicename,gt.show_name,gt.expect,gt.money,gt.prize,gt.ok,FROM_UNIXTIME(gt.addtime, '%Y-%m-%d %H:%i:%s') addtime from cmf_game_ticket gt join cmf_user u on gt.user_id = u.id where $where order by gt.expect desc limit $of,$page_size";
        $list = DI()->notorm->game_ticket->queryAll($sql);

        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $list;
        return $rs;
    }

    /**
     * 投注奖金明细
     * @desc 用于获取投注明细
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     * @return string total 个人+下级总金额
     * @return string self 个人总金额
     * @return string team 下级总金额
     * @return string list.user_id 用户id
     * @return string list.money 下注奖金
     * @return string list.prize 奖金
     * @return string list.addtime 时间
     * @return string list.game_name 游戏名称
     */
    public function touDetail(){

        $uid = $this->uid;
        $plat = $this->plat;
        $type = $this->type;
        $start = $this->start;
        $end = $this->end;
        $id = $this->id;
        $cate = $this->cate;
        $page = $this->page;
        $page_size = $this->page_size;
        $of = ($page-1) * $page_size;

        if ($type == 1){
            $field1 = 'money';
            $field2 = 'bet_amount';
        }else{
            $field1 = 'prize';
            $field2 = 'pay_off';
        }
        //统计固定
        $user = DI()->notorm->user->where('id',$uid)->fetchOne();
        if ($plat == 1){
            $sql = "select sum(gt.$field1) total,sum(if(gt.user_id = :uid,$field1,0)) self FROM cmf_game_ticket gt WHERE gt.status in (0,1) AND gt.user_id in ( select id from cmf_user where invite_level like :level);";
            $params = [
                ':uid' => $uid,
                ':level' => $user['invite_level'] . '%'
            ];
            $info = DI()->notorm->game_ticket->queryAll($sql,$params);
        }else{
            $sql = "select sum(gr.$field2) total,sum(if(gr.user_login= :uid,$field2,0)) self FROM cmf_game_record gr WHERE gr.platform_code = :plat and gr.user_login in ( select id from cmf_user where invite_level like :level);";
            $params = [
                ':uid' => $uid,
                ':level' => $user['invite_level'] . '%',
                ':plat' => $plat
            ];
            $info = DI()->notorm->game_ticket->queryAll($sql,$params);
        }
        $return = [
            'total' => number_format(abs($info[0]['total']), 2),   //总充值金额
            'self'  => number_format(abs($info[0]['self']), 2),    //个人总额
            'team'  => number_format(abs($info[0]['total'] - $info[0]['self']),2)     //下级总额
        ];

        //查询列表
        $where = "";


        if ($plat == 1){
            if ($start){
                $where .= " addtime >= ".strtotime($start);
            }
            if ($end){
                $where .= " and addtime <= ".strtotime($end);
            }
            if ($type == 2){
                $where .= " and prize > 0";
            }
            if ($id){
                $where .= " and user_id = $id";
            }else{
                if ($cate == 1){
                    $where .= " and user_id = $uid";
                }else{
                    $ids = DI()->notorm->user->where('id <> ?',$uid)->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
                    $str = "";
                    foreach ($ids as $v){
                        $str .= $v['id'].',';
                    }
                    $str = substr($str,0,-1);
                    $where .= " and user_id in ($str)";
                }
            }
//            var_dump($where);die;
            $list = DI()->notorm->game_ticket
                ->where("$where")
                ->select("user_id,money,FROM_UNIXTIME(addtime, '%Y-%m-%d %H:%i:%s') addtime,prize")
                ->order('id desc')
                ->limit(($page-1) * $page_size,$page_size)
                ->fetchAll();
            foreach ($list as $k => $v){
                $list[$k]['game_name'] = "官方彩票";
            }
        }else{
            $where .= " platform_code = $plat";
            if ($start){
                $where .= " and bet_time >= ".strtotime($start);
            }
            if ($end){
                $where .= " and bet_time <= ".strtotime($end);
            }
            if ($type == 2){
                $where .= " and pay_off > 0";
            }
            if ($id){
                $where .= " and user_login = $id";
            }else{
                if ($cate == 1){
                    $where .= " and user_login = $uid";
                }else{
                    $ids = DI()->notorm->user->where('id <> ?',$uid)->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
                    $str = "";
                    foreach ($ids as $v){
                        $str .= $v['id'].',';
                    }
                    $str = substr($str,0,-1);
                    $where .= " and user_login in ($str)";
                }
            }
//            var_dump($where);die;
            $list = DI()->notorm->game_record
                ->where("$where")
                ->select("user_login user_id,bet_amount money,FROM_UNIXTIME(bet_time, '%Y-%m-%d %H:%i:%s') addtime,game_name,pay_off prize")
                ->order('id desc')
                ->limit(($page-1) * $page_size,$page_size)
                ->fetchAll();
        }

        $return['list'] = $list;
        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $return;
        return $rs;
    }

//    /**
//     * 投注奖金明细
//     * @desc 用于获取投注明细
//     * @return int code 操作码，0表示成功
//     * @return array info
//     * @return string msg 提示信息
//     * @return string total 个人+下级总金额
//     * @return string self 个人总金额
//     * @return string team 下级总金额
//     * @return string list.user_id 用户id
//     * @return string list.money 下注奖金
//     * @return string list.prize 奖金
//     * @return string list.addtime 时间
//     * @return string list.game_name 游戏名称
//     */
//    public function touDetail(){
//
//        $uid = $this->uid;
//        $plat = $this->plat;
//        $type = $this->type;
//        $start = $this->start;
//        $end = $this->end;
//        $id = $this->id;
//        $cate = $this->cate;
//        $page = $this->page;
//        $page_size = $this->page_size;
//        $of = ($page-1) * $page_size;
//
//        if ($type == 1){
//            $field1 = 'money';
//            $field2 = 'bet_amount';
//        }else{
//            $field1 = 'prize';
//            $field2 = 'pay_off';
//        }
//        //统计固定
//        $user = DI()->notorm->user->where('id',$uid)->fetchOne();
//        if ($plat == 1){
//            $sql = "select sum(gt.$field1) total,sum(if(gt.user_id = :uid,$field1,0)) self FROM cmf_game_ticket gt WHERE gt.status in (0,1) AND gt.user_id in ( select id from cmf_user where invite_level like :level);";
//            $params = [
//                ':uid' => $uid,
//                ':level' => $user['invite_level'] . '%'
//            ];
//            $info = DI()->notorm->game_ticket->queryAll($sql,$params);
//        }else{
//            $sql = "select sum(gr.$field2) total,sum(if(gr.user_login= :uid,$field2,0)) self FROM cmf_game_record gr WHERE gr.platform_code = :plat and gr.user_login in ( select id from cmf_user where invite_level like :level);";
//            $params = [
//                ':uid' => $uid,
//                ':level' => $user['invite_level'] . '%',
//                ':plat' => $plat
//            ];
//            $info = DI()->notorm->game_ticket->queryAll($sql,$params);
//        }
//        $return = [
//            'total' => number_format(abs($info[0]['total']), 2),   //总充值金额
//            'self'  => number_format(abs($info[0]['self']), 2),    //个人总额
//            'team'  => number_format(abs($info[0]['total'] - $info[0]['self']),2)     //下级总额
//        ];
//
//        //查询列表
//        $where = "";
//
//
//        if ($plat == 1){
//            if ($start){
//                $where .= " addtime >= ".strtotime($start);
//            }
//            if ($end){
//                $where .= " and addtime <= ".strtotime($end);
//            }
//            if ($id){
//                $where .= " and user_id = $id";
//            }else{
//                if ($cate == 1){
//                    $where .= " and user_id = $uid";
//                }else{
//                    $ids = DI()->notorm->user->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
//                    $str = "";
//                    foreach ($ids as $v){
//                        $str .= $v['id'].',';
//                    }
//                    $str = substr($str,0,-1);
//                    $where .= " and user_id in ($str)";
//                }
//            }
//
//            $list = DI()->notorm->game_ticket
//                ->where("$where")
//                ->select("user_id,money,FROM_UNIXTIME(addtime, '%Y-%m-%d %H:%i:%s') addtime,prize")
//                ->order('id desc')
//                ->limit(($page-1) * $page_size,$page_size)
//                ->fetchAll();
//            foreach ($list as $k => $v){
//                $list[$k]['game_name'] = "官方彩票";
//            }
//        }else{
//            $where .= " platform_code = $plat";
//            if ($start){
//                $where .= " and bet_time >= ".strtotime($start);
//            }
//            if ($end){
//                $where .= " and bet_time <= ".strtotime($end);
//            }
//            if ($id){
//                $where .= " and user_login = $id";
//            }else{
//                if ($cate == 1){
//                    $where .= " and user_login = $uid";
//                }else{
//                    $ids = DI()->notorm->user->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
//                    $str = "";
//                    foreach ($ids as $v){
//                        $str .= $v['id'].',';
//                    }
//                    $str = substr($str,0,-1);
//                    $where .= " and user_login in ($str)";
//                }
//            }
//            $list = DI()->notorm->game_record
//                ->where("$where")
//                ->select("user_login user_id,bet_amount money,FROM_UNIXTIME(bet_time, '%Y-%m-%d %H:%i:%s') addtime,game_name,pay_off prize")
//                ->order('id desc')
//                ->limit(($page-1) * $page_size,$page_size)
//                ->fetchAll();
//        }
//
//        $return['list'] = $list;
//        $rs['code'] = 0;
//        $rs['msg'] = '获取成功';
//        $rs['info'] = $return;
//        return $rs;
//    }


    /**
     * 团队明细（充值和提现）
     * @desc 用于获取团队明细
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     * @return string total 个人+下级总金额
     * @return string self 个人总金额
     * @return string team 下级总金额
     * @return string list.user_id 用户id
     * @return string list.user_nicename 昵称
     * @return string list.change_money 变动金额
     * @return string list.next_money 变动后金额
     * @return string list.status 状态，4为成功，其他不成功
     * @return string list.addtime 时间
     */
    public function teamDetail(){

        $uid = $this->uid;
        $type = $this->type;
        $start = $this->start;
        $end = $this->end;
        $id = $this->id;
        $cate = $this->cate;
        $page = $this->page;
        $page_size = $this->page_size;
        $of = ($page-1) * $page_size;
        //统计固定
        $user = DI()->notorm->user->where('id',$uid)->fetchOne();
        $sql = "select sum(uc.change_money) total,sum(if(uc.user_id = :uid,change_money,0)) self FROM cmf_user_change uc WHERE uc.change_type = :type AND uc.user_id in ( select id from cmf_user where invite_level like :level);";
        $params = [
            ':uid' => $uid,
            ':type' => $type,
            ':level' => $user['invite_level'] . '%'
        ];
        $info = DI()->notorm->user_change->queryAll($sql,$params);
        $return = [
            'total' => number_format(abs($info[0]['total']), 2),   //总充值金额
            'self'  => number_format(abs($info[0]['self']), 2),    //个人总额
            'team'  => number_format(abs($info[0]['total'] - $info[0]['self']),2)     //下级总额
        ];

        //查询列表
        $where = "";
        if ($type){
            $where .= " uc.change_type = ".$type;
        }
        if ($start){
            $where .= " and uc.addtime >= ".strtotime($start);
        }
        if ($end){
            $where .= " and uc.addtime <= ".strtotime($end);
        }
        if ($id){
            $where .= " and uc.user_id = $id";
        }else{
            if ($cate == 1){
                $where .= " and uc.user_id = $uid";
            }else{
                $ids = DI()->notorm->user->where('invite_level like ?',$user['invite_level'].'%')->select('id')->fetchAll();
                $str = "";
                foreach ($ids as $v){
                    $str .= $v['id'].',';
                }
                $str = substr($str,0,-1);
                $where .= " and uc.user_id in ($str)";
            }
        }
//        $list = DI()->notorm->user_change
//            ->where("$where")
//            ->select("user_id,change_money,next_money,FROM_UNIXTIME(addtime, '%Y-%m-%d %H:%i:%s'),status")
//            ->order('id desc')
//            ->limit(($page-1) * $page_size,$page_size)
//            ->fetchAll();
        $sql = "select uc.user_id,u.user_nicename,uc.change_money,uc.next_money,uc.status,FROM_UNIXTIME(uc.addtime, '%Y-%m-%d %H:%i:%s') addtime from cmf_user_change uc join cmf_user u on uc.user_id = u.id where $where order by uc.id desc limit $of,$page_size";
//        var_dump($sql);die;
        $list = DI()->notorm->user_change->queryAll($sql);

        foreach ($list as $k => $v){
            $list[$k]['change_money'] = number_format(abs($v['change_money']),2);
            $list[$k]['next_money'] = number_format(abs($v['next_money']),2);
            if ($type == 1) {
                $list[$k]['status'] = 4;
            }
        }

        $return['list'] = $list;
        $rs['code'] = 0;
        $rs['msg'] = '获取成功';
        $rs['info'] = $return;
        return $rs;
    }

    /**
     * 邀请记录
     * @desc 用于获取邀请记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function InvitationRecord()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        $page = checkNull($this->page);
        $page_size = checkNull($this->page_size);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        $share_record = DI()->notorm->user
            ->where('parent_id = ?', $uid)
            ->select('id,user_nicename,create_time')
            ->limit(($page-1)*$page_size,$page_size)
            ->order('create_time DESC')
            ->fetchAll();
        if($share_record){
            foreach ($share_record as $k => $v){
                $share_record[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
        }
        return ['code' => 0, 'msg' => 'ok','info' => $share_record];
    }


    /**
     * 代理挣钱
     * @desc 用于获取代理挣钱页面相关数据
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[share_url] 复制地址
     * @return array info[invite_code] 邀请码
     * @return array info[sum_user_count] 累积推广
     * @return array info[today_user_count] 今日推广
     * @return array info[today_profit] 今日收益
     * @return array info[sum_profit] 累积收益
     * @return string msg 提示信息
     */
    public function DaiLiZq()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        $user_info = DI()->notorm->user
            ->where('id = ?', $uid)
            ->fetchOne();

        $users_count = DI()->notorm->user
            ->where('parent_id = ?', $uid)
            ->count();

        $today = strtotime(date('Y-m-d', time()));
        $today_end = $today + 86400;
        $today_user_count = DI()->notorm->user
            ->where('parent_id = ? and create_time >= ?', $uid, $today)
            ->count();

        $today_profit = DI()->notorm->user_change->where('change_type = ? and user_id = ?', 7, $uid)->where("addtime BETWEEN $today AND $today_end")->sum('change_money');
        if(!$today_profit) $today_profit = 0;
        $sum_profit = DI()->notorm->user_change->where('change_type = ? and user_id = ?', 7, $uid)->sum('change_money');
        if(!$sum_profit) $sum_profit = 0;
        $key = 'fh' . $user_info['invite_code'];
        $con = getConfigPub();
        $url = $con['app_android'] . '?invite=';
    
        if(!$u = DI()->redis->get($key))
        {
            $u = @file_get_contents('http://rrgysc.com/api/v1/url/short/create/1100a8f0d7ff90ec9a1c9dcda8b92d51?format=txt&type=default&url=' . $url . $user_info['invite_code']);
            DI()->redis->set($key, $u);
            
        }
        $info['share_url'] = $u;
        $info['invite_code'] = $user_info['invite_code'];
        $info['sum_user_count'] = $users_count;
        $info['today_user_count'] = $today_user_count;
        $info['today_profit'] = $today_profit;
        $info['sum_profit'] = $sum_profit;
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }

    /**
     * 开户中心--列表
     * @desc 用于获取该用户开户中心界面
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 邀请码信息
     * @return string info[0]id 邀请码id
     * @return string info[0]uid 用户id
     * @return string info[0]key 邀请码
     * @return string info[0]rate 返点数(百分比)
     * @return string info[0]invite_url 邀请链接
     * @return string info[0]count 总人数
     * @return string info[0]new 新增人数
     * @return string msg 提示信息
     */
    public function getOpenCenter()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
//        $date_type = $this->date_type;
//        $date = $this->date;
        $page = $this->page;
        $page_size = $this->page_size;

//        if (!in_array($date_type, ['day', 'week', 'month'])) {
//            $rs['code'] = 1002;
//            $rs['msg'] = 'date_type格式错误';
//            return $rs;
//        }

//        $rs = $domain->getOpenCenter($uid, $date_type, $date,$page,$page_size);
        $rs = $domain->getOpenCenter($uid,$page,$page_size);
        return $rs;
    }

    /**
     * 开户中心--生成邀请码
     * @desc 用于获取该用户开户中心界面
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function addOpenCenter()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $rate = $this->rate;

        //rate最小值判定
        if ($rate <= 0) {
            $rs['code'] = 1003;
            $rs['msg'] = '返点格式错误';
            return $rs;
        }

        $rs = $domain->addOpenCenter($uid, $rate);
        return $rs;
    }

    /**
     * 开户中心--删除
     * @desc 用于删除该用户开户中心某条记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 邀请码信息
     * @return string info[0]id 邀请码id
     * @return string info[0]uid 用户id
     * @return string info[0]invite_key 邀请码
     * @return string info[0]rate 返点数(百分比)
     * @return string info[0]invite_url 邀请链接
     * @return string info[0]count 总人数
     * @return string info[0]new 新增人数
     * @return string msg 提示信息
     */
    public function delOpenCenter()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $id = $this->id;

        $rs = $domain->delOpenCenter($uid,$id);
        return $rs;
    }

    /**
     * 会员管理--列表
     * @desc 根据条件筛选查询所有会员
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 邀请码信息
     * @return string info[0]id 用户id
     * @return string info[0]user_login 会员账号
     * @return string info[0]is_dai 是否代理,1-是，2-否
     * @return string info[0]create_time 注册时间
     *      * @return string info[0]coin 账户余额
     * @return string info[0]level 用户级别：代理/会员
     * @return string info[0]count 下级人数
     * @return string info[0]rates[0][platform] 平台号
     * @return string info[0]rates[0][remark]   备注
     * @return string info[0]rates[0][rate] 返点
     * @return string info[0]rates[0][self_rate] 自身返点
     * @return string msg 提示信息
     */
    public function getMemberList()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $account = $this->account;
        $type = $this->type;
        $start = $this->start;
        $end = $this->end;
        $page = $this->page;
        $page_size = $this->page_size;

        $rs = $domain->getMemberList($uid,$account,$type,$start,$end,$page,$page_size);
        return $rs;
    }

    /**
     * 会员管理--编辑
     * @desc 修改会员返点
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function editMember()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $id = $this->id;
        $rate = $this->rate;
        $platform = $this->platform;

//        $platforms = ['ticket_rate','live_rate','ag_rate'];
//        if (!in_array($platform,$platforms))
//        {
//            $rs['code'] = 1001;
//            $rs['msg'] = '平台号不存在';
//            return $rs;
//        }

        $rs = $domain->editMember($uid,$id,$rate,$platform);
        return $rs;
    }

    /**
     * 会员管理--转账
     * @desc 会员转账功能
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function changeMoney()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $id = $this->id;
        $money = $this->money;
        $money_password = $this->money_password;

        $rs = $domain->changeMoney($uid,$id,$money,$money_password);
        return $rs;
    }

    /**
     * 团队总览--展示
     * @desc 团队总览接口
     * @return int code 操作码，0表示成功
     * @return array info[yin] 总盈亏
     * @return array info[tou] 有效投注(彩票)
     * @return array info[zho] 奖金派送(彩票)
     * @return array info[rate] 投注返点(彩票)
     * @return array info[recharge] 充值
     * @return array info[withdrawal] 提现
     * @return array info[discount] 优惠(活动)
     * @return array info[count] 下级人数
     * @return array info[reg_count] 注册人数
     * @return array info[reward] 打赏(直播)
     * @return array info[expend] 消费(直播)
     * @return string msg 提示信息
     */
    public function teamShow()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $platform = $this->platform;
        $start = $this->start;
        $end = $this->end;

        $rs = $domain->teamShow($uid,$platform,$start,$end);
        return $rs;
    }

    /**
     * 游戏报表
     * @desc 游戏报表接口
     * @return int code 操作码，0表示成功
     * @return array info[yin] 总盈亏
     * @return array info[tou] 有效投注(彩票)
     * @return array info[zho] 奖金派送(彩票)
     * @return array info[rate] 投注返点(彩票)
     * @return array info[recharge] 充值
     * @return array info[withdrawal] 提现
     * @return array info[discount] 优惠(活动)
     * @return array info[count] 下级人数
     * @return array info[reg_count] 注册人数
     * @return array info[reward] 打赏(直播)
     * @return array info[expend] 消费(直播)
     * @return string msg 提示信息
     */
    public function gameReport()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $platform = $this->platform;
        $start = $this->start;
        $end = $this->end;

        $start = strtotime($start);
        $end = strtotime($end);
        if (!is_int($start) || !is_int($end)) {
            $rs['code'] = 1001;
            $rs['msg'] = '起始日期或结束日期错误';
            return $rs;
        }

        $rs = $domain->gameReport($uid,$platform,$start,$end);
        return $rs;
    }

    /**
     * 盈亏报表
     * @desc 盈亏报表接口
     * @return int code 操作码，0表示成功
     * @return array info[user_login] 用户名
     * @return array info[is_dai] 级别
     * @return array info[yin] 盈亏/总盈亏
     * @return array info[tou] 投注流水
     * @return array info[zho] 奖金派送
     * @return array info[rate] 投注返点
     * @return array info[recharge] 充值
     * @return array info[withdrawal] 提现
     * @return array info[discount] 优惠赠送/活动
     * @return array info[count] 下级人数
     * @return array info[reg_count] 注册人数
     * @return array info[reward] 打赏
     * @return array info[expend] 消费(开元棋牌/直播)
     * @return array info[game_in] 游戏转入
     * @return array info[game_out] 游戏转出

     * @return string msg 提示信息
     */
    public function getReport()
    {
        $domain = new Domain_Daili();
        $uid = $this->uid;
        $id = $this->id;
        $user_login = $this->user_login;
        $start = $this->start;
        $end = $this->end;
        $platform = $this->platform;
        $page = $this->page;
        $page_size = $this->page_size;

//        if (!in_array($date_type, ['day', 'week', 'month'])) {
//            $rs['code'] = 1002;
//            $rs['msg'] = 'date_type格式错误';
//            return $rs;
//        }

        $rs = $domain->getReport($uid,$id,$user_login,$start,$end,$platform,$page,$page_size);
        return $rs;
    }

//    /**
//     * 盈亏报表--下级列表
//     * @desc 获取下级列表
//     * @return int code 操作码，0表示成功
//     * @return array info[id] 用户id
//     * @return array info[user_login] 用户账号
//     * @return array info[is_dai] 是否代理，1-代理，2-会员
//     * @return array info[tou] 总有效投注
//     * @return array info[rate] 总返点
//     * @return array info[yin] 总盈亏
//     * @return string msg 提示信息
//     */
//    public function getReportDown()
//    {
//        $domain = new Domain_Daili();
//        $uid = $this->uid;
//
//        $rs = $domain->getReportDown($uid);
//        return $rs;
//    }
}
