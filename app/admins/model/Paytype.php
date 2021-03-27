<?php


namespace app\admin\model;


class Paytype extends BaseModel
{
    protected $table = 'cmf_paytype';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 追加属性
    protected $append = [
        'src_text',
    ];

    public function getSrcTextAttr( $value,$data )
    {
        $data['src'] = isset($data['src']) ? $data['src'] : '';
        if($data['src'] != '') return get_upload_path($data['src']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
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