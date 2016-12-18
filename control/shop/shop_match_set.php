<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-2 16:06
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
kekezu::check_login();
if($user_info['uid']!=$shop_info['uid']){
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&view=index&sid=".$shop_info['shop_id'],2,"您没有权限查看该页面。","warning");
}
//同城速配短信时间
$match_info = db_factory::get_one(sprintf("select * from %switkey_shop_match where uid=%d order by m_id desc",TABLEPRE,$shop_info['uid']));
$mobile_end = kekezu::match_time_to_units($shop_info['vip_end_time'], $match_info['end_time']);

$match_obj = new Keke_witkey_shop_match_class();

if($op){
	if($op=="on"){
		$space_obj = new Keke_witkey_space_class();

		$space_obj->setWhere("uid=".$shop_info['uid']);
		$space_obj->setCity_match(1);
		$rec = $space_obj->edit_keke_witkey_space();
		if($rec){
			//初始化短信速配时间
			$match_obj->setUid($shop_info['uid']);
			$match_obj->setUsername($shop_info['username']);
			$match_obj->setShop_id($shop_info['shop_id']);
			$match_obj->setShop_name($shop_info['shop_name']);
			$match_obj->setM_status(1);
			$match_obj->setStart_time(time());
			$match_obj->setEnd_time($user_info['vip_end_time']);
			$match_obj->create_keke_witkey_shop_match();
			kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&view=match_list&sid=".$shop_info['shop_id'],2,"同城配送功能开通成功！","success");
		}
	}elseif($op=="goon"){
		if($frm_submit){
			//生成短信续费记录
			$match_obj->setUid($shop_info['uid']);
			$match_obj->setUsername($shop_info['username']);
			$match_obj->setShop_id($shop_info['shop_id']);
			$match_obj->setShop_name($shop_info['shop_name']);
			$match_obj->setM_status(0);
			if($match_info){
				if($match_info['end_time']>time()){
					$match_obj->setStart_time($match_info['end_time']);
					$match_obj->setEnd_time($match_info['end_time']+3600*24*$amount);
				}else{
					$match_obj->setStart_time(time());
					$match_obj->setEnd_time(time()+3600*24*30);
				}
			}
			$match_res=$match_obj->create_keke_witkey_shop_match();
			$match_res or kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&view=match_list&sid=".$shop_info['shop_id'],2,"短信续费操作失败。","warning");
			//生成支付的订单
			$order_obj = new Keke_witkey_order_class ();
			$order_obj->setOrder_amount ( $amount );
			$order_obj->setOrder_status ( 'wait' );
			$order_obj->setOrder_name ( '短信续费' );
			$order_obj->setOrder_uid ( $uid );
			$order_obj->setOrder_username ( $username );
			$order_obj->setObj_type ( 'buy_mobile' );//要和财务表的保持一致  扣款是属于短信购买扣款
			$order_obj->setObj_id ( $match_res );//这里存入的实际上是match_id  因为要在回调中处理
			$order_obj->setOrder_time(time());
			
			//因为完成付款之前可能会有重复操作
			$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='buy_mobile' and obj_id='".$match_res."'" );
			if ($order_exsit) {
				$order_obj->setOrder_id ( $order_exsit ['order_id'] );
				$order_obj->edit_keke_witkey_order ();
				$order_id = $order_exsit ['order_id'];
			} else {
				$order_id = $order_obj->create_keke_witkey_order ();
			}
			
			$order_id or kekezu::show_msg($_lang['fail'], $_K['siteurl'].'/index.php?do=shop&view=match_list&sid='.$shop_info['shop_id'], 3, '短信续费操作失败！', 'warning');
			header ( "location:".$_K['siteurl']."/index.php?do=pay&order_id=$order_id&obj_type=buy_mobile" );
		}
	}
}
if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}
//require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );