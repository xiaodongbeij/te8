<?php


namespace app\game\model;


class GameCate extends BaseModel
{
    protected $table = 'cmf_game_cate';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $updateTime = 'edittime';
    // 追加属性
    protected $append = [
        'icon_text',
    ];

    public function getIconTextAttr( $value,$data )
    {
        $data['icon'] = isset($data['icon']) ? $data['icon'] : '';
        if($data['icon'] != '') return get_upload_path($data['icon']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
    }

    protected function getAddtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
    protected function getEdittimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}