<?php
/**
 * 请在下面放置任何您需要的应用配置
 */

return array(
    
    //分享图片地址
    // 'share_img'              => 'https://www.qingwazb.com/invitation/share_code.png',

    //APP下载地址
    // 'app_download_url'              => 'https://www.qingwazb.com/download/index.html?invite=',

    //分享图片地址
//    'share_img'              => 'https://www.qingwazb.com/invitation/share_code.png',

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),

    'verify_token' => ['Ticket','Daili','Pay','Gaming'],
    'verify_token' => [],
    'REDIS_HOST' => "r-j6crcpxniav4upemlfpd.redis.rds.aliyuncs.com",
    'REDIS_AUTH' => "te8@123456",

    'REDIS_PORT' => "6379",
    
    'sign_key' => '76576076c1f5f657b634e966c8836a06',

	'daili_invite' => 'https://www.qingwazb.com/daili/register.html?invite=',
		
	'uptype'=>2,//上传方式：1表示---七牛，2表示---本地

    /**
     * 七牛相关配置
     */
    'Qiniu' =>  array(
        //ak
        'accessKey' => 'UP6M1s9WAo3ZwVHJXEVyfubk_ZcIQ1ir6iYMyTID',
        //sk
        'secretKey' => 'GqvyRWd1gP7vBLP1ZgkyO50oMc-3--qJ0mz2jG9A',
        //存储空间
        'space_bucket' => 'yunbao7749',
        //cdn加速域名 格式：http(s)://a.com，结尾不带/
        'space_host' => 'te8',
        //区域上传域名(服务端)  参考文档：https://developer.qiniu.com/kodo/manual/1671/region-endpoint
        'uphost' => 'cdn.meppag.cn', 
        //七牛云存储区域 华东：z0，华北：z1，华南：z2，北美：na0，东南亚：as0，参考文档：https://developer.qiniu.com/kodo/manual/1671/
        'region'=>'z2',
    ),


		
		 /**
     * 本地上传
     */
    'UCloudEngine' => 'local',

    /**
     * 本地存储相关配置（UCloudEngine为local时的配置）
     */
    'UCloud' => array(
        //对应的文件路径  站点域名
        'host' => 'https://yngjly.cn/upload/'
    ),
		

    /**
     * 账变类型
     * 11-赠送礼物，12-弹幕，13-登录奖励，14-购买VIP，15-购买坐骑，16-发送红包，17-抢红包，18-开通守护，19-转盘游戏，20-转盘中奖，21-游戏下注
     */
        'change_type' => array(
            1 => '充值',
            2 => '提现',
            3 => '彩票',
            4 => '补单',
            5 => '会员管理转账',
            6 => '优惠赠送',
            7 => '返水',
            8 => '额度转换',
            9 => '登录奖励',
            10 => '每日任务',
            11 => '赠送礼物',
            12 => '弹幕',
            13 => '购买VIP',
            14 => '购买坐骑',
            15 => '发送红包',
            16 => '抢红包',
            17 => '开通守护',
            18 => '转盘游戏',
            19 => '转盘中奖',
            20 => '游戏下注',
            21 => '直播反水',
            22 => '邀请奖励',
            23 => '游戏存取',
            24 => '提现服务费',
            25 => '彩票下注撤销',
        ),
    //返点平台
    'rate_plat'=>[
        ['platform'=>"1",'remark'=>'彩票'],
        ['platform'=>"2",'remark'=>'直播'],
        ['platform'=>"0016",'remark'=>'开元棋牌'],
        ['platform'=>"0004",'remark'=>'AG游戏'],
        ['platform'=>"0027",'remark'=>'OG游戏'],
        ['platform'=>"0022",'remark'=>'德胜棋牌'],
        ['platform'=>"0002",'remark'=>'PT游戏'],
        ['platform'=>"0024",'remark'=>'速博体育'],
        ['platform'=>"0035",'remark'=>'泛亚电竞'],
    ]
);
