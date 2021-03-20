<?php


namespace app\user\model;


use think\Db;

class UserChange extends BaseModel
{
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $table = 'cmf_user_change';
    protected $type = [
        'addtime'  =>  'timestamp',
    ];
    // 追加属性
    protected $append = [
        'withdraw_id_name',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function iszombie()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->field('id,iszombie')->bind(['isz' => 'iszombie']);
    }

    public function tou()
    {
        return $this->hasOne(User::class, 'id', 'touid');
    }

    public function getAddtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getAuditTimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);

    }

    public function getExamineTimeAttr($value)
    {
        if($value) return date('Y-m-d H:i:s', $value);
    }

    public function getWithdrawIdNameAttr($value, $data)
    {
        if($data['withdraw_id']){
            switch ($data['withdraw_type']){
                case 1:
                    return $data['real_name'] . '-' . $data['bank_name'] . '-' . $data['bank_card'];
                case 2:
                    $user_info = UserInfo::get($data['withdraw_id']);
                    return $user_info['wxpay_account'];
            }

            switch ($data['change_type']){
                case 11:
                    $gift = Db::name('gift')->where('id',$data['withdraw_id'])->find();
                    return $gift['giftname'];
                case 14:
                    $car = Db::name('car')->where('id',$data['withdraw_id'])->find();
                    return $car['name'];
                case 24:
                    return '提现ID(' . $data['withdraw_id'] . ')';
            }
        }
    }


}