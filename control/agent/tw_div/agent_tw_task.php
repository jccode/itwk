<?php
define("IN_KEKE", true);
include '../../../app_comm.php';
$cash_cove_arr = kekezu::get_cash_cove();
/**
 * 推荐任务
 */
$task_recomm = db_factory::query ( sprintf ( " select task_id,uid,username,task_title,task_cash,model_id,view_num,work_num,task_cash_coverage from %switkey_task where ((model_id in (1,2,3) ) or (model_id=4 and  task_type!=3 ) ) and task_status='2' and task_cash>1000 order by start_time desc limit 0,30", TABLEPRE ), 1, 86400 );
require $template_obj->template ( 'control/agent/tw_div/agent_tw_task' );