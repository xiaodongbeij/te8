<?php
/**
 * 用户信息
 */
if (!session_id()) session_start();
class Api_User extends PhalApi_Api {

	public function getRules() {
		return array(
			'iftoken' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'getBaseInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'version_ios' => array('name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号'),
			),
			
			'updateAvatar' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'file' => array('name' => 'file','type' => 'file', 'min' => 0, 'max' => 1024 * 1024 * 30, 'range' => array('image/jpg', 'image/jpeg', 'image/png'), 'ext' => array('jpg', 'jpeg', 'png')),
			),
            'updateBackImg' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'file' => array('name' => 'file','type' => 'file', 'min' => 0, 'max' => 1024 * 1024 * 30, 'range' => array('image/jpg', 'image/jpeg', 'image/png'), 'ext' => array('jpg', 'jpeg', 'png')),
            ),
			
			'updateFields' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'fields' => array('name' => 'fields', 'type' => 'string', 'require' => true, 'desc' => "修改信息('user_nicename','sex','signature','birthday','bat','qq','wechat')，json字符串"),
			),
			
			'updatePass' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'oldpass' => array('name' => 'oldpass', 'type' => 'string', 'require' => true, 'desc' => '旧密码'),
				'pass' => array('name' => 'pass', 'type' => 'string', 'require' => true, 'desc' => '新密码'),
				'pass2' => array('name' => 'pass2', 'type' => 'string', 'require' => true, 'desc' => '确认密码'),
			),
			
			'getBalance' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'string', 'desc' => '设备类型，0android，1IOS'),
                'version_ios' => array('name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号'),
			),
			
			'getProfit' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'setCash' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'accountid' => array('name' => 'accountid', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
				'cashvote' => array('name' => 'cashvote', 'type' => 'int', 'require' => true, 'desc' => '提现的票数'),
			),
			
			'setAttent' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'setIszombieAttent' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'isAttent' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'isBlacked' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			'checkBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),

			'setBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getBindCode' => array(
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
				'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '用户token'),
				'uid' => array('name' => 'uid', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '用户ID'),
			),
			
			'setMobile' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
				'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '设备号'),
				'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
			),
			
			'getFollowsList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getFansList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getBlackList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getLiverecord' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getAliCdnRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '直播记录ID'),
            ),
			
			'getUserHome' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getContributeList' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
			
			'getPmUserInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getMultiInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'uids' => array('name' => 'uids', 'type' => 'string', 'min' => 1,'require' => true, 'desc' => '用户ID，多个以逗号分割'),
				'type' => array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '关注类型，0 未关注 1 已关注'),
			),
            
            'getUidsInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'uids' => array('name' => 'uids', 'type' => 'string', 'min' => 1,'require' => true, 'desc' => '用户ID，多个以逗号分割'),
			),
			'Bonus' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
            'getBonus' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			'setDistribut' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'code' => array('name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '邀请码'),
			),

			'getUserLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
            
            'setUserLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
                'labels' => array('name' => 'labels', 'type' => 'string', 'require' => true, 'desc' => '印象标签ID，多个以逗号分割'),
			),

            'getMyLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

            'getUserAccountList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

            'setUserAccount' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '账号类型，1表示支付宝，2表示微信，3表示银行卡'),
                'account_bank' => array('name' => 'account_bank', 'type' => 'string', 'default' => '', 'desc' => '银行名称'),
                'account' => array('name' => 'account', 'type' => 'string', 'require' => true, 'desc' => '账号'),
                'name' => array('name' => 'name', 'type' => 'string', 'default' => '', 'desc' => '姓名'),
			),
            
            'delUserAccount' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
			),

			'setShopCash' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'accountid' => array('name' => 'accountid', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
				'money' => array('name' => 'money', 'type' => 'float', 'require' => true, 'desc' => '提现的金额'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getAuthInfo'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'seeDailyTasks'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'default' => '0', 'desc' => '主播ID'),
				'islive' => array('name' => 'islive', 'type' => 'int', 'default' => '0',  'desc' => '是否在直播间 0不在 1在'),
			),
			'receiveTaskReward'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'taskid' => array('name' => 'taskid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '任务ID'),
			),
            'getUserBankList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),
            'getBankList' => array(
            'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),
            'addUserBank' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'name' => array('name' => 'name', 'type' => 'string', 'require' => true, 'desc' => '真实姓名'),
                'bank_id' => array('name' => 'bank_id', 'type' => 'string', 'require' => true, 'desc' => '开户银行id'),
                'bank_card' => array('name' => 'bank_card', 'type' => 'string', 'require' => true, 'desc' => '银行卡号'),
                'outlets' => array('name' => 'outlets', 'type' => 'string', 'require' => true, 'desc' => '开户网点'),
                'status' => array('name' => 'status', 'type' => 'string', 'desc' => '是否默认,1=默认,不传则是不默认'),
            ),
            'isMoneyPasswd' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),
            'setMoneyPasswd' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'money_passwd' => array('name' => 'money_passwd', 'type' => 'string', 'require' => true, 'desc' => '支付密码'),
                'con_money_passwd' => array('name' => 'con_money_passwd', 'type' => 'string', 'require' => true, 'desc' => '确认支付密码'),
            ),
            'editMoneyPasswd' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'old_pass' => array('name' => 'old_pass', 'type' => 'string', 'require' => true, 'desc' => '原密码'),
                'new_pass' => array('name' => 'new_pass', 'type' => 'string', 'require' => true, 'desc' => '新密码'),
                'con_pass' => array('name' => 'con_pass', 'type' => 'string', 'require' => true, 'desc' => '确认新密码'),
            ),
            'getWxPay' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),
            'setImg' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'file' => array('name' => 'file','type' => 'file', 'min' => 0, 'max' => 1024 * 1024 * 30, 'range' => array('image/jpg', 'image/jpeg', 'image/png'), 'ext' => array('jpg', 'jpeg', 'png')),
            ),
            'upWxPay' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'wxpay_img' => array('name' => 'wxpay_img', 'type' => 'string', 'require' => true, 'desc' => '收款码'),
                'wxpay_account' => array('name' => 'wxpay_account', 'type' => 'string', 'require' => true, 'desc' => '收款账号'),
            ),
            'withdraw' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'money' => array('name' => 'money', 'type' => 'int', 'require' => true, 'desc' => '多少钱'),
                'money_pass' => array('name' => 'money_pass', 'type' => 'string', 'require' => true, 'desc' => '资金密码'),
                'withdraw_type' => array('name' => 'withdraw_type', 'type' => 'int', 'require' => true, 'desc' => '提现类型(1-银行，2-微信)'),
                'withdraw_id' => array('name' => 'withdraw_id', 'type' => 'int', 'desc' => '提现去处'),
            ),

            'withdrawList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),

            'feedBackAdd' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'content' => array('name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '反馈内容'),
                'thumb' => array('name' => 'thumb', 'type' => 'string', 'desc' => '图片地址（一张）'),
                'version' => array('name' => 'version', 'type' => 'string', 'desc' => '系统版本号'),
                'model' => array('name' => 'model', 'type' => 'string', 'desc' => '设备'),
            ),

            'IEDetailed' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'm_time' => array('name' => 'm_time', 'type' => 'int', 'desc' => '月份筛选(月份时间戳 11月=1604160000)'),
                'd_time' => array('name' => 'd_time', 'type' => 'int', 'min' => 1, 'desc' => '日期筛选（时间戳）'),
                'type' => array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '0=全部，1=收入，2=支出'),
                'change_type' => array('name' => 'change_type', 'type' => 'int', 'desc' => '1-充值,3-彩票,4-补单,5-会员管理转账,6-优惠赠送,7-返水,8-额度转换'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页数,1开始'),
                'page_size' => array('name' => 'page_size', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '每页条数'),
            ),

            'MyGrade' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),

            'BuyVip' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'vid' => array('name' => 'vid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => 'VIP ID'),
            ),

            'GetVipList' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),

            'withdrawH' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
            ),

            'GetPerSetting' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
            ),

            'DelUserBank' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'bank_id' => array('name' => 'bank_id', 'type' => 'int', 'desc' => '用户银行ID'),
            ),
            'repairShareCode' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'share_code' => array('name' => 'share_code', 'type' => 'string', 'require' => true, 'desc' => '邀请码'),
            ),
		);
	}

    /**
     * NEW补填邀请码
     * @desc 用于 补填邀请码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function repairShareCode()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $share_code = checkNull($this->share_code);
        
 
        
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $uinfo = DI()->notorm->user->where('id', $user_id)->fetchOne();
        if(!$uinfo) return ['code' => 1, 'msg' => '用户不存在'];
        if ($share_code == $uinfo['invite_code']) return ['code' => 1, 'msg' => '不能填写自身邀请码'];
        if($uinfo && !empty($uinfo['parent_id'])) return ['code' => 1, 'msg' => '您已填写过邀请码'];


        if (strlen($share_code) == 8){
            $share_uinfo = DI()->notorm->user->where('invite_code',$share_code)->fetchOne();
        }else{
            $pid = DI()->notorm->user_invite->where('invite_key',$share_code)->fetchOne();
            if (!$pid) return ['code' => 1, 'msg' => '邀请码不存在'];
            $share_uinfo = DI()->notorm->user->where('id',$pid['uid'])->fetchOne();
        }

        if(!$share_uinfo) return ['code' => 1, 'msg' => '邀请码不存在'];

        $res = DI()->notorm->user->where('id', $user_id)->update(['parent_id' => $share_uinfo['id'], 'invite_level' => $share_uinfo['invite_level'] . $uinfo['id'] . '-']);
        DI()->notorm->user_rate->where('user_id',$user_id)->where('platform',1)->update(['rate'=>$pid['rate']]);
        if($res) return ['code' => 0, 'msg' => '补填成功'];
        return ['code' => 1, 'msg' => '补填失败'];
    }

    /**
     * NEW删除用户银行
     * @desc 用于 删除用户银行
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function DelUserBank()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $bank_id = checkNull($this->bank_id);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $res = DI()->notorm->user_bank
            ->where('id = ? and u_id = ?', $bank_id, $user_id)
            ->update(['del_status' => 1]);

        if($res) return ['code' => 0, 'msg' => '删除成功'];
        return ['code' => 1, 'msg' => '删除失败'];
    }

    /**
     * NEW 提现页面所需数据
     * @desc 用于 提现页面所需数据
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return array info['count_Withdrawal'] 累积提现
     * @return array info['coin'] 用户余额
     * @return array info['count_profit'] 累积收益
     * @return array info['withdrawal_procedures'] 手续费百分比(%)
     * @return string msg 提示信息
     */
    public function withdrawH(){
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $user_info = DI()->notorm->user->where('id = ?', $user_id)->fetchOne();
        $configpri = DI()->notorm->option->where('option_name = ?', 'configpri')->fetchOne();
        $configpri = json_decode($configpri['option_value'], true);

        $info['count_Withdrawal'] = $user_info['count_Withdrawal'];
        $info['coin'] = $user_info['coin'];
        $info['count_profit'] = 0;
        $info['withdrawal_procedures'] = $configpri['withdrawal_procedures'];

        $rs = [
            'code' => 0,
            'msg' => 'ok',
            'info' => $info,
        ];
        return $rs;
    }

    /**
     * NEW Vip列表
     * @desc 用于 获取Vip列表
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return array info['coin']
     * @return array info['name']
     * @return array info['length'] (月)
     * @return string msg 提示信息
     */
    public function GetVipList()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $vip_list = DI()->notorm->vip
            ->order('length ASC')
            ->select('id,coin,name,length')
            ->fetchAll();

        return ['code' => 0, 'msg' => 'ok', 'info' => $vip_list];
    }

    /**
     * NEW购买VIP
     * @desc 用于 购买VIP
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function BuyVip()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $vid = checkNull($this->vid);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $vip_info = DI()->notorm->vip->where("id = ?", $vid)->fetchOne();

        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        $res1 = DI()->notorm->user
            ->where('id = ? and coin >= ?', $user_id,$vip_info['coin'])
            ->update(array('coin' => new NotORM_Literal("coin - {$vip_info['coin']}") ,'consumption' => new NotORM_Literal("consumption + {$vip_info['coin']}") ) );
        if(!$res1) return ['code' => 1, 'msg' => '余额不足'];

        $res2 = user_change_action($user_id,13,-1 * $vip_info['coin'],DI()->config->get('app.change_type')[13],$user_id,$vip_info['id'],1,'','',2);

        $user_vip_info = DI()->notorm->vip_user->where("uid = ?",$user_id)->fetchOne();
        if($user_vip_info){
            $add_time = $vip_info['length'] * 30 * 86400;
            $res3 = DI()->notorm->vip_user
                ->where("uid = ?",$user_id)
                ->update(array('endtime' => new NotORM_Literal("endtime + {$add_time}") ) );

            if ($res1 && $res2 && $res2 != 2 && $res3){
                DI()->notorm->commit('db_appapi');
                delcache('vip_' . $user_id);
                return ['code' => 0, 'msg' => '续费成功'];
            }else{
                DI()->notorm->rollback('db_appapi');
                return ['code' => 1, 'msg' => '购买失败'];
            }

        }else{
            $endtime = ($vip_info['length'] * 30 * 86400) + time();
            $data = [
                'uid' => $user_id,
                'addtime' => time(),
                'endtime' => $endtime,
            ];
            $res3 = DI()->notorm->vip_user->insert($data);

            if ($res1 && $res2 && $res3){
                DI()->notorm->commit('db_appapi');
                delcache('vip_' . $user_id);
                return ['code' => 0, 'msg' => '购买成功'];
            }else{
                DI()->notorm->rollback('db_appapi');
                return ['code' => 1, 'msg' => '购买失败'];
            }
        }



    }

    /**
     * NEW我的等级
     * @desc 用于 获取用户等级
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return array info[0][consumption] 用户经验
     * @return array info[0][level] 用户等级
     * @return array info[0][level_info] 等级详情
     * @return string msg 提示信息
     */
    public function MyGrade()
    {
        $user_id = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($user_id,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $info = DI()->notorm->user
            ->select("id,consumption")
            ->where('id=?  and user_type = "2"', $user_id)
            ->fetchOne();
        if(!$info) return ['code' => 1, 'msg' => '未知错误请联系客服'];
        $info['level'] = getLevel($info['consumption']);

        $info['level_info'] = DI()->notorm->level
            ->where("levelid = ?",$info['level'])
            ->fetchOne();

        return ['code' => 1, 'msg' => '', 'info' => $info];
    }

    /**
     * NEW收支明细
     * @desc 用于 获取用户收支明细
     * @return int code 操作码，0表示成功， 1表示失败
     * @return int income_count 收入
     * @return int expenditure_count 支出
     * @return array info
     * @return array info[0][change_type] 类型名称
     * @return array info[0][change_money] 变动金额
     * @return array info[0][addtime] 时间
     * @return string msg 提示信息
     */
    public function IEDetailed()
    {
        $user_id = checkNull($this->uid);
        $type = checkNull($this->type);
        $change_type = checkNull($this->change_type);
        $m_time = checkNull($this->m_time);
        $d_time = checkNull($this->d_time);
        $token = checkNull($this->token);
        $checkToken=checkToken($user_id,$token);
        $page = $this->page;
        $page_size = $this->page_size;
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $time = "user_id = {$user_id} ";
        $m_time = isset($m_time) ? $m_time : '';
        $d_time = isset($d_time) ? $d_time : '';

        if($m_time != '' && $d_time != '') return ['code' => 1, 'msg' => '日期筛选和月份筛选不能同时选中'];

        if($m_time != ''){
            $start_time = strtotime(date( 'Y-m-1 00:00:00', $m_time ));
            $mdays = date( 't', $m_time );
            $end_time = strtotime(date( 'Y-m-' . $mdays . ' 23:59:59', $m_time ));
            $time .= "and addtime BETWEEN {$start_time} AND {$end_time} ";
        }


        if($d_time != '') {
            $start = strtotime(date('Y-m-d', $d_time));
            $end = strtotime(date('Y-m-d', $d_time)) + 86400;
            $time .= "and addtime BETWEEN $start AND $end ";
        }

        $change_type = isset($change_type) ? $change_type : '';
        if($change_type != '' && is_numeric($change_type)) {
            $time .= "and change_type = {$change_type} ";
        }

        $income_count = DI()->notorm->user_change
            ->where("$time")
            ->where('change_money > 0')
            ->sum('change_money');
        $expenditure_count = DI()->notorm->user_change
            ->where("$time")
            ->where('change_money < 0')
            ->sum('change_money');

        $where = '';
        switch ($type){
            case 0:
                $where = $time;
                break;
            case 1:
                $where = "change_money > 0 and $time";
                break;
            case 2:
                $where = "change_money < 0 and $time";
                break;
        }

        $user_changes = DI()->notorm->user_change
            ->select('id,change_type,change_money,addtime')
            ->where("$where")
            ->order('addtime desc')
            ->limit(($page - 1) * $page_size, $page_size)
            ->fetchAll();
        $user_changes_count = DI()->notorm->user_change
            ->where("$where")
            ->count();

        if($user_changes){
//            $change_type = [1 => '充值', 2 => '提现', 3 => '彩票', 4 => '补单', 5 => '会员管理转账', 6 => '优惠赠送', 7 => '返水', 8 => '额度转换'];
            $change_type = DI()->config->get('app.change_type');
            foreach ($user_changes as $k => $v){
                $user_changes[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
                $user_changes[$k]['change_type'] = $change_type[$v['change_type']];
            }
        }

        return [
            'code' => 0,
            'msg' => 'ok',
            'income_count' => $income_count,
            'expenditure_count' => $expenditure_count,
            'info' => $user_changes,
            'count' => $user_changes_count
        ];

    }

    /**
     * NEW用户反馈
     * @desc 用于 用户反馈
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function feedBackAdd()
    {
        $data['uid'] = checkNull($this->uid);
        $data['content'] = checkNull($this->content);
        $data['thumb'] = checkNull($this->thumb);
        $data['version'] = checkNull($this->version);
        $data['model'] = checkNull($this->model);
        $token = checkNull($this->token);
        $checkToken=checkToken($data['uid'],$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $day_start = strtotime(date('Y-m-d'));
        $day_end = strtotime(date('Y-m-d')) + 86400;
        $feedback = DI()->notorm->feedback
            ->where("uid = ? and addtime >= {$day_start} and addtime <= {$day_end}",$data['uid'])
            ->fetchOne();
        if($feedback) return ['code' => 1, 'msg' => '今天已反馈'];
        if(empty($data['content'])) return ['code' => 1, 'msg' => '请填写反馈内容'];

        $data['addtime'] = time();
        $res = DI()->notorm->feedback->insert($data);
        if($res) return ['code' => 0, 'msg' => '提交成功'];
        return ['code' => 1, 'msg' => '提交失败'];

    }

    /**
     * NEW用户银行列表
     * @desc 用于 获取用户银行列表
     * @return int code 操作码，0表示成功， 1表示用户不存在
     * @return array info
     * @return string msg 提示信息
     */
    public function getUserBankList() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $p = checkNull($this->p);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $domain = new Domain_User();
        $info = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if($p < 1){
            $p = 1;
        }
        $pnum = 50;
        $start = ($p-1) * $pnum;
        $list = DI()->notorm->user_bank
            ->select('id,bank_id,bank_card,addtime,status')
            ->where("u_id = ? and del_status = 0",$uid)
            ->order('addtime desc')
            ->limit($start,$pnum)
            ->fetchAll();
        foreach ($list as $k => $v){
            $bank = DI()->notorm->bank->where("id = ?",$v['bank_id'])->fetchOne();
            if($bank){
                $list[$k]['bank_name'] = $bank['bank_name'];
            }
        }
        $rs['info'] = $list;
        return $rs;
    }

    /**
     * NEW银行列表
     * @desc 用于 银行列表
     * @return int code 操作码，0表示成功， 1表示错误
     * @return array info
     * @return string msg 提示信息
     */
    public function getBankList()
    {
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);

        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $list = DI()->notorm->bank->fetchAll();

        return ['code' => 0, 'msg' => 'ok', 'info' => $list];
    }

    /**
     * NEW添加用户银行
     * @desc 用于 添加用户银行
     * @return int code 操作码，0表示成功， 1表示用户不存在
     * @return array info
     * @return string msg 提示信息
     */
    public function addUserBank() {
        $rs = array('code' => 0, 'msg' => '添加成功', 'info' => array());
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $data['name'] = checkNull($this->name);
        $data['bank_id'] = checkNull($this->bank_id);
        $data['bank_card'] = checkNull($this->bank_card);
        $data['outlets'] = checkNull($this->outlets);
        $data['status'] = checkNull($this->status);
        $checkToken=checkToken($uid,$token);

        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $domain = new Domain_User();
        $info = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(empty($data['name'])) return ['code' => 1, 'msg' => '请输入真实姓名'];
        if(empty($data['bank_id'])) return ['code' => 1, 'msg' => '请选择银行'];
        if(empty($data['bank_card'])) return ['code' => 1, 'msg' => '请输入银行卡号'];
        if(empty($data['outlets'])) return ['code' => 1, 'msg' => '请输入网点'];
        if(!empty($data['status']) && $data['status'] != 1) return ['code' => 1, 'msg' => '参数错误'];
        if(empty($data['status'])) unset($data['status']);

//        preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $data['bank_card'],$match);
//        unset($match[0]);
//        if(empty(implode(' ', $match))){
//            $rs['code'] = 1;
//            $rs['msg'] = '请输入正确银行卡号';
//            return $rs;
//        }
//        var_dump(check_bankCard($data['bank_card']));die;

        if(check_bankCard($data['bank_card']) == 2){
            $rs['code'] = 1;
            $rs['msg'] = '请输入正确银行卡号';
            return $rs;
        }

        $bank_user_info = DI()->notorm->user_bank->where("bank_card = ? and del_status = 0",$data['bank_card'])->fetchOne();
        if($bank_user_info) {
            $rs['code'] = 1;
            $rs['msg'] = '该银行已存在';
            return $rs;
        }

        $banks = DI()->notorm->user_bank->where("u_id = ?", $uid)->fetchAll();
        if(count($banks) >= 5) return ['code' => 1, 'msg' => '最多绑定五张银行卡'];

        $data['u_id'] = $uid;
        $data['addtime'] = time();
        if(!empty($data['bank_card'])) $data['last_card'] = substr($data['bank_card'],-4);

        $res = DI()->notorm->user_bank->insert($data);
        if($res) return $rs;
        $rs['code'] = 1;
        $rs['msg'] = '添加失败';
        return $rs;
    }

    /**
     * NEW是否设置资金密码
     * @desc 用于 是否设置资金密码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function isMoneyPasswd()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $info = DI()->notorm->user_info
            ->where("user_id = ?",$uid)
            ->fetchOne();
        if($info && $info['money_passwd']) {
            $rs['code'] = 1;
            $rs['msg'] = '已设置';
        }else{
            $rs['msg'] = '未设置';
        }
        return $rs;
    }

    /**
     * NEW设置资金密码
     * @desc 用于 设置资金密码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function setMoneyPasswd()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $data['money_passwd'] = checkNull($this->money_passwd);
        $data['con_money_passwd'] = checkNull($this->con_money_passwd);
        $token = checkNull($this->token);
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(empty($data['money_passwd'])) return ['code' => 1, 'msg' => '请输入密码'];
        if(empty($data['con_money_passwd'])) return ['code' => 1, 'msg' => '请输入确认密码'];
        if(strlen($data['money_passwd']) < 6 || strlen($data['money_passwd']) > 18) return ['code' => 1, 'msg' => '密码长度为6到18'];
        if(!preg_match("/^[A-Za-z_0-9]{1,}$/", $data['money_passwd'])) return ['code' => 1, 'msg' => '密码必须为字母数字下划线'];
        if($data['money_passwd'] != $data['con_money_passwd']) return ['code' => 1, 'msg' => '确认密码与原密码不一致'];

        $info = DI()->notorm->user_info
            ->where("user_id = ?",$uid)
            ->fetchOne();
        if($info['money_passwd']) return ['code' => 1, 'msg' => '已设置'];
        $money_passwd = password_hash($data['money_passwd'],PASSWORD_DEFAULT);

        if($info){
            $res = DI()->notorm->user_info
                ->where("user_id = ?",$uid)
                ->update(['money_passwd' => $money_passwd]);
        }else{
            $res = DI()->notorm->user_info
                ->where("user_id = ?",$uid)
                ->insert(['user_id' => $uid, 'money_passwd' => $money_passwd]);
        }

        if(!$res) return ['code' => 1, 'msg' => '添加失败'];
        $rs['msg'] = '添加成功';
        return $rs;
    }

    /**
     * NEW修改资金密码
     * @desc 用于 修改资金密码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function editMoneyPasswd()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $data['old_pass'] = checkNull($this->old_pass);
        $data['new_pass'] = checkNull($this->new_pass);
        $data['con_pass'] = checkNull($this->con_pass);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        $info = DI()->notorm->user_info
            ->where("user_id = ?",$uid)
            ->fetchOne();
        if(!$info || empty($info['money_passwd'])) return ['code' => 1, 'msg' => '请先设置密码'];

        if(empty($data['old_pass'])) return ['code' => 1, 'msg' => '请输入原密码'];
        if(empty($data['new_pass'])) return ['code' => 1, 'msg' => '请输入新密码'];
        if(empty($data['con_pass'])) return ['code' => 1, 'msg' => '请输入确认新密码'];
        if(strlen($data['old_pass']) < 6 || strlen($data['old_pass']) > 18) return ['code' => 1, 'msg' => '原密码长度为6到18'];
        if(strlen($data['new_pass']) < 6 || strlen($data['new_pass']) > 18) return ['code' => 1, 'msg' => '新密码长度为6到18'];
        if(strlen($data['con_pass']) < 6 || strlen($data['con_pass']) > 18) return ['code' => 1, 'msg' => '确认新密码长度为6到18'];
        if(!preg_match("/^[A-Za-z_0-9]{1,}$/", $data['old_pass'])) return ['code' => 1, 'msg' => '原密码必须为字母数字下划线'];
        if(!preg_match("/^[A-Za-z_0-9]{1,}$/", $data['new_pass'])) return ['code' => 1, 'msg' => '新密码必须为字母数字下划线'];
        if(!preg_match("/^[A-Za-z_0-9]{1,}$/", $data['con_pass'])) return ['code' => 1, 'msg' => '确认新密码必须为字母数字下划线'];
        if($data['new_pass'] != $data['con_pass']) return ['code' => 1, 'msg' => '确认新密码与新密码不一致'];


        if (!password_verify($data['old_pass'], $info['money_passwd'])) return ['code' => 1, 'msg' => '原密码错误'];
        $password = password_hash($data['new_pass'], PASSWORD_DEFAULT);
        $res = DI()->notorm->user_info
            ->where("user_id = ?",$uid)
            ->update(['money_passwd' => $password]);
        if(!$res) return ['code' => 1, 'msg' => '更新失败'];
        $rs['msg'] = '更新成功';
        return $rs;
    }


    /**
     * NEW获取微信收款码
     * @desc 用于 获取微信收款码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function getWxPay()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        $info = DI()->notorm->user_info
            ->select('wxpay_img,wxpay_account')
            ->where("user_id = ?",$uid)
            ->fetchOne();
        if(!$info) return ['code' => 1, 'msg' => '未配置收款'];
        $rs['msg'] = '获取成功';
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * NEW上传图片
     * @desc 用于 上传图片
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].img 图片
     * @return string list[0].img_thumb 缩略图
     * @return string msg 提示信息
     */
    public function setImg() {
        $rs = array('code' => 0 , 'msg' => '', 'info' => array());

        $checkToken=checkToken($this->uid,$this->token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if (!isset($_FILES['file'])) {
            $rs['code'] = 1001;
            $rs['msg'] = "请选择上传文件";
            return $rs;
        }

        if ($_FILES["file"]["error"] > 0) {
            $rs['code'] = 1002;
            $rs['msg']='上传失败'.$_FILES["file"]["error"];
            //$rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
            DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
            return $rs;
        }

        $uptype=DI()->config->get('app.uptype');

        if($uptype == 1){
            //七牛
            $url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

            if (!empty($url)) {
                $avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
                $avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200

                $data=array(
                    "img"=>$avatar,
                    "img_thumb"=>$avatar_thumb,
                );

                $data2=array(
                    "img"=>$avatar,
                    "img_thumb"=>$avatar_thumb,
                );


                /* 统一服务器 格式 */
                /* $space_host= DI()->config->get('app.Qiniu.space_host');
                $avatar2=str_replace($space_host.'/', "", $avatar);
                $avatar_thumb2=str_replace($space_host.'/', "", $avatar_thumb);
                $data2=array(
                    "avatar"=>$avatar2,
                    "avatar_thumb"=>$avatar_thumb2,
                ); */
            }
        }else if($uptype == 2){
            //本地上传
            //设置上传路径 设置方法参考3.2
            DI()->ucloud->set('save_path','image/'.date("Ymd"));

            //新增修改文件名设置上传的文件名称
            // DI()->ucloud->set('file_name', $this->uid);

            //上传表单名
            $res = DI()->ucloud->upfile($_FILES['file']);

            $files='../upload'.$res['file'];
            $newfiles=str_replace(".png","_thumb.png",$files);
            $newfiles=str_replace(".jpg","_thumb.jpg",$newfiles);
            $newfiles=str_replace(".gif","_thumb.gif",$newfiles);
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

            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg

            $PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($files);

            $PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($newfiles);

            $avatar=  '/upload'.$res['file']; //600 X 600

            $avatar_thumb=str_replace(".png","_thumb.png",$avatar);
            $avatar_thumb=str_replace(".jpg","_thumb.jpg",$avatar_thumb);
            $avatar_thumb=str_replace(".gif","_thumb.gif",$avatar_thumb);

            $data=array(
                "img"=>get_upload_path($avatar),
                "img_thumb"=>get_upload_path($avatar_thumb),
            );

            $data2=array(
                "img"=>$avatar,
                "img_thumb"=>$avatar_thumb,
            );

        }

        @unlink($_FILES['file']['tmp_name']);
        if(!$data){
            $rs['code'] = 1003;
            $rs['msg'] = '更换失败，请稍候重试';
            return $rs;
        }
        /* 清除缓存 */
        delCache("userinfo_".$this->uid);

        $rs['msg'] = '获取成功';
        $rs['info'][0] = $data2;

        return $rs;

    }

    /**
     * NEW修改微信收款码
     * @desc 用于 修改微信收款码
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function upWxPay()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $data['wxpay_img'] = checkNull($this->wxpay_img);
        $data['wxpay_account'] = checkNull($this->wxpay_account);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        if(empty($data['wxpay_img'])) return ['code' => 1, 'msg' => '请选择收款码'];
        if(empty($data['wxpay_account'])) return ['code' => 1, 'msg' => '请输入收款账号'];

        $res = DI()->notorm->user_info
            ->where("user_id = ?",$uid)
            ->update(['wxpay_img' => $data['wxpay_img'], 'wxpay_account' => $data['wxpay_account']]);
        if(!$res) return ['code' => 1, 'msg' => '更新失败'];
        $rs['msg'] = '更新成功';
        return $rs;
    }

    /**
     * NEW提现
     * @desc 用于 用户提现
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return string msg 提示信息
     */
    public function withdraw()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $data['money'] = checkNull($this->money);
        $money_pass = checkNull($this->money_pass);
        $data['withdraw_type'] = checkNull($this->withdraw_type);
        $data['withdraw_id'] = checkNull($this->withdraw_id);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        $user_info = DI()->notorm->user_info->where("user_id = ?",$uid)->fetchOne();
        if(!$user_info || !$user_info['money_passwd']) return ['code' => 1, 'msg' => '请先完善资金密码信息'];
        if(!password_verify($money_pass, $user_info['money_passwd'])) return ['code' => 1, 'msg' => '资金密码错误'];

        if(empty($data['money'])) return ['code' => 1, 'msg' => '请输入金额'];
        if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/", $data['money'])) return ['code' => 1, 'msg' => '金额格式错误'];
        if(empty($data['withdraw_type'])) return ['code' => 1, 'msg' => '请选择提现方式'];
        if(!preg_match("/^[1-2]$/", $data['withdraw_type'])) return ['code' => 1, 'msg' => '提现方式错误'];

        $userinfo = DI()->notorm->user
            ->where("id = ?",$uid)
            ->select('coin as user_money')
            ->fetchOne();
            var_dump($userinfo);
        if ($userinfo['user_money'] < $data['money']) return ['code' => 1, 'msg' => '余额不足'];

        //获取提现比例
        $configpri = DI()->notorm->option->where('option_name = ?', 'configpri')->fetchOne();
        $configpri = json_decode($configpri['option_value'], true);
        $procedures = $configpri['withdrawal_procedures'];
        // var_dump($procedures);
        if($data['money'] < $configpri['cash_min']) return ['code' => 1, 'msg' => '小于最小提现额度'];

        //手续费
        $procedures_money = ($procedures/100) * $data['money'];
        $procedures_money = number_format($procedures_money, 4);
        // var_dump($procedures_money);
        //扣除手续费的真实提现金额
        $withdraw_money = $data['money'] - $procedures_money;

        $ins_data = [
            'user_id' => $uid,
            'change_type' => 2,
            'remark' => '提现申请',
            'money' => $userinfo['user_money'],
            'next_money' => $userinfo['user_money'] - $data['money'],
            'change_money' => -1 * $withdraw_money,
            'service_charge' => -1 * $procedures_money,
            'addtime' => time(),
            'withdraw_type' => $data['withdraw_type'],
            'status' => 2,
        ];
        // var_dump($ins_data);die;
        if ($data['withdraw_type'] == 1){
            if(empty($data['withdraw_id'])) return ['code' => 1, 'msg' => '请输入提现去处'];
            $user_bank = DI()->notorm->user_bank
                ->where('id = ? and u_id = ? and del_status = 0', $data['withdraw_id'], $uid)
                ->fetchOne();
            $bank_info = DI()->notorm->bank->where('id=?',$user_bank['bank_id'])->fetchOne();
            if(!$user_bank || !$bank_info) return ['code' => 1, 'msg' => '所选银行不存在'];
            $ins_data['withdraw_id'] = $data['withdraw_id'];
            $ins_data['bank_name'] = $bank_info['bank_name'];
            $ins_data['bank_card'] = $user_bank['bank_card'];
            $ins_data['real_name'] = $user_bank['name'];
        }else{
            return ['code' => 1, 'msg' => '暂不支持微信提现'];
            if(!$user_info['wxpay_account']) return ['code' => 1, 'msg' => '请完善微信提现账户'];
            $ins_data['withdraw_id'] = $user_info['id'];
        }
        $up_data = [
            'coin' => $userinfo['user_money'] - $data['money'],
            'freeze_money' => $userinfo['freeze_money'] + $data['money']
        ];
        var_dump($userinfo['freeze_money']);
        var_dump($data['money']);
        var_dump($up_data);die;
        //开启事务
        DI()->notorm->beginTransaction('db_appapi');

        $res1 = DI()->notorm->user_change->insert($ins_data);
        $up_data = [
            'coin' => $userinfo['user_money'] - $data['money'],
            'freeze_money' => $userinfo['freeze_money'] + $data['money']
        ];
        $res2 = DI()->notorm->user->where("id = ?",$uid)->update($up_data);
        if ($res1 && $res2){
            DI()->notorm->commit('db_appapi');
            
            $message = "@chendan777 @zuanshi6688 @tiane36 @caiwukefu66 \n天鹅提现通知: \n 用户id:$uid \n 提现金额:$withdraw_money \n 客服尽快处理！！！！" ;
            
            $this->telegram($message);
            
            
            $rs['msg'] = '提现成功提交，等待审核';
            return $rs;
        }else{
            DI()->notorm->rollback('db_appapi');
            $rs['msg'] = '提现异常';
            $rs['code'] = 1001;
            return $rs;
        }

    }

    /**
     * NEW获取提现记录
     * @desc 用于 获取提现记录
     * @return int code 操作码，0表示成功， 1表示失败
     * @return array info
     * @return array info【‘status’】 1：已审核，2：审核中，3：拒绝
     * @return string msg 提示信息
     */
    public function withdrawList()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid = checkNull($this->uid);
        $p = checkNull($this->p);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700) return ['code' => $checkToken, 'msg' => '您的登陆状态失效，请重新登陆！'];

        if($p < 1){
            $p = 1;
        }
        $pnum = 50;
        $start = ($p-1) * $pnum;
        $withdraw_list = DI()->notorm->user_change
            ->select('change_money,addtime,withdraw_type,withdraw_id,status')
            ->where("user_id = ? and change_type = 2",$uid)
            ->order('addtime desc')
            ->limit($start,$pnum)
            ->fetchAll();

        foreach ($withdraw_list as $k => $v){
            if ($v['withdraw_type'] == 1){
                $temp = DI()->notorm->user_bank
                    ->select('bank_id,last_card')
                    ->where("id = ?",$v['withdraw_id'])
                    ->fetchOne();
                $bank = DI()->notorm->bank->select('bank_name')->where("id = ?",$temp['bank_id'])->fetchOne();
                $withdraw_list[$k]['name'] = $bank['bank_name'].'（尾号：'.$temp['last_card'].'）';
            }else{
                $withdraw_list[$k]['name'] = '微信提现';
            }
            unset($withdraw_list[$k]['withdraw_type']);
            unset($withdraw_list[$k]['withdraw_id']);
            $withdraw_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
        }

        $rs['info'] = $withdraw_list;
        $rs['msg'] = 'ok';
        return $rs;
    }


	/**
	 * 判断token
	 * @desc 用于判断token
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function iftoken() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		return $rs;
	}
	/**
	 * 获取用户信息
	 * @desc 用于获取单个用户基本信息
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return array info[0] 用户信息
	 * @return int info[0].id 用户ID
	 * @return string info[0].level 等级
	 * @return string info[0].lives 直播数量
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].vip['type'] type : 0-过期 1-是VIP
	 * @return string info[0].vip['endtime'] 当type=1时才会有此字段
	 * @return string info[0].agent_switch 分销开关
	 * @return string info[0].family_switch 家族开关
	 * @return string msg 提示信息
	 */
	public function getBaseInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$info = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
        }

        $family_user = DI()->notorm->family_user->where('uid', $info['id'])->fetchOne();
        $patriarch = DI()->notorm->family->where('uid',$info['id'])->fetchOne();

        if ($family_user){
            $finfo = DI()->notorm->family->where('id', $family_user['familyid'])->fetchOne();
            $info['family_id'] = $family_user['familyid'];
            $info['family_name'] = $finfo['name'];
        }else if($patriarch){
            $info['family_id'] = $patriarch['id'];
            $info['family_name'] = $patriarch['name'];
        } else{
            $info['family_id'] = '';
            $info['family_name'] = '';
        }
		
		$configpri=getConfigPri();

		$configpub=getConfigPub();
		$agent_switch=$configpri['agent_switch'];
		$family_switch=$configpri['family_switch'];
		$service_switch=$configpri['service_switch'];
		$service_url=$configpri['service_url'];
		$ios_shelves=$configpub['ios_shelves'];
		
		$info['agent_switch']=$agent_switch;
		$info['family_switch']=$family_switch;




		/* 个人中心菜单 */
		$version_ios=$this->version_ios;
		$list=array();
		$list1=array();
		$list2=array();
		$list3=array();
		$shelves=1;
		if($version_ios && $version_ios==$ios_shelves){
			$agent_switch=0;
			$family_switch=0;
			$shelves=0;
		}

//        $list1[]=array('id'=>'19','name'=>'我的视频','thumb'=>get_upload_path("/static/appapi/images/personal/video.png"),'href'=>'' );
//
//        $list1[]=array('id'=>'23','name'=>'我的动态','thumb'=>get_upload_path("/static/appapi/images/personal/dymic.png"),'href'=>'' );
//		if($shelves){
//			$list1[]=array('id'=>'1','name'=>'我的收益','thumb'=>get_upload_path("/static/appapi/images/personal/votes.png"),'href'=>'' );
//		}
//
		//$list1[]=array('id'=>'2','name'=>'我的'.$configpub['name_coin'],'thumb'=>get_upload_path("/static/appapi/images/personal/coin.png") ,'href'=>'');
//		$list1[]=array('id'=>'3','name'=>'我的等级','thumb'=>get_upload_path("/static/appapi/images/personal/level.png") ,'href'=>get_upload_path("/Appapi/Level/index"));
//
//        $list1[]=array('id'=>'11','name'=>'我的认证','thumb'=>get_upload_path("/static/appapi/images/personal/auth.png") ,'href'=>get_upload_path("/Appapi/Auth/index"));
//
//		$list1[]=array('id'=>'26','name'=>'我的收藏','thumb'=>get_upload_path("/static/appapi/images/personal/collect.png") ,'href'=>'');
//
//
//        $list1[]=array('id'=>'25','name'=>'每日任务','thumb'=>get_upload_path("/static/appapi/images/personal/renwu.png") ,'href'=>'');
//
//
//        $list1[]=array('id'=>'22','name'=>$configpri['shop_system_name'],'thumb'=>get_upload_path("/static/appapi/images/personal/shop.png?t=1") ,'href'=>'' ); //我的小店
//
//        $list1[]=array('id'=>'24','name'=>'付费内容','thumb'=>get_upload_path("/static/appapi/images/personal/pay.png") ,'href'=>'' );
//
        
//        $list2[]=array('id'=>'20','name'=>'房间管理','thumb'=>get_upload_path("/static/appapi/images/personal/room.png") ,'href'=>'');
//		if($shelves){
//			$list1[]=array('id'=>'14','name'=>'我的明细','thumb'=>get_upload_path("/static/appapi/images/personal/detail.png") ,'href'=>get_upload_path("/Appapi/Detail/index"));
//			$list2[]=array('id'=>'4','name'=>'在线商城','thumb'=>get_upload_path("/static/appapi/images/personal/shop.png") ,'href'=>get_upload_path("/Appapi/Mall/index"));
//			$list2[]=array('id'=>'5','name'=>'装备中心','thumb'=>get_upload_path("/static/appapi/images/personal/equipment.png") ,'href'=>get_upload_path("/Appapi/Equipment/index"));
//		}

		if($family_switch){
			$list2[]=array('id'=>'7','name'=>'家族中心','thumb'=>get_upload_path("/static/appapi/images/personal/family.png") ,'href'=>get_upload_path("/Appapi/Family/index2"));
			$list2[]=array('id'=>'8','name'=>'家族驻地','thumb'=>get_upload_path("/static/appapi/images/personal/family2.png") ,'href'=>get_upload_path("/Appapi/Family/home"));
		}
//
//        if($service_switch && $service_url){
//           $list3[]=array('id'=>'21','name'=>'在线客服(Beta)','thumb'=>get_upload_path("/static/appapi/images/personal/kefu.png") ,'href'=>$service_url);
//        }
//
		//$list[]=array('id'=>'12','name'=>'关于我们','thumb'=>get_upload_path("/static/appapi/images/personal/about.png") ,'href'=>get_upload_path("/portal/page/lists"));
//		$list3[]=array('id'=>'13','name'=>'个性设置','thumb'=>get_upload_path("/static/appapi/images/personal/set.png") ,'href'=>'');

        $list1[]=array('id'=>'1','name'=>'账户记录','thumb'=>get_upload_path("/static/appapi/images/personal/detail.png"),'href'=>'' );
        $list1[]=array('id'=>'2','name'=>'投注记录','thumb'=>get_upload_path("/static/appapi/images/personal/dymic.png"),'href'=>'' );
        $list1[]=array('id'=>'3','name'=>'开奖历史','thumb'=>get_upload_path("/static/appapi/images/personal/votes.png"),'href'=>'' );
        $list2[]=array('id'=>'4','name'=>'我的关注','thumb'=>get_upload_path("/static/appapi/images/personal/level.png"),'href'=>'' );
        $list2[]=array('id'=>'5','name'=>'代理统计','thumb'=>get_upload_path("/static/appapi/images/personal/auth.png"),'href'=>'' );
        $list2[]=array('id'=>'6','name'=>'房间管理','thumb'=>get_upload_path("/static/appapi/images/personal/room.png") ,'href'=>'');
        $list3[]=array('id'=>'11','name'=>'优惠活动','thumb'=>get_upload_path("/static/appapi/images/personal/renwu.png") ,'href'=>'');
        if($service_switch && $service_url){
           $list3[]=array('id'=>'7','name'=>'在线客服(Beta)','thumb'=>get_upload_path("/static/appapi/images/personal/kefu.png") ,'href'=>$service_url);
        }
        $list3[]=array('id'=>'10','name'=>'帮助中心','thumb'=>get_upload_path("/static/appapi/images/personal/collect.png"),'href'=> 'https://'. $_SERVER['HTTP_HOST'] .'/portal/page/index?id=45' );
        $list3[]=array('id'=>'9','name'=>'设置','thumb'=>get_upload_path("/static/appapi/images/personal/set.png") ,'href'=>'');
        if($agent_switch){
            $list3[]=array('id'=>'8','name'=>'邀请奖励','thumb'=>get_upload_path("/static/appapi/images/personal/agent.png") ,'href'=>get_upload_path("/Appapi/Agent/index"));
		}

        $list[]=$list1;
        $list[]=$list2;
        $list[]=$list3;
		$info['list']=$list;
		$rs['info'][0] = $info;

		return $rs;
	}

    /**
     * 背景上传 (七牛)
     * @desc 用于用户修改背景
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].avatar 用户主头像
     * @return string list[0].avatar_thumb 用户头像缩略图
     * @return string msg 提示信息
     */
    public function updateBackImg() {
        $rs = array('code' => 0 , 'msg' => '设置背景成功', 'info' => array());

        $checkToken=checkToken($this->uid,$this->token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if (!isset($_FILES['file'])) {
            $rs['code'] = 1001;
            $rs['msg'] = "请选择上传文件";
            return $rs;
        }

        if ($_FILES["file"]["error"] > 0) {
            $rs['code'] = 1002;
            $rs['msg']='上传失败'.$_FILES["file"]["error"];
            //$rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
            DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
            return $rs;
        }

        $uptype=DI()->config->get('app.uptype');

        if($uptype==1){
            //七牛
            $url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

            if (!empty($url)) {
                $avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
                $avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200
                $data=array(
                    "avatar"=>$avatar,
                    "avatar_thumb"=>$avatar_thumb,
                );

                $data2=array(
                    "avatar"=>$avatar,
                    "avatar_thumb"=>$avatar_thumb,
                );


                /* 统一服务器 格式 */
                /* $space_host= DI()->config->get('app.Qiniu.space_host');
                $avatar2=str_replace($space_host.'/', "", $avatar);
                $avatar_thumb2=str_replace($space_host.'/', "", $avatar_thumb);
                $data2=array(
                    "avatar"=>$avatar2,
                    "avatar_thumb"=>$avatar_thumb2,
                ); */
            }
        }else if($uptype==2){
            //本地上传
            //设置上传路径 设置方法参考3.2
            DI()->ucloud->set('save_path','avatar/'.date("Ymd"));

            //新增修改文件名设置上传的文件名称
            // DI()->ucloud->set('file_name', $this->uid);

            //上传表单名
            $res = DI()->ucloud->upfile($_FILES['file']);

            $files='../upload'.$res['file'];
            $newfiles=str_replace(".png","_thumb.png",$files);
            $newfiles=str_replace(".jpg","_thumb.jpg",$newfiles);
            $newfiles=str_replace(".gif","_thumb.gif",$newfiles);
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

            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg

            $PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($files);

            $PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($newfiles);

            $avatar=  '/upload'.$res['file']; //600 X 600

            $avatar_thumb=str_replace(".png","_thumb.png",$avatar);
            $avatar_thumb=str_replace(".jpg","_thumb.jpg",$avatar_thumb);
            $avatar_thumb=str_replace(".gif","_thumb.gif",$avatar_thumb);

            $data=array(
                "avatar"=>get_upload_path($avatar),
                "avatar_thumb"=>get_upload_path($avatar_thumb),
            );

            $data2=array(
                "avatar"=>$avatar,
                "avatar_thumb"=>$avatar_thumb,
            );

        }

        @unlink($_FILES['file']['tmp_name']);
        if(!$data){
            $rs['code'] = 1003;
            $rs['msg'] = '更换失败，请稍候重试';
            return $rs;
        }
        /* 清除缓存 */
        delCache("userinfo_".$this->uid);

        $info = DI()->notorm->user->where('id', $this->uid)->update(['back_img' => $data2['avatar']]);

        $rs['info'][0] = $data;

        return $rs;

    }

	/**
	 * 头像上传 (七牛)
	 * @desc 用于用户修改头像
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].avatar 用户主头像
	 * @return string list[0].avatar_thumb 用户头像缩略图
	 * @return string msg 提示信息
	 */
	public function updateAvatar() {
		$rs = array('code' => 0 , 'msg' => '设置头像成功', 'info' => array());

		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if (!isset($_FILES['file'])) {
			$rs['code'] = 1001;
			$rs['msg'] = "请选择上传文件";
			return $rs;
		}

		if ($_FILES["file"]["error"] > 0) {
			$rs['code'] = 1002;
			$rs['msg']='上传失败'.$_FILES["file"]["error"];
			//$rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
			DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
			return $rs;
		}

		$uptype=DI()->config->get('app.uptype');

		if($uptype==1){
			//七牛
			$url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

			if (!empty($url)) {
				$avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
				$avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200
				$data=array(
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);
                
                $data2=array(
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);

				
				/* 统一服务器 格式 */
				/* $space_host= DI()->config->get('app.Qiniu.space_host');
				$avatar2=str_replace($space_host.'/', "", $avatar);
				$avatar_thumb2=str_replace($space_host.'/', "", $avatar_thumb);
				$data2=array(
					"avatar"=>$avatar2,
					"avatar_thumb"=>$avatar_thumb2,
				); */
			}
		}else if($uptype==2){
			//本地上传
			//设置上传路径 设置方法参考3.2
			DI()->ucloud->set('save_path','avatar/'.date("Ymd"));

			//新增修改文件名设置上传的文件名称
		   // DI()->ucloud->set('file_name', $this->uid);

			//上传表单名
			$res = DI()->ucloud->upfile($_FILES['file']);
			
			$files='../upload'.$res['file'];
			$newfiles=str_replace(".png","_thumb.png",$files);
			$newfiles=str_replace(".jpg","_thumb.jpg",$newfiles);
			$newfiles=str_replace(".gif","_thumb.gif",$newfiles); 
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

			// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
			
			$PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
			$PhalApi_Image->save($files);

			$PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
			$PhalApi_Image->save($newfiles);			
			
			$avatar=  '/upload'.$res['file']; //600 X 600
			
			$avatar_thumb=str_replace(".png","_thumb.png",$avatar);
			$avatar_thumb=str_replace(".jpg","_thumb.jpg",$avatar_thumb);
			$avatar_thumb=str_replace(".gif","_thumb.gif",$avatar_thumb);

			$data=array(
				"avatar"=>get_upload_path($avatar),
				"avatar_thumb"=>get_upload_path($avatar_thumb),
			);
            
            $data2=array(
				"avatar"=>$avatar,
				"avatar_thumb"=>$avatar_thumb,
			);
			
		}
		
		@unlink($_FILES['file']['tmp_name']);
        if(!$data){
            $rs['code'] = 1003;
			$rs['msg'] = '更换失败，请稍候重试';
			return $rs;
        }
		/* 清除缓存 */
		delCache("userinfo_".$this->uid);
		
		$domain = new Domain_User();
		$info = $domain->userUpdate($this->uid,$data2);

		$rs['info'][0] = $data;

		return $rs;

	}
	
	/**
	 * 修改用户信息
	 * @desc 用于修改用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].msg 修改成功提示信息 
	 * @return string msg 提示信息
	 */
	public function updateFields() {
		$rs = array('code' => 0, 'msg' => '修改成功', 'info' => array());
		
		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$fields=json_decode($this->fields,true);
    
        $allow=['user_nicename','sex','signature','birthday','bat','qq','wechat'];
		$domain = new Domain_User();
		foreach($fields as $k=>$v){
            if(in_array($k,$allow)){
                if($fields[$k])
                {
                    $fields[$k]=checkNull($v);
                }else{
                    unset($fields[$k]);
                }
                
            }else{
                unset($fields[$k]);
            }
			
		}
		
		
		if(array_key_exists('user_nicename', $fields)){
			if($fields['user_nicename']==''){
				$rs['code'] = 1002;
				$rs['msg'] = '昵称不能为空';
				return $rs;
			}
			$isexist = $domain->checkName($this->uid,$fields['user_nicename']);
			if(!$isexist){
				$rs['code'] = 1002;
				$rs['msg'] = '昵称重复，请修改';
				return $rs;
			}



			if(strstr($fields['user_nicename'], '已注销')!==false){ //昵称包含已注销三个字
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}

			if(mb_substr($fields['user_nicename'], 0,1)=='='){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}

        
			//$fields['user_nicename']=filterField($fields['user_nicename']);
            $sensitivewords=sensitiveField($fields['user_nicename']);
			if($sensitivewords==1001){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}
		}
		if(array_key_exists('signature', $fields)){
			$sensitivewords=sensitiveField($fields['signature']);
			if($sensitivewords==1001){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}
		}
        
        if(array_key_exists('birthday', $fields)){
            $sensitivewords=strtotime($fields['birthday']);
            if($sensitivewords==1001){
                $rs['code'] = 10011;
                $rs['msg'] = '输入非法，请重新输入';
                return $rs;
            }
		}
		if(array_key_exists('bat', $fields)){
            $sensitivewords=strtotime($fields['bat']);
            if($sensitivewords==1001){
                $rs['code'] = 10011;
                $rs['msg'] = '输入非法，请重新输入';
                return $rs;
            }
		}
		if(array_key_exists('qq', $fields)){
            $sensitivewords=strtotime($fields['qq']);
            if($sensitivewords==1001){
                $rs['code'] = 10011;
                $rs['msg'] = '输入非法，请重新输入';
                return $rs;
            }
		}
		if(array_key_exists('wechat', $fields)){
            $sensitivewords=strtotime($fields['wechat']);
            if($sensitivewords==1001){
                $rs['code'] = 10011;
                $rs['msg'] = '输入非法，请重新输入';
                return $rs;
            }
		}

		$info = $domain->userUpdate($this->uid,$fields);
	 
		if($info===false){
			$rs['code'] = 1001;
			$rs['msg'] = '修改失败';
			return $rs;
		}
		/* 清除缓存 */
		delCache("userinfo_".$this->uid);
		$rs['info'][0]['msg']='修改成功';
		return $rs;
	}

	/**
	 * 修改密码
	 * @desc 用于修改用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].msg 修改成功提示信息
	 * @return string msg 提示信息
	 */
	public function updatePass() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=$this->token;
		$oldpass=$this->oldpass;
		$pass=$this->pass;
		$pass2=$this->pass2;
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		if($pass != $pass2){
			$rs['code'] = 1002;
			$rs['msg'] = '两次新密码不一致';
			return $rs;
		}
		
		$check = passcheck($pass);
		if(!$check ){
			$rs['code'] = 1004;
			$rs['msg'] = '密码为6-20位字母数字组合';
			return $rs;										
		}
		
		$domain = new Domain_User();
		$info = $domain->updatePass($uid,$oldpass,$pass);
	 
		if($info==1003){
			$rs['code'] = 1003;
			$rs['msg'] = '旧密码错误';
			return $rs;
		}else if($info===false){
			$rs['code'] = 1001;
			$rs['msg'] = '修改失败';
			return $rs;
		}

		$rs['info'][0]['msg']='修改成功';
		return $rs;
	}	
	
	/**
	 * 我的钻石
	 * @desc 用于获取用户余额,充值规则 支付方式信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 用户钻石余额
	 * @return array info[0].rules 充值规则
	 * @return string info[0].rules[].id 充值规则
	 * @return string info[0].rules[].coin 钻石
	 * @return string info[0].rules[].money 价格
	 * @return string info[0].rules[].money_ios 苹果充值价格
	 * @return string info[0].rules[].product_id 苹果项目ID
	 * @return string info[0].rules[].give 赠送钻石，为0时不显示赠送
	 * @return string info[0].aliapp_switch 支付宝开关，0表示关闭，1表示开启
	 * @return string info[0].aliapp_partner 支付宝合作者身份ID
	 * @return string info[0].aliapp_seller_id 支付宝帐号	
	 * @return string info[0].aliapp_key_android 支付宝安卓密钥
	 * @return string info[0].aliapp_key_ios 支付宝苹果密钥
	 * @return string info[0].wx_switch 微信支付开关，0表示关闭，1表示开启
	 * @return string info[0].wx_appid 开放平台账号AppID
	 * @return string info[0].wx_appsecret 微信应用appsecret
	 * @return string info[0].wx_mchid 微信商户号mchid
	 * @return string info[0].wx_key 微信密钥key
	 * @return string msg 提示信息
	 */
	public function getBalance() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $type=checkNull($this->type);
        $version_ios=checkNull($this->version_ios);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_User();
		$info = $domain->getBalance($uid);
		
		$key='getChargeRules';
		$rules=getcaches($key);
		if(!$rules){
			$rules= $domain->getChargeRules();
			setcaches($key,$rules);
		}
		$info['rules'] =$rules;
		
		$configpub=getConfigPub();
		$configpri=getConfigPri();
		
		$aliapp_switch=$configpri['aliapp_switch'];
		
		$info['aliapp_switch']=$aliapp_switch;
		$info['aliapp_partner']=$aliapp_switch==1?$configpri['aliapp_partner']:'';
		$info['aliapp_seller_id']=$aliapp_switch==1?$configpri['aliapp_seller_id']:'';
		$info['aliapp_key_android']=$aliapp_switch==1?$configpri['aliapp_key_android']:'';
		$info['aliapp_key_ios']=$aliapp_switch==1?$configpri['aliapp_key_ios']:'';

        $wx_switch=$configpri['wx_switch'];
		$info['wx_switch']=$wx_switch;
		$info['wx_appid']=$wx_switch==1?$configpri['wx_appid']:'';
		$info['wx_appsecret']=$wx_switch==1?$configpri['wx_appsecret']:'';
		$info['wx_mchid']=$wx_switch==1?$configpri['wx_mchid']:'';
		$info['wx_key']=$wx_switch==1?$configpri['wx_key']:'';
		
        $aliscan_switch=$configpri['aliscan_switch'];

        $wx_mini_switch=$configpri['wx_mini_switch'];
        $info['wx_mini_switch']=$wx_mini_switch;

        /* 支付列表 */
        $shelves=1;
        $ios_shelves=$configpub['ios_shelves'];
        if($version_ios && $version_ios==$ios_shelves){
			$shelves=0;
		}
        
        $paylist=[];
        
        if($aliapp_switch && $shelves){
            $paylist[]=[
                'id'=>'ali',
                'name'=>'支付宝支付',
                'thumb'=>get_upload_path("/static/app/pay/ali.png"),
                'href'=>'',
            ];
        }
        
        if($wx_switch && $shelves){
            $paylist[]=[
                'id'=>'wx',
                'name'=>'微信支付',
                'thumb'=>get_upload_path("/static/app/pay/wx.png"),
                'href'=>'',
            ];
        }
        
        // if($aliscan_switch && $shelves){
            // $paylist[]=[
                // 'id'=>'2',
                // 'name'=>'当面付',
                // 'thumb'=>get_upload_path("/static/app/pay/ali.png"),
                // 'href'=>get_upload_path("/appapi/aliscan/index"),
            // ];
        // }
        
        if($shelves==0 && $type==1){
            $paylist[]=[
                'id'=>'apple',
                'name'=>'苹果支付',
                'thumb'=>get_upload_path("/static/app/pay/apple.png"),
                'href'=>'',
            ];
        }
        
        /* $paylist[]=[
                'id'=>'1',
                'name'=>'测试1',
                'thumb'=>get_upload_path("/static/app/pay/apple.png"),
                'href'=>'https://livenew.yunbaozb.com/portal/page/index?id=31',
            ]; */
        
        $info['paylist'] =$paylist;
        $info['tip_t'] =$configpub['name_coin'].'/'.$configpub['name_score'].'说明:';
        $info['tip_d'] =$configpub['name_coin'].'可通过平台提供的支付方式进行充值获得，'.$configpub['name_coin'].'适用于平台内所有消费； '.$configpub['name_score'].'可通过直播间内游戏奖励获得，所得'.$configpub['name_score'].'可用于平台商城内兑换会员、坐 骑、靓号等服务，不可提现。';
        
        
     
		$rs['info'][0]=$info;
		return $rs;
	}
	
	/**
	 * 我的收益
	 * @desc 用于获取用户收益，包括可体现金额，今日可提现金额
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votes 可提取映票数
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].cash_rate 映票兑换比例
	 * @return string info[0].total 可体现金额
	 * @return string info[0].tips 温馨提示
	 * @return string msg 提示信息
	 */
	public function getProfit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
		
		$domain = new Domain_User();
		$info = $domain->getProfit($this->uid);
	 
		$rs['info'][0]=$info;
		return $rs;
	}	
	
	/**
	 * 用户提现
	 * @desc 用于进行用户提现
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 提现成功信息
	 * @return string msg 提示信息
	 */
	public function setCash() {
		$rs = array('code' => 0, 'msg' => '提现成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);		
        $accountid=checkNull($this->accountid);		
        $cashvote=checkNull($this->cashvote);		
        
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择提现账号';
			return $rs;
        }
        
        if(!$cashvote){
            $rs['code'] = 1002;
			$rs['msg'] = '请输入有效的提现票数';
			return $rs;
        }
		
        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'cashvote'=>$cashvote,
        );
        $config=getConfigPri();
		$domain = new Domain_User();
		$info = $domain->setCash($data);
		if($info==1001){
			$rs['code'] = 1001;
			$rs['msg'] = '您输入的金额大于可提现金额';
			return $rs;
		}else if($info==1003){
			$rs['code'] = 1003;
			$rs['msg'] = '请先进行身份认证';
			return $rs;
		}else if($info==1004){
			$rs['code'] = 1004;
			$rs['msg'] = '提现最低额度为'.$config['cash_min'].'元';
			return $rs;
		}else if($info==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '不在提现期限内，不能提现';
			return $rs;
		}else if($info==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '每月只可提现'.$config['cash_max_times'].'次,已达上限';
			return $rs;
		}else if($info==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '提现账号信息不正确';
			return $rs;
		}else if(!$info){
			$rs['code'] = 1002;
			$rs['msg'] = '提现失败，请重试';
			return $rs;
		}
	 
		$rs['info'][0]['msg']='提现成功';
		return $rs;
	}		
	/**
	 * 判断是否关注
	 * @desc 用于判断是否关注
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function isAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$info = isAttention($this->uid,$this->touid);
	 
		$rs['info'][0]['isattent']=(string)$info;
		return $rs;
	}			
	
	/**
	 * 关注/取消关注
	 * @desc 用于关注/取消关注
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function setAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $token = checkNull($this->token);
        $checkToken = checkToken($this->uid,$token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
		
		if($this->uid==$this->touid){
			$rs['code']=1001;
			$rs['msg']='不能关注自己';
			return $rs;	
		}
		
		$domain = new Domain_User();
		$info = $domain->setAttent($this->uid,$this->touid);
		$rs['info'][0]['isattent']=(string)$info;
		return $rs;
	}
	
	
	public function setIszombieAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		if($this->uid==$this->touid){
			$rs['code']=1001;
			$rs['msg']='不能关注自己';
			return $rs;	
		}
		
		$domain = new Domain_User();
		$info = $domain->setAttent($this->uid,$this->touid);
		$rs['info'][0]['isattent']=(string)$info;
		return $rs;
	}			
	
	/**
	 * 判断是否拉黑
	 * @desc 用于判断是否拉黑
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent  拉黑信息,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function isBlacked() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			
			$info = isBlack($this->uid,$this->touid);
		 
			$rs['info'][0]['isblack']=(string)$info;
			return $rs;
	}	

	/**
	 * 检测拉黑状态
	 * @desc 用于私信聊天时判断私聊双方的拉黑状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].u2t  是否拉黑对方,0表示未拉黑，1表示已拉黑
	 * @return string info[0].t2u  是否被对方拉黑,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function checkBlack() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());

			$uid=checkNull($this->uid);
			$touid=checkNull($this->touid);

			//判断对方是否已注销
			$is_destroy=checkIsDestroyByUid($touid);
			if($is_destroy){
				$rs['code']=1001;
				$rs['msg']='对方已注销';
				return $rs;
			}
			
			$u2t = isBlack($uid,$touid);
			$t2u = isBlack($touid,$uid);
		 
			$rs['info'][0]['u2t']=(string)$u2t;
			$rs['info'][0]['t2u']=(string)$t2u;
			return $rs;
	}			
		
	/**
	 * 拉黑/取消拉黑
	 * @desc 用于拉黑/取消拉黑
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isblack 拉黑信息,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function setBlack() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			
			$domain = new Domain_User();
			$info = $domain->setBlack($this->uid,$this->touid);
		 
			$rs['info'][0]['isblack']=(string)$info;
			return $rs;
	}		
	
	/**
	 * 绑定手机号
	 * @desc 用于绑定手机号发送短信
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return array info[0]  
	 * @return string msg 提示信息
	 */
	 
	public function getBindCode() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$mobile = $this->mobile;
		
		$ismobile=checkMobile($mobile);
		if(!$ismobile){
			$rs['code']=1001;
			$rs['msg']='请输入正确的手机号';
			return $rs;	
		}
		
		$limit = ip_limit();
        if( $limit == 1){
            $rs['code']=1003;
            $rs['msg']='您已当日发送次数过多';
            return $rs;
        }
        $set_mobile_key = 'set_mobile_' . $mobile;
        $set_mobile = DI()->redis->Get($set_mobile_key);
        $set_mobile = json_decode($set_mobile, true);
		if($set_mobile['mobile']==$mobile && $set_mobile['mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}

		$mobile_code = random(6,1);
		
		/* 发送验证码 */
		$result=sendsmscode($mobile,$mobile_code);
	
		if($result['code']==0){
	        $rs['msg']=$result['msg'];
//	        $rs['info']=$mobile_code;
		}else if($result['code']==667){
   
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
            
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		}
        DI()->redis->set($set_mobile_key, json_encode(['mobile' => $mobile, 'mobile_code' => $mobile_code, 'mobile_expiretime' => time() +60*5]));
	

		return $rs;
	}		

	/**
	 * 绑定手机号
	 * @desc 用于用户绑定手机号
	 * @return int code 操作码，0表示成功，非0表示有错误
	 * @return array info
	 * @return object info[0].mobile 手机号
	 * @return object info[0].msg 绑定成功提示
	 * @return string msg 提示信息
	 */
	public function setMobile() {

		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        $mobile = $this->mobile;
        $set_mobile_key = 'set_mobile_' . $mobile;
        $user_login = $this->user_login;
        $set_mobile = DI()->redis->get($set_mobile_key);

        if(empty($set_mobile))
        {
            $rs['code'] = 1002;
			$rs['msg'] = '验证码错误';
			return $rs;
        }
        $set_mobile = json_decode($set_mobile, true);

		if($this->mobile!=$set_mobile['mobile']){
			$rs['code'] = 1001;
			$rs['msg'] = '手机号码不一致';
			return $rs;					
		}

		if($this->code!=$set_mobile['mobile_code']){
			$rs['code'] = 1002;
			$rs['msg'] = '验证码错误';
			return $rs;					
		}	
		
		if(time()>$set_mobile['mobile_expiretime'])
		{
		    $rs['code'] = 1002;
			$rs['msg'] = '验证码错误';
			return $rs;	
		}

		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        $user = DI()->notorm->user->where('mobile',$mobile)->fetchOne();

        if($user)
        {
            $rs['code'] = 1003;
			$rs['msg'] = '手机号已被其他用户绑定！';
			return $rs;
            
        }
		$domain = new Domain_User();
		//更新数据库
		$data=array("mobile"=>$mobile,'user_login' => $user_login);
	
		$result = $domain->userUpdate($this->uid,$data);
	
		if($result === false){
			$rs['code'] = 1003;
			$rs['msg'] = $result;
			return $rs;
		}

	    DI()->redis->del($set_mobile_key);
	    $rs['info'][0]['mobile'] = $this->mobile;
		$rs['msg'] = '绑定成功';

		return $rs;
	}		
	
	/**
	 * 关注列表
	 * @desc 用于获取用户的关注列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function getFollowsList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_User();
		$info = $domain->getFollowsList($this->uid,$this->touid,$this->p);
	 
		$rs['info']=$info;
		return $rs;
	}		
	
	/**
	 * 粉丝列表
	 * @desc 用于获取用户的关注列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function getFansList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_User();
		$info = $domain->getFansList($this->uid,$this->touid,$this->p);
	 
		$rs['info']=$info;
		return $rs;
	}	

	/**
	 * 黑名单列表
	 * @desc 用于获取用户的名单列表
	 * @return int code 操作码，0表示成功
	 * @return array info 用户基本信息
	 * @return string msg 提示信息
	 */
	public function getBlackList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_User();
		$info = $domain->getBlackList($this->uid,$this->touid,$this->p);
	 
		$rs['info']=$info;
		return $rs;
	}		
	
	/**
	 * 直播记录
	 * @desc 用于获取用户的直播记录
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].nums 观看人数
	 * @return string info[].datestarttime 格式化的开播时间
	 * @return string info[].dateendtime 格式化的结束时间
	 * @return string info[].video_url 回放地址
	 * @return string info[].file_id 回放标示
	 * @return string msg 提示信息
	 */
	public function getLiverecord() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_User();
		$info = $domain->getLiverecord($this->touid,$this->p);
	 
		$rs['info']=$info;
		return $rs;
	}	

    /**
     *获取阿里云cdn录播地址
     *@desc 如果使用的阿里云cdn，则使用该接口获取录播地址
     *@return int code 操作码，0表示成功
     *@return string info[0].url 录播视频地址
	 * @return string msg 提示信息
    */		
    public function getAliCdnRecord(){

        $rs = array('code' => 0,'msg' => '', 'info' => array());
        $domain = new Domain_Cdnrecord();
        $info = $domain->getCdnRecord($this->id);
        
        if(!$info['video_url']){
            $rs['code']=1002;
            $rs['msg']='直播回放不存在';
            return $rs;
        }

        $rs['info'][0]['url']=$info['video_url'];

        return $rs;
    }	


	/**
	 * 个人主页 
	 * @desc 用于获取个人主页数据
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].isattention 是否关注，0表示未关注，1表示已关注
	 * @return string info[0].isblack 我是否拉黑对方，0表示未拉黑，1表示已拉黑
	 * @return string info[0].isblack2 对方是否拉黑我，0表示未拉黑，1表示已拉黑
	 * @return array info[0].contribute 贡献榜前三
	 * @return array info[0].contribute[].avatar 头像
	 * @return string info[0].islive 是否正在直播，0表示未直播，1表示直播
	 * @return string info[0].videonums 视频数
	 * @return string info[0].livenums 直播数
	 * @return array info[0].liverecord 直播记录
	 * @return array info[0].label 印象标签
	 * @return string info[0].isshop 是否有店铺，0否1是
	 * @return object info[0].shop 店铺信息
	 * @return string info[0].shop.name 名称
	 * @return string info[0].shop.thumb 封面
	 * @return string info[0].shop.nums 商品数量
	 * @return string msg 提示信息
	 */
	public function getUserHome() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $touid=checkNull($this->touid);
        
		$domain = new Domain_User();
		$info=$domain->getUserHome($uid,$touid);
        
        /* 守护 */
        $data=array(
			"liveuid"=>$touid,
		);

		$domain_guard = new Domain_Guard();
		$guardlist = $domain_guard->getGuardList($data);
        
        $info['guardlist']=array_slice($guardlist,0,3);
        
        /* 标签 */
        $key="getMyLabel_".$touid;
        $label=getcaches($key);
        if(!$label){
            $label = $domain->getMyLabel($touid);
            setcaches($key,$label); 
        }
        
        $labels=array_slice($label,0,3);
        
        $info['label']=$labels;
        
        /* 视频 */
        $domain_video = new Domain_Video();
		$video = $domain_video->getHomeVideo($uid,$touid,1);
        
        $info['videolist']=$video;
        
        /* 店铺 */
        
		
		$rs['info'][0]=$info;
		return $rs;
	}		

	/**
	 * 贡献榜 
	 * @desc 用于获取贡献榜
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].total 贡献总数
	 * @return string info[].userinfo 用户信息
	 * @return string msg 提示信息
	 */
	public function getContributeList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$domain = new Domain_User();
		$info=$domain->getContributeList($this->touid,$this->p);
		
		$rs['info']=$info;
		return $rs;
	}	
	
	/**
     * 私信用户信息
     * @desc 用于获取其他用户基本信息
     * @return int code 操作码，0表示成功，1表示用户不存在
     * @return array info   
     * @return string info[0].id 用户ID
     * @return string info[0].isattention 我是否关注对方，0未关注，1已关注
     * @return string info[0].isattention2 对方是否关注我，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getPmUserInfo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $info = getUserInfo($this->touid);
		 if (empty($info)) {
            $rs['code'] = 1001;
            $rs['msg'] = T('user not exists');
            return $rs;
        }
        $info['isattention2']= (string)isAttention($this->touid,$this->uid);
        $info['isattention']= (string)isAttention($this->uid,$this->touid);
       
        $rs['info'][0] = $info;

        return $rs;
    }		

	/**
	 * 获取多用户信息 
	 * @desc 用于获取获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].utot 是否关注，0未关注，1已关注
	 * @return string info[].ttou 对方是否关注我，0未关注，1已关注
	 * @return string msg 提示信息
	 */
	public function getMultiInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $configpri=getConfigPri();
        
        if($configpri['letter_switch']!=1){
            return $rs;
        }
		
		$uids=explode(",",$this->uids);

		foreach ($uids as $k=>$userId) {
			if($userId){
				$userinfo= getUserInfo($userId);
				if($userinfo){
					$userinfo['utot']= isAttention($this->uid,$userId);
					
					$userinfo['ttou']= isAttention($userId,$this->uid);
					
					if($userinfo['utot']==$this->type){						
						$rs['info'][]=$userinfo;
					}												
				}					
			}
		}

		return $rs;
	}	

	/**
	 * 获取多用户信息(不区分是否关注)
	 * @desc 用于获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].utot 是否关注，0未关注，1已关注
	 * @return string info[].ttou 对方是否关注我，0未关注，1已关注
	 * @return string msg 提示信息
	 */
	public function getUidsInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uids=explode(",",$this->uids);

		foreach ($uids as $k=>$userId) {
			if($userId){
				$userinfo= getUserInfo($userId);
				if($userinfo){
					$userinfo['utot']= isAttention($this->uid,$userId);
					
					$userinfo['ttou']= isAttention($userId,$this->uid);					
                    
                    $rs['info'][]=$userinfo;
											
				}					
			}
		}

		return $rs;
	}	

	/**
	 * 登录奖励
	 * @desc 用于用户登录奖励
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].bonus_switch 登录开关，0表示未开启
	 * @return string info[0].bonus_day 登录天数,0表示已奖励
	 * @return string info[0].count_day 连续登陆天数
	 * @return string info[0].bonus_list 登录奖励列表
	 * @return string info[0].bonus_list[].day 登录天数
	 * @return string info[0].bonus_list[].coin 登录奖励
	 * @return string msg 提示信息
	 */
	public function Bonus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$domain = new Domain_User();
		$info=$domain->LoginBonus($uid);

		$rs['info'][0]=$info;

		return $rs;
	}		
    
	/**
	 * 登录奖励
	 * @desc 用于用户登录奖励
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].bonus_switch 登录开关，0表示未开启
	 * @return string info[0].bonus_day 登录天数,0表示已奖励
	 * @return string msg 提示信息
	 */
	public function getBonus() {
		$rs = array('code' => 0, 'msg' => '领取成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$domain = new Domain_User();
		$info=$domain->getLoginBonus($uid);

		if(!$info){
            $rs['code'] = 1001;
			$rs['msg'] = '领取失败';
			return $rs;
        }

		return $rs;
	}
	
	/**
	 * 设置分销上级 
	 * @desc 用于用户首次登录设置分销关系
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 提示信息
	 * @return string msg 提示信息
	 */
	public function setDistribut() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$code=checkNull($this->code);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		if($code==''){
			$rs['code']=1001;
			$rs['msg']='请输入邀请码';
			return $rs;
		}
		
		$domain = new Domain_User();
		$info=$domain->setDistribut($uid,$code);
		if($info==1004){
			$rs['code']=1004;
			$rs['msg']='已设置，不能更改';
			return $rs;
		}
        
		if($info==1002){
			$rs['code']=1002;
			$rs['msg']='邀请码错误';
			return $rs;
		}
        
        if($info==1003){
			$rs['code']=1003;
			$rs['msg']='不能填写自己下级的邀请码';
			return $rs;
		}
		
		$rs['info'][0]['msg']='设置成功';

		return $rs;
	}	

	/**
	 * 获取用户间印象标签 
	 * @desc 用于获取用户间印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string info[].ifcheck 是否选择
	 * @return string msg 提示信息
	 */
	public function getUserLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $touid=checkNull($this->touid);
        
        $key="getUserLabel_".$uid.'_'.$touid;
		$label=getcaches($key);

		if(!$label){
            $domain = new Domain_User();
			$info = $domain->getUserLabel($uid,$touid);
            $label=$info['label'];
			setcaches($key,$label); 
		}
        
        $label_check=preg_split('/,|，/',$label);
		
        $label_check=array_filter($label_check);
        
        $label_check=array_values($label_check);
        
        
        $key2="getImpressionLabel";
		$label_list=getcaches($key2);
		if(!$label_list){
            $domain = new Domain_User();
			$label_list = $domain->getImpressionLabel();
		}
        
        foreach($label_list as $k=>$v){
            $ifcheck='0';
            if(in_array($v['id'],$label_check)){
                $ifcheck='1';
            }
            $label_list[$k]['ifcheck']=$ifcheck;
        }
        
		$rs['info']=$label_list;

		return $rs;
	}	


	/**
	 * 获取用户间印象标签 
	 * @desc 用于获取用户间印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string msg 提示信息
	 */
	public function setUserLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $touid=checkNull($this->touid);
        $labels=checkNull($this->labels);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        if($uid==$touid){
            $rs['code'] = 1003;
			$rs['msg'] = '不能给自己设置标签';
			return $rs;
        }
        
        if($labels==''){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择印象';
			return $rs;
        }
        
        $labels_a=preg_split('/,|，/',$labels);
        $labels_a=array_filter($labels_a);
        $nums=count($labels_a);
        if($nums>3){
            $rs['code'] = 1002;
			$rs['msg'] = '最多只能选择3个印象';
			return $rs;
        }
        

        $domain = new Domain_User();
        $result = $domain->setUserLabel($uid,$touid,$labels);

        if($result){
            $key="getUserLabel_".$uid.'_'.$touid;
            setcaches($key,$labels); 
            
            $key2="getMyLabel_".$touid;
            delcache($key2);
        }

		
		$rs['msg']='设置成功';

		return $rs;
	}	


	/**
	 * 获取自己所有的印象标签 
	 * @desc 用于获取自己所有的印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string info[].nums 数量
	 * @return string msg 提示信息
	 */
	public function getMyLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
    
        $key="getMyLabel_".$uid;
		$info=getcaches($key);
		
		if(!$info){
            $domain = new Domain_User();
            $info = $domain->getMyLabel($uid);
			

			setcaches($key,$info); 
		}

		$rs['info']=$info;

		return $rs;
	}	
    

	/**
	 * 获取个性设置列表 
	 * @desc 用于获取个性设置列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getPerSetting() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=$this->uid;
        $token=$this->token;

        $domain = new Domain_User();
        $info = $domain->getPerSetting();

        if($uid && $token){

            // $checkToken=checkToken($uid,$token);
            // if($checkToken==700){
            //     $rs['code'] = $checkToken;
            //     $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            //     return $rs;
            // }

            $user_info = DI()->notorm->user_info
                ->where('user_id = ?', $uid)
                ->fetchOne();
            if($user_info['money_passwd']){
                $info[]=array('id'=>'20','name'=>'修改资金密码','thumb'=>'' ,'href'=>'');
            }else{
                $info[]=array('id'=>'21','name'=>'设置资金密码','thumb'=>'' ,'href'=>'');
            }

            $user = DI()->notorm->user->where('id=?', $uid)->fetchOne();

            if(!$user['mobile']){
                $info[]=array('id'=>'22','name'=>'绑定手机号','thumb'=>'' ,'href'=>'');
            }
        }else{
        	$info[]=array('id'=>'22','name'=>'绑定手机号','thumb'=>'' ,'href'=>'');
        }

        

        $info[]=array('id'=>'17','name'=>'意见反馈','thumb'=>'' ,'href'=>get_upload_path('/Appapi/feedback/index'));
        $info[]=array('id'=>'15','name'=>'修改密码','thumb'=>'' ,'href'=>'');
        $info[]=array('id'=>'18','name'=>'清除缓存','thumb'=>'' ,'href'=>'');
        $info[]=array('id'=>'19','name'=>'注销账号','thumb'=>'' ,'href'=>get_upload_path('/portal/page/index?id=44'));
        $info[]=array('id'=>'16','name'=>'检查更新','thumb'=>'' ,'href'=>'');
        

		$rs['info']=$info;

		return $rs;
	}	

	/**
	 * 获取用户提现账号 
	 * @desc 用于获取用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 账号ID
	 * @return string info[].type 账号类型
	 * @return string info[].account_bank 银行名称
	 * @return string info[].account 账号
	 * @return string info[].name 姓名
	 * @return string msg 提示信息
	 */
	public function getUserAccountList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
    

        $domain = new Domain_User();
        $info = $domain->getUserAccountList($uid);

		$rs['info']=$info;

		return $rs;
	}	

	/**
	 * 添加提现账号 
	 * @desc 用于添加提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setUserAccount() {
		$rs = array('code' => 0, 'msg' => '添加成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $type=checkNull($this->type);
        $account_bank=checkNull($this->account_bank);
        $account=checkNull($this->account);
        $name=checkNull($this->name);

        if($type==3){
            if($account_bank==''){
                $rs['code'] = 1001;
                $rs['msg'] = '银行名称不能为空';
                return $rs;
            }
        }
        
        if($account==''){
            $rs['code'] = 1002;
            $rs['msg'] = '账号不能为空';
            return $rs;
        }
        
        
        if(mb_strlen($account)>40){
            $rs['code'] = 1002;
            $rs['msg'] = '账号长度不能超过40个字符';
            return $rs;
        }
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'type'=>$type,
            'account_bank'=>$account_bank,
            'account'=>$account,
            'name'=>$name,
            'addtime'=>time(),
        );
        
        $domain = new Domain_User();
        $where=[
            'uid'=>$uid,
            'type'=>$type,
            'account_bank'=>$account_bank,
            'account'=>$account,
        ];
        $isexist=$domain->getUserAccount($where);
        if($isexist){
            $rs['code'] = 1004;
            $rs['msg'] = '账号已存在';
            return $rs;
        }
        
        $result = $domain->setUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = '添加失败，请重试';
            return $rs;
        }
        
        $rs['info'][0]=$result;

		return $rs;
	}	


	/**
	 * 删除用户提现账号 
	 * @desc 用于删除用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function delUserAccount() {
		$rs = array('code' => 0, 'msg' => '删除成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $id=checkNull($this->id);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'id'=>$id,
        );
        
        $domain = new Domain_User();
        $result = $domain->delUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = '删除失败，请重试';
            return $rs;
        }

		return $rs;
	}	
    

    /**
     * 用户申请店铺余额提现
     * @desc 用于用户申请店铺余额提现
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function setShopCash(){
    	$rs = array('code' => 0, 'msg' => '提现成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);		
        $accountid=checkNull($this->accountid);		
        $money=checkNull($this->money);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==""||!$time||!$sign){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择提现账号';
			return $rs;
        }

        if(!$money){
            $rs['code'] = 1002;
			$rs['msg'] = '请输入有效的提现金额';
			return $rs;
        }

		$now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'accountid'=>$accountid,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $configpri=getConfigPri();

        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'money'=>$money,
        );

        $domain=new Domain_User();
        $res = $domain->setShopCash($data);

        if($res==1001){
			$rs['code'] = 1001;
			$rs['msg'] = '余额不足';
			return $rs;
		}else if($res==1004){
			$rs['code'] = 1004;
			$rs['msg'] = '提现最低额度为'.$configpri['balance_cash_min'].'元';
			return $rs;
		}else if($res==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '不在提现期限内，不能提现';
			return $rs;
		}else if($res==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '每月只可提现'.$configpri['balance_cash_max_times'].'次,已达上限';
			return $rs;
		}else if($res==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '提现账号信息不正确';
			return $rs;
		}else if(!$res){
			$rs['code'] = 1002;
			$rs['msg'] = '提现失败，请重试';
			return $rs;
		}
	 
		$rs['info'][0]['msg']='提现成功';
		return $rs;

    }

    /**
     * 获取用户的认证信息
     * @desc 用于用户申请店铺余额提现
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getAuthInfo(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$isauth=isAuth($uid);
		if(!$isauth){
			$rs['code']=1001;
			$rs['msg']='请先进行实名认证';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->getAuthInfo($uid);

		$rs['info'][0]=$res;
		return $rs;

    }
    

    /**
     * 查看每日任务
     * @desc 用于用户查看每日任务的进度
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function seeDailyTasks(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        $islive=checkNull($this->islive);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		
		if($islive==1){   //判断请求是否在直播间
			if($uid==$liveuid){ //主播访问
				/*观看直播计时---每日任务--取出用户进入时间*/
				$key='open_live_daily_tasks_'.$uid;
				$starttime=getcaches($key);
				if($starttime){ 
					$endtime=time();  //当前时间
					$data=[
						'type'=>'3',
						'starttime'=>$starttime,
						'endtime'=>$endtime,
					];
					dailyTasks($uid,$data);
					//删除当前存入的时间
					delcache($key);
				}	
				/*观看直播计时---用于每日任务--记录用户进入时间*/
				$enterRoom_time=time();
				setcaches($key,$enterRoom_time);
				
			}else{  //用户访问
			
				/*观看直播计时---每日任务--取出用户进入时间*/
				$key='watch_live_daily_tasks_'.$uid;
				$starttime=getcaches($key);
				if($starttime){ 
					$endtime=time();  //当前时间
					$data=[
						'type'=>'1',
						'starttime'=>$starttime,
						'endtime'=>$endtime,
					];
					dailyTasks($uid,$data);
					//删除当前存入的时间
					delcache($key);
				}	
				/*观看直播计时---用于每日任务--记录用户进入时间*/
				$enterRoom_time=time();
				setcaches($key,$enterRoom_time);

			}
		}
		
		$domain=new Domain_User();
		$info=$domain->seeDailyTasks($uid);

		$configpub=getConfigPub();
		$name_coin=$configpub['name_coin']; //钻石名称

		$rs['info'][0]['tip_m']="温馨提示：当您某个任务达成时就会获得平台奖励给您的{$name_coin}，获得的奖励需要您手动领取才可放入余额中，当日不领取次日系统会自动清零，亲爱的您一定要记得领取当日奖励哦~";
		$rs['info'][0]['list']=$info;
		return $rs;

    }
	
	
	/**
     * 领取每日任务奖励
     * @desc 用于用户领取每日任务奖励
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function receiveTaskReward(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $taskid=checkNull($this->taskid);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain=new Domain_User();
		$info=$domain->receiveTaskReward($uid,$taskid);

		
		return $info;

    }


    /**
     * 获取七牛上传Token
     * @desc 用于获取七牛上传Token
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getQiniuToken(){
	   	$rs = array('code' => 0, 'msg' => '', 'info' =>array());

	   	//获取后台配置的腾讯云存储信息
		$Qiniu=DI()->config->get('app.Qiniu');

		
		require_once API_ROOT.'/../sdk/qiniu/autoload.php';
		
		// 需要填写你的 Access Key 和 Secret Key
		// 需要填写你的 Access Key 和 Secret Key
		$accessKey =$Qiniu['accessKey'];// $configpri['qiniu_accesskey'];
		
		$secretKey = $Qiniu['secretKey'];//$configpri['qiniu_secretkey'];
		$bucket =$Qiniu['space_bucket'];// $configpri['qiniu_bucket'];
		$qiniu_domain_url = $Qiniu['space_host'];
		// 构建鉴权对象
		$auth = new Qiniu\Auth($accessKey, $secretKey);
		// 生成上传 Token
		$token = $auth->uploadToken($bucket);
		$rs['info'][0]['token']=$token ; 
		return $rs; 
		
	}
    
    
    
    protected function telegram($message)
    {
        $url = "https://api.telegram.org/bot1720556111:AAFfUNhLiY-TmL1H0MDbwTZbWleY4hvdT1k/sendMessage?chat_id=-522977006&text=";
        $message = urlencode($message);
        $url = $url . $message;
        file_get_contents($url);
    }
}
