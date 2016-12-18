<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');

$ops = array ('pub', 'task', 'toolbox','integ','shop','g_pub','prom','credit','service','index','employerpay','needmark','needwork','collect');

in_array($op,$ops) or $op ='index';
/**
 * 子集菜单
 */
$sub_nav = array(
	array ("pub" => array ($_lang['pub_goods'], "doc-new" )),
	array ("task" => array ($_lang['work_manage'], "doc-lines-stright" ),
		  "g_pub"=>array ($_lang['g_pub'],"notepad"),
 		   "shop" => array ($_lang['goods_trans'], "box" )),
	array ("prom" => array ($_lang['prom_make_money'], "emotion-smile" ) )
);

$op=='task' and $model_type='task' or $model_type='shop';
$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = '$model_type'", 'model_id asc ', '', '', 'model_id', 3600 );
$model_fds = array_keys($model_list);
$model_id or $model_id = intval($model_fds['0']);

// 取各类任务统计数目
$all_task_count = keke_task_class::get_witkey_task_count($uid);

require 'user_'.$view.'_'.$op.'.php';