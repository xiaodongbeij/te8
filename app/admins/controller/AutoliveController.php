<?php

namespace app\admin\controller;
use think\Db;


class AutoliveController{
    
    public function index()
    {
 
        $users = DB::name('user')->field("id,ishot,isrecommend,user_nicename")->select();

        // $res2 = file_get_contents("http://api.hclyz.com:81/mf/jsonshijuexiu.txt");
        // $res3 = file_get_contents("http://api.hclyz.com:81/mf/jsonmeixi.txt");
        // $res4 = file_get_contents("http://api.hclyz.com:81/mf/jsonxiaojingling.txt");

        // $res2 = json_decode($res2,true);
        // $res3 = json_decode($res3,true);
        // $res4 = json_decode($res4,true);
        // $list = array_merge($res2['zhubo'],$res3['zhubo'],$res4['zhubo']);
        
        $config = Db::name('option')->where('option_name', 'configpri')->value('option_value');
        $config = json_decode($config, true);
        // 名片
        $card = explode(',',$config['card']);
        // 金额
        $card_money = explode(',', $config['card_money']);
        // 彩种
        $lbcz_setup = explode(',', $config['lbcz_setup']);
        
        $list = [];
        for($i=1;$i<=4;$i++)
        {
            $res = file_get_contents('http://qwe.fanbanxxjs5.cn/api/public/?service=Home.getHot&p=' . $i);
            $res = json_decode($res,true);
            
            if(!empty($res['data']['info'][0]['list']))
            {
               $list = array_merge($list, $res['data']['info'][0]['list']);
            }
 
        }
       
        foreach($list as $k => $v)
        {
            if(empty($users[$k]['id'])) continue;
            $nowtime=time();
            $uid=$users[$k]['id'];
            
            DB::name('live')->where('uid',$uid)->delete();
            
            $thumb =  $v['thumb'];
            $pull=urldecode($v['pull']);
            $type=0;
            $type_val=0;
            $anyway=0;
            $liveclassid=0;
            $stream=$uid.'_'.$nowtime;
            $title= $users[$k]['user_nicename'];

            $cid = $lbcz_setup[array_rand($lbcz_setup)];
    
            $cp = DB::name('gameCaizhong')->where('id',$cid)->find();
            
            $data2[] = array(
                "uid"=>$uid,
                "ishot"=>$users[$k]['ishot'],
                "isrecommend"=>$users[$k]['isrecommend'],
                "showid"=>$nowtime,
                "starttime"=>$nowtime,
                "title"=>$title,
                "province"=>'',
                "city"=>'好像在火星',
                "stream"=>$stream,
                "thumb"=>$thumb,
                "pull"=>$pull,
                "lng"=>'',
                "lat"=>'',
                "type"=>$type,
                "type_val"=>$type_val,
                "isvideo"=>1,
                "islive"=>1,
                "anyway"=>$anyway,
                "liveclassid"=>$liveclassid,
                'show_name' => $cp['show_name'],
                'short_name' => $cp['short_name'],
                'c_id' => $cp['id'],
                'c_type' => $cp['type'],
                'icon' => get_upload_path($cp['icon']),
                'hot' => $cp['hot'],
                'reward_amount' => $card_money[array_rand($card_money)]
            );

            DB::name('user')->where('id', $uid)->setField('wechat',$card[array_rand($card)]);
           
        }
        
        $rs = DB::name('live')->insertAll($data2);
    }
    
    
    
    
    
}