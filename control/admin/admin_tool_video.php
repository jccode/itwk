<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role (154);

$video_cat = keke_glob_class::get_video_cat();
$video_obj = new Keke_witkey_video_class();
$table_obj = new keke_table_class ( "witkey_video" );
$url = "index.php?do=$do&view=$view&w[v_id]=".$w['v_id']."&w[v_title]=".$w['v_title']."&w[page_size]=$page_size&w[ord]=".$w['ord']."&page=$page";

if (isset ( $ac )) { //单个删除
	if ($v_id) {
			if($ac=='del'){
				$res =$table_obj->del ( 'v_id', $v_id, $url );
				kekezu::admin_system_log('删除视频'.':' . $v_id );//日志记录
				$res and kekezu::admin_show_msg ( "删除成功", $url,3,'','success' ) or kekezu::admin_show_msg ("删除失败", $url,3,'','warning' );
			}
	} else {
		kekezu::admin_show_msg ( "删除失败", $url );
	}
} elseif (isset ( $sbt_action )) { //批量删除
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	if (count ( $ckb_string )) {
		$video_obj->setWhere ( 'v_id in (' . $ckb_string . ')' );
		$res = $video_obj->del_keke_witkey_video ();//删除
		kekezu::admin_system_log('删除多个视频'.':' . $ckb_string );//日志记录
		$res and kekezu::admin_show_msg ( "批量操作成功", $url ,3,'','success') or kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );
	} else
		kekezu::admin_show_msg ( "批量操作失败", $url,3,'','warning' );
} else {

	$where = '  1 = 1'; //查询s
	
	$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
	$page and $page = intval ( $page ) or $page = '1';
	
	//条件
	$w ['v_id'] and $where .= " and v_id = '".$w['v_id']."' ";
	$w ['v_title'] and $where .= " and v_title like '%".$w['v_title']."%' ";
	$w['v_cat'] and $where.=" and v_cat= '".$w['v_cat']."'";
	is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'];

	$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null);
	$video_arr = $r [data];
	$pages = $r [pages];
}

require $template_obj->template ( 'control/admin/tpl/admin_tool_' . $view );