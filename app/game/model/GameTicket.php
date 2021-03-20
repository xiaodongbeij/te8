<?php


namespace app\game\model;


use app\user\model\User;

class GameTicket extends BaseModel
{
    protected $table = 'cmf_game_ticket';


    protected function getAddtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->field('id,user_login');
    }
}