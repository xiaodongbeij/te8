<?php
/**
 * 用户反馈
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class LiveController extends HomebaseController{
    
    
    
   
    
    
    public function video()
    {
        $url = 'http://x4.qiezizy8.com/api.php/provide/vod/?ac=detail&h=72';
        $json = $this->http_request($url);
        if(!$json) die('错误');
        $json = json_decode($json, true);
        $p = $json['pagecount'];
        for($i=1;$i<=$p;$i++)
        {

            $list = $this->http_request("http://x4.qiezizy8.com/api.php/provide/vod/?ac=detail&h=72&pg=" . $i);
            $list = json_decode($list, true);
           
            if(!empty($list['list']))
            {
                foreach ($list['list']  as $v)
                {
                    $param = [
                        'uid' => 1,
                        'title' => $v['vod_name'],
                        'thumb' => $v['vod_pic'],
                        'href' => mb_substr($v['vod_play_url'],4),
                        'classid' => 7,
                        'anyway' => '1.1',
                        'addtime' => time(),
                    ];
                    var_dump($param);
                    Db::name('video')->insert($param,true);
                }
            }
        }
        
        echo '采集成功';
        
    }
    
    
    
    public function autoLive()
    {
        
        
        $users = DB::name('user')->where('iszombie',1)->field("id,ishot,isrecommend,user_nicename")->select();
     
        $live = [
            'jsonkugua.txt',
            'jsonfanjiashequ.txt',
            'jsonxiaonaimao.txt',
            'jsonrichu.txt',
            'jsonshibajin.txt',
            'jsonqingqu.txt',
            'jsonxiaomianao.txt',
            'jsonxiaolajiao.txt',
            'jsonyechun.txt',
            'jsonxiaojingling.txt',
            'jsonxiaomiaochong.txt',
            'jsonlanmao.txt',
            'jsonhuihui.txt'
        ];
        $list = [];
        foreach ($live as $v)
        {
            $url = "http://api.vipmisss.com:81/mf/".trim($v);
            $res = @file_get_contents($url);
            $res = json_decode($res, true);
            $list = array_merge($list, $res['zhubo']); 

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
        
        if(empty($list))
        {
            file_get_contents('https://api.telegram.org/bot1793450342:AAH6wHOtvvFyWqrWkmzxXBAyRFbcW52nwF0/sendMessage?chat_id=-532342889&text=@zuanshi6688%20@chendan777  @kk798  警告没有采集到视频!!!!');
             die('没采集到数据');
        }
        
        file_get_contents('https://api.telegram.org/bot1793450342:AAH6wHOtvvFyWqrWkmzxXBAyRFbcW52nwF0/sendMessage?chat_id=-532342889&text= %20%E7%9B%B4%E6%92%AD%E5%B7%B2%E9%87%87%E9%9B%86%EF%BC%8C%E8%AF%B7%E6%A3%80%E6%9F%A5%E6%98%AF%E5%90%A6%E6%9C%89%E5%B9%BF%E5%91%8A');
        foreach($list as $k => $v)
        {
           
            $pull=$v['address'];
            $thumb =  $v['img'];
    
        
            $nowtime=time();
            $uid=$users[$k]['id'];
           
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
        
        echo "更新成功\n";
    }
    
    
    public function telegram($message = '测试')
    {
        $telegram =  config('telegram');
        $message = urlencode($message);
        $url = $telegram . $message;
        file_get_contents($url);
    }
    
    
    public function statistics()
    {
        $day = date('Y-m-d H:i:s');
        $user_num = DB::name('user')->whereTime('create_time','today')->count();
        $user_money = DB::name('user_change')->whereTime('addtime','today')->where('change_type',1)->sum('change_money');
        $tx = DB::name('user_change')->whereTime('addtime','today')->where('change_type',2)->sum('change_money');
        $tx = abs($tx);
        $yh = DB::name('user_change')->whereTime('addtime','today')->where('change_type',6)->sum('change_money');
        $message = " 天鹅直播(截止当日时间)：\n $day \n 注册量: $user_num \n 充值量: $user_money 元 \n 提现: $tx 元 \n 优惠赠送: $yh 元";
        $this->telegrams($message);
    }

    
    
    public function telegrams($message = '测试')
    {
        $telegram =  config('telegram');
        $message = urlencode($message);
        $url = $telegram . $message;
        file_get_contents($url);
    }
    
    
    protected function base64EncodeImage($img_url)
    {
        $img = '';

		$img = @file_get_contents($img_url);
		

		if(!$img) return true;

		
		
		$token = '24.bd7277bdfec2dae2c78ed4f1f99bd9b1.2592000.1619528453.282335-23887031';
		
		$url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token=' . $token;

		$bodys = array(
            'image' => base64_encode($img)
        );
        $res = $this->request_post($url, $bodys);
     
		return !empty($res['words_result'][0]);
    }
    
    
    protected function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
    
        $postUrl = $url;
        $curlPost = $param;
        // 初始化curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // post提交方式
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        // 运行curl
        $data = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($data,true);
    }
    
    
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    
}