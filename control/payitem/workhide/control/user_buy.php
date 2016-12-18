<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-5下午03:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if($sbt_buy){
	$obj_id=keke_payitem_class::payitem_record($item_code,$buy_num);//服务记录号
	$order_id = keke_payitem_class::payitem_order($item_info,$obj_id,$buy_num);
	if($order_id){
		header ( "location:".$_K['siteurl']."/index.php?do=pay&order_id=$order_id&obj_type=payitem&obj_id=$obj_id" );
		die ();
	}else{
		kekezu::show_msg($item_info['item_name'].$_lang['buy_fail'],$_SERVER['HTTP_REFERER'],"3","","warning");
	}
}
//隐藏交稿剩余数量
$remain= keke_payitem_class::payitem_exists($uid,$item_code);
$dz_credit = keke_user_class::get_credit($uid);//论坛积分
$is_vip    = $user_info['isvip'];
require keke_tpl_class::template("control/payitem/$item_code/tpl/user_buy");