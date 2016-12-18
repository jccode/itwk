<?php
define("IN_KEKE", true);
include '../../../app_comm.php';
$cash_cove_arr = kekezu::get_cash_cove();
/**
 * 推荐任务
 */
//作弊时间天数
$day_count =  (time()-strtotime('2011-04-01 00:00:00'))/24/3600;
$day_count = intVal($day_count);
/**
 * 交易额
*/
$task_add_cash = 3488000+40000+$day_count*200000;
$task_cash = intval(db_factory::get_count("select sum(task_cash) from ".TABLEPRE."witkey_task ",0,null,86400));
$task_cash += $task_add_cash;
$task_cash = number_format($task_cash);
/**
 * 交易数量
*/
$task_add_num = 5000+120+$day_count*50;
$task_num = intval(db_factory::get_count("select count(task_id) from ".TABLEPRE."witkey_task ",0,null,86400));
$task_num+= $task_add_num;
$task_num = number_format($task_num);

/**
 * 人才
*/

$user_add_num = 433620+15000+$day_count*3000+180000+780000;
$user_num = intval(db_factory::get_count("select count(uid) from ".TABLEPRE."witkey_member",0,null,86400));
$user_num+= $user_add_num;
$user_num = number_format($user_num);
require $template_obj->template ( 'control/agent/tw_div/agent_tw_zs' );