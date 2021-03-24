<?php
/**
 * 用户反馈
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class LiveController extends HomebaseController{
    
    
    
    public function index()
    {
        

 
        $users = DB::name('user')->where('iszombie',1)->field("id,ishot,isrecommend,user_nicename")->select();
        
        // $live = [];
        // $getJson = file_get_contents('http://api.maiyoux.com:81/mf/json.txt');
        // $getJson = json_decode($getJson, true);

        // foreach ($getJson['pingtai'] as $v)
        // {
        //     if($v['Number'] > 50)
        //     {
        //         $live[] = $v['address'];
        //     }
        // }
        // $live = [
        //     'jsonkugua.txt',
        //     'jsonfanjiashequ.txt',
        //     'jsonxiaonaimao.txt',
        //     'jsonrichu.txt',
        //     'jsonshibajin.txt',
        //     'jsonqingqu.txt',
        //     'jsonxiaomianao.txt',
        //     'jsonxiaolajiao.txt',
        //     'jsonyechun.txt',
        //     'jsonxiaojingling.txt',
        //     'jsonxiaomiaochong.txt',
        //     'jsonlanmao.txt',
        //     'jsonhuihui.txt'
        // ];
        // $list = [];
        // foreach ($live as $v)
        // {
        //     $url = "http://api.maiyoux.com:81/mf/".trim($v);
        //     $res = file_get_contents($url);
        //     $res = json_decode($res, true);
        //     $list = array_merge($list, $res['zhubo']); 

        // }
        
        $lists = [];
        for($i=1;$i<=3;$i++)
        {
            $list = file_get_contents('http://qwe.fanbanxxjs5.cn/api/public/?service=Home.getHot&p=' . $i);
            $list = json_decode($list, true);
            $list = isset($list['data']['info'][0]['list']) ? $list['data']['info'][0]['list'] :[];
            $lists = array_merge($lists,$list);
        }
        

        $config = getConfigPri();

        // 名片
        $card = explode(',',$config['card']);
        $card_qq = explode(',',$config['card_qq']);
        
    
        // 金额
        $card_money = explode(',', $config['card_money']);
        // 彩种
        $lbcz_setup = explode(',', $config['lbcz_setup']);
        

//        DB::name('live')->where('uid','>',0)->delete();
        
        if(empty($lists)) die('没采集到数据');
        foreach($lists as $k => $v)
        {
            $pull=$v['pull'];
            // if(empty($users[$k]['id'])) continue;
            // if(mb_strlen($pull) > 255 ) continue;
            // 获取视频源
            
            
            // 过滤这个源  06b.anhuazhujiu.cn 02b.anhuazhujiu.cn  pull1.llhappy.xyz 07b.anhuazhujiu.cn  czrk.net.cn  05b.anhuazhujiu.cn 06b.anhuazhujiu.cn 04b.anhuazhujiu.cn
            // if(strstr($pull,'.mp4')) continue;
            if(strstr($pull,'anhuazhujiu.cn') || strstr($pull,'llhappy.xyz')  || strstr($pull,'czrk.net.cn') || true)
            {

                // if(!strstr($pull,'llhappy.xyz')) continue;
        
                $nowtime=time();
                $uid=$users[$k]['id'];
                $thumb =  $v['thumb'];
                $type=0;
                $type_val=0;
                $anyway=0;
                $liveclassid=0;
                $stream=$uid.'_'.$nowtime;
                $title= $users[$k]['user_nicename'];
                // 随机彩种
                $cid = $lbcz_setup[array_rand($lbcz_setup)];
        
                $cp = DB::name('gameCaizhong')->where('id',$cid)->find();
      
                $data2 = array(
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
                DB::name('live')->insert($data2,true);
                delcache('userinfo_' . $uid);
                DB::name('user')->where('id', $uid)->update(['wechat'=>$card[array_rand($card)], 'qq' => $card_qq[array_rand($card_qq)]]);
            }
        }
        
        echo "更新成功\n";
    }



    
}