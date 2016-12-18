<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 1.4
 * 2011-9-19上午10:15:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(63);
$oauth_type_list = keke_glob_class::get_open_api();
$config_basic_obj = new Keke_witkey_basic_config_class ();
$config_arr = $kekezu->_weibo_list;
$api_open = $kekezu->_api_open;
/**微博任务开放平台**/
$url = 'index.php?do=msg&view=weibo';
//是否编辑
if (isset ( $submit )) {
		/**interface**/
		foreach ($conf as $k=>$v){
			$config_basic_obj->setWhere ( "k = '$k'" );
			$config_basic_obj->setV($v);
			$res .= $config_basic_obj->edit_keke_witkey_basic_config ();
		}
		/*oauth_api*/
		!empty($api) and $oauth_api=$api or $oauth_api=array();
		$config_basic_obj->setWhere ( "k = 'oauth_api_open'" );
		$config_basic_obj->setV(serialize($oauth_api));
		$config_basic_obj->edit_keke_witkey_basic_config ();
		
		kekezu::admin_system_log($_lang['config_interface_log']);
		if ($res) {
			$kekezu->_cache_obj->del("keke_b3c58336");
			kekezu::admin_show_msg($_lang['oauth_api_config_success'],$url,3,'','success');
		}else{
			kekezu::admin_show_msg($_lang['oauth_api_config_fail'],$url,3,'','warning');
		}
 

}
require $template_obj->template('control/admin/tpl/admin_'.$do.'_'.$view);