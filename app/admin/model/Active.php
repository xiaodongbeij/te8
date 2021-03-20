<?php


namespace app\admin\model;


class Active extends BaseModel
{
    protected $table = 'cmf_active';

    // 追加属性
    protected $append = [
        'img_text',
    ];

    public function getImgTextAttr( $value,$data )
    {
        $data['img'] = isset($data['img']) ? $data['img'] : '';
        if($data['img'] != '') return get_upload_path($data['img']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
    }

    public function getStartAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getEndAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}