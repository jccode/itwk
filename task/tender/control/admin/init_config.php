<?php

defined('ADMIN_KEKE') or 	exit('Access Denied');

$init_menu = array(
	'普通招标'=>'index.php?do=model&model_id=4&view=list&task_type=1&cove=1',
	'雇佣任务'=>'index.php?do=model&model_id=4&view=list&task_type=1&cove=0',
	'服务需求'=>'index.php?do=model&model_id=4&view=list&task_type=2',
	'直接雇佣'=>'index.php?do=model&model_id=4&view=list&task_type=3',
	$_lang['task_config']=>'index.php?do=model&model_id=4&view=config',
);
 

$init_config = array(
	'model_id'=>4,
	'model_code'=>'tender',
	'model_type'=>'task',
	'model_name'=>$_lang['normal_tender'],
	'model_dir'=>'tender',
	'model_dev'=>'kekezu',
	'model_type'=>'task',
	'model_dev'=>'kekezu',
	'model_status'=>1,
	'zb_fees'=>20,
	'zb_audit'=>2,
	'zb_max_time'=>2,

);
