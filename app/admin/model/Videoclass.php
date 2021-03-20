<?php


namespace app\admin\model;


class Videoclass extends BaseModel
{
    protected $table = 'cmf_video_class';


    // 追加属性
    protected $append = [
        'unchecked_text',
        'checked_text'
    ];

    public function getCheckedTextAttr( $value,$data )
    {
        $data['checked'] = isset($data['checked']) ? $data['checked'] : '';
        if($data['checked'] != '') return get_upload_path($data['checked']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
    }

    public function getUncheckedTextAttr( $value,$data )
    {
        $data['unchecked'] = isset($data['unchecked']) ? $data['unchecked'] : '';
        if($data['unchecked'] != '') return get_upload_path($data['unchecked']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
    }

    
}