<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

(!$user_info['uid']) and kekezu::show_msg('非法操作', $_K['siteurl'].'/index.php?do=login', 3, '请先登录再进行此操作！', 'warning');

if($opp == 'ajax_add' && $link_name){	
	$readly_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_link where uid='$uid' and link_type is null and link_name = '$link_name'");
	if($readly_info){
		kekezu::echojson ( '您已申请链接已重复，请等待管理员审核', "0" );
		die();
	}
	
	$link_obj = keke_table_class::get_instance("witkey_link");
	$fds['uid'] = $user_info['uid'];
	$fds['username'] = $user_info['username'];
	$fds['on_time'] = time();
	$fds['link_status'] = 0;
	$fds['listorder'] = 0;
	$fds['link_name'] = $link_name;
	$fds['link_url'] = $link_url;

	if($location_id){
		$fds['location'] = keke_core_class::link_make_tag( array( $location_id => 1 ) );
	}
	
	$res = $link_obj->save($fds);	
	$res and kekezu::echojson ( '申请友情链接成功，请等待管理员审核', "1" ) or kekezu::echojson ( '操作失败', "0" );
	
	exit;
}

$link_cat_arr = keke_glob_class::get_link_cat();
$link_cat_arr[$location_id] and $cat_title = $link_cat_arr[$location_id].' -  ';
$title = $cat_title.'申请友情链接';
require $template_obj->template ( 'ajax/ajax_link' );