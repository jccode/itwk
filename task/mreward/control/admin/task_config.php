<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-11-07 11:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (184);
//$a =db_factory::query("select uid,username,work_status from ".TABLEPRE."witkey_task_work where task_id=12 and work_status in(1,2,3) ");
//var_dump($a);
$ops = array ("config", "control", "priv" );
in_array ( $op, $ops ) or $op = 'config';
$ac_url="index.php?do=model&model_id=$model_id&view=config&op=$op";

kekezu::empty_cache();
switch ($op) {
	case "config" : //基本配置
		if($sbt_edit){
			$model_obj=keke_table_class::get_instance("witkey_model");
			! empty ( $fds ['indus_bid'] ) and $fds['indus_bid'] = implode ( ",", $fds ['indus_bid'] ) or $fds['indus_bid'] = '';
			$fds['on_time']=time();
			$fds=kekezu::escape($fds);
			$res=$model_obj->save($fds,$pk);
			
			$res and kekezu::admin_show_msg ($_lang['update_success'],$ac_url, 3,'','success' ) or kekezu::admin_show_msg ($_lang['update_fail'],$ac_url, 3,'','warning');
			}else{
				$indus_arr = $kekezu->_indus_arr;//任务行业
				$indus_index =kekezu::get_indus_by_index ();//索引行业
			}
		break;
	case "control" : //流程配置
		if($ac){
			switch ($ac){
				case "del_time_rule":
					$rule_id and keke_task_config::del_time_rule($rule_id);
					break;
				case "del_delay_rule":
					$rule_id and keke_task_config::del_delay_rule($rule_id);
					break;
			}
		}elseif($sbt_edit){
			$res.=keke_task_config::set_time_rule($model_id,$timeOld,$timeNew); //时间规则配置
			$res.=keke_task_config::set_delay_rule($model_id,$delayOld,$delayNew);//延期规则配置
			
			is_array($conf) and $res.=keke_task_config::set_task_ext_config($model_id,$conf);
			
			$res and kekezu::admin_show_msg ($_lang['update_success'],$ac_url, 3,'','success' ) or kekezu::admin_show_msg ( $_lang['update_fail'],$ac_url, 3,'','warning');
			
		}else{
			$confs = unserialize($model_info['config']);
			is_array($confs)&&extract($confs);//配置解压
			$time_rule=keke_task_config::get_time_rule($model_id);//时间规则
			$delay_rule=keke_task_config::get_delay_rule($model_id);//延期规则
		}
		break;
	case "priv" : //权限配置
		if ($sbt_edit) {
			if ($fds ['allow_times']){
				$perm_item_obj = new Keke_witkey_priv_item_class ();
					foreach ( $fds ['allow_times'] as $k => $v ) {
						$perm_item_obj->setWhere ( " op_id = '$k'" );
						$perm_item_obj->setAllow_times ( intval ( $v ) );
						$perm_item_obj->edit_keke_witkey_priv_item ();
					}
			}
			kekezu::admin_show_msg ( $model_info['model_name'].$_lang['permissions_config_update_success'], "$ac_url",'3','','success');
		} else {
			$perm_item = keke_privission_class::get_model_priv_item($model_id);//权限配置项
		}
		break;
}

if($sbt_edit){
	$log_op_arr = array("config"=>kekezu::lang("basic_config"),"control"=>$_lang['control_config'],"priv"=>$_lang['private_config']);
	$log_msg = $_lang['has_update_more_reward'].$log_op_arr[$op];
	kekezu::admin_system_log($log_msg);
}
require keke_tpl_class::template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_' . $op );