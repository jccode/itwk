<?php
/**
 * @copyright keke-tech
 * @author hr
 * @version v 2.0
 * @date 2011-12-19 下午02:51:51
 * @encoding GBK
 */

defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 59 );
! isset ( $op ) && $op = 'config'; //默认跳转到reg_prom配置页面
$url = 'index.php?do=' . $do . '&view=' . $view . '&op=' . $op;
$table_name = 'witkey_prom_rule';

//编辑
if (isset ( $sbt_edit )) {
	switch ($op) {
		case 'config' : //编辑注册推广配置
			$config = array ();
			$rule_obj = new Keke_witkey_prom_rule_class ();
			$rule_obj->setWhere ( 'prom_code="auth"' );
			$rule_obj->setCash ( floatval ( $auth_cash ) ); //注册奖励
			$rule_obj->setCredit ( floatval ( $auth_credit ) );
			$rule_obj->setConfig ( implode($auth_code,','));
			$result .= $rule_obj->edit_keke_witkey_prom_rule ();
			//修改basic_config记录
			$result .= db_factory::execute ( 'update ' . TABLEPRE . 'witkey_basic_config set v="' . intval ( $prom_reg_is_open ) . '" where k="prom_open";' );
			$result .= db_factory::execute ( 'update ' . TABLEPRE . 'witkey_basic_config set v="' . intval ( $prom_period ) . '" where k="prom_period";' );
			
			$message = $result ? $_lang['register_prom_config_edit_success'] : $_lang['no_change'];
			kekezu::admin_system_log ( $_lang['edit_register_prom_config'] );
			kekezu::admin_show_msg ( $message, $url,3,'','success' );
		
		case 'pub_task' :
		case 'task_bid' :
			$ext_config = array ();
			($ckb_model && is_array ( $ckb_model )) and $ext_config ['model'] = implode ( ',', $ckb_model );
			switch ($op) {
				case 'pub_task' :
					//修改pub_task          cash,credit,rate记录
					$pub_task_rate && $ext_config ['pub_task_rate'] = floatval ( $pub_task_rate ); //小数
					//修改config记录
					$ext_config = serialize ( $ext_config );
					$result = db_factory::execute ( 'update ' . TABLEPRE . $table_name . " set config='$ext_config',cash='" . $pub_task_cash . "' , credit='" . $pub_task_credit . "' , rate='" . $pub_task_rate . "' where prom_code='pub_task';" );
					kekezu::admin_system_log ( $_lang['update_task_prom_config'] );
					$result and kekezu::admin_show_msg($_lang['task_prom_config_update_success'],$url,3,'','success') or kekezu::admin_show_msg( $_lang['record_no_change'],$url,3,'','warning');
					
					break;
				case 'task_bid' :
					$bid_task_rate && $ext_config ['bid_task_rate'] = intval ( $bid_task_rate );
					//修改config记录
					$ext_config = serialize ( $ext_config );
					$result = db_factory::execute ( 'update ' . TABLEPRE . $table_name . " set config='$ext_config',rate='" . intval ( $bid_task_rate ) . " ' where prom_code='task_bid';" );
					kekezu::admin_system_log ( $_lang['update_bid_prom_config'] );					
					$result and kekezu::admin_show_msg ($_lang['bid_prom_config_update_success'],$url,3,'','success') or kekezu::admin_show_msg($_lang['record_no_change'],$url,3,'','warning');
					break;
			}
			break;
	}
}

switch ($op) {
	case 'config' : //注册推广初始化
		$config = $kekezu->get_table_data ( '*', $table_name, ' type in ("reg","auth")  ', '', '', '', 'prom_code', null );
		$global_config = $kekezu->get_table_data ( '*', 'witkey_basic_config', ' type="prom"', '', '', '', 'k' );
		
		$auth_item = keke_auth_base_class::get_auth_item('','',true);
		$auth = $config ['auth'];
		break;
	case 'pub_task' : //任务推广
	case "task_bid" : //承接推广
		$op == 'pub_task' || $op == 'task_bid' and $model_type = 'task' or $model_type = 'shop';
		$model_info = kekezu::get_table_data ( 'model_id,model_dir,model_name,config,model_type', 'witkey_model', "model_status=1 and model_dir!='employtask' and model_dir!='tender' and model_type='$model_type'", '', '', '', 'model_name' );
		$prom_config = $kekezu->get_table_data ( '*', 'witkey_prom_rule', "prom_code='$op'", '', '', '', 'prom_code' );
		$prom_config [$op] ['config'] and $prom_config = array_merge ( $prom_config [$op], unserialize ( $prom_config [$op] ['config'] ) ); //配置
		break;
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );	