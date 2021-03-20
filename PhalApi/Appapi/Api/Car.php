<?php

/**
 * 坐骑
 */
class Api_Car extends PhalApi_Api
{
    public function getRules() {
        return array(
            'getMyCarList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),
            'ExCar' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'my_car_id' => array('name' => 'my_car_id', 'type' => 'int', 'require' => true, 'desc' => '我的坐骑ID'),
                'status' => array('name' => 'status', 'type' => 'int', 'require' => true, 'desc' => '装备状态：1-装备，0-不装备'),
            ),
            'getCarList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),
            'buyCar' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'car_id' => array('name' => 'car_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '坐骑ID'),
            ),
        );
    }

    /**
     * 装备坐骑
     * @desc 用于 装备坐骑
     * @return int code 操作码，0表示成功， 1表示失败
     * @return string msg 提示信息
     */
    public function ExCar()
    {
        $user_id = checkNull($this->uid);
        $my_car_id = checkNull($this->my_car_id);
        $status = checkNull($this->status);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $myCarInfo = DI()->notorm->car_user->where('id = ? and uid = ?', $my_car_id, $user_id)->fetchOne();
        if(!$myCarInfo){
            $rs['code'] = 1001;
            $rs['msg'] = '您还未用拥有该坐骑';
            return $rs;
        }
        if($myCarInfo['endtime'] < time()) return ['code' => 1, 'msg' => '已过期'];
        $res = DI()->notorm->car_user->where('id = ? and uid = ?', $my_car_id, $user_id)->update(['status' => $status]);
        if($status && $status == 1){
            DI()->notorm->car_user->where('id != ? and uid = ?', $my_car_id, $user_id)->update(['status' => 0]);
        }

        if($res) return ['code' => 0, 'msg' => '操作成功'];
        return ['code' => 1, 'msg' => '操作失败'];
    }


    /**
     * 我的坐骑列表
     * @desc 用于 我的坐骑列表
     * @return int code 操作码，0表示成功， 1表示失败
     * @return string msg 提示信息
     */
    public function getMyCarList()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $sql = "SELECT A.id as id, A.status, A.endtime, B.name, B.thumb, B.swf, B.swftime, B.needcoin, B.score, B.words FROM cmf_car_user AS A LEFT JOIN cmf_car AS B ON A.carid = B.id WHERE (A.uid = :uid);";
        $params = array(':uid' => $user_id);

        $car_list = DI()->notorm->car_user->queryAll($sql, $params);

        if($car_list) return ['code' => 0, 'msg' => 'ok', 'info' => $car_list];
        return ['code' => 1, 'msg' => '获取失败'];

    }

    /**
     * 坐骑列表
     * @desc 用于 获取坐骑列表
     * @return int code 操作码，0表示成功， 1表示失败
     * @return string msg 提示信息
     */
    public function getCarList()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $car_list = DI()->notorm->car
            ->select('name,thumb,swf,swftime,needcoin,words')
            ->order('list_order asc')
            ->fetchAll();
        if($car_list) return ['code' => 0, 'msg' => 'ok', 'info' => $car_list];
        return ['code' => 1, 'msg' => '获取失败'];
    }


    /**
     * 购买坐骑
     * @desc 用于 购买坐骑
     * @return int code 操作码，0表示成功， 1表示失败
     * @return string msg 提示信息
     */
    public function buyCar()
    {
        $user_id = checkNull($this->uid);
        $car_id = checkNull($this->car_id);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $car_info = DI()->notorm->car
            ->select('*')
            ->where('id=?',$car_id)
            ->fetchOne();
        if(!$car_info){
            $rs['code'] = 1001;
            $rs['msg'] = '坐骑信息不存在';
            return $rs;
        }

        $type = 0;
        $action = 5;
        $addtime = time();
        $total = $car_info['needcoin'];

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        /* 更新用户余额 消费 */
        $res1 = DI()->notorm->user
            ->where('id = ? and coin>=?', $user_id,$total)
            ->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );
        if(!$res1){
            $rs['code'] = 1002;
            $rs['msg'] = '余额不足';
            return $rs;
        }

        $insert = array("type" => $type, "action" => $action, "uid" => $user_id, "touid" => $user_id, "giftid" => $car_id, "giftcount" => '1', "totalcoin" => $total, "addtime" => $addtime );
        $res2 = DI()->notorm->user_coinrecord->insert($insert);

        $res4 = user_change_action($user_id,14,-1 * $total,DI()->config->get('app.change_type')[14],$user_id,$car_id,1,'','',2);

        $endtime = $addtime + (86400 * 30);

        $user_car_info = DI()->notorm->car_user
            ->where("uid = ? and carid = ?",$user_id,$car_id)
            ->select('*')
            ->fetchOne();

        if($user_car_info)
        {
            $new_endtime = $user_car_info['endtime'] + (86400 * 30);
            $res3 = DI()->notorm->car_user->where('uid = ? and carid = ?', $user_id, $car_id)->update(['endtime' => $new_endtime]);
        }else{
            $data = ['uid' => $user_id, 'carid' => $car_id, 'endtime' => $endtime, 'addtime' => $addtime];
            $res3 = DI()->notorm->car_user->insert($data);
        }

        if ($res1 && $res2 && $res3 && $res4 && $res4 != 2){
            DI()->notorm->commit('db_appapi');
            return ['code' => 0, 'msg' => '购买成功,请去我的坐骑装备上吧'];
        }else{
            DI()->notorm->rollback('db_appapi');
            return ['code' => 1, 'msg' => '购买失败'];
        }
    }
}