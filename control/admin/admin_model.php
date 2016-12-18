<?php
/**
 * 后台任务模型入口 
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-8-13上午04:49:25
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$model_id or kekezu::admin_show_msg ( $_lang['error_model_param'], "index.php?do=info",3,'','warning' );

$model_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_model where model_id = '$model_id'" );

if (! $model_info ['model_status']) {
	header ( "location:index.php?do=config&view=model" );
	die ();
}


keke_lang_class::package_init ( "task_{$model_info ['model_dir']}" );
keke_lang_class::loadlang ( "admin_{$do}_{$view}" );
keke_lang_class::loadlang("task_{$view}");
keke_lang_class::package_init ( "shop" );
keke_lang_class::loadlang("{$model_info [model_dir]}_{$view}");

if($view=='edit'){
	if($ac=='modify'&&$task_id){
		$sql = ' update '.TABLEPRE.'witkey_task set pay_item="'.$item.'"';
		//$sql.= ',is_top='.intval($top);
		$sql.= ' where task_id='.intval($task_id);
		$res = db_factory::execute($sql);
		if($res){
			kekezu::admin_system_log("配置任务#".$task_id.'增值项为:'.$item);
			kekezu::echojson('增值项配置成功',1);
		}else{
			
		}kekezu::echojson('增值项配置失败',0);
		die();
	}else{
		//增值服务列表
		$item_list = kekezu::get_table_data ( 'item_code,big_pic,small_pic,model_code,item_name', 'witkey_payitem', ' is_open=1 and user_type="employer" ', '', '', '', 'item_code', 3600 );
	}
}
require S_ROOT . $model_info ['model_type'] . "/" . $model_info ['model_dir'] . "/control/admin/admin_route.php";


