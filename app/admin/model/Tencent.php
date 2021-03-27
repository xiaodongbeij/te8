<?php


namespace app\admin\model;


class Tencent extends BaseModel
{
    protected $table = 'cmf_tencent';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}