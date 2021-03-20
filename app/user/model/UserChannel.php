<?php


namespace app\user\model;


use app\admin\model\Channel;

class UserChannel extends BaseModel
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $table = 'cmf_user_channel';

    // 追加属性
    protected $append = [
        'channel_name',
    ];

    public function getChannelNameAttr($value, $data)
    {
        $channels = explode('|', $data['channel_id']);
        if($channels){
            $channel_names = [];
            foreach ($channels as $k => $v){
                $channel_names[] = Channel::where('status', 1)->where('del_status', 0)->get($v)['channel_name'];
            }
            $value = implode(' | ', $channel_names);
            return $value;
        }
        return '';
    }

    public function channel()
    {
        return $this->belongsToMany(Channel::class,'ChannelPay' ,'user_channel_id', 'channel_id');
    }
 
    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getUpdateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}