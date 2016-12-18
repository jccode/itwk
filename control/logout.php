<?php
/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-27上午18:29:16
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$refer = parse_url ( $_SERVER ['HTTP_REFERER'] );
$refer_do = array ('do' => null );
isset ( $refer ['query'] ) and parse_str ( $refer ['query'], $refer_do );
! $refer_do ['do'] && $do = 'logout' and $refer_do ['do'] = 'logout';
$_SESSION ['uid'] = '';
$_SESSION ['username'] = '';
$_SESSION ['auid'] = "";
$_SESSION ['user_info'] = "";

unset ( $_SESSION );
if (isset ( $_COOKIE ['user_login'] )) {
	setcookie ( 'user_login', '' );
}

if (isset ( $_COOKIE ['prom_cke'] )) {
	setcookie ( 'prom_cke', '' );
}
if (isset ( $_COOKIE ['epautologin'])) {
	setcookie ( 'epautologin', '', 0, COOKIE_PATH, COOKIE_DOMAIN );
}
if ($kekezu->_sys_config ['user_intergration'] != 1) {
	$synhtml = keke_user_class::user_synlogout ();
	preg_match_all ( "/http(.*)\"/iU", $synhtml, $sys );
	$uc_url = rtrim ( $sys [0] [0], '"' );
	$data ['sys'] = $uc_url;
}
unset ( $_COOKIE );
session_destroy ();
in_array ( $refer_do ['do'], array ('user', 'release', 'shop_release', 'logout', 'register_wizard' ) ) and $jump = $_K['siteurl'] . '/index.php' or $jump = $_SERVER ['HTTP_REFERER'];
$data['syn'] = $jump;
kekezu::echojson('',1,$data);
die ();
//header("location:$jump");