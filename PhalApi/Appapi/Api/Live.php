<?php
/**
 * 直播间
 */
class Api_Live extends PhalApi_Api {

	public function getRules() {
		return array(
			'getSDK' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
			),
            'giftRank' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '主播id'),
                'type' => array('name' => 'type', 'require' => true, 'type' => 'string', 'desc' => 'day = 天, week = 周, month = 月, all = 总'),
            ),
			'createRoom' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'short_name' => array('name' => 'short_name', 'type' => 'string', 'require' => true, 'default'=>'', 'desc' => '彩种标识'),
				'reward_amount' => array('name' => 'reward_amount', 'type' => 'float', 'require' => true, 'default'=>'', 'desc' => '查看主播名片打赏金额'),
				'title' => array('name' => 'title', 'type' => 'string','default'=>'', 'desc' => '直播标题 url编码'),
                'thumb' => array('name' => 'thumb', 'type' => 'string','default'=>'', 'desc' => '封面图地址'),
				'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '直播类型，0是一般直播，1是私密直播，2是收费直播，3是计时直播'),
				'type_val' => array('name' => 'type_val', 'type' => 'string', 'default'=>'', 'desc' => '类型值'),
				'anyway' => array('name' => 'anyway', 'type' => 'int', 'default'=>'0', 'desc' => '直播类型 1 PC, 0 app'),
				'liveclassid' => array('name' => 'liveclassid', 'type' => 'int', 'default'=>'0', 'desc' => '直播分类ID'),
                'deviceinfo' => array('name' => 'deviceinfo', 'type' => 'string', 'default'=>'', 'desc' => '设备信息'),
                'language' => array('name' => 'language', 'type' => 'string', 'default'=>'zh', 'desc' => '主播接收语言 中文=zh; 英语=en;泰语=th;日语=jp;韩语=kor;越南语= vie'),
                
			),
			'changeLive' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'status' => array('name' => 'status', 'type' => 'int', 'require' => true, 'desc' => '直播状态 0关闭 1直播'),
			),
			'changeLiveType' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '直播类型，0是一般直播，1是私密直播，2是收费直播，3是计时直播'),
				'type_val' => array('name' => 'type_val', 'type' => 'string', 'default'=>'', 'desc' => '类型值'),
			),
			'stopRoom' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '类型'),
				'source' => array('name' => 'source', 'type' => 'string', 'desc' => '访问来源 socekt:断联socket，app传值空'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '当前时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
			'newStopRoom' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '类型'),
				'source' => array('name' => 'source', 'type' => 'string', 'desc' => '访问来源 socekt:断联socket，app传值空'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '当前时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
			
			'stopInfo' => array(
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
			),
			
			'checkLive' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
			),
			
			'roomCharge' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
			),
			'timeCharge' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
			),
			
			'enterRoom' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),

			),
			
			'showVideo' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '上麦会员ID'),
                'pull_url' => array('name' => 'pull_url', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '连麦用户播流地址'),
            ),
			
			'getZombie' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '流名'),
            ),

			'getUserLists' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getPop' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getGiftList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'sendGift' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'giftid' => array('name' => 'giftid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物ID'),
				'giftcount' => array('name' => 'giftcount', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物数量'),
				'ispack' => array('name' => 'ispack', 'type' => 'int', 'default'=>'0', 'desc' => '是否背包'),
				'is_sticker' => array('name' => 'is_sticker', 'type' => 'int', 'default'=>'0', 'desc' => '是否为贴纸礼物：0：否；1：是'),
			),
			'zombieSendGift' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'giftid' => array('name' => 'giftid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物ID'),
				'giftcount' => array('name' => 'giftcount', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物数量'),

			),
			
			'sendBarrage' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'),
				'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '弹幕内容'),
			),
			
			'setAdmin' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getAdminList' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
			),
			
			'setReport' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '举报内容'),
			),
			
			'getVotes' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
			),
			
			'setShutUp' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'),
                'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '禁言用户ID'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
                'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '禁言类型,0永久，1本场'),
                'stream' => array('name' => 'stream', 'type' => 'string', 'default'=>'0', 'desc' => '流名，0永久'),
            ),
			
			'kicking' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'superStopRoom' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'desc' => '会员token'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'type' => array('name' => 'type', 'type' => 'int','default'=>0, 'desc' => '关播类型 0表示关闭当前直播 1表示禁播，2表示封禁账号'),
            ),
			'searchMusic' => array(
				'key' => array('name' => 'key', 'type' => 'string','require' => true,'desc' => '关键词'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
            ),
			
			'getDownurl' => array(
				'audio_id' => array('name' => 'audio_id', 'type' => 'int','require' => true,'desc' => '歌曲ID'),
            ),
			
			'getCoin' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'),
            ),
            
            'checkLiveing' => array(
				'uid' => array('name' => 'uid', 'type' => 'int','desc' => '会员ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'),
                'stream' => array('name' => 'stream', 'type' => 'string','desc' => '流名'),
            ),

            'getLiveInfo' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
            ),

            'signOutWatchLive'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'desc' => '会员token'),
            ),
			'shareLiveRoom'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'desc' => '会员token'),
            ),
            'getLiverPhone'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'token' => array('name' => 'token', 'require' => true, 'min' => 1, 'desc' => '会员token'),
                'live_id' => array('name' => 'live_id', 'require' => true, 'min' => 1, 'desc' => '主播id'),
            ),
            'anchorData'=>array(
                'liverid' => array('name' => 'liverid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播id'),
            ),
            'giftRankNew' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '主播id'),
                'type' => array('name' => 'type', 'require' => true, 'type' => 'string', 'desc' => 'day = 天, week = 周, month = 月, all = 总'),
            ),
		);
	}

	/**
     * NEW主播收支情况
     * @desc NEW主播收支情况
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['attention_num'] 主播被订阅数量
     * @return array info['gift_num'] 主播收礼总额
     * @return array info['cash_num'] 主播提现总额
     * @return string msg 提示信息
     */
	public function anchorData()
	{
		$liverid = $this->liverid;
		$liver = DI()->notorm->live->where('uid = ?', $liverid)->fetchOne();
		if(!$liver) return ['code' => 1, 'msg' => '该用户不是主播'];

		$info['attention_num'] = DI()->notorm->user_coinrecord->where("action = 6 and touid = ?",$liverid)->sum('totalcoin');
		if(!$info['attention_num']) $info['attention_num'] = 0;
		$info['gift_num'] = DI()->notorm->user_coinrecord->where("action = 1 and touid = ?",$liverid)->sum('totalcoin');
		if(!$info['gift_num']) $info['gift_num'] = 0;
		$info['cash_num'] = DI()->notorm->cash_record->where('uid = ? and status = ?', $liverid, 1)->sum('money');
		if(!$info['cash_num']) $info['cash_num'] = 0;

		return ['code' => 0, 'msg' => 'ok', 'info' => $info];
	}

	/**
     * NEW_new获取直播间排行
     * @desc NEW_new获取直播间排行
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[list_day] 日榜
     * @return array info[list_day_total] 日榜总
     * @return array info[list_week] 周榜
     * @return array info[list_week_total] 周榜总
     * @return array info[list_month] 月榜
     * @return array info[list_month_total] 月榜总
     * @return array info[list_all] 总榜
     * @return array info[list_all_total] 总榜总计
     * @return string msg 提示信息
     */
	public function giftRankNew()
	{
		$uid = $this->uid;
        $type=checkNull($this->type);

        $nowtime=time();

        $p=1;
        $page_nums=20;
        $start=($p-1)*$page_nums;

        switch ($type){
            case 'day':
                /* 日榜 */
                //当天0点
		        $today=date("Ymd",$nowtime);
		        $starttime=strtotime($today);
		        //当天 23:59:59
		        $endtime=strtotime("{$today} + 1 day");
		        $sql = "SELECT user_id as uid,(sum(change_money) * -1) total,cu.user_nicename,cu.avatar FROM cmf_user_change cv join cmf_user cu on cv.user_id = cu.id WHERE  cv.change_type in (11,12) and cv.touid = :uid and addtime >= :starttime and addtime <= :endtime GROUP BY uid ORDER BY cv.change_money asc limit :pagenums";
        		$params = array(':uid' => $uid, ':pagenums' => $page_nums, ':starttime' => $starttime, ':endtime' => $endtime);

        		$list = DI()->notorm->user_change->queryAll($sql, $params);
    
        		$list_total=DI()->notorm->user_change
                    ->where(" change_type in (11,12) and touid='{$uid}' and addtime >={$starttime} and addtime < {$endtime} ")
                    ->sum('change_money');
                if(!$list_total){
                	$list_total = 0;
                }else{
                	$list_total = $list_total * -1;
                }    
				$info['list_day_total'] = $list_total;
                break;
            case 'week':
                /* 周榜 */
				$w=date('w',$nowtime);
		        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
		        $first=1;
		        //周一
		        $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'));
		        $starttime=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days');

		        //本周结束日期
		        //周天
		        $endtime=strtotime("{$week} +1 week");
		        $sql = "SELECT user_id as uid,(sum(change_money) * -1) total,cu.user_nicename,cu.avatar FROM cmf_user_change cv join cmf_user cu on cv.user_id = cu.id WHERE  cv.change_type in (11,12) and cv.touid = :uid and addtime >= :starttime and addtime <= :endtime GROUP BY uid ORDER BY cv.change_money asc limit :pagenums";
        		$params = array(':uid' => $uid, ':pagenums' => $page_nums, ':starttime' => $starttime, ':endtime' => $endtime);

        		$list = DI()->notorm->user_change->queryAll($sql, $params);
        		
        		$list_total=DI()->notorm->user_change
                    ->where(" change_type in (11,12) and touid='{$uid}' and addtime >={$starttime} and addtime < {$endtime} ")
                    ->sum('change_money');
                if(!$list_total){
                	$list_total = 0;
                }else{
                	$list_total = $list_total * -1;
                }
				$info['list_week_total'] = $list_total;
                break;
            case 'month':
                /* 月榜 */
                //本月第一天
		        $month=date("Y-m",$nowtime).'-01';
		        $starttime=strtotime($month);

		        //本月最后一天
		        $endtime=strtotime("{$month} +1 month");
		        $sql = "SELECT user_id as uid,(sum(change_money) * -1) total,cu.user_nicename,cu.avatar FROM cmf_user_change cv join cmf_user cu on cv.user_id = cu.id WHERE  cv.change_type in (11,12) and cv.touid = :uid and addtime >= :starttime and addtime <= :endtime GROUP BY uid ORDER BY cv.change_money asc limit :pagenums";
        		$params = array(':uid' => $uid, ':pagenums' => $page_nums, ':starttime' => $starttime, ':endtime' => $endtime);

        		$list = DI()->notorm->user_change->queryAll($sql, $params);
        		
        		$list_total=DI()->notorm->user_change
                    ->where(" change_type in (11,12) and touid='{$uid}' and addtime >={$starttime} and addtime < {$endtime} ")
                    ->sum('change_money');
                if(!$list_total){
                	$list_total = 0;
                }else{
                	$list_total = $list_total * -1;
                }
				$info['list_month_total'] = $list_total;
                break;
            case 'all':
                /* 总榜 */
                $sql = "SELECT user_id as uid,(sum(change_money) * -1) total,cu.user_nicename,cu.avatar FROM cmf_user_change cv join cmf_user cu on cv.user_id = cu.id WHERE  cv.change_type in (11,12) and cv.touid = :uid GROUP BY uid ORDER BY cv.change_money asc limit :pagenums";
			    $params = array(':uid' => $uid, ':pagenums' => $page_nums);

			    $list = DI()->notorm->user_change->queryAll($sql, $params);
			    
			 
			    $list_total=DI()->notorm->user_change
                    ->where(" change_type in (11,12) and touid='{$uid}' ")
                    ->sum('change_money');
                if(!$list_total){
                	$list_total = 0;
                }else{
                	$list_total = $list_total * -1;
                }
                $info['list_all_total'] = $list_total;    
                break;
        }
        $info['list'] = $list;
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
	}

    /**
     * NEW获取直播间排行
     * @desc NEW获取直播间排行
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[list_day] 日榜
     * @return array info[list_day_total] 日榜总
     * @return array info[list_week] 周榜
     * @return array info[list_week_total] 周榜总
     * @return array info[list_month] 月榜
     * @return array info[list_month_total] 月榜总
     * @return array info[list_all] 总榜
     * @return array info[list_all_total] 总榜总计
     * @return string msg 提示信息
     */
    public function giftRank(){
        $uid = $this->uid;
        $type=checkNull($this->type);


        $nowtime=time();
        //当天0点
        $today=date("Ymd",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");

        $w=date('w',$nowtime);
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $first=1;
        //周一
        $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'));
        $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days');

        //本周结束日期
        //周天
        $week_end=strtotime("{$week} +1 week");


        //本月第一天
        $month=date("Y-m",$nowtime).'-01';
        $month_start=strtotime($month);

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");

        $p=1;
        $page_nums=20;
        $start=($p-1)*$page_nums;

        switch ($type){
            case 'day':
                /* 日榜 */
                $list_day=DI()->notorm->user_voterecord
                    ->select('fromid as uid,sum(total) as total')
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")
                    ->group('fromid')
                    ->order('total desc')
                    ->limit($page_nums)
                    ->fetchAll();

                foreach($list_day as $k=>$v){
                    $list_day[$k]['userinfo']=getUserInfo($v['uid']);
                }
                $info['list'] = $list_day;
                $list_day_total=DI()->notorm->user_voterecord
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")
                    ->sum('total');
                if(!$list_day_total){
                    $list_day_total=0;
                }
                $info['total'] = $list_day_total;
                break;
            case 'week':
                /* 周榜 */
//                $list_week=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
                $list_week=DI()->notorm->user_voterecord
                    ->select('fromid as uid,sum(total) as total')
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")
                    ->group('fromid')
                    ->order('total desc')
                    ->limit($page_nums)
                    ->fetchAll();
                foreach($list_week as $k=>$v){
                    $list_week[$k]['userinfo']=getUserInfo($v['uid']);
                }
                $info['list'] = $list_week;

                $list_week_total=DI()->notorm->user_voterecord
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")
                    ->sum('total');
                if(!$list_week_total){
                    $list_week_total=0;
                }
                $info['total'] = $list_week_total;
                break;
            case 'month':
                /* 月榜 */
                //        $list_week=Db::name('user_voterecord')->field("fromid as uid,sum(total) as total")->where(" action in (1,2) and uid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->group("fromid")->order("total desc")->limit($page_nums)->select()->toArray();
                $list_month=DI()->notorm->user_voterecord
                    ->select('fromid as uid,sum(total) as total')
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")
                    ->group('fromid')
                    ->order('total desc')
                    ->limit($page_nums)
                    ->fetchAll();
                foreach($list_month as $k=>$v){
                    $list_month[$k]['userinfo']=getUserInfo($v['uid']);
                }
                $info['list'] = $list_month;

                $list_month_total=DI()->notorm->user_voterecord
                    ->where(" action in (1,2) and uid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")
                    ->sum('total');
                if(!$list_month_total){
                    $list_month_total=0;
                }
                $info['total'] = $list_month_total;
                break;
            case 'all':
                /* 总榜 */
                $list_all=DI()->notorm->user_voterecord
                    ->select('fromid as uid,sum(total) as total')
                    ->where(" action in (1,2) and uid='{$uid}'")
                    ->group('fromid')
                    ->order('total desc')
                    ->limit($page_nums)
                    ->fetchAll();
                foreach($list_all as $k=>$v){
                    $list_all[$k]['userinfo']=getUserInfo($v['uid']);
                }
                $info['list'] = $list_all;

                $list_all_total=DI()->notorm->user_voterecord
                    ->where(" action in (1,2) and uid='{$uid}' ")
                    ->sum('total');
                if(!$list_all_total){
                    $list_all_total=0;
                }
                $info['total'] = $list_all_total;
                break;
        }

        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }

    /**
     * NEW获取主播联系方式
     * @desc 用于获取主播联系方式
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[z_speed] 总进度
     * @return array info[c_speed] 已完成进度
     * @return string msg 提示信息
     */
    public function getLiverPhone()
    {
        $uid = $this->uid;
        $live_id = $this->live_id;
        $token=checkNull($this->token);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $info = [];
        $live_info = DI()->notorm->live->where('uid = ?', $live_id)->fetchOne();
        if(!$live_info) return ['code' => 1, 'msg' => '该直播间不存在'];
        if($live_info['reward_amount'] <= 0) return ['code' => 1, 'msg' => '该主播还未设置联系方式获取任务'];
        $info['z_speed'] = $live_info['reward_amount'];

        $coin_sum = DI()->notorm->user_change
            ->where('change_type = 11 and user_id = ? and touid = ?', $uid, $live_id)
            ->sum('change_money');
        $coin_sum = -1 * $coin_sum;
        $info['c_speed'] = $coin_sum;

        $user_info = DI()->notorm->user->where('id = ?', $live_id)->select('bat,qq,wechat')->fetchOne();
        if(!$user_info) return ['code' => 1, 'msg' => '该主播不存在'];
        if($coin_sum < $live_info['phone_coin']){
            unset($user_info['bat']);
            unset($user_info['qq']);
            unset($user_info['wechat']);
        }
        $info['live'] = $user_info;
        return ['code' => 0, 'msg' => 'ok', 'info' => $info];
    }


    /**
	 * 获取SDK
	 * @desc 用于获取SDK类型
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].live_sdk SDK类型，0金山SDK 1腾讯SDK
	 * @return object info[0].android 安卓CDN配置
	 * @return object info[0].ios IOS CDN配置
	 * @return string info[0].isshop 是否有店铺，0否1是
	 * @return string msg 提示信息
	 */
	public function getSDK() { 
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $configpri=getConfigPri();
        
  
        //$info['live_sdk']=$configpri['live_sdk'];
		
        $cdnset=include API_ROOT.'/../data/config/cdnset.php';
        
        $cdnset['live_sdk']=1;
     
        /* 店铺信息 */
//		$isshop = checkShopIsPass($uid);
        
        $cdnset['isshop']='';
		$rs['info'][0]=$cdnset;


		return $rs;
	}	

     /**
	 * 创建开播
	 * @desc 用于用户开播生成记录
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].userlist_time 用户列表请求间隔
	 * @return string info[0].barrage_fee 弹幕价格
	 * @return string info[0].votestotal 主播映票
	 * @return string info[0].stream 流名
	 * @return string info[0].push 推流地址
	 * @return string info[0].pull 播流地址
	 * @return string info[0].chatserver socket地址
	 * @return array info[0].game_switch 游戏开关
	 * @return string info[0].game_switch[][0] 开启的游戏类型 
	 * @return string info[0].game_bankerid 庄家ID
	 * @return string info[0].game_banker_name 庄家昵称
	 * @return string info[0].game_banker_avatar 庄家头像 
	 * @return string info[0].game_banker_coin 庄家余额 
	 * @return string info[0].game_banker_limit 上庄限额 
	 * @return object info[0].liang 用户靓号信息
	 * @return string info[0].liang.name 号码，0表示无靓号
	 * @return object info[0].vip 用户VIP信息
	 * @return string info[0].vip.type VIP类型，0表示无VIP，1表示有VIP
	 * @return string info[0].guard_nums 守护数量
	 * @return string info[0].thumb 直播封面
	 * @return string info[0].show_name 彩种名字
	 * @return string info[0].short_name 彩种标识
	 * @return string info[0].c_type 彩种分类
	 * @return string info[0].c_id 彩种ID
	 * @return string info[0].hot 彩种推荐 1=推荐 2=不推荐
	 * @return string info[0].icon 彩种图标
	 * @return string info[0].reward_amount 查看主播名片打赏金额
	 * @return string msg 提示信息
	 */
	public function createRoom() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid = $this->uid;
		$token=checkNull($this->token);
		$reward_amount = checkNull($this->reward_amount);
		$thumb = checkNull($thumb->thumb);
		$configpub=getConfigPub();
		if($configpub['maintain_switch']==1){
			$rs['code']=1002;
			$rs['msg']=$configpub['maintain_tips'];
			return $rs;

		}
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$isban = isBan($uid);
		if(!$isban){
			$rs['code']=1001;
			$rs['msg']='该账号已被禁用';
			return $rs;
		}
		
        $domain = new Domain_Live();
		$result = $domain->checkBan($uid);
		if($result){
			$rs['code'] = 1015;
			$rs['msg'] = '已被禁播';
			return $rs;
		}

		$configpri=getConfigPri();
		if($configpri['auth_islimit']==1){
			$isauth=isAuth($uid);
			if(!$isauth){
				$rs['code']=1002;
				$rs['msg']='请先进行身份认证或等待审核';
				return $rs;
			}
		}
		$userinfo=getUserInfo($uid);

		$fuinfo = DI()->notorm->family_user->where('uid = ? and state = ?', $uid, 2)->fetchOne();
		if(!$fuinfo) {
            $rs['code']=1;
            $rs['msg']='您还未加入任何家族或等待家族审核通过';
            return $rs;
        }
		
		if($configpri['level_islimit']==1){
			if( $userinfo['level'] < $configpri['level_limit'] ){
				$rs['code']=1003;
				$rs['msg']='等级小于'.$configpri['level_limit'].'级，不能直播';
				return $rs;
			}
		}
				
		$nowtime=time();
		
		$showid=$nowtime;
		$starttime=$nowtime;
		$title=checkNull($this->title);
		$type=checkNull($this->type);
		$type_val=checkNull($this->type_val);
		$anyway=checkNull($this->anyway);
		$liveclassid=checkNull($this->liveclassid);
		$deviceinfo=checkNull($this->deviceinfo);
		$language=checkNull($this->language);

        $short_name = checkNull($this->short_name);
		$sensitivewords=sensitiveField($title);
		if($sensitivewords==1001){
			$rs['code'] = 10011;
			$rs['msg'] = '输入非法，请重新输入';
			return $rs;
		}
			
		
		if( $type==1 && $type_val=='' ){
			$rs['code']=1002;
			$rs['msg']='密码不能为空';
			return $rs;
		}else if($type > 1 && $type_val<=0){
			$rs['code']=1002;
			$rs['msg']='价格不能小于等于0';
			return $rs;
		}
		
		
		$stream=$uid.'_'.$nowtime;
        $wy_cid='';
		$push=PrivateKeyA('rtmp',$stream,1);
		$pull=PrivateKeyA('rtmp',$stream,0);
	
      
		if(!$city){
			$city='好像在火星';
		}
		if(!$lng && $lng!=0){
			$lng='';
		}
		if(!$lat && $lat!=0){
			$lat='';
		}
		

		$thumb='';
		if($_FILES){
			if ($_FILES["file"]["error"] > 0) {
				$rs['code'] = 1003;
				$rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
				DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
				return $rs;
			}
			
			if(!checkExt($_FILES["file"]['name'])){
				$rs['code']=1004;
				$rs['msg']='图片仅能上传 jpg,png,jpeg';
				return $rs;
			}

			$uptype=DI()->config->get('app.uptype');
			if($uptype==1){
				//七牛
				$url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

				if (!empty($url)) {
					$thumb=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
				}
			}else if($uptype==2){
				//本地上传
				//设置上传路径 设置方法参考3.2
				DI()->ucloud->set('save_path','thumb/'.date("Ymd"));

				//新增修改文件名设置上传的文件名称
			   // DI()->ucloud->set('file_name', $this->uid);

				//上传表单名
				$res = DI()->ucloud->upfile($_FILES['file']);
				
				$files='../upload/'.$res['file'];
				$PhalApi_Image = new Image_Lite();
				//打开图片
				$PhalApi_Image->open($files);
				/**
				 * 可以支持其他类型的缩略图生成，设置包括下列常量或者对应的数字：
				 * IMAGE_THUMB_SCALING      //常量，标识缩略图等比例缩放类型
				 * IMAGE_THUMB_FILLED       //常量，标识缩略图缩放后填充类型
				 * IMAGE_THUMB_CENTER       //常量，标识缩略图居中裁剪类型
				 * IMAGE_THUMB_NORTHWEST    //常量，标识缩略图左上角裁剪类型
				 * IMAGE_THUMB_SOUTHEAST    //常量，标识缩略图右下角裁剪类型
				 * IMAGE_THUMB_FIXED        //常量，标识缩略图固定尺寸缩放类型
				 */
				$PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
				$PhalApi_Image->save($files);							
				
				$thumb=$res['url'];
			}
			
			@unlink($_FILES['file']['tmp_name']);			
		}
		
				
		/* 主播靓号 */
		$liang=getUserLiang($uid);
		$goodnum=0;
		if($liang['name']!=0){
			$goodnum=$liang['name'];
		}
		$info['liang']=$liang;

		
		/* 主播VIP */
		$vip=getUserVip($uid);
		$info['vip']=$vip;
		
        $cp = getCaizhong($short_name);
        
		if(!$cp)
		{
		    $rs['code']=1003;
			$rs['msg']='彩票不能为空';
			return $rs;
		}
		
		    
		$dataroom=array(
			"uid"=>$uid,
			"showid"=>$showid,
			"starttime"=>$starttime,
			"title"=>$title,
			"stream"=>$stream,
			"thumb"=>$thumb,
			"pull"=>$pull,
			"type"=>$type,
			"type_val"=>$type_val,
			"goodnum"=>$goodnum,
			"isvideo"=>0,
			"islive"=>0,
            "wy_cid"=>$wy_cid,
			"anyway"=>$anyway,
			"liveclassid"=>$liveclassid,
			"deviceinfo"=>$deviceinfo,
			"language"=>$language,
			"hotvotes"=>0,
			"pkuid"=>0,
			"pkstream"=>'',
			"banker_coin"=>10000000,
			'show_name' => $cp['show_name'],
			'short_name' => $short_name,
			'c_id' => $cp['id'],
			'c_type' => $cp['type'],
			'icon' => get_upload_path($cp['icon']),
			'hot' => $cp['hot'],
			'reward_amount' => $reward_amount,
		);	

		$domain = new Domain_Live();

		$result = $domain->createRoom($uid,$dataroom);
		if($result===false){
			$rs['code'] = 1011;
			$rs['msg'] = '开播失败，请重试';
			return $rs;
		}

		$domain2 = new Domain_User();
		$info2 = $domain2->userUpdate($uid,$data);


		$userinfo['usertype']=50;
		$userinfo['sign']='0';
        $userinfo['lang'] = $language;
		DI()->redis->set($token,json_encode($userinfo));

		$votestotal=$domain->getVotes($uid);

		$info['userlist_time']=$configpri['userlist_time'];
		$info['barrage_fee']=$configpri['barrage_fee'];
		$info['chatserver']=$configpri['chatserver'];

		$info['votestotal']=$votestotal;
		$info['stream']=$stream;
		$info['push']=$push;
		$info['pull']=$pull;
		$info["language"]=$language ? $language : 'zh';
        $info['show_name'] =$dataroom['show_name'];
        $info['short_name'] =$short_name;
        $info['c_type'] =$dataroom['c_type'];
        $info['c_id'] =$dataroom['c_id'];
        $info['hot'] =$dataroom['hot'];
        $info['icon'] =get_upload_path($dataroom['icon']);
	
		
		/* 游戏配置信息 */
        
        /* 守护数量 */
        $domain_guard = new Domain_Guard();
		$guard_nums = $domain_guard->getGuardNums($uid);
        $info['guard_nums']=$guard_nums;
        
        /* 腾讯APPID */
        // $info['tx_appid']=$configpri['tx_appid'];
        
        /* 奖池 */
        $info['jackpot_level']='-1';
		$jackpotset = getJackpotSet();
        if($jackpotset['switch']){
            $jackpotinfo = getJackpotInfo();
            $info['jackpot_level']=$jackpotinfo['level'];
        }
		/** 敏感词集合*/
		$dirtyarr=array();
		if($configpri['sensitive_words']){
            $dirtyarr=explode(',',$configpri['sensitive_words']);
        }
		$info['sensitive_words']=$dirtyarr;

		//返回直播封面
		if($thumb){
			$info['thumb']=get_upload_path($thumb);
		}else{
			$info['thumb']=$userinfo['avatar_thumb'];
		}
		$info['reward_amount'] = $reward_amount;
		
		$rs['info'][0] = $info;
        $rs['code'] = 0;
        
        /* 清除连麦PK信息 */
        DI()->redis  -> hset('LiveConnect',$uid,0);
        DI()->redis  -> hset('LivePK',$uid,0);
        DI()->redis  -> hset('LivePK_gift',$uid,0);

        /* 后台禁用后再启用，恢复发言 */
        DI()->redis -> hDel($uid . 'shutup',$uid);
		return $rs;
	}
	

	/**
	 * 修改直播状态
	 * @desc 用于主播修改直播状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 成功提示信息
	 * @return string msg 提示信息
	 */
	public function changeLive() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid = $this->uid;
		$token=checkNull($this->token);
		$stream=checkNull($this->stream);
		$status=$this->status;
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Live();
		$info=$domain->changeLive($uid,$stream,$status);
        
        $configpri=getConfigPri();
        /* 极光推送 */
		$app_key = $configpri['jpush_key'];
		$master_secret = $configpri['jpush_secret'];

		if($app_key && $master_secret && $status==1 && $info){
			require API_ROOT.'/../sdk/JPush/autoload.php';
			// 初始化
			$client = new \JPush\Client($app_key, $master_secret,null);
            
            $userinfo=getUserInfo($uid);
			
			$anthorinfo=array(
				"uid"=>$info['uid'],
				"avatar"=>$info['avatar'],
				"avatar_thumb"=>$info['avatar_thumb'],
				"user_nicename"=>$info['user_nicename'],
				"title"=>$info['title'],

				"stream"=>$info['stream'],
				"pull"=>$info['pull'],
				"thumb"=>$info['thumb'],
				"isvideo"=>'0',
				"type"=>$info['type'],
				"type_val"=>$info['type_val'],
				"game_action"=>'0',
				"goodnum"=>$info['goodnum'],
				"anyway"=>$info['anyway'],
				"nums"=>0,
				"level_anchor"=>$userinfo['level_anchor'],
                "game"=>'',
			);
			$title='你的好友：'.$anthorinfo['user_nicename'].'正在直播，邀请你一起';
			$apns_production=false;
			if($configpri['jpush_sandbox']){
				$apns_production=true;
			}
            
            $pushids = $domain->getFansIds($uid); 
			$nums=count($pushids);	
			for($i=0;$i<$nums;){
                $alias=array_slice($pushids,$i,900);
                $i+=900;
				try{	
					$result = $client->push()
							->setPlatform('all')
							->addRegistrationId($alias)
							->setNotificationAlert($title)
							->iosNotification($title, array(
								'sound' => 'sound.caf',
								'category' => 'jiguang',
								'extras' => array(
									'type' => '1',
									'userinfo' => $anthorinfo
								),
							))
							->androidNotification('', array(
								'extras' => array(
									'title' => $title,
									'type' => '1',
									'userinfo' => $anthorinfo
								),
							))
							->options(array(
								'sendno' => 100,
								'time_to_live' => 0,
								'apns_production' =>  $apns_production,
							))
							->send();
				} catch (Exception $e) {   
					file_put_contents('./jpush.txt',date('y-m-d h:i:s').'提交参数信息 设备名:'.json_encode($alias)."\r\n",FILE_APPEND);
					file_put_contents('./jpush.txt',date('y-m-d h:i:s').'提交参数信息:'.$e."\r\n",FILE_APPEND);
				}					
			}			
		}
		/* 极光推送 */

		$rs['info'][0]['msg']='成功';
		return $rs;
	}	

	/**
	 * 修改直播类型
	 * @desc 用于主播修改直播类型
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 成功提示信息
	 * @return string msg 提示信息
	 */
	public function changeLiveType() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid = $this->uid;
		$token=checkNull($this->token);
		$stream=checkNull($this->stream);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$type=checkNull($this->type);
		$type_val=checkNull($this->type_val);
		
		if( $type==1 && $type_val=='' ){
			$rs['code']=1002;
			$rs['msg']='密码不能为空';
			return $rs;
		}else if($type > 1 && $type_val<=0){
			$rs['code']=1002;
			$rs['msg']='价格不能小于等于0';
			return $rs;
		}
		
		
		$data=array(
			"type"=>$type,
			"type_val"=>$type_val,
		);
		
		$domain = new Domain_Live();
		$info=$domain->changeLiveType($uid,$stream,$data);

		$rs['info'][0]['msg']='成功';
		return $rs;
	}	
	
	
	/**
	 * 关闭直播
	 * @desc 用于用户结束直播
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 成功提示信息
	 * @return string msg 提示信息
	 */
	public function newStopRoom() { 
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
    
            file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 开始:'."\r\n",FILE_APPEND);
            file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 _REQUEST:'.json_encode($_REQUEST)."\r\n",FILE_APPEND);
        $uid = $this->uid;
        $token=checkNull($this->token);
        $stream=checkNull($this->stream);
        $type=checkNull($this->type);
        $source=checkNull($this->source);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
    
        if(!$source){ //非socket来源，app访问
    
          if(!$time){
            $rs['code'] = 1001;
            $rs['msg'] = '参数错误,请重试';
            return $rs;
          }
    
          $now=time();
          if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
          }
    
          if(!$sign){
            $rs['code']=1001;
            $rs['msg']="参数错误,请重试";
            return $rs;
          }
              
              $checkdata=array(
                  'uid'=>$uid,
                  'token'=>$token,
                  'time'=>$time,
                  'stream'=>$stream,
              );
              
              $issign=checkSign($checkdata,$sign);
              if(!$issign){
                  $rs['code']=1001;
                  $rs['msg']='签名错误';
                  return $rs; 
              }
        }
        
        $key='stopRoom_'.$stream;
        $isexist=getcaches($key);
            file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 isexist:'.json_encode($isexist)."\r\n",FILE_APPEND);
        //if(!$isexist && $type==1){
        if(!$isexist ){
    
          $domain = new Domain_Live();
    
          $checkToken=checkToken($uid,$token);
                file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 checkToken:'.json_encode($checkToken)."\r\n",FILE_APPEND);
                setcaches($key,'1',10);
    
          if($checkToken==700){
    
            $domain->stopRoom($uid,$stream);
    
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
          }
                
                $info=$domain->stopRoom($uid,$stream);
                
        }
        delcache($uid.':nums');
        delcache('live_record:'.$uid);
        $rs['info'][0]['msg']='关播成功';
            file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 结束:'."\r\n",FILE_APPEND);
    
        return $rs;
      }
	
	/**
	 * 关闭直播
	 * @desc 用于用户结束直播
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 成功提示信息
	 * @return string msg 提示信息
	 */
	public function stopRoom() { 
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 开始:'."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 _REQUEST:'.json_encode($_REQUEST)."\r\n",FILE_APPEND);
		$uid = $this->uid;
		$token=checkNull($this->token);
		$stream=checkNull($this->stream);
		$type=checkNull($this->type);
		$source=checkNull($this->source);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);

        $user = DI()->notorm->user
				->where('id=?',$uid)->fetchOne();
				

		if($user['issuper'] != 1)
		{
		    $rs['code'] = 1;
		    $rs['info'][0]['msg']='您权限不够！';
	    	return $rs;
		}
		
		$isdel = DI()->notorm->live
				->where('uid=?',$stream)
				->delete();
				
		if(!$isdel){
            $rs['code'] = 1;
		    $rs['info'][0]['msg']='关闭失败！';
	    	return $rs;
        }
            
        file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 isexist:'.json_encode($isexist)."\r\n",FILE_APPEND);
        
		delcache($uid.':nums');
		delcache('live_record:'.$uid);
		$rs['info'][0]['msg']='关播成功';
        file_put_contents(API_ROOT.'/Runtime/stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 结束:'."\r\n",FILE_APPEND);

		return $rs;
	}	
	
	/**
	 * 直播结束信息
	 * @desc 用于直播结束页面信息展示
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].nums 人数
	 * @return string info[0].length 时长
	 * @return string info[0].votes 映票数
	 * @return string msg 提示信息
	 */
	public function stopInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$stream=checkNull($this->stream);
		
		$domain = new Domain_Live();
		$info=$domain->stopInfo($stream);

		$rs['info'][0]=$info;
		return $rs;
	}		
	
	/**
	 * 检查直播
	 * @desc 用于用户进房间时检查直播
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].type 房间类型	
	 * @return string info[0].type_val 收费房间价格，默认0	
	 * @return string info[0].type_msg 提示信息
	 * @return string msg 提示信息
	 */
	public function checkLive() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
		
		$configpub=getConfigPub();
		if($configpub['maintain_switch']==1){
			$rs['code']=1002;
			$rs['msg']=$configpub['maintain_tips'];
			return $rs;

		}
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        
		$isban = isBan($uid);
		if(!$isban){
			$rs['code']=1001;
			$rs['msg']='该账号已被禁用';
			return $rs;
		}
        
        
        if($uid==$liveuid){
			$rs['code'] = 1011;
			$rs['msg'] = '不能进入自己的直播间';
			return $rs;
		}
		

		$domain = new Domain_Live();
		$info=$domain->checkLive($uid,$liveuid,$stream);
		
		if($info==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '直播已结束';
			return $rs;
		}else if($info==1007){
            $rs['code'] = 1007;
			$rs['msg'] = '超管不能进入1v1房间';
			return $rs;
        }else if($info==1008){
            $rs['code'] = 1004;
			$rs['msg'] = '您已被踢出房间';
			return $rs;
        }
        
        
        $configpri=getConfigPri();
        
        $info['live_sdk']=$configpri['live_sdk'];
        
		$rs['info'][0]=$info;
		
		
		return $rs;
	}
	
	/**
	 * 房间扣费
	 * @desc 用于房间扣费
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function roomCharge() { 
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
		
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Live();
		$info=$domain->roomCharge($uid,$liveuid,$stream);
		
		if($info==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '直播已结束';
			return $rs;
		}else if($info==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '该房间非扣费房间';
			return $rs;
		}else if($info==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '房间费用有误';
			return $rs;
		}else if($info==1008){
			$rs['code'] = 1008;
			$rs['msg'] = '余额不足';
			return $rs;
		}
		$rs['info'][0]['coin']=$info['coin'];
	
		return $rs;
	}	

	/**
	 * 房间计时扣费
	 * @desc 用于房间计时扣费
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function timeCharge() { 
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
		$domain = new Domain_Live();
		
		$key='timeCharge_'.$stream.'_'.$uid;
		$cache=getcaches($key);
		if($cache){
			$coin=$domain->getUserCoin($uid);
			$rs['info'][0]['coin']=$coin['coin'];
			return $rs;
		}
        
        
		
		$info=$domain->roomCharge($uid,$liveuid,$stream);
		
		if($info==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '直播已结束';
			return $rs;
		}else if($info==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '该房间非扣费房间';
			return $rs;
		}else if($info==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '房间费用有误';
			return $rs;
		}else if($info==1008){
			$rs['code'] = 1008;
			$rs['msg'] = '余额不足';
			return $rs;
		}
		$rs['info'][0]['coin']=$info['coin'];
		
		setcaches($key,1,50); 
	
		return $rs;
	}		
	

	/**
	 * 进入直播间
	 * @desc 用于用户进入直播
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votestotal 直播映票
	 * @return string info[0].barrage_fee 弹幕价格
	 * @return string info[0].userlist_time 用户列表获取间隔
	 * @return string info[0].chatserver socket地址
	 * @return string info[0].isattention 是否关注主播，0表示未关注，1表示已关注
	 * @return string info[0].nums 房间人数
	 * @return string info[0].pull_url 播流地址
	 * @return object info[0].vip 用户VIP信息
	 * @return string info[0].vip.type VIP类型，0表示无VIP，1表示普通VIP，2表示至尊VIP
	 * @return object info[0].liang 用户靓号信息
	 * @return string info[0].liang.name 号码，0表示无靓号
     * @return object info[0].guard 守护信息
	 * @return string info[0].guard.type 守护类型，0表示非守护，1表示月守护，2表示年守护
	 * @return string info[0].guard.endtime 到期时间
	 * @return string info[0].guard_nums 主播守护数量
	 * @return string info[0].show_lottery 直播间彩票展示
	 * @return string info[0].show_lottery['c_id'] 彩种ID
	 * @return string info[0].show_lottery['hot'] 是否推荐 1 = 推荐 2=不推荐
	 * @return string info[0].show_lottery['c_type'] 彩种分类
	 * @return string info[0].show_lottery['icon'] 图标
	 * @return string info[0].show_lottery['show_name'] 彩种名字
	 * @return string info[0].show_lottery['short_name'] 彩种标识

	 * @return string msg 提示信息
	 */
	public function enterRoom() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		

		$token=checkNull($this->token);
		$liveuid=$this->liveuid;

		$stream=checkNull($this->stream);
     
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        
		$isban = isBan($uid);
		if(!$isban){
			$rs['code']=1001;
			$rs['msg']='该账号已被禁用';
			return $rs;
		}

		
		$domain = new Domain_Live();
        
        $domain->checkShut($uid,$liveuid);
		$userinfo=getUserInfo($uid);

// 		$carinfo=getUserCar($uid);
// 		$userinfo['car']=$carinfo;
		$issuper='0';
		if($userinfo['issuper']==1){
			$issuper='1';
			DI()->redis  -> hset('super',$userinfo['id'],'1');
		}else{
			DI()->redis  -> hDel('super',$userinfo['id']);
		}
		
		

// 		$domain2 = new Domain_User();
// 		$info = $domain2->userUpdate($uid,$data);

        
// 		$usertype = isAdmin($uid,$liveuid);
// 		$userinfo['usertype'] = $usertype;
        
//         $stream2=explode('_',$stream);
// 		$showid=$stream2[1];
        
//         $contribution='0';
//         if($showid){
//             $contribution=$domain->getContribut($uid,$liveuid,$showid);
//         }

// 		$userinfo['contribution'] = $contribution;

		
		unset($userinfo['issuper']);
        
        /* 守护 */
        $domain_guard = new Domain_Guard();
		$guard_info=$domain_guard->getUserGuard($uid,$liveuid);
        
		$guard_nums=$domain_guard->getGuardNums($liveuid);
        // $userinfo['guard_type']=$guard_info['type'];
        /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
        $userinfo['sign']=$userinfo['consumption'].'.'.($userinfo['level']+100).'1';
		
		DI()->redis  -> set($token,json_encode($userinfo));
		
        /* 用户列表 */
        // $userlists=$this->getUserList($liveuid,$stream);
        
        
        $liveUser = DI()->notorm->user
                ->select('qq,wechat')
                ->where('id=? and user_type="2"', $liveuid)
                ->fetchOne();
		$configpri=getConfigPri();
        $configpub = getConfigPub();
        $nums = DI()->redis->zCard('user_'.$stream);
        $nums += rand(1000,3000);
        $votestotal = $domain->getVotes($liveuid);
	    $info=array(
			'votestotal'=>$votestotal,
// 			'barrage_fee'=>$configpri['barrage_fee'],
			'userlist_time'=>$configpri['userlist_time'],
			'chatserver'=>$configpri['chatserver'],
// 			'linkmic_uid'=>$linkmic_uid,
// 			'linkmic_pull'=>$linkmic_pull,
			'nums'=>$nums,
			'ispopup' => $configpub['ispopup'],
			'speak_limit'=>$configpri['speak_limit'],
// 			'barrage_limit'=>$configpri['barrage_limit'],
			'vip'=>$userinfo['vip'],
// 			'liang'=>$userinfo['liang'],
// 			'car'=>$userinfo['car'],
			'issuper'=>(string)$issuper,
// 			'usertype'=>(string)$usertype,
// 			'turntable_switch'=>(string)$configpri['turntable_switch'],
		);
		$info['isattention']=(string)isAttention($uid,$liveuid);
		// $info['userlists']=$userlists['userlist'];
        
        /* 用户余额 */
//         $domain2 = new Domain_User();
// 		$usercoin=$domain2->getBalance($uid);
//         $info['coin']=$usercoin['coin'];
        
        /* 守护 */
        $info['guard']=$guard_info;
        $info['guard_nums']=$guard_nums;
        $info['wechat'] = $liveUser['wechat'];
        $info['qq'] = $liveUser['qq'];
        
		//获取直播间在彩票
		$info['show_lottery']=DI()->notorm->live
			->select("c_id,hot,c_type,icon,show_name,short_name")
			->where('uid = ?',$liveuid)
			->fetch();
			
		$pull=getPull($stream);
		$info['pull']=$pull;
		
		
		
		/*观看直播计时---用于每日任务--记录用户进入时间*/
		$daily_tasks_key='watch_live_daily_tasks_'.$uid;
		$enterRoom_time=time();
		setcaches($daily_tasks_key,$enterRoom_time);

		$rs['info'][0]=$info;
		return $rs;
	}	

    /**
     * 连麦信息
     * @desc 用于主播同意连麦 写入redis
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string msg 提示信息
     */
		 
    public function showVideo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$touid=checkNull($this->touid);
		$pull_url=checkNull($this->pull_url);
		
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 token:'.json_encode($token)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 touid:'.json_encode($touid)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 pull_url:'.json_encode($pull_url)."\r\n",FILE_APPEND);
        
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $data=array(
            'uid'=>$touid,
            'pull_url'=>$pull_url,
        );
		
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 set:'.json_encode($data)."\r\n",FILE_APPEND);
        
		DI()->redis  -> hset('ShowVideo',$uid,json_encode($data));
					
        return $rs;
    }		

    /**
     * 获取最新流地址
     * @desc 用于连麦获取最新流地址
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string msg 提示信息
     */
		 
    protected function getPullWithSign($pull) {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        if($pull==''){
            return '';
        }
		$list1 = preg_split ('/\?/', $pull);
        $originalUrl=$list1[0];
        
        $list = preg_split ('/\//', $originalUrl);
        $url = preg_split ('/\./', end($list));
        
        $stream=$url[0];

        $play_url=PrivateKeyA('rtmp',$stream,0);
					
        return $play_url;
    }

	
    /**
     * 获取僵尸粉
     * @desc 用于获取僵尸粉
     * @return int code 操作码，0表示成功
     * @return array info 僵尸粉信息
     * @return string msg 提示信息
     */
		 
    public function getZombie() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$stream=checkNull($this->stream);
		
		$stream2=explode('_',$stream);
		$liveuid=$stream2[0];

		$domain = new Domain_Live();

		$iszombie=$domain->isZombie($liveuid);
		
// 		if($iszombie==0){
// 			$rs['code']=1001;
// 			$rs['info']='未开启僵尸粉';
// 			$rs['msg']='未开启僵尸粉';
// 			return $rs;
			
// 		}

		/* 判断用户是否进入过 */
		$isvisit=DI()->redis ->sIsMember($liveuid.'_zombie_uid',$uid);

		if($isvisit){
			$rs['code']=1003;
			$rs['info']='用户已访问';
			$rs['msg']='用户已访问';
			return $rs;
			
		}
	
		$times=DI()->redis  -> get($liveuid.'_zombie');
		
		if($times && $times>10){
			$rs['code']=1002;
			$rs['info']='次数已满';
			$rs['msg']='次数已满';
			return $rs;
		}else if($times){
			$times=$times+1;
			
		}else{
			$times=0;
		}
	
		DI()->redis  -> set($liveuid.'_zombie',$times);
		DI()->redis  -> sAdd($liveuid.'_zombie_uid',$uid);
		
		/* 用户列表 */ 

        $uidlist=DI()->redis -> zRevRange('user_'.$stream,0,-1);
	
		$uid=implode(",",$uidlist);

		$where='0';
		if($uid){
			$where.=','.$uid;
		} 
        
		$where=str_replace(",,",',',$where);
		$where=trim($where, ",");
		$list=$domain->getZombie($stream,$where);
		if($list){
			$rs['info'][0]['list'] = $list;
			$nums=DI()->redis->zCard('user_'.$stream);
			if(!$nums){
				$nums=0;
			}
			$nums += round(2000,3000) + round(1,10) * round(1000,9999);
			$rs['info'][0]['nums']=(string)$nums;
		}
		
        return $rs;
    }	
	/**
	 * 用户列表 
	 * @desc 用于直播间获取用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].userlist 用户列表
	 * @return string info[0].nums 房间人数
	 * @return string info[0].votestotal 主播映票
	 * @return string info[0].guard_type 守护类型
	 * @return string msg 提示信息
	 */
	public function getUserLists() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
		$p=$this->p;

		/* 用户列表 */ 
		$info=$this->getUserList($liveuid,$stream,$p);

		$rs['info'][0]=$info;

        return $rs;
	}			
    

    
    protected function getUserList($liveuid,$stream,$p=1) {
		/* 用户列表 */ 
		$n=1;
		$pnum=20;
		$start=($p-1)*$pnum;
        
        $domain_guard = new Domain_Guard();
		
		$key="getUserLists_".$stream.'_'.$p;
		$u_nums=DI()->redis->zCard('user_'.$stream);
		$list=getcaches($key);
		 if(!$list){  
           $list=array();
            
            $uidlist=DI()->redis -> zRevRange('user_'.$stream,$start,$pnum,true);
            foreach($uidlist as $k=>$v){
                $userinfo=getUserInfo($k);
            
                $info=explode(".",$v);

                $userinfo['contribution']=(string)$info[0];
                
                /* 守护 */
                $guard_info=$domain_guard->getUserGuard($k,$liveuid);
                $userinfo['guard_type']=$guard_info['type'];
                
                $list[]=$userinfo;
            }
            $zombie_nums = DI()->redis ->zCard('zombie');
            $end_key = 'zombie:' . $liveuid . ':' . $stream;
            if(!$zombie_list = getcaches($end_key))
            {
                $zombie_list = DI()->redis -> sRandMember('zombie',100 - $nums);
                setcaches($end_key,$zombie_list,120);
            }
            
            if($zombie_list)
            {
                foreach ($zombie_list as $v)
                {
                    $userinfo = json_decode($v, true);
                    $list[]= $userinfo;
                }
            }
            if($list){
                setcaches($key,$list,10);
            }
		} 
        
        if(!$list){
            $list=array();
        }
        
// 		
//         if(!$nums){
//             $nums=0;
//         }
        // shuffle($list);
        $stream = explode('_', $stream);
        
		$rs['userlist']=$list;
		$key = $liveuid.":nums";
		$redis = connectionRedis();
		$nums = $redis->get($key);
        
        if(empty($nums))
        {
            $nums = 51;
            $redis->set($key,50);
        }elseif($nums<500){
            $redis->incr($key,rand(1,50));
        }
        $nums += $u_nums;
        $nums -= round(1,10);
		$rs['nums']=(string)$nums;

		/* 主播信息 */
		$domain = new Domain_Live();
		$rs['votestotal']=$domain->getVotes($liveuid);
		

        return $rs; 
    }
		

		
	/**
	 * 弹窗 
	 * @desc 用于直播间弹窗信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].consumption 消费总数
	 * @return string info[0].votestotal 票总数
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].isattention 是否关注，0未关注，1已关注
	 * @return string info[0].action 操作显示，0表示自己，30表示普通用户，40表示管理员，501表示主播设置管理员，502表示主播取消管理员，60表示超管管理主播，70表示对方是超管 
	 * @return object info[0].vip 用户VIP信息
	 * @return string info[0].vip.type VIP类型，0表示无VIP，1表示普通VIP，2表示至尊VIP
	 * @return object info[0].liang 用户靓号信息
	 * @return string info[0].liang.name 号码，0表示无靓号
	 * @return array info[0].label 印象标签
	 * @return string msg 提示信息
	 */
	public function getPop() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$liveuid=$this->liveuid;
		$touid=$this->touid;

        $info=getUserInfo($touid);
		if(!$info){
			$rs['code']=1002;
			$rs['msg']='用户信息不存在';
			return $rs;
		}
		$info['follows']=getFollows($touid);
		$info['fans']=getFans($touid);
        
		$info['isattention']=(string)isAttention($uid,$touid);
		if($uid==$touid){
			$info['action']='0';
		}else{
			$uid_admin=isAdmin($uid,$liveuid);
			$touid_admin=isAdmin($touid,$liveuid);

			if($uid_admin==40 && $touid_admin==30){
				$info['action']='40';
			}else if($uid_admin==50 && $touid_admin==30){
				$info['action']='501';
			}else if($uid_admin==50 && $touid_admin==40){
				$info['action']='502';
			}else if($uid_admin==60 && $touid_admin<50){
				$info['action']='40';
			}else if($uid_admin==60 && $touid_admin==50){
				$info['action']='60';
			}else if($touid_admin==60){
				$info['action']='70';
			}else{
				$info['action']='30';
			}
			
		}
        
        /* 标签 */
        $labels=array();
        if($touid==$liveuid){
            $key="getMyLabel_".$touid;
            $label=getcaches($key);
			
            if(!$label){
                $domain2 = new Domain_User();
                $label = $domain2->getMyLabel($touid);

                setcaches($key,$label); 
            }
            
            $labels=array_slice($label,0,2);
        }
        $info['label']=$labels;
        
		$rs['info'][0]=$info;
		return $rs;
	}				

	/**
	 * 礼物列表 
	 * @desc 用于获取礼物列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 余额
	 * @return array info[0].giftlist 礼物列表
	 * @return string info[0].giftlist[].id 礼物ID
	 * @return string info[0].giftlist[].type 礼物类型
	 * @return string info[0].giftlist[].mark 礼物标识
	 * @return string info[0].giftlist[].giftname 礼物名称
	 * @return string info[0].giftlist[].needcoin 礼物价格
	 * @return string info[0].giftlist[].gifticon 礼物图片
	 * @return string msg 提示信息
	 */
	public function getGiftList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_Live();
        $giftlist=$domain->getGiftList();
        $proplist=$domain->getPropgiftList();
		
		$domain2 = new Domain_User();
		$coin=$domain2->getBalance($uid);
		
		$rs['info'][0]['giftlist']=$giftlist;
		$rs['info'][0]['proplist']=$proplist;
		$rs['info'][0]['coin']=$coin['coin'];
		return $rs;
	}	
	//zombieSendGift
	
	/**
	 * 机器人赠送礼物 
	 * @desc 用于机器赠送礼物
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].gifttoken 礼物token
	 * @return string info[0].level 用户等级
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function zombieSendGift() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=$this->uid;
		$liveuid=$this->liveuid;
		$giftid=$this->giftid;
		$giftcount=$this->giftcount;


        $domain = new Domain_Live();
		$giftlist=$domain->getGiftList();
		$gift_info=array();
		foreach($giftlist as $k=>$v){
			if($giftid == $v['id']){
			   $gift_info=$v; 
			}
		}

        if(!$gift_info){
            $rs['code']=1002;
			$rs['msg']='礼物信息不存在';
			return $rs;
        }
        
        if($gift_info['mark']==2){
            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info=$domain_guard->getUserGuard($uid,$liveuid);
            if($guard_info['type']!=2){
               $rs['code']=1002;
                $rs['msg']='该礼物是年守护专属礼物奥~';
                return $rs; 
            }
        }
        $stream = DI()->notorm->live->select("stream")
					->where('uid=? and islive=?',$liveuid,1)
					->fetchOne();
		$stream = $stream['stream'];

		$result=$domain->sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack);
		if($result==1001){
			$rs['code']=1001;
			$rs['msg']='余额不足';
			return $rs;
		}else if($result==1003){
			$rs['code']=1003;
			$rs['msg']='背包中数量不足';
			return $rs;
		}else if($result==1002){
			$rs['code']=1002;
			$rs['msg']='礼物信息不存在';
			return $rs;
		}else if($result==1004){
            $rs['code']=1004;
            $rs['msg']='赠送异常';
            return $rs;
        }
		
		$rs['info'][0]['gifttoken']=$result['gifttoken'];
        $rs['info'][0]['level']=$result['level'];
        $rs['info'][0]['coin']=$result['coin'];
		
		unset($result['gifttoken']);

		DI()->redis  -> set($rs['info'][0]['gifttoken'],json_encode($result));
		
		
		return $rs;
	}
	/**
	 * 赠送礼物 
	 * @desc 用于赠送礼物
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].gifttoken 礼物token
	 * @return string info[0].level 用户等级
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function sendGift() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=$this->uid;
		$token=$this->token;
		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
		$giftid=$this->giftid;
		$giftcount=$this->giftcount;
		$ispack=$this->ispack;
		$is_sticker=$this->is_sticker;

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $domain = new Domain_Live();
		if($is_sticker=='1'){
			$giftlist=$domain->getPropgiftList();

			$gift_info=array();
			foreach($giftlist as $k=>$v){
				if($giftid == $v['id']){
				   $gift_info=$v; 
				}
			}
		}else{
			$giftlist=$domain->getGiftList();
			$gift_info=array();
			foreach($giftlist as $k=>$v){
				if($giftid == $v['id']){
				   $gift_info=$v; 
				}
			}
		}

        if(!$gift_info){
            $rs['code']=1002;
			$rs['msg']='礼物信息不存在';
			return $rs;
        }
        
        if($gift_info['mark']==2){
            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info=$domain_guard->getUserGuard($uid,$liveuid);
            if($guard_info['type']!=2){
               $rs['code']=1002;
                $rs['msg']='该礼物是年守护专属礼物奥~';
                return $rs; 
            }
        }
		$domain = new Domain_Live();
		$result=$domain->sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack);
		if($result==1001){
			$rs['code']=1001;
			$rs['msg']='余额不足';
			return $rs;
		}else if($result==1003){
			$rs['code']=1003;
			$rs['msg']='背包中数量不足';
			return $rs;
		}else if($result==1002){
			$rs['code']=1002;
			$rs['msg']='礼物信息不存在';
			return $rs;
		}else if($result==1004){
            $rs['code']=1004;
            $rs['msg']='赠送异常';
            return $rs;
        }
		
		$rs['info'][0]['gifttoken']=$result['gifttoken'];
        $rs['info'][0]['level']=$result['level'];
        $rs['info'][0]['coin']=$result['coin'];
		
		unset($result['gifttoken']);

		DI()->redis  -> set($rs['info'][0]['gifttoken'],json_encode($result));
		
		
		return $rs;
	}		
	
	/**
	 * 发送弹幕 
	 * @desc 用于发送弹幕
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].barragetoken 礼物token
	 * @return string info[0].level 用户等级
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function sendBarrage() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=$this->uid;
		$token=$this->token;
		$liveuid=$this->liveuid;
		$stream=checkNull($this->stream);
		$giftid=0;
		$giftcount=1;
		
		$content=checkNull($this->content);
		if($content==''){
			$rs['code'] = 1003;
			$rs['msg'] = '弹幕内容不能为空';
			return $rs;
		}
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
		
		$domain = new Domain_Live();
		$result=$domain->sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content);
		
		if($result==1001){
			$rs['code']=1001;
			$rs['msg']='余额不足';
			return $rs;
		}else if($result==1002){
			$rs['code']=1002;
			$rs['msg']='礼物信息不存在';
			return $rs;
		}else if($result==1003){
            $rs['code']=1003;
            $rs['msg']='异常';
            return $rs;
        }
		
		$rs['info'][0]['barragetoken']=$result['barragetoken'];
        $rs['info'][0]['level']=$result['level'];
        $rs['info'][0]['coin']=$result['coin'];
		
		unset($result['barragetoken']);

		DI()->redis -> set($rs['info'][0]['barragetoken'],json_encode($result));

		return $rs;
	}			

	/**
	 * 设置/取消管理员 
	 * @desc 用于设置/取消管理员
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isadmin 是否是管理员，0表示不是管理员，1表示是管理员
	 * @return string msg 提示信息
	 */
	public function setAdmin() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=$this->token;
		$liveuid=$this->liveuid;
		$touid=$this->touid;
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
		
		if($uid!=$liveuid){
			$rs['code'] = 1001;
			$rs['msg'] = '你不是该房间主播，无权操作';
			return $rs;
		}
		
		$domain = new Domain_Live();
		$info=$domain->setAdmin($liveuid,$touid);
		
		if($info==1004){
			$rs['code'] = 1004;
			$rs['msg'] = '最多设置5个管理员';
			return $rs;
		}else if($info==1003){
			$rs['code'] = 1003;
			$rs['msg'] = '操作失败，请重试';
			return $rs;
		}
		
		$rs['info'][0]['isadmin']=$info;
		return $rs;
	}		
	
	/**
	 * 管理员列表 
	 * @desc 用于获取管理员列表
	 * @return int code 操作码，0表示成功
	 * @return array info 管理员列表
	 * @return array info[0]['list'] 管理员列表
	 * @return array info[0]['list'][].userinfo 用户信息
	 * @return string info[0]['nums'] 当前人数
	 * @return string info[0]['total'] 总数
	 * @return string msg 提示信息
	 */
	public function getAdminList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_Live();
		$info=$domain->getAdminList($this->liveuid);
		
		$rs['info'][0]=$info;
		return $rs;
	}			

	/**
	 * 举报类型 
	 * @desc 用于获取举报类型
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].name 类型名称
	 * @return string msg 提示信息
	 */
	public function getReportClass() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$domain = new Domain_Live();
		$info=$domain->getReportClass();

		
		$rs['info']=$info;
		return $rs;
	}	

	
	/**
	 * 用户举报 
	 * @desc 用于用户举报
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 举报成功
	 * @return string msg 提示信息
	 */
	public function setReport() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$touid=$this->touid;
		$content=checkNull($this->content);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
		
		if(!$content){
			$rs['code'] = 1001;
			$rs['msg'] = '举报内容不能为空';
			return $rs;
		}
        
        if(mb_strlen($account)>200){
            $rs['code'] = 1002;
            $rs['msg'] = '账号长度不能超过200个字符';
            return $rs;
        }
		
		$domain = new Domain_Live();
		$info=$domain->setReport($uid,$touid,$content);
		if($info===false){
			$rs['code'] = 1002;
			$rs['msg'] = '举报失败，请重试';
			return $rs;
		}
		
		$rs['info'][0]['msg']="举报成功";
		return $rs;
	}			
	
	/**
	 * 主播映票 
	 * @desc 用于获取主播映票
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votestotal 用户总数
	 * @return string msg 提示信息
	 */
	public function getVotes() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_Live();
		$info=$domain->getVotes($this->liveuid);
		
		$rs['info'][0]=$info;
		return $rs;
	}		
	
    /**
     * 禁言
     * @desc 用于 禁言操作
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string msg 提示信息
     */
		 
    public function setShutUp() { 
        $rs = array('code' => 0, 'msg' => '禁言成功', 'info' => array());
		
		$uid=$this->uid;
		$token=$this->token;
		$liveuid=$this->liveuid;
		$touid=$this->touid;
		$type=$this->type;
		$stream=$this->stream;
        
        //file_put_contents('./setShutUp.txt',date('Y-m-d H:i:s').' 提交参数信息 request:'.json_encode($_REQUEST)."\r\n",FILE_APPEND);

		$checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='token已过期，请重新登陆';
			return $rs;
		}
						
        $uidtype = isAdmin($uid,$liveuid);

		if($uidtype==30 ){
			$rs["code"]=1001;
			$rs["msg"]='无权操作';
			return $rs;									
		}

        $touidtype = isAdmin($touid,$liveuid);
		
		if($touidtype==60){
			$rs["code"]=1001;
			$rs["msg"]='对方是超管，不能禁言';
			return $rs;	
		}

		if($uidtype==40){
			if( $touidtype==50){
				$rs["code"]=1002;
				$rs["msg"]='对方是主播，不能禁言';
				return $rs;		
			}	
			if($touidtype==40 ){
				$rs["code"]=1002;
				$rs["msg"]='对方是管理员，不能禁言';
				return $rs;		
			}
            
            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info=$domain_guard->getUserGuard($touid,$liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type']==2){
                $rs["code"]=1004;
                $rs["msg"]='对方是尊贵守护，不能禁言';
                return $rs;	
            }
			
		}
		$showid=0;
        if($type ==1 || $stream){
            $showid=1;
        }
        $domain = new Domain_Live();
		$result = $domain->setShutUp($uid,$liveuid,$touid,$showid);
        
        if($result==1002){
            $rs["code"]=1003;
            $rs["msg"]='对方已被禁言';
            return $rs;	
            
        }else if(!$result){
            $rs["code"]=1005;
            $rs["msg"]='操作失败，请重试';
            return $rs;	
        }
        
        DI()->redis -> hSet($liveuid . 'shutup',$touid,1);	 
        
        return $rs;
    }	
	
	/**
	 * 踢人 
	 * @desc 用于直播间踢人
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 踢出成功
	 * @return string msg 提示信息
	 */
	public function kicking() {
		$rs = array('code' => 0, 'msg' => '踢人成功', 'info' => array());
		
		$uid=$this->uid;
		$token=$this->token;
		$liveuid=$this->liveuid;
		$touid=$this->touid;
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 

		$admin_uid=isAdmin($uid,$liveuid);
		if($admin_uid==30){
			$rs['code']=1001;
			$rs['msg']='无权操作';
			return $rs;
		}
		$admin_touid=isAdmin($touid,$liveuid);
		
		if($admin_touid==60){
			$rs["code"]=1002;
			$rs["msg"]='对方是超管，不能被踢出';
			return $rs;
		}
		
		if($admin_uid!=60){
			if($admin_touid==50 ){
				$rs['code']=1001;
				$rs['msg']='对方是主播，不能被踢出';
				return $rs;
			} 
            
            if($admin_touid==40 ){
				$rs['code']=1002;
				$rs['msg']='对方是管理员，不能被踢出';
				return $rs;
			}
            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info=$domain_guard->getUserGuard($touid,$liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type']==2){
                $rs["code"]=1004;
                $rs["msg"]='对方是尊贵守护，不能被踢出';
                return $rs;	
            }            
		}		
        
        $domain = new Domain_Live();
        
		$result = $domain->kicking($uid,$liveuid,$touid);
        if($result==1002){
            $rs["code"]=1005;
			$rs["msg"]='对方已被踢出';
			return $rs;
        }else if(!$result){
            $rs["code"]=1006;
			$rs["msg"]='操作失败，请重试';
			return $rs;
        }

		$rs['info'][0]['msg']='踢出成功';
		return $rs;
	}		
	
	/**
     * 超管关播
     * @desc 用于超管关播
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].msg 提示信息 
     * @return string msg 提示信息
     */
		
	public function superStopRoom(){

		$rs = array('code' => 0, 'msg' => '关闭成功', 'info' =>array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        $type=checkNull($this->type);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
        
		
		$domain = new Domain_Live();
		
		$result = $domain->superStopRoom($uid,$liveuid,$type);
		if($result==1001){
			$rs['code'] = 1001;
            $rs['msg'] = '你不是超管，无权操作';
			$rs['info'][0]['msg'] = '你不是超管，无权操作';
            return $rs;
		}else if($result==1002){
			$rs['code'] = 1002;
            $rs['msg'] = '该主播已被禁播';
			$rs['info'][0]['msg'] = '该主播已被禁播';
            return $rs;
		}
		$rs['info'][0]['msg']='关闭成功';
 
    	return $rs;
	}	

	/**
	 * 用户余额 
	 * @desc 用于获取用户余额
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 余额
	 * @return string msg 提示信息
	 */
	public function getCoin() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		
		$domain2 = new Domain_User();
		$coin=$domain2->getBalance($uid);

		$rs['info'][0]['coin']=$coin['coin'];
		return $rs;
	}

	/**
	 * 检测房间状态 
	 * @desc 用于检测房间状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].status 状态 0关1开
	 * @return string msg 提示信息
	 */
	public function checkLiveing() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$stream=checkNull($this->stream);

		$domain = new Domain_Live();

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){

			//将主播关播
			$domain->stopRoom($uid,$stream);

			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		//file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
		//file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 stream:'.json_encode($stream)."\r\n",FILE_APPEND);
        
		$info=$domain->checkLiveing($uid,$stream);
        
        //file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 info:'.json_encode($info)."\r\n",FILE_APPEND);

		$rs['info'][0]['status']=$info;
		return $rs;
	}		

	/**
	 * 获取直播信息 
	 * @desc 用于个人中心进入直播间获取直播信息
	 * @return int code 操作码，0表示成功
	 * @return array info  直播信息
	 * @return string msg 提示信息
	 */
	public function getLiveInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$liveuid=checkNull($this->liveuid);
		
        if($liveuid<1){
            $rs['code'] = 1001;
			$rs['msg'] = '参数错误';
			return $rs;
        }
		
		
		$domain2 = new Domain_Live();
		$info=$domain2->getLiveInfo($liveuid);
        if(!$info){
            $rs['code'] = 1002;
			$rs['msg'] = '直播已结束';
			return $rs;
        }

		$rs['info'][0]=$info;
		return $rs;
	}



	/**
	 * 用户离开直播间
	 * @desc 用于每日任务统计用户观看时长
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function signOutWatchLive(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=checkNull($this->uid);
        $token=checkNull($this->token);


        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$type='1';  //用户观看直播间时长任务
		/*观看直播计时---每日任务--取出用户进入时间*/
		$key='watch_live_daily_tasks_'.$uid;
		$starttime=getcaches($key);
		if($starttime){ 
			$endtime=time();  //当前时间
			$data=[
				'type'=>$type,
				'starttime'=>$starttime,
				'endtime'=>$endtime,
			];
			
			dailyTasks($uid,$data);
			
			//删除当前存入的时间
			delcache($key);
		}

		return $rs;	
	}
	

	
	
	/**
	 * 用户分享直播间
	 * @desc 用于每日任务统计分享次数
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function shareLiveRoom(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$data=[
			'type'=>'5',
			'nums'=>'1',

		];
		dailyTasks($uid,$data);

		return $rs;	
	}
	
	
	
	

}
