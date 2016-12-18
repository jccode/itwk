<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * @todo 后台认证 项编辑
 * 2011-9-01 11:35:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
/**
 * 认证项配置编辑
 */
$auth_code or kekezu::admin_show_msg ( $_lang['error_param'], "index.php?do=auth",3,'','warning');
//编辑认证项目
if ($sbt_edit){
	$big_icon = $hdn_big_icon;
	$small_before_icon = $hdn_small_before_icon;
	$small_after_icon = $hdn_small_after_icon;
	keke_auth_fac_class::edit_item($auth_code, $fds,$pk,$big_icon,$small_after_icon,$small_before_icon);
}
//var_dump($auth_item);
kekezu::admin_system_log($_lang['edit_auth'] . $auth_code);//日志记录

if($auth_code!='weibo') 
	require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
else 
	require  S_ROOT.'./auth/'.$auth_item['auth_dir'].'/control/admin/auth_config.php';

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}