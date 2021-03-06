<?php
/**
 * @copyright keke-tech
 * @author zhang
 * @version v 2.0
 * 2011-11-11下午02:29:58
 */

defined ( "ADMIN_KEKE" ) or exit ( "Access denied!" );
$path = S_ROOT.'/keke_client/keke/config.php';
include $path;
kekezu::admin_check_role ( 133 );
$op or $op='config';
if ( $op=='config' ) {//配置
	$data = file_get_contents($path);
	if ($submit){
		$res = preg_replace(array(
		"/application\'] = (\d)*(\s)*/",
		"/auto_commit\'] = (\d)*(\s)*/",
		"/log\'] = '(\w)*(\s)*'/",
		"/keke_app_id\'] = '(\w)*(\s)*'/",
		"/keke_app_secret\'] = '(\w)*(\s)*'/",
		), array(
		"application'] = ".intval($application),
		"auto_commit'] = ".intval($auto_commit),
		"log'] = '$log'",
		"keke_app_id'] = '$keke_id'",
		"keke_app_secret'] = '$keke_secret'",
		), $data);
		if(file_put_contents($path, $res)){
			kekezu::admin_show_msg('配置成功', '', 3);
		}
	}
}
require $template_obj->template ( "control/admin/tpl/admin_keke_account_config" );