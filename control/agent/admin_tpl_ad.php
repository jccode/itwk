<?php
/**
 * 后台广告位管理
 * @copyright keke-tech
 * @author hr
 * @version v 2.0
 * @date 2011-12-21 下午05:58:43
 * @encoding GBK
 */

defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 32 );
if(strpos($_SESSION['brandType'],'tw')!==false) $where=" target_id in(42,48)";//台湾馆两个广告位


$table_name = 'witkey_ad_target';
$target_arr = kekezu::get_table_data ( '*', $table_name, $where, '', '', '', 'target_id', null ); //private
 
$target_ad_num = kekezu::get_table_data('target_id, count(*) as num', 'witkey_ad', 'target_id is not null', $where, 'target_id', '', 'target_id', null);
while (list($key, $value) = each($target_arr)){
	$target_ad_arr[$key] = $target_ad_num[$key]['num'] ? $target_ad_num[$key]['num'] : '0';
}
 
require $template_obj->template ( 'control/agent/tpl/admin_' . $do . '_' . $view );