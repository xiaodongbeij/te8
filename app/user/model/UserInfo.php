<?php


namespace app\user\model;


class UserInfo extends BaseModel
{
    protected $table = 'cmf_user_info';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'addtime';
    protected $updateTime = 'updatedtime';

    // 追加属性
    protected $append = [
        'wxpay_img_text',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getWxpayImgTextAttr( $value,$data )
    {
        $data['wxpay_img'] = isset($data['wxpay_img']) ? $data['wxpay_img'] : '';
        if($data['wxpay_img'] != '') return get_upload_path($data['wxpay_img']);
        return '__TMPL__/public/assets/images/default-thumbnail.png';
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