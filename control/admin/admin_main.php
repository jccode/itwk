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

/* 新增用户留言 */
$news_count = intval ( db_factory::get_count ( sprintf ( " select count(msg_id) from %switkey_msg where to_uid='%d' and  uid>0 ", TABLEPRE, $admin_info [uid] ) ) );
/* 新增发布任务 */
$task_count = intval ( db_factory::get_count ( sprintf ( " select count(task_id) from %switkey_task where date(from_unixtime(start_time))='%s'", TABLEPRE, date ( 'Y-m-d', time () ) ) ) );
/* 新增注册会员 */
$user_count = intval ( db_factory::get_count ( sprintf ( " select count(uid) from %switkey_space where date(from_unixtime(reg_time))='%s'", TABLEPRE, date ( 'Y-m-d', time () ) ) ) );
/* 新增提现申请 */
$withdraw_count = intval ( db_factory::get_count ( sprintf ( " select count(withdraw_id) from %switkey_withdraw where date(from_unixtime(applic_time))='%s'", TABLEPRE, date ( 'Y-m-d', time () ) ) ) );
/* 新增用户充值 */
$charge_count = intval ( db_factory::get_count ( sprintf ( " select count(order_id) from %switkey_order_charge where date(from_unixtime(pay_time))='%s' ", TABLEPRE, date ( 'Y-m-d', time () ) ) ) );

/* 新增交易维权 */
$report_count = intval ( db_factory::get_count ( sprintf ( " select count(report_id) from %switkey_report where date(from_unixtime(on_time))='%s'", TABLEPRE, date ( 'Y-m-d', time () ) ) ) );

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

$lic = $_K ['ci'];
$str_lic = "";
$verify ="";
$notice = "" ;
$sys = array (
);
$sysinfo = "";

require $template_obj->template ( 'control/admin/tpl/admin_' . $do );