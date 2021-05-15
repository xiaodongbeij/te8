<?php


namespace app\admin\model;


class CustomerService extends BaseModel
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $table = 'cmf_customer_service';

    public function getCreateTimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);
    }

    public function getUpdateTimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);
    }
}