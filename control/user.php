<?php
/**
  * 用户中心入口文件
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
keke_lang_class::package_init ( "user" );
$_K ['is_rewrite'] = 0;
//取当前页面地址
$this_url = 'index.php?do=login&refer='.urlencode($_SERVER['REQUEST_URI']);
kekezu::check_login ($this_url);
$user_info = $kekezu->_userinfo;
$do and keke_lang_class::loadlang ( $do );
$views = array (
		'setting',
		'finance',
		'employer',
		'witkey',
		'message',
		'space',
);
if(! in_array ( $view, $views )){
	if($user_info['homepage']!=='')
		$view=$user_info['homepage'];
	else $view = 'setting';
}
$view==null && $view='setting';
($view || $op == 'basic') and keke_lang_class::loadlang ( "{$do}_{$view}" );
$view == 'setting' and keke_lang_class::loadlang ( "{$do}_{$op}" );
$op and keke_lang_class::loadlang ( "{$do}_{$view}_{$op}" );
/* 中心最顶级url */
$origin_url = $_K['siteurl']."/index.php?do=$do&view=$view";
$page_title = $_lang ['user_center'];

$nav=array(
		"setting"=>array('账号设置',"cog"),
		"employer"=>array('我是雇主',"buyer"),
		"witkey"=>array('我是威客',"seller"),
		"message"=>array('消息管理',"sound-high"),
		"finance"=>array('财务管理',"chart-line2"),
		"space"=>array('商铺管理',"chart-line2")
);

$auth_item_list = keke_auth_base_class::get_auth_item ( null, null, 1, $w );
require 'user/user_' . $view . '.php';

