<?php
namespace app\admin\model;

class Channel extends BaseModel
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $updateTime = 'updatetime';

    protected $table = 'cmf_channel';

    public function getAddtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}