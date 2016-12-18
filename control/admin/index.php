<?php
define ( "ADMIN_KEKE", TRUE );
define ( "IN_KEKE", TRUE );
require '../../app_comm.php';
/*后台禁止静态化*/
$_K ['is_rewrite'] = 0;

define ( 'ADMIN_ROOT', S_ROOT . './control/admin/' ); //后台根目录

$_K ['admin_tpl_path'] = S_ROOT . './control/admin/tpl/'; //后台模板目录

$dos = array ('work','bid','static','preview','database_manage','permission','prom', 'main', 'side', 'menu', 'tpl', 'index', 'config', 'article',  'art_cat', 'edit_art_cat', 'finance', 'task', 'model', 'tool', 'user', 'login', 'logout', 'button_a', 'user_integration', 'score_config', 'score_rule', 'mark_config', 'mark_rule', 'mark_addico', 'mark_mangeico', 'auth',  'shop', 'group', 'rule', 'case', 'relation_info','nav','msg','trans','keke','payitem','comment','vip','service','product','track','talent');

(! empty ( $do ) && in_array ( $do, $dos )) or $do = 'index';

$admin_info = kekezu::get_user_info ( $_SESSION ['uid'] );
if($do != 'login' && $do != 'logout'){
	if(! $_SESSION ['auid'] || ! $_SESSION ['uid'] || $admin_info ['group_id'] == 0||$_SESSION['group_id']=='12'){
		echo "<script>window.parent.location.href='index.php?do=login';</script>";
		die();
	}
}


keke_lang_class::package_init("admin");
keke_lang_class::loadlang("admin_$do");
$view and 	keke_lang_class::loadlang("admin_{$do}_$view");
$op and keke_lang_class::loadlang("admin_{$do}_{$view}_{$op}");
keke_lang_class::loadlang("admin_screen_lock");


$menu_arr = array (
'task' => '任务管理',
'msg' => '信息管理',
'user' => '会员管理', 
'config' => '配置管理', 
'article' => '文章管理',
'shop' => '商铺管理',
'comment'=>'留言管理',
'finance' => '财务管理',  
'tool' => '系统工具',
'keke'=>'推广员');

/**
 * 后台业务类测试
 */
$admin_obj=new keke_admin_class();

$indus_p_arr = $kekezu->_indus_p_arr;
$indus_c_arr = $kekezu->_indus_c_arr;
$indus_arr = $kekezu->_indus_arr;

$service_indus_p_arr = $kekezu->_service_indus_p_arr;
$service_indus_c_arr = $kekezu->_service_indus_c_arr;
$service_indus_arr = $kekezu->_service_indus_arr;

require ADMIN_ROOT . 'admin_' . $do . '.php';
?>