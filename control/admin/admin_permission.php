<?php

 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-8-26 14:49:25
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$model_id or kekezu::admin_show_msg($_lang['error_model_param'],"index.php?do=info",3,'','warning');
$op_code or kekezu::admin_show_msg($_lang['error_rights_project'],"index.php?do=info",3,'','warning');
$model_info=$kekezu->_model_list[$model_id];
!$model_info['model_status'] and header("location:index.php?do=config&view=model&model_id=$model_id");//无法编辑已关闭模型的权限

$permission_class_name=$model_info['model_dir']."_permission_class";
switch (isset($sbt_action)){
	case "0":
		$auth_item =keke_auth_base_class::get_auth_item(null,"auth_code,auth_title");
		$perm_rule= keke_privission_class::get_model_priv_item ($model_id,$op_code,'op_id,op_code,condit,op_name,allow_times','op_code');//条件规则，vip特权
		$perm_item = keke_privission_class::get_priv_item($model_id);//获取权限配置
		break;
	case "1":
		if($sbt_action){
			//更新权限项目
			$perm_item_obj=new Keke_witkey_priv_item_class();
			$perm_item_obj->setWhere(" op_id = '".$fds['op_id']."'");
			isset($fds['condit']) or $fds['condit']=array();
			$perm_item_obj->setCondit(implode(",",$fds['condit']));
			$perm_item_obj->edit_keke_witkey_priv_item();
			//更新权限规则
			$perm_rule_obj=new Keke_witkey_priv_rule_class();
			
			if($fds['rule'])
				foreach ($fds['rule'] as $k => $v){
					$perm_rule_obj->setWhere(" r_id = '$k'");
					$v!=1 and $perm_rule_obj->setRule(intval($fds['rule'][$k]));
					$v==1 and $perm_rule_obj->setRule(intval($fds['times'][$k]));
					$perm_rule_obj->edit_keke_witkey_priv_rule();
			}
			//$kekezu->_cache_obj->del ( "priv_rule_item_" . $model_id);
			$file_obj = new keke_file_class();
			$file_obj->delete_files(S_ROOT."./data/data_cache/");
			kekezu::admin_show_msg($_lang['rights_edit_successfully'],$_SERVER['HTTP_REFERER'],3,'','success');
		}
		break;
}



require keke_tpl_class::template ( 'control/admin/tpl/admin_' . $do );