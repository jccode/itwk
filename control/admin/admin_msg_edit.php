<?php
/**
 * @copyright eweike
 * @author wrh
 * @version v1.0
 * 2012-06-13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 160 );
$tab_obj = keke_table_class::get_instance("witkey_msg");
if ($sbt_edit) {
	$msg ['on_time'] = time ();
	if ($msg_id) {
		$edit = $tab_obj->save ( $msg, $pk );
		kekezu::admin_system_log ( $_lang ['edit_material'] . $msg_id );
		$edit && kekezu::admin_show_msg ( $_lang ['material_edit_success'], '', 3, '', 'success' ); //die
	}
	$add = $tab_obj->save ( $msg );
	kekezu::admin_system_log ( $_lang ['add_material'] );
	$add && kekezu::admin_show_msg ( $_lang ['material_add_success'], '', 3, '', 'success' );
} else {
	$msg_id and $msg_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_msg where msg_id = '$msg_id'" );
}

require $template_obj->template ( 'control/admin/tpl/admin_msg_' . $view );

?>