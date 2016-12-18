<?php
/**
 * 管理首页
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19下午09:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$task_obj = new Keke_witkey_task_class (); // 实例化任务对象
$user_obj = new Keke_witkey_space_class (); // 实例化用户对象
$file_obj = new keke_file_class ();
$file_size = $file_obj->getdirsize ( S_ROOT . '/data/uploads' );
$file_size = intval ( $file_size / 1024 / 1024 ); // 获取当前附件大小
$tables = db_factory::query ( "SHOW TABLE STATUS " );
foreach ( $tables as $table ) { // 数据库大小
	$dbsize += $table ['Data_length'] + $table ['Index_length'];
}

$dbsize = round ( $dbsize / 1024 / 1024, 2 ); // 转换单位
$mysql_ver = mysql_get_server_info (); // 获得 MySQL 版本

/* 系统信息 */
$sys_info ['os'] = PHP_OS;
$sys_info ['ip'] = $_SERVER ['SERVER_ADDR'];
$sys_info ['web_server'] = $_SERVER ['SERVER_SOFTWARE'];
$sys_info ['php_ver'] = PHP_VERSION;
$sys_info ['mysql_ver'] = $mysql_ver;
$sys_info ['safe_mode'] = ( boolean ) ini_get ( 'safe_mode' ) ? $_LANG ['yes'] : $_LANG ['no'];
$sys_info ['safe_mode_gid'] = ( boolean ) ini_get ( 'safe_mode_gid' ) ? $_LANG ['yes'] : $_LANG ['no'];
$sys_info ['timezone'] = function_exists ( 'date_default_timezone_set' ) ? date_default_timezone_set ( 'Asia/Shanghai' ) : date_default_timezone_set ( 'Asia/Shanghai' );

/* 允许上传的最大文件大小 */
$sys_info ['max_filesize'] = ini_get ( 'upload_max_filesize' );
$sys_info ['file_uploads'] = ini_get ( 'file_uploads' );

$pars = array (
		'ac' => 'run',
		'sitename' => urlencode ( $basic_config ['website_name'] ),
		'siteurl' => htmlentities ( $basic_config ['website_url'] ),
		'charset' => $_K ['charset'],
		'version' => KEKE_VERSION,
		'release' => KEKE_RELEASE,
		'os' => PHP_OS,
		'php' => $_SERVER ['SERVER_SOFTWARE'],
		'mysql' => $mysql_ver,
		'browser' => urlencode ( $_SERVER ['HTTP_USER_AGENT'] ),
		'username' => urlencode ( $_SESSION ['username'] ),
		'email' => $basic_config ['email'] ? $basic_config ['email'] : 'noemail',
		'p_name' => P_NAME
);

$data = http_build_query ( $pars );

$sys = array (
		"ac" => "sysinfo",
		'charset' => $_K ['charset'],
		'p_name' => P_NAME 
);

require $template_obj->template ( 'control/agent/tpl/admin_' . $do );