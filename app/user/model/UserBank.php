<?php


namespace app\user\model;


class UserBank extends BaseModel
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $updateTime = 'updatedtime';

    protected $table = 'cmf_user_bank';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'u_id');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function getAddtimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);
    }

    public function getUpdatedtimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);
    }
}