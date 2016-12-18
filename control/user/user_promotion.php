<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-12-21 12:07
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/**
 * 三级导航
 */
$third_nav=array("reg"=>array($_lang['reg'], $_lang['desc_reg']),
				 "pub_task"=>array($_lang['pub_task'], $_lang['desc_pub_task']),
				 "bid_task"=>array($_lang['bid_task'], $_lang['desc_bid_task']),
				 "service"=>array($_lang['service'], $_lang['desc_service']));
$show or $show = 'reg';

$effect_mode = keke_prom_class::get_prom_rule($show);
switch ($show){
	case "reg":
		$effect_mode = keke_prom_class::get_prom_rule($effect_mode['auth_step']);
		$global_config = $kekezu->get_table_data ( '*', 'witkey_basic_config', ' type="prom"', '', '', '', 'k' );		
		break;
	case "pub_task":
	case "bid_task":
	case "service":
		$model_list = kekezu::get_table_data("model_id,model_name","witkey_model","model_status='1'","","","","model_id",3600);
		$indus_list = $kekezu->_indus_arr;
		break;
}
require keke_tpl_class::template("user/user_".$op);