<?php
/**
 * this not free,powered by keke-tech
 * @auther 九江
 * encoding GBK  last-modify 2011-2-24
 */
keke_lang_class::load_lang_class ( 'keke_glob_class' );
class keke_glob_class {
	
	/**
	 * 
	 * @return 返回 财务动作
	 */
	public static function get_finance_action() {
		global $_lang;
		return array ("realname_auth" => $_lang ['realname_auth'], "bank_auth" => $_lang ['bank_auth'], "email_auth" => $_lang ['email_auth'], "mobile_auth" => $_lang ['mobile_auth'], "buy_vip" => $_lang ['buy_vip'], "buy_service" => $_lang ['buy_service'], "pub_task" => $_lang ['pub_task'], "hosted_reward" => $_lang ['hosted_reward'], "withdraw" => $_lang ['withdraw'], "task_delay" => $_lang ['task_delay'], //==========(in)=========
"ext_cash" => $_lang ['ext_cash'], "online_recharge" => $_lang ['online_recharge'], "offline_recharge" => $_lang ['offline_recharge'], 
"task_bid" => $_lang ['task_bid'],'task_mark'=>'任务入围', "task_fail" => $_lang ['task_fail'], "task_prom_fail" => $_lang ['task_prom_fail'], 
"sale_service" => $_lang ['sale_service'], "admin_recharge" => $_lang ['admin_recharge'], "withdraw_fail" => $_lang ['withdraw_fail'], 
"report" => $_lang ['report_processs'], "payitem" => $_lang ['payitem'], "prom_reg" => $_lang ['prom_reg'], "task_fj" => $_lang ['task_fj'], 
'rights_return' => $_lang ['rights_return'], "task_auto_return" => $_lang ['task_auto_return'], 'order_cancel' => $_lang ['order_cancel'], 
"online_charge" => $_lang ['online_charge'], "order_charge" => $_lang ['order_charge'], 'prom_pub_task' => $_lang ['prom_pub_task'], 
'prom_task_bid' => $_lang ['prom_bid_task'], 'enterprise_auth' => $_lang ['enterprise_auth'], 'task_remain' => $_lang ['task_remain_return'], 
'hosted_return' => $_lang ['task_hosted_return'], 'admin_charge' => $_lang ['admin_charge'], 'host_deposit' => $_lang ['host_deposit'], 
'deposit_return' => $_lang ['deposit_return'], 'host_return' => $_lang ['host_return'], 'host_split' => $_lang ['host_split'], 
'task_lettory' => '任务摇奖', 'invoice_taxes' => '开票税金', 'buy_mobile' => '短信购买', 'credit_return' => '金币返还', 'task_pay' => '任务付款' ,
		'admin_recharge_out' => '管理员扣除', 
		'prom_task_bid' => '推广中标', 
		'prom_reg' => '邀请会员','integrity'=>'诚信保障付款','integrity_refund'=>'诚信保障金退还',
		'prom_pub_task' => '推广发布',
		 'prom_auth' => '推广认证','admin_correction'=>'管理员后台纠错', 'task_prom'=>'推广注册');
	}
	
	public static function get_value_add_type() {
		global $_lang;
		return array ("workhide" => $_lang ['workhide'], 'emphide' => '雇主隐藏稿件', "top" => $_lang ['task_top'], "urgent" => $_lang ['task_urgent'], "map" => $_lang ['map_locate'] );
	}
	public static function get_payitem_type() {
		global $_lang;
		return array ("task" => $_lang ['task_pub'], "work" => $_lang ['witkey_submit'] );
	}
	public static function withdraw_status() {
		global $_lang;
		return array ("1" => $_lang ['wait_audit'], "2" => $_lang ['has_success'], "3" => $_lang ['has_fail'] );
	}
	/**
	 * message_send类型
	 */
	public static function get_message_send_type() {
		global $_lang;
		return array (array ("1" => "send_sms", "2" => "send_email", "3" => "send_mobile_sms" ), array ("send_sms" => $_lang ['site_msg'], "send_email" => $_lang ['send_email'], "send_mobile_sms" => $_lang ['send_mobile_sms'] ) )

		;
	}
	/**
	 * 消息发送分类对象类型
	 */
	public static function get_message_send_obj() {
		global $_lang;
		return array ("task" => $_lang ['task'], "service" => $_lang ['goods'], "space" => $_lang ['space'], "user" => $_lang ['user'], "found" => $_lang ['funds'], 'safe' => $_lang ['safe'], "trans" => $_lang ['rights'], "auth" => '认证', 'buyer' => '雇主', 'witkey' => $_lang ['witkey'] );
	}
	/**
	 * feed类型
	 * 
	 */
	public static function get_feed_type() {
		global $_lang;
		return array ("pub_task" => $_lang ['pub_task'], "lottery_win" => '摇奖中奖', "join_work" => $_lang ['join_work'], "task_pay" => $_lang ['pay_task_cost'], "task_prom" => $_lang ['from_prom_task'], "vip" => $_lang ['become_vip'], "withdraw" => $_lang ['withdraw'], "work_accept" => $_lang ['task_bid'], "work_delay" => $_lang ['task_delay'], "add_service" => $_lang ['create_service'], 'user_index' => $_lang ['website_feed'], "user_talent" => $_lang ['lastest_user_feed'], "index_all" => $_lang ['taking_place_in'], "bank_auth" => $_lang ['bank_auth'], "pub_work" => $_lang ['pub_work'], 'task_bid' => '任务中标', 'prom_task_bid' => '推广中标', 'prom_reg' => '邀请会员', 'prom_pub_task' => '推广发布', 'prom_auth' => '推广认证', "realname_auth" => $_lang ['realname_auth'], "enterprise_auth" => $_lang ['enterprise_auth'], "email_auth" => $_lang ['email_auth'], "weibo_auth" => $_lang ['weibo_auth'], "realname_auth" => $_lang ['realname_auth'], "task_pay" => $_lang ['get_commission'], "default" => $_lang ['default'] );
	}
	
	public static function get_event_status() {
		global $_lang;
		return array ("0" => $_lang ['has_grant'], "1" => $_lang ['not_grant'] );
	}
	public static function get_tag_type() {
		global $_lang;
		return array ("1" => array ("1" => $_lang ['task'], "2" => "task" ), "2" => array ("1" => $_lang ['articles'], "2" => "article" ), //"3" =>array("1"=>$_lang['task_class'],"2"=>"category"),
		"4" => array ("1" => $_lang ['self_defined_sql'], "2" => "autosql" ), "5" => array ("1" => $_lang ['self_defined_code'], "2" => "autocode" ) )//"6" =>array("1"=>$_lang['goods'],"2"=>"service"),
		//"7" =>array("1"=>$_lang['articles_class'],"2"=>"artcate"),
		;
	}
	
	public static function get_open_api() {
		global $_lang;
		$r = array ('sina' => array ('name' => $_lang ['sina_weibo'], 'ico' => 'sina' ), 'ten' => array ('name' => $_lang ['tenxun_weibo'], 'ico' => 'ten' ), 'qq' => array ('name' => $_lang ['qq_number'], 'ico' => 'qq' ), 'taobao' => array ('name' => $_lang ['taobao'], 'ico' => 'taobao' ), 'netease' => array ('name' => $_lang ['wangyi_weibo'], 'ico' => 'netease' ), 'sohu' => array ('name' => $_lang ['sohu_weibo'], 'ico' => 'sohu' ), 'alipay' => array ('name' => $_lang ['alipay'], 'ico' => 'alipay' ), 'renren' => array ('name' => $_lang ['renren'], 'ico' => 'renren' ), 'douban' => array ('name' => $_lang ['douban'], 'ico' => 'douban' ), 'baidu' => array ('name' => $_lang ['baidu'], 'ico' => 'baidu' ), 'msn' => array ('name' => 'msn', 'ico' => 'msn' ) );
		return $r;
	}
	
	public static function get_bank() {
		global $_lang;
		return array ('aboc' => $_lang ['aboc'], 'ccb' => $_lang ['ccb'], 'icbc' => $_lang ['icbc'], 'cmb' => $_lang ['cmb'], 'bocm' => $_lang ['bocm'], 'spdb' => $_lang ['spdb'], 'cmbc' => $_lang ['cmbc'], 'ccb' => $_lang ['ccb'], 'psbc' => $_lang ['psbc'], 'cib' => $_lang ['cib'], 'huaxia_bank' => $_lang ['huaxia_bank'], 'boc' => $_lang ['boc'], 'alipayjs' => $_lang ['alipay'] );
	}
	
	/**
	 * 模型类型
	 */
	public static function get_model_type() {
		global $_lang;
		return array ("mreward" => $_lang ['more_reward'], "preward" => $_lang ['piece_reward'], "sreward" => $_lang ['single_reward'], "dtender" => $_lang ['deposit_tender'], "tender" => $_lang ['normal_tender'], "goods" => $_lang ['witkey_goods'], "service" => $_lang ['witkey_service'], "match" => $_lang ['match'] );
	}
	
	/**
	 * 充值订单类型
	 */
	public static function get_charge_type() {
		global $_lang;
		return array ("online_charge" => $_lang ['online_recharge'], "offline_charge" => $_lang ['offline_recharge'] );
	
	}
	/**
	 * 互评星级
	 * @return array
	 */
	public static function get_mark_star() {
		global $_lang;
		return array ("1" => $_lang ['one_star'], "2" => $_lang ['two_star'], "3" => $_lang ['three_star'], "4" => $_lang ['four_star'], "5" => $_lang ['five_star'] );
	}
	
	/**
	 * 
	 * 获取aouth登录方式
	 */
	public static function get_oauth_type() {
		global $_lang;
		return array ('sina' => array ('name' => $_lang ['sina_weibo'], 'ico' => 'sina' ), 'ten' => array ('name' => $_lang ['tenxun_weibo'], 'ico' => 'ten' ), 'qq' => array ('name' => $_lang ['tenxun_qq'], 'ico' => 'qq' ), 'netease' => array ('name' => $_lang ['wangyi_weibo'], 'ico' => 'netease' ), 'sohu' => array ('name' => $_lang ['sohu_weibo'], 'ico' => 'sohu' ), 'taobao' => array ('name' => "淘宝", 'ico' => 'taobao' ), 'renren' => array ('name' => "人人网", 'ico' => 'renren' ), 'baidu' => array ('name' => "百度", 'ico' => 'baidu' ), 'douban' => array ('name' => "豆瓣", 'ico' => 'douban' ) );
	}
	/**
	 * 
	 * 获取工作年龄要求数组
	 */
	public static function get_job_age() {
		return array ('1'=>'不限','2' =>'18岁至22岁' ,'3'=>'20岁至28岁','4'=>'25岁至40岁' );
	}
	/**
	 * 
	 * 获取工资待遇数组
	 */
	public static function get_job_salary() {
		return array ('1'=>'面议','2' =>'1500-2000元' ,'3'=>'2000-3000元','4'=>'3000-5000元','5'=>'5000元以上' );
	}
	/**
	 * 
	 * 获取工作经验数组
	 */
	public static function get_job_experience() {
		return array ('1'=>'不限','2' =>'1-2年' ,'3'=>'2-3年','4'=>'3-5年','5'=>'5年以上' );
	}
	/**
	 * 
	 * 获取招聘对象
	 */
	public static function get_job_obj() {
		return array ('1'=>'不限','2' =>'学生' ,'3'=>'下岗职工' );
	}
	/**
	 * 
	 * 获取招聘学历要求
	 */
	public static function get_job_education() {
		return array ('1'=>'不限','2' =>'大专以上' ,'3'=>'本科以上','4'=>'硕士以上','5'=>'博士以上' );
	}
	/**
	 * 
	 * 获取任务类型 
	 */
	public static function get_task_type() {
		global $_lang;
		
		return array ("1" => $_lang ['single_reward'], "2" => $_lang ['more_reward'], "3" => $_lang ['piece_reward'], "4" => $_lang ['normal_tender'], "5" => $_lang ['deposit_tender'], "6" => $_lang ['works'], "7" => $_lang ['service'] );
	
	}
	
	/**
	 *
	 * 获取收藏类型
	 */
	static function get_favor_type() {
		global $_lang;
		return array ("task" => '任务', "shop" => '商铺', 'service' => '服务' );
	}
	
	/**
	 * 
	 * 获取企业空间风格图片路径 
	 */
	public static function get_e_space_style() {
		global $_lang;
		return array ("default" => "data/uploads/space/e_default.jpg", "hs" => "data/uploads/space/e_hs.jpg", "js" => "data/uploads/space/e_js.jpg", "qy" => "data/uploads/space/e_qy.jpg", "ty" => "data/uploads/space/e_ty.jpg", "zs" => "data/uploads/space/e_zs.jpg" );
	}
	/**
	 * 
	 * 获取企业空间风格图片名字 
	 */
	public static function get_e_space_name() {
		global $_lang;
		return array ("default" => $_lang ['bule_classic'], "hs" => $_lang ['gray_country'], "js" => $_lang ['golden_boundless'], "qy" => $_lang ['akiba_story'], "ty" => $_lang ['days_wing'], "zs" => $_lang ['purple_country'] );
	}
	/**
	 * 
	 * 获取个人空间风格图片路径 
	 */
	public static function get_p_space_style() {
		global $_lang;
		return array ("default" => "data/uploads/space/p_default.jpg", "bh" => "data/uploads/space/p_bh.jpg", "lsjd" => "data/uploads/space/p_lsjd.jpg", "lj" => "data/uploads/space/p_lj.jpg", "qxy" => "data/uploads/space/p_qxy.jpg", "qxyy" => "data/uploads/space/p_qxyy.jpg" );
	}
	/**
	 * 
	 * 获取个人空间风格图片名字 
	 */
	public static function get_p_space_name() {
		global $_lang;
		return array ("default" => $_lang ['default'], "bh" => $_lang ['lily'], "lsjd" => $_lang ['bule_classic'], "lj" => $_lang ['lj'], "qxy" => $_lang ['qxy'], "qxyy" => $_lang ['qxyy'] );
	}
	
	/**
	 * 
	 * 获取附件类型
	 */
	
	public static function get_file_type() {
		global $_lang;
		return array ("task" => $_lang ['task_attachment'],'brand'=>'品牌馆', "work" => $_lang ['work_attachment'], "agreement" => $_lang ['agreement_attachment'], "user_cert" => $_lang ['auth_attachment'], "space" => $_lang ['user_space'] );
	
	}
	/**
	 * 
	 * 每个模型任务状态的描述
	 */
	public static function get_taskstatus_desc() {
		global $_lang;
		return array (

		"2" => array ("desc" => $_lang ['submit_deadline'], "time" => "sub_time" ), "3" => array ("desc" => $_lang ['choose_end'], "time" => "end_time" ) )

		;
	
	}
	
	/**
	 * 
	 * 增值服务时间，顺序数组
	 * 下面的数组代表：
	 * 第一个时间是top结束时间
	 * 第二个时间代表加急的结束时间
	 * 以此类推：
	 * 注：(当添加增值服务是需要更新此数组)
	 */
	public static function get_payitem_arr() {
		$payitem_arr = array ("top", "urgent" );
		return $payitem_arr;
	}
	/**
	 * 将阿拉伯数字换成汉字
	 * @param number $num 数字(如果有值,返回对应的值,没有返回数组)
	 * @return array/string 
	 */
	public static function num2ch($num = '') {
		$ch_arr = array (1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七', 8 => '八', 9 => '九', 10 => '十' );
		if ($num != '' && array_key_exists ( ( int ) $num, $ch_arr )) {
			return $ch_arr [( int ) $num];
		}
		return $ch_arr;
	}
	
	/* *
	 * 返回服务视频类型
	 */
	
	public static function get_video_cat() {
		return array ('1' => '成功雇主', '2' => '玩转一品', '3' => '优秀威客', '4' => '媒体宣传' );
	}
	
	/* *
	 * 返回客户类型（用于客户跟踪）
	*/
	public static function get_track_type() {
		return array ('1' => '新联系，有待跟进', '2' => '意向不是很明确', '3' => '为高意向客户', '4' => '有明确合作需求，发了合同', '5' => '最终确认合作细节', '6' => '悔单客户', '7' => '没意向客户', '8' => '未接通的客户', '9' => '空号停机' );
	}
	
	/* *
	 * 返回开票状态（开票管理）
	*/
	public static function get_iv_status() {
		return array ('-1' => '取消申请', '0' => '提交等待审核', '1' => '已受理', '2' => '拒绝受理' );
	}
	
	/* *
	 * 返回开票证件类型（开票管理）
	*/
	public static function get_document_type() {
		return array ('1' => '身份证', '2' => '护照', '3' => '军官证', '4' => '驾驶证' );
	}
	
	/* *
	 * 返回举报雇主类型
	*/
	public static function get_buyer_report_type() {
		return array ('1' => array ('1', '虚假任务', '任务属于测试信息，或者广告内容，有炒作嫌疑的任务类型。' ), '2' => array ('2', '任务违规', '任务内容或完成过程涉及违反法律、法规、政策、规则的或者有损社会善良风俗习惯的内容。' ), '3' => array ('3', '雇主作弊', '任务雇主通过注册马甲小号参与任务的作弊方法，参与任务选自己中标拿回赏金。' ), '4' => array ('4', '其他', '其他违规内容。' ) );
	}
	
	/* *
	 * 返回举报雇主类型
	*/
	public static function get_seller_report_type() {
		return array ('1' => array ('1', '广告', '与任务要求没有联系，对任务完成没有帮助的各类广告。' ), '2' => array ('2', '非法内容', '违反法律、法规、政策、规则的或者有损社会善良风俗习惯的内容。' ), '3' => array ('3', '重复交稿', '指在同一任务中，无特殊原因或正常理由的多次交稿。' ), '4' => array ('4', '虚假交稿', '威客报名参与了任务，没有提交任务作品或者提交的任务作品无效。' ), '5' => array ('5', '完全抄袭', '威客报名后提交的稿件作品，涉嫌和已注册或者使用的作品完全一样。' ), '6' => array ('6', '其他', '其他违规内容。' ) );
	}
	
	/* *
	 * 返回举报雇主类型
	*/
	public static function get_credit_use() {
		return array ('1' => '隐藏交稿', '2' => '增值服务', '3' => '首页商铺展示', '4' => '频道页任务推荐', '5' => '威客频道商铺展示', '6' => '人才首页商铺展示' );
	}
	
	/* *
	 * 返回用户实名认证地区
	*/
	public static function get_realname_zone() {
		return array ('1' => array ('cn_name' => '中国大陆', 'en_name' => 'In mainland China', 'value' => '1', 'logo' => 'In_mainland_China.gif', 'selected' => '1' ), '2' => array ('cn_name' => '港澳地区', 'en_name' => 'Hong Kong and Macao', 'value' => '2', 'logo' => 'Hong_Kong_and_Macao.png' ), '3' => array ('cn_name' => '台湾地区', 'en_name' => 'Tawan', 'value' => '3', 'logo' => 'taiwan.png' ), '4' => array ('cn_name' => '海外地区', 'en_name' => 'Overseas areas', 'value' => '4', 'logo' => 'Overseas_areas.png' ) );
	}
	
	/* *
	 * 友情链接分类
	*/
	public static function get_link_cat() {
		return array ('1' => '首页', //index 1
'2' => '社区', //home
'3' => '推广员', //tuigo 1
'4' => '帮助中心', //help  1
'5' => '新闻中心', //Chief 1首席设计师
'6' => '人才大厅', //rencai 1 人才宝库
'7' => '成功案例', //anli  1
'8' => '精彩专题', //zt  1
'9' => '劳务大厅', //fuwu  1服务悬赏
'10' => '摇奖中标', //yaojiang 1
'11' => '全部任务', //chuangyi 1
'12' => '直接雇佣' )//guyong 1
;
	}
	
	/* *
	 * 专题分类
	*/
	public static function get_special_cat() {
		return array ('1' => '任务专题', '2' => '人才专题', '3' => '活动专题', '4' => '其它专题');
	}
	/* *
	 * 专题分类
	*/
	public static function get_special_seocatname() {
		return array ('1' => 'rwzt', '2' => 'rczt', '3' => 'hdzt', '4' => 'qtzt','5'=>'cyds');
	}
	
	/**
	 * 服务单位
	 * 
	 */
	public static function get_price_unit() {
		return array ('1' => '年', '2' => '月', '3' => '日', '4' => '小时', '5' => '件', '6' => '次', '7' => '一口价', '8' => '其他' );
	}
	
	/**
	 * 获取用户类型
	 *
	 */
	public static function get_user_type() {
		return array ('1' => '个人', '2' => '工作室', '3' => '公司' );
	}
	
	/**
	 *获取认证状态ICO图标
	 */
	public static function get_auth_ico($auth_realname, $auth_mobile, $auth_email, $auth_bank, $auth_integrity = '') {
		global $_K;
		if ($auth_realname==1) {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_1_yes.png' alt='实名认证通过'> ";
		} else {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_1_no.png' alt='实名认证未通过'> ";
		}
		if ($auth_mobile) {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_2_yes.png' alt='手机认证通过'> ";
		} else {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_2_no.png' alt='手机认证未通过'> ";
		}
		if ($auth_email) {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_3_yes.png' alt='邮箱认证通过'> ";
		} else {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_3_no.png' alt='邮箱认证未通过'> ";
		}
		if ($auth_bank) {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_4_yes.png' alt='银行卡认证通过'> ";
		} else {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_4_no.png' alt='银行卡认证未通过'> ";
		}
		if ($auth_integrity !== '') {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_5_" . ( $auth_integrity ? 'yes' : 'no') . ".png' alt='诚信会员认证通过'> ";
		}
		return $str;
	}
	/**
	 * 获取单认证图标
	 */
	public static function get_single_ico($type = 1, $auth = 0) {
		global $_K;
		$arr = array (1 => '实名认证', 2 => '手机认证', 3 => '邮箱认证', 4 => '银行卡认证' );
		if ($auth==1) {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_{$type}_yes.png' alt='" . $arr [$type] . "通过'> ";
		} else {
			$str .= " <img src='" . $_K [siteurl] . '/' . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/auth_{$type}_no.png' alt='" . $arr [$type] . "未通过'> ";
		}
		return $str;
	}
	
	/**
	 *获取店铺等级ICO图标
	 */
	public static function get_shop_level_ico($level) {
		global $_K;
		switch (intval ( $level )) {
			case 1 :
				$str .= "<a href='{$_K[siteurl]}/index.php?do=vip' target='_blank'>  <img src='{$_K[siteurl]}/" . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/shop_level_1.png' alt='基础版商铺'> </a>";
				;
				break;
			case 2 :
				$str .= "<a href='{$_K[siteurl]}/index.php?do=vip' target='_blank'>   <img src='{$_K[siteurl]}/" . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/shop_level_2.png' alt='VIP拓展版商铺'> </a> ";
				;
				break;
			case 3 :
				$str .= "<a href='{$_K[siteurl]}/index.php?do=vip' target='_blank'>   <img src='{$_K[siteurl]}/" . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/shop_level_3.png' alt='VIP旗舰版商铺'>  </a>";
				;
				break;
		}
		return $str;
	}
	
	/**
	 *获取威客等级ICO图标
	 */
	public static function get_w_level_ico($level) {
		global $_K;
		if (! intval ( $level )) {
			$level = 1;
		}
		
		return "<a href='{$_K[siteurl]}/index.php?do=help&view=info&art_cat_id=611&art_id=9156' target='_blank'> <img src='{$_K[siteurl]}/" . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/w_level_" . intval ( $level ) . ".gif' alt='威客能力等级'> </a>";
	}
	
	/**
	 *获取威客等级ICO图标
	 */
	public static function get_g_level_ico($level) {
		global $_K;
		if (! intval ( $level )) {
			$level = 1;
		}
		
		return "<a href='{$_K[siteurl]}/index.php?do=help&view=info&art_cat_id=611&art_id=9156' target='_blank'>  <img src='{$_K[siteurl]}/" . SKIN_PATH . "/theme/{$_K['theme']}/img/ico/g_level_" . intval ( $level ) . ".gif' alt='雇主信誉等级'> </a>";
	}
	
	/**
	 * 身份证审核失败的话术
	 * */
	public static function get_auth_realname_error_mes() {
		return array ('1、提交的是其他照片，非身份证照片' => '您好，很抱歉！由于您提交的不是正面的身份证照片，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '2、提交的身份证照片不清晰' => '您好，很抱歉！由于您提交的正面身份证照片显示不清晰，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '3、名字与实际不符' => '您好，很抱歉，您所提交的身份证名字与实际名字不符，审核不通过，麻烦您重新提交，谢谢！详询在线客服企业QQ：4006999467', '4、号码与实际不符' => '您好，很抱歉，您所提交的身份证号码与实际不符，审核不通过，麻烦您重新提交，谢谢！详询在线客服企业QQ：4006999467', '5、提交的身份证照片不完整' => '您好，很抱歉！由于您提交的正面身份证照片显示不完整，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '6、提交的是身份证复印件照片' => '您好，很抱歉！由于您提交的不是身份证原件正面照片，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '7、提交的是临时身份证照片' => '您好，很抱歉！由于您提交的不是正式身份证照片，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '8、提交的身份证已过期' => '您好，很抱歉！由于您提交的身份证已过期，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '9、提交的是一代身份证不足一年' => '您好，很抱歉！由于您提交的身份证有效期不足一年，审核不通过，请您重新提交二代身份证。详询在线客服企业QQ：4006999467', '10、无法显示' => '您好，很抱歉！由于您提交的身份证照片无法显示，审核不通过，请您重新提交。详询在线客服企业QQ：4006999467', '11、身份证疑是合成' => '您好，很抱歉，您所提交的身份证，疑是合成，审核不通过，麻烦您重新提交，谢谢！详询在线客服企业QQ：4006999467', '12、身份证已认证过' => '您好，很抱歉，您所提交的身份证，已经认证过了，认证不能通过，麻烦您重新提交，谢谢！详询在线客服企业QQ：4006999467' );
	}
	
	/**
	 * 银行卡认证失败话术
	 * */
	public static function get_auth_blank_error_mes() {
		return array ('1、	针对先提交银行卡、未提交身份证认证的' => '您好，很抱歉！由于您未提交身份证认证，银行卡暂时无法认证，麻烦您先提交身份证认证。详询在线客服企业QQ：4006999467', '2、	名字与实际不符' => '您好！您的提交银行卡账号名字与身份证上的姓名不一致，请提供您本人的银行卡账号，进行认证申请，谢谢！', '3、	银行卡未提交正确信息，无法认证的，需提交失败处理' => '您好！很抱歉！您提交的银行卡认证没有填写相关的开户行信息，我们无法准确转账，请重新填写，谢谢！企业QQ：4006999467', '4、	银行卡提交信息不详细（未填写到支行），无法认证的，需提交失败处理' => '您好！很抱歉！您提交的银行卡认证开户行信息不详细，我们无法准确转账，请重新填写，谢谢！企业QQ：4006999467 ', '5、	转账后银行退款' => '您好！很抱歉！您提交的银行卡认证信息错误，认证金无法转账，请重新填写，谢谢！ 企业QQ：4006999467' );
	}
	
	/**
	 * 留言类型
	 * */
	public static function get_comment_type() {
		return array ('work' => '稿件留言', 'article' => '新闻留言', 'shop' => '商铺留言', 'ask' => '自助问答', 'task' => '任务留言', 'suggest' => '意见和建议' );
	}
	
	/**
	 * 留言类型
	 * */
	public static function get_track_status() {
		return array (0 => '无', 1 => '任务发布已跟进', 2 => '到期选稿已跟进', 3 => '未选稿已跟进', 4 => '选稿结束已跟进', 5 => '雇主付款已跟进' );
	}
	
	//稿件隐藏状态
	public static function get_work_hide() {
		return array (0 => '公开', 1 => '隐藏，结束后公开', 2 => '永久隐藏' );
	}
	/**
	 * 验证稿件是否需要隐藏
	 * @param $wuid 交稿人UID
	 * @param param $hidemode 隐藏模式 1 结束后公开  2永久隐藏
	 */
	public static function valid_hide_work($wuid, $hidemode = 1,$status) {
		global $task_info, $uid, $user_info;
		$hide = false;
		if (! ($status >= 1 && $status <= 13)) {
			$g_hide = strpos ( $task_info ['pay_item'], 'emphide' ) !== FALSE;
			if ($g_hide || $hidemode) {
				if ($uid != $task_info ['uid'] && $uid != $wuid && $user_info ['group_id'] < 1) {
					if ($g_hide || $hidemode == 2) { //雇主发布时选择或威客交稿时选择永久隐藏......永久隐藏
						$hide = true;
					} elseif ($hidemode == 1 && $task_info ['task_status'] < 8) {
						$hide = true;
					}
				}
			}
		}
		return $hide;
	}
	
	/**
	 * 验证用户的隐藏模式
	 */
	public static function valid_hide_mode($hidemode) {
		return in_array ( $hidemode, array ('vip', 'dzcredit', 'remain', 'free' ) );
	}
	
	/**
	 * 支付回调提示页面
	 */
	public static function pay_return_notify($bank_code,$content, $type = 'online_recharge', $obj_id = '', $code = 'success') {
		global $_K, $uid;
		$url = $_K ['siteurl'] . '/index.php?';
		switch ($type) {
			case 'task_pay' :
			case 'pub_task' :
			case 'task_delay' :
				$url .= 'do=task&task_id=' . $obj_id;
				break;
			case 'buy_mobile' :
				$url .= 'do=shop&u_id=' . $uid;
				break;
			case 'integrity' :
				$url .= 'do=user&view=witkey&op=integ';
				break;
			default :
				$url .= 'do=user&view=finance&op=detail&action=charge';
		}
		if($bank_code){
			$code=='success' and $str='RespCode=0000|JumpURL='.$url or $str='RespCode=9999|JumpURL='.$url;
			kekezu::show_msg('支付提示',$url,3,$content,$code);
			//echo $str;//die();
		}else{
			kekezu::show_msg('支付提示',$url,3,$content,$code);
		}
	}
	/**
	 * 
	 */
	public static function get_charge_action(){
		return array('pub_task'=>'任务发布','task_pay'=>'赏金托管','buy_vip'=>'VIP购买','task_delay'=>'任务延期','buy_mobile'=>'同城速配','user_charge'=>'账户充值','integrity'=>'诚信保障付款');
	} 
	/**
	 * 获取稿件图片列表
	 */
	public static function work_pics($ids){
		$pics = array();
		$ids  = array_unique(explode(',',$ids));
		$ids  = implode(',',array_filter($ids));
		$ids and $pics =  db_factory::query ( "select save_name from " . TABLEPRE . "witkey_file where file_id in ({$ids}) and file_ext in ('jpg','gif','png') " );
		return $pics;
	}
	/**
	 * 品牌馆申请状态
	 */
	public static function get_brand_status(){
		return array('-1'=>'未处理','1'=>'已处理','2'=>'未通过');
	}
	/**
	 * 品牌馆类别
	 */
	public static function get_brand_type(){
		return array('hk'=>'香港馆','tw'=>'台湾馆'//,'abroad'=>'海外'
		);
	}
	
	/**
	 * 获得任务类别
	 * @param $model_id 任务模型
	 * @param $task_type 招标模型任务分类
	 * @param $task_cash_coverage 价格区间
	 */
	public static function get_task_typename($model_id, $task_type, $task_cash_coverage){
		if ($model_id==1){
			$type_name = '单赏';
		}elseif ($model_id==2){
			$type_name = '多赏';
		}elseif ($model_id==3){
			$type_name = '计件';
		}elseif ($model_id==4){
			if ($task_type==3){
				$type_name = '直接雇佣';
			}elseif($task_type==2){
				$type_name = '服务';
			}else{
				if($task_cash_coverage){
					$type_name = '招标';
				}else{
					$type_name = '雇佣';
				}
			}
		}
		return $type_name;
	}
}