<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-5 12:07
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

/**
 * 三级导航
 */
$third_nav = array ("my"=>array($_lang['my_tools'],$_lang['my_buy_tools']),
		            "list" => array ($_lang['service_record'], $_lang['value_added_services_using_the_record']), 
		            "buy" => array ($_lang['service_purchase'], $_lang['purchase_value_added_services'] ) );
$shows = array ('buy', 'list','my' );
/**
 * 排序
 */
$ord_arr = array (" record_id desc " => $_lang['record_num_desc'], " record_id asc " => $_lang['record_num_asc'], " on_time desc  " => $_lang['produce_desc'], " on_time asc " => $_lang['produce_asc'] );
in_array ( $show, $shows ) or $show = 'buy';	
switch ($show){
	case 'buy':	
		$payitem_list = keke_payitem_class::get_payitem_info ('witkey','',true);//可购买服务
		$payitem_type = keke_glob_class::get_payitem_type (); //使用范围
		$payitem_standard = keke_payitem_class::payitem_standard (); //收费标准		
		if ($item_code) { //购买页面加载
			in_array ( $item_code, array('top','urgent','workhide','seohide','map','emphide') ) or kekezu::show_msg($_lang['operate_notice'],'index.php?do=user&view=witkey&op=toolbox',3,$_lang['param_error'],'warning');
			$item_info = $payitem_list [$item_code]; //选择项信息
			$ac_url = $origin_url . "&view=$view&op=$op&show=buy&item_code=".$item_code; //提交链接
			require "control/payitem/$item_code/control/user_buy.php";
			die ();
		}
		break;
	case 'my'://我购买的
		//读取配置
		$payitem_config = keke_payitem_class::get_payitem_info ('witkey','',true);//可购买服务
		//威客增值服务统计
		//读取记录
		$p ['page'] = intval ( $page );
		$wh['use_type']='buy';
		$p['url'] = $origin_url . "&view=$view&op=$op&show=$show&wh['item_code']={$wh['item_code']}&wh['use_type']={$wh['use_type']}&ord=$ord&page=$page&p['page_size']={$p['page_size']}";
		$p['anchor'] = "userCenter";
		$wh ['uid'] = $uid;		
		$payitem_arr = keke_payitem_class::get_payitem_record ( $wh, $ord, $p );
		$payitem_list = $payitem_arr ['list'];
		$pages = $payitem_arr ['page'];
		break;
	case 'list'://我使用的
		//读取配置
		$payitem_config = keke_payitem_class::get_payitem_info ('witkey','',true);//可购买服务
		//威客增值服务统计
		//读取记录
		 
		$p ['page'] = intval ( $page );
		$wh ['use_type']='spend';
		$p ['url'] = $origin_url . "&view=$view&op=$op&show=$show&wh['item_code']={$wh['item_code']}&wh['use_type']={$wh['use_type']}&ord=$ord&page=$page&p['page_size']={$p['page_size']}";
		$p ['anchor'] = "userCenter";
		$wh ['uid'] = $uid;		
		$payitem_arr = keke_payitem_class::get_payitem_record ( $wh, $ord, $p );
		$payitem_list = $payitem_arr ['list'];
		$pages = $payitem_arr ['page'];
		break;
}
 
require keke_tpl_class::template ( "user/user_" .$view.'_'. $op . "_" . $show );