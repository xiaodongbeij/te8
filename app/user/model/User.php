<?php


namespace app\user\model;


class User extends BaseModel
{
    protected $table = 'cmf_user';

    // 追加属性
    protected $append = [
        'comprehensive',
    ];

    public function getComprehensiveAttr($value, $data)
    {
        if($data['count_money'] && $data['count_Withdrawal']){
            return number_format($data['count_money'] - $data['count_Withdrawal'],2);
        }else{
            return 0.00;
        }
    }
}