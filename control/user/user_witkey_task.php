<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-9 12:10
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$cove_arr = kekezu::get_table_data("*","witkey_task_cash_cove","","","","","cash_rule_id");
/**
 * 三级横向菜单
 */
$third_nav=array();
foreach ($model_list as $v){
	  $third_nav[]=array(
	  				"1"=>$v['model_id'],
	  				"2"=>$_lang['go_in'].$v['model_name']);
}
$where = '';
switch($opp){
	case 'xsrw':
		$model_id = 2;
		$model_name = '悬赏任务';
		$where .=" and model_id between 1 and 2 ";
		break;
	case 'zbrw':
		$model_name = '招标任务';
		$model_id = 4;
		$where .=" and model_id = 4 and task_cash_coverage>0 ";
		break;
	case 'jjrw':
		$model_name = '计件任务';
		$model_id = 3;
		$where .=" and model_id = 3 ";
		break;
	case 'fwxq':
		$model_name = '服务需求';
		$model_id = 4;
		$where .=" and model_id = 4 and task_type = 2 ";
		break;
	case 'gyrw':
		$model_name = '雇佣任务';
		$model_id = 4;
		$where .=" and model_id = 4 and task_type = 1 and task_cash_coverage<1 ";
		break;
	case 'zjgy':
		$model_name = '直接雇佣';
		$model_id = 4;
		$where .=" and model_id = 4 and task_type = 3 ";
		break;
}
if($model_id){
	
	$model_info=$model_list[$model_id];//模型信息
	$tab_name="witkey_task_work";
	$time_fds  ="work_time";
	$id_fds    ="work_id";
	$satus_fds="work_status";

/* 排序方式*/
$ord_arr=array(" a.$id_fds desc "=>$_lang['manuscript_id_desc'],
		   " a.$id_fds asc "=>$_lang['manuscript_id_asc'],
		   " a.$time_fds desc "=>$_lang['submit_time_desc'],
		   " a.$time_fds asc "=>$_lang['submit_time_asc']);
	$cln     =$model_info['model_code']."_task_class";
    
    /* echo $cln; */
    
	$page_obj=$kekezu->_page_obj;
	/**获取对应任务的状态值**/
	$status_arr=call_user_func(array($cln,"get_task_status"));
	/*获取稿件状态**/
	$work_arr  =call_user_func(array($cln,"get_work_status"));
	/* $work_arr[0] = $_lang['yet_deal_with']; */

	//**周参加任务统计**//
	
	isset($task_status) or $task_status='all';
	isset($$satus_fds) or $$satus_fds='all';
	
	$join_count=intval(db_factory::get_count(sprintf("select count(task_id) from %s%s
 	where YEARWEEK(FROM_UNIXTIME(%d)) = YEARWEEK('%s') and uid='%d' ",TABLEPRE,$tab_name,$time_fds,date('Y-m-d H:i:s',time()),$uid)));
	
	$sql=" select a.$satus_fds,a.$time_fds,a.$id_fds,b.task_id,b.task_cash,b.task_title,b.model_id,b.task_cash_coverage,b.task_status from ".TABLEPRE
		 .$tab_name." a left join ".TABLEPRE."witkey_task b on a .task_id=b.task_id where a.uid='$uid'";
	$count_sql="select a.$id_fds from ".TABLEPRE.$tab_name." a left join ".TABLEPRE."witkey_task b on a .task_id=b.task_id where a.uid='$uid'";
	
	($task_status==='0') and $where.=" and b.task_status='".intval($task_status)."'" or ($task_status!='all' and $where.=" and b.task_status = '".intval($task_status)."' ");
	($$satus_fds==='0') and $where.=" and a.$satus_fds='".intval($$satus_fds)."'" or ($$satus_fds!='all' and $where.=" and a.$satus_fds = '".intval($$satus_fds)."' ");
	
	$$id_fds&&$$id_fds!=$_lang['enter_manuscript_id']       and $where.=" and a.$id_fds = '".intval($$id_fds)."' ";
	$ord and $where.=" order by $ord " or $where.=" order by a.$id_fds desc ";
	
	$page_size and $page_size=intval($page_size) or $page_size='10';
	$page and $page=intval($page) or $page='1';
	$url="index.php?do=$do&view=$view&op=$op&model_id=$model_id&page_size=$page_size&task_status=$task_status&$satus_fds=".$$satus_fds."&page=$page";
	$count=intval(db_factory::execute($count_sql.$where));
	$pages=$page_obj->getPages($count, $page_size, $page, $url);

	$task_info = db_factory::query($sql.$where.$pages['where']);
}
require keke_tpl_class::template ( "user/" . $do . "_".$view."_" . $op );


