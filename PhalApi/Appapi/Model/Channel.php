<?php


class Model_Channel extends PhalApi_Model_NotORM
{
    //获取通道信息
    public function get_channel($changeid) {

//        $charge=DI()->notorm->charge_rules->select('*')->where('id=?',$changeid)->fetchOne();
        $channel = DI()->notorm->channel->select('*')->where('id=?',$changeid)->fetchOne();

        if(!$channel){
            return 1003;
        }
        return $channel;
    }
}