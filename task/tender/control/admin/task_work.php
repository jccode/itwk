<?php
/**
 * 后台入稿件管理
 * @copyright keke-tech
 * @author wrh
 * @version v 2.0
 * 2012-06-19 15:15:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 172);
    $task_work_obj = new Keke_witkey_task_work_class();
    $work_obj = keke_table_class::get_instance ( 'witkey_task_work' );
	$page_obj = $kekezu->_page_obj;  //分页实例化
	$page and $page=intval ( $page ) or $page = 1;
	$page_size and $page_size = intval($page_size) or $page_size=10;

$url = "index.php?do=model&model_id=$model_id&view=work&ord[0]=$ord[0]&ord[1]=$ord[1]&page=$page&page_size=$page_size";

$sql = "select b.task_id,a.username,a.task_id,a.work_id,b.task_title,a.work_desc,a.work_title,a.work_status from "
		.TABLEPRE."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id=b.task_id
		 where b.model_id='$model_id'";
$ex  = '';
intval($task_id) and $ex.=' and b.task_id='.$task_id;
strval($task_title) and $ex.=' and b.task_title like "%'.$task_title.'%" ';
//行数
$sql_count = "select count(*) from ".TABLEPRE."witkey_task_work a left join ".TABLEPRE
			."witkey_task b on a.task_id=b.task_id where b.model_id='$model_id' ".$ex;
$count = db_factory::get_count ( $sql_count );


$ord[0]&&$ord[1] and $ex.=' order by a.'.$ord[0].' '.$ord[1] or $ex.=' order by a.work_id desc ';

$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
$sql .= $ex . $pages ['where'];
$work_arr = db_factory::query ( $sql );
//按任务查询暂放
/*if ($task_id){
   $url_str="index.php?do=model&model_id='$model_id'&view=work&task_id={'$task_id'}&page=$page&page_size=$page_size";
   $work_arr= db_factory::query("select b.task_id,b.username,a.task_id,a.work_id,a.work_title,a.work_desc,a.work_title,a.work_status from ".TABLEPRE."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id=b.task_id where b.model_id='$model_id'");	
}*/
	
if($ac =='del') {
	$work_info = db_factory::get_one(sprintf("select work_title,task_id from  ".TABLEPRE."witkey_task_work where work_id='$work_id' ",TABLEPRE,$work_id));
	kekezu::admin_system_log('刪除'."：{$work_info['work_title']}(".'成功'.")");
	$res = $work_obj->del ( 'work_id', $work_id, $url );
	$res&&db_factory::execute('update '.TABLEPRE.'witkey_task set work_num=work_num-1 where task_id='.$work_info['task_id']);
	$res and kekezu::admin_show_msg($_lang['operate_notice'],$url,2,$_lang['delete_success'],'success') or kekezu::admin_show_msg($_lang['operate_notice'],$url_str,2,$_lang['delete_fail'],"warning");
}

switch ($ac) {
	case "novalid" : //无效
		if ($work_status==16){
		kekezu::keke_show_msg($url,'操作失败稿件已经是无效','error');
		}else{
			db_factory::execute ("update ". TABLEPRE ."witkey_task_work set work_status=16 where work_id='$work_id&page={$page}&page_size={$page_size}'");
			kekezu::admin_show_msg ( '稿件成功设置无效！', $url, 3, '', 'success' );
		}
		
		break;
}

	
//批量删除
if ($sbt_action==$_lang['mulit_delete']&&!empty($ckb)) {
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	$task_ids = db_factory::query('select task_id from '.TABLEPRE.'witkey_task_work where work_id in ("'.$ckb_string.'")');
	$ids      = array();
	foreach($task_ids as $v){
		$ids[$v['task_id']]=1;
	}
	$task_ids = implode(',',array_keys($ids));
	if (count ( $ckb_string )) {
		$task_work_obj->setWhere ( ' work_id in (' . $ckb_string . ') ' );
		$res = $task_work_obj->del_keke_witkey_task_work();
		$res&&db_factory::execute('update '.TABLEPRE.'witkey_task set work_num=work_num-1 where task_id in ("'.$task_ids.'")');
		kekezu::admin_system_log ("批量删除"."$ids" );
		kekezu::admin_show_msg ( $res ? $_lang['mulit_delete_success'] : $_lang['mulit_operate_fail_please_again'], $url,3,'', $res?'success':'warning');
	} else
		kekezu::admin_show_msg ( $_lang['mulit_delete_fail'], $url,3,'','warning' );
}	
require $kekezu->_tpl_obj->template ( 'task/' . $model_info ['model_dir'] . '/control/admin/tpl/task_' . $view );
?>