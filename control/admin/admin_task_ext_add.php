<?php
/**
 * @copyright keke-tech
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 191 );
if ($op == 'show') {
	$task_info = db_factory::get_one('select task_desc,ext_desc from '.TABLEPRE.'witkey_task where task_id='.$task_id);
} else {
	$page = max ( $page, 1 );
	$page_size = max ( $page_size, 10 );
	$url = 'index.php?do=task&ext_status='.$ext_status.'&view='. $view. '&page_size=' . $page_size;
	if ($ac && $task_id) {
		switch ($ac) {
			case 'pass' :
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set ext_status=1 where task_id =' . $task_id );
				$res&&kekezu::admin_system_log ( '审核通过任务#' . $task_id . '的补充需求' );
				break;
			case 'nopass' :
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set ext_status=0 where task_id =' . $task_id );
				$res&&kekezu::admin_system_log ( '审核不通过任务#' . $task_id . '的补充需求' );
				break;
		}
		$res and kekezu::admin_show_msg ( "操作成功", $url, 3, '', 'success' ) or kekezu::admin_show_msg ( "操作失败", $url, 3, '', 'warning' );
	} elseif ($sbt_action && $ckb) {
		$ids = implode ( ',', $ckb );
		switch ($sbt_action) {
			case '批量通过' :
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set ext_status=1 where task_id in (' . $ids . ')' );
				$res&&kekezu::admin_system_log ( '批量审核通过任务#' . $ids . '的补充需求' );
				break;
			case '批量不通过' :
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_task set ext_status=0 where task_id in (' . $ids . ')' );
				$res&&kekezu::admin_system_log ( '批量审核不通过任务#' . $ids . '的补充需求' );
				break;
		}
		$res and kekezu::admin_show_msg ( "批量操作成功", $url, 3, '', 'success' ) or kekezu::admin_show_msg ( "批量操作失败", $url, 3, '', 'warning' );
	}
	intval($ext_status)>-1 or $ext_status=-1;
	$wh = ' from ' . TABLEPRE . 'witkey_task where LENGTH(ext_desc)>0 ';
	//intval($ext_status)>-1 and ($ext_status=='n' and $wh.=' and ext_status=0 ' or $wh.=' and ext_status=1');
	$ord[0]&&$ord[1] and $wh.=' order by '.$ord[0].' '.$ord[1] or $wh.=' order by task_id desc ';
	$c_sql = ' select count(task_id) ' . $wh;
	
	$count = intval ( db_factory::get_count ( $c_sql ) );
	
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	$sql = 'select * ' . $wh. $pages ['where'];
	$task_list = db_factory::query ( $sql );
}

require $template_obj->template ( 'control/admin/tpl/admin_task_' . $view );