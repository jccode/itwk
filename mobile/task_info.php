<?php
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );

$task_id = intval ( $task_id );
if ($task_id) {
	$task_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_task where task_id=" . intval ( $task_id ) );
	$task_info ['indus_p_name'] = $indus_p_arr [$task_info [indus_pid]] [indus_name];
	$task_info ['indus_name'] = $indus_arr [$task_info [indus_id]] [indus_name];
	
	kekezu::echojson('',1,$task_info);
} else {
 	kekezu::echojson(array('r'=>'Task does not exist'),0);
}
