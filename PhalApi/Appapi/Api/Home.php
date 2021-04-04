<?php
/**
 * 首页
 */
class Api_Home extends PhalApi_Api {  

	public function getRules() {
		return array(
			'getRecommendLive' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
		
			'getHot' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
			
			'getFollow' => array(
				'uid' => array('name' => 'uid', 'type' => 'int','min'=>1,'require' => true, 'desc' => '用户ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
			
			'getNew' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
			
			'search' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'min'=>1 ,'desc' => '用户ID'),
				'key' => array('name' => 'key', 'type' => 'string', 'default'=>'' ,'desc' => '用户ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
			
			
			'getNearby' => array(
                'lng' => array('name' => 'lng', 'type' => 'string', 'desc' => '经度值'),
                'lat' => array('name' => 'lat', 'type' => 'string','desc' => '纬度值'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
			
			'getRecommend' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'min'=>1 ,'desc' => '用户ID'),
			),
			
			'attentRecommend' => array(
				'uid' => array('name' => 'uid', 'type' => 'int' ,'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'string', 'desc' => '关注用户ID，多个用,分隔'),
			),
            'profitList'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int','min'=>1,'require' => true, 'desc' => '用户ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
                'type' => array('name' => 'type', 'type' => 'string', 'default'=>'day' ,'desc' => '参数类型，day表示日榜，week表示周榜，month代表月榜，total代表总榜'),
            ),

            
            'consumeList'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int','min'=>1,'require' => true, 'desc' => '用户ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
                'type' => array('name' => 'type', 'type' => 'string', 'default'=>'day' ,'desc' => '参数类型，day表示日榜，week表示周榜，month代表月榜，total代表总榜'),
            ),
            
            'getClassLive'=>array(
                'liveclassid' => array('name' => 'liveclassid', 'type' => 'int', 'default'=>'0' ,'desc' => '直播分类ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
			'getShopList'=>array(
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
			'getShopThreeClass'=>array(
				'shopclassid' => array('name' => 'shopclassid', 'type' => 'int', 'default'=>'0' ,'desc' => '商品二级分类ID'),
            ),
			'getShopClassList'=>array(
				'shopclassid' => array('name' => 'shopclassid', 'type' => 'int', 'default'=>'0' ,'desc' => '商品三级分类ID'),
				'sell' => array('name' => 'sell', 'type' => 'string','desc' => '销量 asc正序   desc倒叙'),
				'price' => array('name' => 'price', 'type' => 'string','desc' => '价格 asc正序   desc倒叙'),
				'isnew' => array('name' => 'isnew', 'type' => 'int', 'default'=>'0' ,'desc' => '是否为新品(三天内发布的商品) 0否  1是'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
			
			'searchShop' => array(
				'key' => array('name' => 'key', 'type' => 'string', 'default'=>'' ,'desc' => '商品昵称'),
				'sell' => array('name' => 'sell', 'type' => 'string','desc' => '销量 asc正序   desc倒叙'),
				'price' => array('name' => 'price', 'type' => 'string','desc' => '价格 asc正序   desc倒叙'),
				'isnew' => array('name' => 'isnew', 'type' => 'int', 'default'=>'0' ,'desc' => '是否为新品(三天内发布的商品) 0否  1是'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
            'getActive' => array(
                'cate' => array('name' => 'cate', 'type' => 'int', 'default'=>'1' ,'desc' => '活动分类,1-直播,2-游戏'),
            )
		);
	}

    /**
     * 轮播图APP
     * @desc 用于 轮播图APP
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function SlideApp()
    {
        $info = DI()->notorm->slide_item
            ->select("title,image,url,target,description,content")
            ->where('status = 1 and slide_id = 2')
            ->order("list_order desc")
            ->fetchAll();
        if($info)
        {
            foreach ($info as $key => $value) {
                $info[$key]['image'] = get_upload_path($value['image']);
            }
        }
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];     
    }

    /**
     * 直播分类
     * @desc 用于 直播分类
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function LiveClass()
    {
        $list = getLiveClass();
        return ['code' => 0, 'msg' => 'ok', 'info' => $list];
    }

    /**
     * 视频分类
     * @desc 用于 视频分类
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function videoClass()
    {
        $videoclasslist = getVideoClass();
        return ['code' => 0, 'msg' => 'ok', 'info' => $videoclasslist];
    }

    /**
     * 等级列表
     * @desc 用于 等级列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function Level()
    {
        $level= getLevelList();
        return ['code' => 0, 'msg' => 'ok', 'info' => $level];
    }

    /**
     * APP下载详情
     * @desc 用于APP下载详情
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function getAppDownload()
    {
        $data = getConfigPub();
        $info['qr_url'] = get_upload_path($data['qr_url']);
        $info['apk_url'] = $data['apk_url'];
        $info['ipa_url'] = $data['ipa_url'];
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }
	
    /**
     * 配置信息
     * @desc 用于获取配置信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0] 配置信息
     * @return object info[0].guide 引导页
	 * @return string info[0].guide.switch 开关，0关1开
	 * @return string info[0].guide.type 类型，0图片1视频
	 * @return string info[0].guide.time 图片时间
	 * @return array  info[0].guide.list
	 * @return string info[0].guide.list[].thumb 图片、视频链接
	 * @return string info[0].guide.list[].href 页面链接
     * @return string msg 提示信息
     */
    public function getConfig() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $key = 'gethome';
        
        if($info = getcache($key))
        {
            $rs['info'][0] = $info;
            return $rs;
        }
        $info = getConfigPub();
        $ConfigPri = getConfigPri();
        unset($info['site_url']);
        unset($info['site_seo_title']);
        unset($info['site_seo_keywords']);
        unset($info['site_seo_description']);
        unset($info['site_icp']);
        unset($info['site_gwa']);
        unset($info['site_admin_email']);
        unset($info['site_analytics']);
        unset($info['copyright']);

        unset($info['sina_icon']);
        unset($info['sina_title']);
        unset($info['sina_desc']);
        unset($info['sina_url']);
        unset($info['qq_icon']);
        unset($info['qq_title']);
        unset($info['qq_desc']);
        unset($info['qq_url']);
        unset($info['payment_des']);

        $info_pri = getConfigPri();

        $list = getLiveClass();
        $videoclasslist = getVideoClass();
        $level= getLevelList();

        foreach($level as $k=>$v){
            unset($v['level_up']);
            unset($v['addtime']);
            unset($v['id']);
            unset($v['levelname']);
            $level[$k]=$v;
        }

        $levelanchor= getLevelAnchorList();

        foreach($levelanchor as $k=>$v){
            unset($v['level_up']);
            unset($v['addtime']);
            unset($v['id']);
            unset($v['levelname']);
            $levelanchor[$k]=$v;
        }
        $info['chatserver'] = $ConfigPri['chatserver'];
        $info['liveclass']=$list;

        $info['videoclass']=$videoclasslist;

        $info['level']=$level;

        $info['levelanchor']=$levelanchor;

        $info['tximgfolder']='';//腾讯云图片存储目录
        $info['txvideofolder']='';//腾讯云视频存储目录
        $info['txcloud_appid']='';//腾讯云视频APPID
        $info['txcloud_region']='';//腾讯云视频地区
        $info['txcloud_bucket']='';//腾讯云视频存储桶
        $info['cloudtype']='1';//视频云存储类型

        $info['qiniu_domain']=DI()->config->get('app.Qiniu.space_host').'/';//七牛云存储空间地址
        $info['qiniu_uphost']=DI()->config->get('app.Qiniu.uphost');//七牛上传域名（小程序使用）
        $info['qiniu_region']=DI()->config->get('app.Qiniu.region');//七牛上存储区域（小程序使用）
        $info['video_audit_switch']=$info_pri['video_audit_switch']; //视频审核是否开启

        /* 私信开关 */
        $info['letter_switch']=$info_pri['letter_switch']; //视频审核是否开启

        /* 引导页 */
        $domain = new Domain_Guide();
        $guide_info = $domain->getGuide();

        $info['guide']=$guide_info;

        /** 敏感词集合*/
        $dirtyarr=array();
        if($info_pri['sensitive_words']){
            $dirtyarr=explode(',',$info_pri['sensitive_words']);
        }
        $info['sensitive_words']=$dirtyarr;
        //视频水印图片
        $info['video_watermark']=get_upload_path($info_pri['video_watermark']); //视频审核是否开启

        $info['shopexplain_url']=$info['site']."/portal/page/index?id=38";
        $info['stricker_url']=$info['site']."/portal/page/index?id=39";

        $info['shop_system_name']=$info_pri['shop_system_name']; //系统店铺名称

        $info['login_private_url']=get_upload_path($info['login_private_url']);
        $info['login_service_url']=get_upload_path($info['login_service_url']);

        $info['socket_url']=$info_pri['chatserver']; //socket url地址（小程序用）

        $info['share_img'] = DI()->config->get('app.share_img');

        $info['slide_app'] = $level = DI()->notorm->slide_item
            ->select("title,image,url,target,description,content")
            ->where('status = 1 and slide_id = 2')
            ->order("list_order desc")
            ->fetchAll();

        //APP设置
        $app_set = DI()->notorm->option
            ->select("id,option_value")
            ->where('id = 10')
            ->fetchOne();
        $info['app_set'] = json_decode($app_set['option_value'], true);
        


        setcaches($key,$info);
        $rs['info'][0] = $info;

        return $rs;
    }
    
    
    
    /**
     * 天鹅配置信息
     * @desc 用于获取配置信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0] 配置信息
     * @return object info[0].guide 引导页
	 * @return string info[0].guide.switch 开关，0关1开
	 * @return string info[0].guide.type 类型，0图片1视频
	 * @return string info[0].guide.time 图片时间
	 * @return array  info[0].guide.list
	 * @return string info[0].guide.list[].thumb 图片、视频链接
	 * @return string info[0].guide.list[].href 页面链接
     * @return string msg 提示信息
     */
    public function getPubConfig() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $key = 'gethome';
        
        if($info = getcache($key))
        {
            $rs['info'][0] = $info;
            return $rs;
        }
        $config = getConfigPub();
        // var_dump($config);die;
        $ConfigPri = getConfigPri();
        $info['service_address'] = $config['service_address'];
        $info['isnotice'] = $config['isnotice'];;
        $info['notice'] = $config['app_notice'];
        $info['isup'] = $config['isup'];
        $info['down_url'] = $config['down_url'];
        $info['apk_ver'] = $config['apk_ver'];
        $info['apk_url'] = $config['apk_url'];
        $info['apk_des'] = $config['apk_des'];
        $info['ipa_ver'] = $config['ipa_ver'];
        $info['ipa_url'] = $config['ipa_url'];
        $info['ipa_des'] = $config['ipa_des'];
        $info['popup'] = $config['popup'];
        $info['ispopup'] = $config['ispopup'];
        $info['telegram'] = $config['telegram'];
        $info['potato'] = $config['potato'];
        $info['chatserver'] = $config['chatserver'];
        $info['chatserver'] = $ConfigPri['chatserver'];
        $info['join_in_qq'] = $config['join_in_qq'];
        $info['join_in_wx'] = $config['join_in_wx'];
        $info['join_in_tg'] = $config['join_in_tg'];
        
        // $ConfigPri = getConfigPri();
        // unset($info['site_url']);
        // unset($info['site_seo_title']);
        // unset($info['site_seo_keywords']);
        // unset($info['site_seo_description']);
        // unset($info['site_icp']);
        // unset($info['site_gwa']);
        // unset($info['site_admin_email']);
        // unset($info['site_analytics']);
        // unset($info['copyright']);

        // unset($info['sina_icon']);
        // unset($info['sina_title']);
        // unset($info['sina_desc']);
        // unset($info['sina_url']);
        // unset($info['qq_icon']);
        // unset($info['qq_title']);
        // unset($info['qq_desc']);
        // unset($info['qq_url']);
        // unset($info['payment_des']);

        // $info_pri = getConfigPri();

        // $list = getLiveClass();
        // $videoclasslist = getVideoClass();
        // $level= getLevelList();

        // foreach($level as $k=>$v){
        //     unset($v['level_up']);
        //     unset($v['addtime']);
        //     unset($v['id']);
        //     unset($v['levelname']);
        //     $level[$k]=$v;
        // }

        // $levelanchor= getLevelAnchorList();

        // foreach($levelanchor as $k=>$v){
        //     unset($v['level_up']);
        //     unset($v['addtime']);
        //     unset($v['id']);
        //     unset($v['levelname']);
        //     $levelanchor[$k]=$v;
        // }
        // $info['chatserver'] = $ConfigPri['chatserver'];
        // $info['liveclass']=$list;

        // $info['videoclass']=$videoclasslist;

        // $info['level']=$level;

        // $info['levelanchor']=$levelanchor;

        // $info['tximgfolder']='';//腾讯云图片存储目录
        // $info['txvideofolder']='';//腾讯云视频存储目录
        // $info['txcloud_appid']='';//腾讯云视频APPID
        // $info['txcloud_region']='';//腾讯云视频地区
        // $info['txcloud_bucket']='';//腾讯云视频存储桶
        // $info['cloudtype']='1';//视频云存储类型

        // $info['qiniu_domain']=DI()->config->get('app.Qiniu.space_host').'/';//七牛云存储空间地址
        // $info['qiniu_uphost']=DI()->config->get('app.Qiniu.uphost');//七牛上传域名（小程序使用）
        // $info['qiniu_region']=DI()->config->get('app.Qiniu.region');//七牛上存储区域（小程序使用）
        // $info['video_audit_switch']=$info_pri['video_audit_switch']; //视频审核是否开启

        // /* 私信开关 */
        // $info['letter_switch']=$info_pri['letter_switch']; //视频审核是否开启

        // /* 引导页 */
        // $domain = new Domain_Guide();
        // $guide_info = $domain->getGuide();

        // $info['guide']=$guide_info;

        // /** 敏感词集合*/
        // $dirtyarr=array();
        // if($info_pri['sensitive_words']){
        //     $dirtyarr=explode(',',$info_pri['sensitive_words']);
        // }
        // $info['sensitive_words']=$dirtyarr;
        // //视频水印图片
        // $info['video_watermark']=get_upload_path($info_pri['video_watermark']); //视频审核是否开启

        // $info['shopexplain_url']=$info['site']."/portal/page/index?id=38";
        // $info['stricker_url']=$info['site']."/portal/page/index?id=39";

        // $info['shop_system_name']=$info_pri['shop_system_name']; //系统店铺名称

        // $info['login_private_url']=get_upload_path($info['login_private_url']);
        // $info['login_service_url']=get_upload_path($info['login_service_url']);

        // $info['socket_url']=$info_pri['chatserver']; //socket url地址（小程序用）

        // $info['share_img'] = DI()->config->get('app.share_img');

        // $info['slide_app'] = $level = DI()->notorm->slide_item
        //     ->select("title,image,url,target,description,content")
        //     ->where('status = 1 and slide_id = 2')
        //     ->order("list_order desc")
        //     ->fetchAll();

        // //APP设置
        // $app_set = DI()->notorm->option
        //     ->select("id,option_value")
        //     ->where('id = 10')
        //     ->fetchOne();
        // $info['app_set'] = json_decode($app_set['option_value'], true);
        


        setcaches($key,$info);
        $rs['info'][0] = $info;

        return $rs;
    }
    
    
    
    

    /**
     * 登录方式开关信息
     * @desc 用于获取登录方式开关信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0].login_type 开启的登录方式
     * @return string info[0].login_type[][0] 登录方式标识

     * @return string msg 提示信息
     */
    public function getLogin() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $info = getConfigPub();

        //登录弹框那个地方
        $login_alert=array(
            'title'=>$info['login_alert_title'],
            'content'=>$info['login_alert_content'],
            'login_title'=>$info['login_clause_title'],
            'message'=>array(
                array(
                    'title'=>$info['login_service_title'],
                    'url'=>get_upload_path($info['login_service_url']),
                ),
                array(
                    'title'=>$info['login_private_title'],
                    'url'=>get_upload_path($info['login_private_url']),
                ),
            )
        );

        $login_type=$info['login_type'];
        foreach ($login_type as $k => $v) {
            if($v=='ios'){
                unset($login_type[$k]);
                break;
            }
        }

        $login_type=array_values($login_type);

        $rs['info'][0]['login_alert'] = $login_alert;
        $rs['info'][0]['login_type'] = $login_type;
        $rs['info'][0]['login_type_ios'] = $info['login_type'];

        return $rs;
    }	
	
	
	
	
    /**
     * 获取热门主播
     * @desc 用于获取首页热门主播
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0]['slide'] 
     * @return string info[0]['slide'][].slide_pic 图片
     * @return string info[0]['slide'][].slide_url 链接
     * @return array info[0]['list'] 热门直播列表
     * @return string info[0]['list'][].uid 主播id
     * @return string info[0]['list'][].avatar 主播头像
     * @return string info[0]['list'][].avatar_thumb 头像缩略图
     * @return string info[0]['list'][].user_nicename 直播昵称
     * @return string info[0]['list'][].title 直播标题
     * @return string info[0]['list'][].city 主播位置
     * @return string info[0]['list'][].stream 流名
     * @return string info[0]['list'][].pull 播流地址
     * @return string info[0]['list'][].nums 人数
     * @return string info[0]['list'][].thumb 直播封面
     * @return string info[0]['list'][].level_anchor 主播等级
     * @return string info[0]['list'][].type 直播类型
     * @return string info[0]['list'][].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getHot() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Home();
		$key1='getSlide';
		$slide=getcaches($key1);
	
		if(!$slide){
			$where="status='1' and slide_id='2' ";
			$slide = $domain->getSlide($where);
			setcaches($key1,$slide,120);
		}
		
		
		//获取热门主播
		$key2="getHot_".$this->p;
		$list=getcaches($key2);
		
		if(!$list){
			$list = $domain->getHot($this->p);
			setCaches($key2,$list,120);
		}
			
		/*获取推荐主播*/
		$key3="getRecommendLive_1";
		$recommend_list=getcaches($key3);
		if(!$recommend_list){
			$recommend_list = $domain->getRecommendLive(1);
			setCaches($key3,$recommend_list,120);
		}

        $rs['info'][0]['slide'] = $slide;
        $rs['info'][0]['list'] = $list;
        $rs['info'][0]['recommend'] = $recommend_list;

        return $rs;
    }
	
	
	/**
     * 获取推荐主播
     * @desc 用于获取首页推荐主播
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0]['slide'] 
     * @return string info[0]['slide'][].slide_pic 图片
     * @return string info[0]['slide'][].slide_url 链接
     * @return array info[0]['list'] 热门直播列表
     * @return string info[0]['list'][].uid 主播id
     * @return string info[0]['list'][].avatar 主播头像
     * @return string info[0]['list'][].avatar_thumb 头像缩略图
     * @return string info[0]['list'][].user_nicename 直播昵称
     * @return string info[0]['list'][].title 直播标题
     * @return string info[0]['list'][].city 主播位置
     * @return string info[0]['list'][].stream 流名
     * @return string info[0]['list'][].pull 播流地址
     * @return string info[0]['list'][].nums 人数
     * @return string info[0]['list'][].thumb 直播封面
     * @return string info[0]['list'][].level_anchor 主播等级
     * @return string info[0]['list'][].type 直播类型
     * @return string info[0]['list'][].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getRecommendLive() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Home();
		$key2="getRecommendLive_".$this->p;
		$list=getcaches($key2);
		if(!$list){
			$list = $domain->getRecommendLive($this->p);
			setCaches($key2,$list,2); 
		}
        $rs['info']= $list;

        return $rs;
    }
	
	
    /**
     * 获取关注主播列表
     * @desc 用于获取用户关注的主播的直播列表
     * @return int code 操作码，0表示成功
     * @return string info[0]['title'] 提示标题
     * @return string info[0]['des'] 提示描述
     * @return array info[0]['list'] 直播列表
     * @return string info[0]['list'][].uid 主播id
     * @return string info[0]['list'][].avatar 主播头像
     * @return string info[0]['list'][].avatar_thumb 头像缩略图
     * @return string info[0]['list'][].user_nicename 直播昵称
     * @return string info[0]['list'][].title 直播标题
     * @return string info[0]['list'][].city 主播位置
     * @return string info[0]['list'][].stream 流名
     * @return string info[0]['list'][].pull 播流地址
     * @return string info[0]['list'][].nums 人数
     * @return string info[0]['list'][].thumb 直播封面
     * @return string info[0]['list'][].level_anchor 主播等级
     * @return string info[0]['list'][].type 直播类型
     * @return string info[0]['list'][].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getFollow() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Home();
        $info = $domain->getFollow($this->uid,$this->p);


        $rs['info'][0] = $info;

        return $rs;
    }

    /**
     * 获取最新主播
     * @desc 用于获取首页最新开播的主播列表
     * @return int code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].uid 主播id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 直播昵称
     * @return string info[].title 直播标题
     * @return string info[].city 主播位置
     * @return string info[].stream 流名
     * @return string info[].pull 播流地址
     * @return string info[].nums 人数
     * @return string info[].distance 距离
     * @return string info[].thumb 直播封面
     * @return string info[].level_anchor 主播等级
     * @return string info[].type 直播类型
     * @return string info[].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getNew() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$p=checkNull($this->p);
		if(!$p){
			$p=1;
		}
		$key='getNewls_'.$p;
		$info=getcaches($key);
		
		if(!$info){
			$domain = new Domain_Home();
			$info = $domain->getNew($p);

			setCaches($key,$info,6);
		}
		
        $rs['info'] = $info;

        return $rs;
    }		
		
	/**
     * 搜索
     * @desc 用于首页搜索会员
     * @return int code 操作码，0表示成功
     * @return array info 会员列表
     * @return string info[].id 用户ID
     * @return string info[].user_nicename 用户昵称
     * @return string info[].avatar 头像
     * @return string info[].sex 性别
     * @return string info[].signature 签名
     * @return string info[].level 等级
     * @return string info[].isattention 是否关注，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function search() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$key=checkNull($this->key);
		$p=checkNull($this->p);
		if($key==''){
			$rs['code'] = 1001;
			$rs['msg'] = "请填写关键词";
			return $rs;
		}
		
		if(!$p){
			$p=1;
		}
		
        $domain = new Domain_Home();
        $info = $domain->search($uid,$key,$p);

        $rs['info'] = $info;

        return $rs;
    }	
	
    /**
     * 获取附近主播
     * @desc 用于获取附近开播的主播列表
     * @return int code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].uid 主播id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 直播昵称
     * @return string info[].title 直播标题
     * @return string info[].city 主播位置
     * @return string info[].stream 流名
     * @return string info[].pull 播流地址
     * @return string info[].nums 人数
     * @return string info[].distance 距离
     * @return string info[].thumb 直播封面
     * @return string info[].level_anchor 主播等级
     * @return string info[].type 直播类型
     * @return string info[].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getNearby() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$lng=checkNull($this->lng);
		$lat=checkNull($this->lat);
		$p=checkNull($this->p);
		
		if($lng==''){
			return $rs;
		}
		
		if($lat==''){
			return $rs;
		}
		
		if(!$p){
			$p=1;
		}
		
		$key='getNearby_'.$lng.'_'.$lat.'_'.$p;
		$info=getcaches($key);
		if(!$info){
			$domain = new Domain_Home();
			$info = $domain->getNearby($lng,$lat,$p);

			setcaches($key,$info,2);
		}
		
        $rs['info'] = $info;

        return $rs;
    }	
	
	/**
     * 推荐主播
     * @desc 用于显示推荐主播
     * @return int code 操作码，0表示成功
     * @return array info 会员列表
     * @return string info[].id 用户ID
     * @return string info[].user_nicename 用户昵称
     * @return string info[].avatar 头像
     * @return string info[].fans 粉丝数
     * @return string info[].isattention 是否关注，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getRecommend() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		
		$key='getRecommend';
		$info=getcaches($key);
		if(!$info){
			$domain = new Domain_Home();
			$info = $domain->getRecommend();

			setcaches($key,$info,60*10);
		}
		
		foreach($info as $k=>$v){
			$info[$k]['isattention']=(string)isAttention($uid,$v['id']);
		}

        $rs['info'] = $info;

        return $rs;
    }	
	
	/**
     * 关注推荐主播
     * @desc 用于关注推荐主播
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string msg 提示信息
     */
    public function attentRecommend() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);

        if($uid<1){
            $rs['code'] = 1001;
			$rs['msg'] = "参数错误";
			return $rs;
        }
        if($touid==''){
            $rs['code'] = 1001;
			$rs['msg'] = "请选择要关注的主播";
			return $rs;
        }

		$domain = new Domain_Home();
		$info = $domain->attentRecommend($uid,$touid);

        //$rs['info'] = $info;

        return $rs;
    }

    /**
     * 收益榜单
     * @desc 获取收益榜单
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息 
     * @return array info
     * @return string info[0]['user_nicename'] 主播昵称
     * @return string info[0]['avatar_thumb'] 主播头像
     * @return string info[0]['totalcoin'] 主播钻石数
     * @return string info[0]['uid'] 主播id
     * @return string info[0]['levelAnchor'] 主播等级
     * @return string info[0]['isAttention'] 是否关注主播 0 否 1 是
     **/
    
    public function profitList(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $p=checkNull($this->p);
        $type=checkNull($this->type);
        $domain=new Domain_Home();
        $res=$domain->profitList($uid,$type,$p);

        $rs['info']=$res;
        return $rs;
    }

    /**
     * 消费榜单
     * @desc 获取消费榜单
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息 
     * @return array info
     * @return string info[0]['user_nicename'] 用户昵称
     * @return string info[0]['avatar_thumb'] 用户头像
     * @return string info[0]['totalcoin'] 用户钻石数
     * @return string info[0]['uid'] 用户id
     * @return string info[0]['levelAnchor'] 用户等级
     * @return string info[0]['isAttention'] 是否关注用户 0 否 1 是
     **/
    
    public function consumeList(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $p=checkNull($this->p);
        $type=checkNull($this->type);
        $domain=new Domain_Home();
        $res=$domain->consumeList($uid,$type,$p);

        $rs['info']=$res;
        return $rs;
    }
    

    /**
     * 获取分类下的直播
     * @desc 获取分类下的直播
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息 
     * @return array info
     * @return string info[].uid 主播id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 直播昵称
     * @return string info[].title 直播标题
     * @return string info[].city 主播位置
     * @return string info[].stream 流名
     * @return string info[].pull 播流地址
     * @return string info[].nums 人数
     * @return string info[].distance 距离
     * @return string info[].thumb 直播封面
     * @return string info[].level_anchor 主播等级
     * @return string info[].type 直播类型
     * @return string info[].goodnum 靓号
     **/
    
    public function getClassLive(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $liveclassid=checkNull($this->liveclassid);
        $p=checkNull($this->p);
        
        if(!$liveclassid){
            return $rs;
        }
        $domain=new Domain_Home();
        $res=$domain->getClassLive($liveclassid,$p);

        $rs['info']=$res;
        return $rs;
    }

    /**
     * 获取过滤词汇
     * @desc 用于获取聊天过滤词
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0] 配置信息

     * @return string msg 提示信息
     */
    public function getFilterField() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $sensitive_words=getcaches('sensitive_words');

        if($sensitive_words){

            $rs['info']=$sensitive_words;

        }else{

            $configpri = getConfigPri();

            if($configpri['sensitive_words']){
                $rs['info'] =explode(',',$configpri['sensitive_words']);
            }

            setcaches("sensitive_words",$rs['info']);
        }

        return $rs;
    }
	
	
    /**
     * 获取商城信息
     * @desc 用于获取商城模块-轮播图-二级商品分类-商品列表
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0] 配置信息
	 * @return array info[0][slide] 轮播图
	 * @return array info[0][shoptwoclass] 商品二级分类
	 * @return array info[0][list] 商品列表
     * @return string msg 提示信息
     */
    public function getShopList() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $p=checkNull($this->p);
      
		$domain=new Domain_Home();
		
		
        $key1='getShopSlide';
		$slide=getcaches($key1);
		if(!$slide){
			$where="status='1' and slide_id='5' ";
			$slide = $domain->getSlide($where);
			
			setcaches($key1,$slide);
		}

		$key2="getShopList_".$p;
		$list=getcaches($key2);
		if(!$list){
			$list = $domain->getShopList($p);
			setCaches($key2,$list,2); 
		}

        $rs['info'][0]['slide'] = $slide;
        $rs['info'][0]['shoptwoclass'] = getShopTwoClass(); 
        $rs['info'][0]['list'] = $list;
		
        return $rs;
    }
	
	 /**
     * 获取三级分类
     * @desc 获取三级分类下的商品
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息 
     * @return array info
     **/
    
    public function getShopThreeClass(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $shopclassid=checkNull($this->shopclassid);  //商品二级分类ID
        if(!$shopclassid){
            return $rs;
        }
        $rs['info']=getShopThreeClass($shopclassid);
       
        return $rs;
    }
	
	 /**
     * 获取分类下的商品
     * @desc 获取分类下的商品
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息 
     * @return array info
     **/
    
    public function getShopClassList(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $shopclassid=checkNull($this->shopclassid);  //商品三级分类ID
        $sell=checkNull($this->sell);  //销量
        $price=checkNull($this->price);  //价格
		$isnew=checkNull($this->isnew);
        $p=checkNull($this->p);
		
		
        
        if(!$shopclassid){
            return $rs;
        }
        $domain=new Domain_Home();
        $list=$domain->getShopClassList($shopclassid,$sell,$price,$isnew,$p);


        $rs['info']=$list;
        return $rs;
    }
	
	
	/**
     * 搜索商品
     * @desc 用于首页搜索商品昵称
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function searchShop() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$key=checkNull($this->key);
		$sell=checkNull($this->sell);  //销量
        $price=checkNull($this->price);  //价格
		$isnew=checkNull($this->isnew);
		$p=checkNull($this->p);
		if($key==''){

			return $rs;
		}
		
		if(!$p){
			$p=1;
		}
		
        $domain = new Domain_Home();
        $info = $domain->searchShop($key,$sell,$price,$isnew,$p);

        $rs['info'] = $info;

        return $rs;
    }

    /**
     * 获取活动
     * @desc 用于我的活动中心页面
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return array info[title] 标题
     * @return array info[cate] 分类，1-直播，2-游戏
     * @return array info[img] 封面
     * @return array info[url] 链接
     * @return array info[start] 活动开始时间
     * * @return array info[end] 活动结束时间为空，表示永久活动。
     * @return string msg 提示信息
     */
    public function getActive()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $cate = $this->cate;

        $info = DI()->notorm->active
        
            ->where('cate',$cate)
            ->select('title,cate,img,url,FROM_UNIXTIME(start) start,FROM_UNIXTIME(end) end')
            ->fetchAll();
        
        
        if ($info){
            foreach ($info as &$v)
            {
                $v['img'] = get_upload_path($v['img']);
            }
            $rs['info'] = $info;
            $rs['msg'] = '获取成功';
        }else{
            $rs['code'] = 1001;
            $rs['msg'] = '暂无活动';
        }
        return $rs;
    }

} 
