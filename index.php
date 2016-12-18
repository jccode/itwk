<?php
define ( "IN_KEKE", TRUE );

include 'app_comm.php';

//$_K['is_rewrite'] = 1;
//$kekezu->_sys_config['website_url'] = $basic_config['website_url'] = $_K['siteurl'] = "http://192.168.1.66/epweike";


$dos = array (
		'payitem_tools',
		'oauth_register',
		'oauth_login',
		'login',
		'ajax',
		'show_menu',
		'index',
		'register',
		'seccode',
		'login',
		'logout',
		'page_404',
		'page_303',
		'help',
		'help_list',
		'help_info',
		'message',
		'release',
		'shelves',
		'user',
		'space',
		'mark',
		'task',
		'task_list',
		'shop',
		'footer',
		'indus',
		'agreement',
		'report',
		'seccode',
		'bid',
		'work',
		'prom',
		'reset_email',
		'avatar',
		'pay',
		'browser',
		'shop_order',
		'article',
		'task_map',
		'verify_secode',
		'protocol',
		'excite_email',
		'mobile',
		'link',
		'single',
		'about',
		'special',
		'hire',
		'hire_list',
		'hiretask',
		'talent',
		'lottery',
		'vip',
		'service',
		'demand',
		'subscribe',
		'map',
		'review',
		'errorpage',
		'brand',
		'match',
		'integrity',
		'coper',
		'liangpu'
);

(! empty ( $do ) && in_array ( $do, $dos )) and $do or $do = 'index';
isset ( $m ) && $m == "user" and $do = "avatar";

keke_lang_class::package_init ( "index" );
keke_lang_class::loadlang ( $do );

$page_keyword = $kekezu->_sys_config ['seo_keywords'];
$page_description = $kekezu->_sys_config ['seo_desc'];

$uid = $kekezu->_uid;
$username = $kekezu->_username;
$messagecount = $kekezu->_messagecount;
$user_info = $kekezu->_userinfo;

$indus_p_arr = $kekezu->_indus_p_arr;
$indus_c_arr = $kekezu->_indus_c_arr;
$indus_arr = $kekezu->_indus_arr;

// jc add 
$indus_map = $kekezu->_indus_map;
// debug
//echo json_encode($indus_map);

$service_indus_p_arr = $kekezu->_service_indus_p_arr;
$service_indus_c_arr = $kekezu->_service_indus_c_arr;
$service_indus_arr = $kekezu->_service_indus_arr;

$model_list = $kekezu->_model_list;
$nav_arr = $kekezu->_nav_list;
$lang_list = $kekezu->_lang_list;
$language = $kekezu->_lang;
$api_open = $kekezu->_api_open;
$weibo_list = $kekezu->_weibo_list;
$attent_api_open = $kekezu->_attent_api_open;
$attent_list = $kekezu->_weibo_attent;
$style_path = $kekezu->_style_path;
$style_path = SKIN_PATH; 

$tt=$_GET['do'];
$dd1='class="normaltab"';
$dd2='class="normaltab"';
$dd3='class="normaltab"';
$dd4='class="normaltab"';
$dd5='class="normaltab"';
$dd6='class="normaltab"';
$dd7='class="normaltab"';
switch($tt){
    case 'task_list':
        $dd1='class="hovertab"';
        break;
    case 'release':
        $dd2='class="hovertab"';
        break;
    case 'talent':
        $dd3='class="hovertab"';
        break;
    case 'vip':
        $dd4='class="hovertab"';
        break;
    case 'liangpu':
        $dd5='class="hovertab"';
        break;
    case 'help':
        $dd6='class="hovertab"';
        break;
    case 'article':
        $dd7='class="hovertab"';
        break;
case 'indus':
$dd1='class="hovertab"';
break; 

}



include S_ROOT . './control/' . $do . '.php';
