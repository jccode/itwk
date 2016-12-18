<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 1.4
 * 2011-9-19上午10:15:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 140 );

if (isset ( $submit )) {
	$basic_obj = new Keke_witkey_basic_config_class ();
	/**attent**/
	foreach ( $conf as $k => $v ) {
		$basic_obj->setWhere ( "k = '$k'" );
		$basic_obj->setV ( $v );
		$res .= $basic_obj->edit_keke_witkey_basic_config ();
	}
	/*attent_api*/
	! empty ( $api ) and $attent_api = $api or $attent_api = array ();
	$basic_obj->setWhere ( "k = 'attent_api_open'" );
	$basic_obj->setV ( serialize($attent_api));
	$basic_obj->edit_keke_witkey_basic_config ();
	
	kekezu::admin_system_log ( $_lang['weibo_config_view'] );
	if ($res) {
		kekezu::admin_show_msg ( $_lang['weibo_view_config_success'], "index.php?do=msg&view=attention", 3 ,'','success');
	} else {
		kekezu::admin_show_msg ( $_lang['weibo_view_config_fail'], "index.php?do=msg&view=attention", 3,'','warning' );
	}
} else {
	
	//关注开启列表
	$attent_api = db_factory::get_count ( sprintf ( " select v from %switkey_basic_config where type='attent_api'", TABLEPRE ) );
	$attent_api = unserialize($attent_api);
	//关注列表
	$attent_list = kekezu::get_table_data ( "k,v,desc", "witkey_basic_config", "type='attention'", 'listorder asc ', "", "", "k" );
}
require keke_tpl_class::template ( 'control/admin/tpl/admin_' . $do . '_' . $view );