<?php


namespace app\game\model;


class GameRuleRate extends BaseModel
{
    protected $table = 'cmf_game_rule_rate';

    public function cz()
    {
        return $this->hasOne(GameCaizhong::class, 'id', 'cai_id');
    }
}