<?php


namespace app\game\model;


class GameCaizhong extends BaseModel
{
    protected $table = 'cmf_game_caizhong';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 追加属性
    protected $append = [
        'icon_text',
    ];


    public function cate()
    {
        return $this->hasOne(GameCate::class, 'id', 'cat_id')->field('id,name');
    }


    public function getIconTextAttr( $value,$data )
    {
        if($data['icon']) return get_upload_path($data['icon']);
        return '';
    }

    protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    protected function getUpdateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}