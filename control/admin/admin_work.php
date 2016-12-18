<?php
/**
 * 后台稿件管理
 * 	所有非报价稿件，即work表cycle字段<0
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 189 );
$task_work_obj = new Keke_witkey_task_work_class ();
$work_obj = keke_table_class::get_instance ( 'witkey_task_work' );
$page_obj = $kekezu->_page_obj; //分页实例化
$page and $page = intval ( $page ) or $page = 1;
$page_size and $page_size = intval ( $page_size ) or $page_size = 10;
$url = "index.php?do=work&task_id=$task_id&task_title=$task_title&uname=$uname&ord[0]=$ord[0]&ord[1]=$ord[1]&page=$page&page_size=$page_size";

$sql = "select b.task_id,b.model_id,a.username,a.task_id,a.work_id,b.task_title,a.work_desc,a.work_title,a.work_status,a.work_time from " 
		. TABLEPRE . "witkey_task_work a left join " . TABLEPRE . "witkey_task b on a.task_id=b.task_id where a.cycle is null";
$ex = '';
intval ( $task_id ) and $ex .= ' and b.task_id=' . $task_id;
strval ( $task_title ) and $ex .= ' and b.task_title like "%' . $task_title . '%" ';
strval($uname) and $ex.=' and a.username like "%'.$uname.'%"';
//行数
$sql_count = "select count(*) from " . TABLEPRE . "witkey_task_work a left join " 
			. TABLEPRE . "witkey_task b on a.task_id=b.task_id where a.cycle is null " . $ex;
$count = db_factory::get_count ( $sql_count );

$ord [0] && $ord [1] and $ex .= ' order by a.' . $ord [0] . ' ' . $ord [1] or $ex .= ' order by a.work_id desc ';

$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
$sql .= $ex . $pages ['where'];
$work_arr = db_factory::query ( $sql );

if ($ac == 'del') {
	$work_info = db_factory::get_one ( sprintf ( "select work_title,task_id from  " . TABLEPRE . "witkey_task_work where work_id='$work_id' ", TABLEPRE, $work_id ) );
	kekezu::admin_system_log ( '刪除' . "：{$work_info['work_title']}(" . '成功' . ")" );
	$res = $work_obj->del ( 'work_id', $work_id, $url );
	$res && db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task set work_num=work_num-1 where task_id=' . $work_info ['task_id'] );
	$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $url, 2, $_lang ['delete_success'], 'success' ) or kekezu::admin_show_msg ( $_lang ['operate_notice'], $url_str, 2, $_lang ['delete_fail'], "warning" );
}
isset($work_id) and $work_info = db_factory::get_one('select * from '.TABLEPRE.'witkey_task_work where work_id='.$work_id);
$s_arr = array(1=>'一等奖',2=>'二等奖',3=>'三等奖',4=>'四等奖',5=>'五等奖',11=>'中标',13=>'入围');
switch ($ac) {
	case "novalid" : //无效
			db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_status=16 where work_id='$work_id'" );
			kekezu::admin_show_msg ( '稿件成功设置无效！', $url, 3, '', 'success' );
		break;
	case "valid" : //有效
			db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_status=0 where work_id='$work_id'" );
			kekezu::admin_show_msg ( '稿件成功设置有效！', $url, 3, '', 'success' );
		break;
	case 'cancel'://取消
		db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_status=0 where work_id='$work_id'" );
		kekezu::admin_system_log ( '客服'.$admin_info['username'].'取消稿件#'.$work_id.'的'.$s_arr[$work_info[work_status]].'资格');
		kekezu::notify_user("稿件取消{$s_arr[$work_info[work_status]]}通知",'由于您的稿件#'.$work_id.'存在争议，客服已取消您的'.$s_arr[$work_info[work_status]].'资格',$work_info['uid'],$work_info['username']);
		kekezu::admin_show_msg ( "稿件成功取消{$s_arr[$work_info[work_status]]}！", $url, 3, '', 'success' );
		break;
	case 'choose':
		db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_status=".intval($st)." where work_id='$work_id'" );
		kekezu::admin_system_log ( '客服'.$admin_info['username'].'设置稿件#'.$work_id.'为'.$s_arr[$st]);
		kekezu::notify_user("稿件获得{$s_arr[$st]}资格通知",'您的稿件#'.$work_id.'已被客服设置为'.$s_arr[$st],$work_info['uid'],$work_info['username']);
		kekezu::admin_show_msg ( "稿件成功设为{$s_arr[$st]}！", $url, 3, '', 'success' );
	break;
}
//批量删除
if ($sbt_action == $_lang ['mulit_delete'] && ! empty ( $ckb )) {
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string ); 
	$task_ids = db_factory::query ( 'select task_id from ' . TABLEPRE . 'witkey_task_work where work_id in ("' . $ckb_string . '")' );
	$ids = array ();  //var_dump($ckb_string);exit;
	foreach ( $task_ids as $v ) {
		$ids [$v ['task_id']] = 1;
	}
	$task_ids = implode ( ',', array_keys ( $ids ) ); 
	if (count ( $ckb_string )) {
		$task_work_obj->setWhere ( ' work_id in (' . $ckb_string . ') ' );
		$res = $task_work_obj->del_keke_witkey_task_work ();
		$res && db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task set work_num=work_num-1 where task_id in ("' . $task_ids . '")' );
		/*if($res){
			foreach($ids as $k => $v){
				get_task_work_count($k);
			}
		}
		exit; */
		
		kekezu::admin_system_log ( "批量删除" . "$ids" );
		kekezu::admin_show_msg ( $res ? $_lang ['mulit_delete_success'] : $_lang ['mulit_operate_fail_please_again'], $url, 3, '', $res ? 'success' : 'warning' );
	} else
		kekezu::admin_show_msg ( $_lang ['mulit_delete_fail'], $url, 3, '', 'warning' );
}

 //计算稿件数目
function get_task_work_count($task_id){
	if(!$task_id){
		return false;
	}
	$count = db_factory::get_count(" select count(*) from " . TABLEPRE . "witkey_task_work where task_id = '$task_id'");
	return db_factory::execute ( 'update ' . TABLEPRE . "witkey_task set work_num= '$count' where task_id = '$task_id'" );
}

function re_button($m,$s){
	global $s_arr;
	$ts    = $s_arr;
	$m     = max($m,1);
	$tmp   = array();
	switch($s){
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 11:
		case 13:
			$tmp = array('ac'=>'cancel',
						 'arr'=>array('desc'=>'取消'.$ts[$s])
						);
			break;
		default:
			switch ($m){
				case 1:
					unset($ts[1],$ts[2],$ts[3],$ts[4],$ts[5]);
					break;
				case 2:
					unset($ts[11]);
					break;
			}
			unset($ts[$s]);
			$tmp = array('ac'=>'choose','arr'=>$ts);
			break;
	}
	return $tmp;
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do );