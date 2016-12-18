<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

/**
 * 子集菜单
 */
$sub_nav=array(
	array(
	   "rights"=>array($_lang['rights_manage'],"key"),
	   "report"=>array($_lang['report_manage'],"shield"),
	   "complaint"=>array($_lang['complain_manage'],"openid")))
	;
$oppps = array('rights',"report","complaint");
in_array ( $opp, $oppps ) or $opp = 'rights';
$ac_url = $origin_url . "&op=$op"."&opp=".$opp;

$role or $role = '1'; //交易维权角色   :1=>我发起的  ,2=>我收到的
intval ( $status ) or $status = ''; //默认状态为所有
//strval ( $obj ) or $obj = "task"; //默认交易维权对象
$report_obj = new Keke_witkey_report_class (); //实例交易维权对象


$where = " 1 = 1 ";
intval ( $page ) or $page = "1";
intval ( $page_size ) or $page_size = "10";
$url = $ac_url . "&role=$role&status=$status&obj=$obj&ord=$ord&page_size=$page_size&page=$page";
if ($ac) {
	switch ($ac) {
		case "download" :
			keke_file_class::file_down ( $filename, $filepath );//下载相应文件
			break;
		case "del" :
			if ($report_id) {
				$res = db_factory::execute (sprintf(" delete from %switkey_report where report_id='%d'",TABLEPRE,$report_id));
				$filepath and keke_file_class::del_file($filepath);//删除相应文件
				$res and kekezu::show_msg ( $_lang['operate_notice'], $url."#userCenter", "3", $_lang['delete'] . $action_arr [$opp] . $_lang['record_success'],'success' ) or kekezu::show_msg ( $_lang['operate_notice'], $url."#userCenter", "3", $_lang['delete'] . $action_arr [$opp] . $_lang['record_fail'],"warning" );
			} else
				kekezu::show_msg ( $_lang['operate_notice'], $url."#userCenter", "3", $_lang['please_select_delete'] . $action_arr [$opp] . $_lang['record'], "warning" );
			break;
	}
} else {
	$transrights_status = keke_report_class::get_transrights_status (); //交易维权状态
	$transrights_object = keke_report_class::get_transrights_obj(); //交易维权对象
	$transrights_type   = keke_report_class::get_transrights_type();//交易维权类型
	//取得举报分类
	$buyer_type = keke_glob_class::get_buyer_report_type();
	$seller_type = keke_glob_class::get_seller_report_type();
	/**
	 *三级菜单 
	 */
	$third_title = kekezu::lang($opp.'_manage');
	$third_nav = array (
	  "launched" => array ($_lang['launch'].$_lang['de'].$transrights_type[$opp]['1'],"",1),
	  "received" => array ($_lang['receive'].$_lang['de'].$transrights_type[$opp]['1'],"",2)
	);
	/**
	 * @todo 排序
	 */
	$ord_arr = array (" report_id desc " => $action_arr[$opp] . $_lang['num_desc'], " report_id asc " => $action_arr[$opp] . $_lang['num_asc'], " on_time desc " => $_lang['submit_time_desc'], " on_time asc " => $_lang['submit_time_asc'] );
	$page_obj = $kekezu->_page_obj; //分页对象
	$role == 1 and $where .= " and uid='$uid' " or $where .= " and to_uid='$uid' ";
	$status and $where .= " and report_status='$status'";
	if($start_time&&!$end_time){
		$where.=" and on_time >= ".intval(strtotime($start_time));
	}elseif(!$start_time&&$end_time){
		$where.=" and on_time <= ".intval(strtotime($end_time));
	}elseif($start_time&&$end_time){
		if($start_time==$end_time){
			$where.=" and on_time = ".intval(strtotime($start_time));
		}else{
			$where.=" and on_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
		}
	}
	$obj and $where .= " and obj='$obj'";
	$opp and $where .= " and report_type = '".$transrights_type[$opp]['0']."'";
	$ord and $where .= " order by $ord " or $where .= " order by on_time desc ";
	//var_dump($where);die();
	$report_obj->setWhere ( $where );
	$count = intval ( $report_obj->count_keke_witkey_report () ); //统计
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url, "#userCenter" );
	
	$report_obj->setWhere ( $where . $pages ['where'] );
	$report_list = $report_obj->query_keke_witkey_report ();
}

require keke_tpl_class::template ( "user/" . $do . "_" . $op);