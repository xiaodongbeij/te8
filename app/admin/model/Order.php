<?php


namespace app\admin\model;


use app\user\model\User;

class Order extends BaseModel
{

    protected $table = 'cmf_order';

    public function getAddtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getPayTimeAttr($value)
    {
        if($value)
        {
             return date('Y-m-d H:i:s', $value);
        }
        return '';
    }
    
    public function getRemarkAttr($value,$data)
    {
        if($value) return $value;

        if($data['payway'] == 3)
        {
            return '姓名:' . $data['name'] . ' 附言:' . $data['postscript']; 
        }
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->field('id,user_login');
    }

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id')->field('id,channel_name');
    }
}