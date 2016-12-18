<?php
/**
 * this not free,powered by keke-tech
 * @author hr
 * @charset:GBK  last-modify 2012-1-7-下午3:29:50
 * @version V2.0
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = 'shop';
//用户类型
$user_type_arr = keke_glob_class::get_user_type ();
//价格单位
$price_unit = keke_glob_class::get_price_unit ();
$views = array ("index", "desc", "mark", "case_list", "case_info", "service_list", "service_info", "match_list", "match_set", "diy" );
! in_array ( $view, $views ) && $view = 'index';

if ( $sid || $u_id ||  $service_id ) {
	if ($service_id) {
		$service_info = db_factory::get_one ( "select *  from " . TABLEPRE . "witkey_service where service_id =" . intval ( $service_id ) );
		$sid = $service_info ['shop_id'];
	}
	
	if ( $sid ) {
		$where = " where 1 = 1  and a.shop_id =  " .  $sid ;
		$ac_url = $_K ['siteurl'] . "/index.php?do=shop&sid=" .  $sid ;
	} else {
		$where = "where 1 = 1  and a.uid =  " . $u_id ;
		$ac_url = $_K ['siteurl'] . "/index.php?do=shop&u_id=" . $u_id ;
	}
	
	$shop_info = db_factory::get_one ( "select a.* ,b.* from " . TABLEPRE . "witkey_space a left join " . TABLEPRE . "witkey_shop b on a.shop_id =b.shop_id " . $where );
	
	if (! $shop_info || ! $shop_info ['shop_id']) {
		kekezu::show_msg ( "您访问的商铺不存在！", $_K ['siteurl'] . "/index.php?do=talent", '3', '', 'warning' );
	}
	
	//更新浏览记录
	$shop_info and keke_shop_class::shop_visit ( $shop_info ['shop_id'], $shop_info ['uid'], $uid, $username );
	
	//更新浏览次数
	if ($uid && $shop_info ['uid'] != $uid && ! $_SESSION ['view_shop_' . $uid]) {
		db_factory::execute ( " update " . TABLEPRE . "witkey_shop set views = views + 1 where uid = '$shop_info[uid]'" );
		$_SESSION ['view_shop_' . $uid] = 1;
	}
	
	if(in_array($view,array('index','mark','case_list','case_info','service_list','service_info','desc','diy'))){
		//幻灯片
		$slide_arr = db_factory::query ( " select * from " . TABLEPRE . "witkey_shop_slide where shop_id=" . $shop_info ['shop_id'] . " ORDER BY listorder asc LIMIT 5" );
	}
	
	//收藏人数
	$favorite_count = db_factory::get_count ( " select count(*) from " . TABLEPRE . "witkey_favorite where obj_type = 'shop' and obj_id =" . $shop_info ['shop_id'] );
	$buyer_aid = keke_user_mark_class::get_user_aid ( $shop_info ['uid'], '2', null, '1' );
	$take_num = intval ( $shop_info ['take_num'] );
	$income = db_factory::get_one ( ' select sum(fina_cash) cash,sum(fina_credit) credit from ' . TABLEPRE . 'witkey_finance where uid=' . $shop_info ['uid'] . ' and fina_type="in" and fina_action="task_bid" ' );

	$shop_info['qq'] and $shop_info['qq'] = unserialize($shop_info['qq']); 
	
	//SEO
	$shop_type_arr = keke_glob_class::get_user_type();
	$shop_title = $shop_info['username'].'的'.$shop_type_arr[$shop_info['shop_type']].'商铺';
	$page_title = $shop_title.'_IT帮手网';
	$page_keyword = $shop_info['shop_name'].','.$shop_title.',公司商铺,商铺展示,个性展示 ';
	$page_description = '欢迎您来到IT帮手网会员'.$shop_title.'。IT帮手网是综合服务平台是威客工作者在线工作平台,知识成果、创意产业成果交易平台。是出售业余时间,分享成功经验的网络兼职平台。';
} else {
	kekezu::show_msg ( "您访问的商铺不存在！", $_K ['siteurl'] . "/index.php?do=talent", '3', '', 'warning' );
}
require $do . '/' . $do . '_' . $view . '.php';