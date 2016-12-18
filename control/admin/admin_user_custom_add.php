<?php
/**
 *客服管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 33 );

$space_obj = new Keke_witkey_space_class ();
$member_group_arr = db_factory::query ( sprintf ( "select group_id,groupname from %switkey_member_group", TABLEPRE ), 1, 3600 );
$edituid = intval ( $edituid );
$edituid and $spaceinfo = kekezu::get_user_info ( intval ( $edituid ) );
if($ac=='get_user_info'){
	kekezu::echojson(1,1,kekezu::get_user_info($guid));
	die();
}
if ($is_submit) {
	$space_obj = keke_table_class::get_instance("witkey_space");	
	$space_obj->save($fds,array('uid'=>$fds['uid']));
 	kekezu::admin_system_log ( $_lang['set_user'] . " $spaceinfo[username]" . $_lang['of_group'] );
	kekezu::notify_user ( $_lang['user_group_set'], $_lang['admin'] . "<b>{$admin_info['username']}</b>" . $_lang['set_your_backstage_user_group'], $spaceinfo ['uid'], $spaceinfo ['username'] );
	kekezu::admin_show_msg ( $_lang['rights_set_success'], "index.php?do=$do&view=custom_list", 2, '', 'success' );
}

require $template_obj->template ( 'control/admin/tpl/admin_user_custom_add' );

