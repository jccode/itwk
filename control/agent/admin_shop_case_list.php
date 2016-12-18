<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-6-26 14:09
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (166);

$table_obj = new keke_table_class ( "witkey_shop_case" );
$case_obj = new Keke_witkey_shop_case_class();
$url="index.php?do=$do&view=$view&w[member_id]=".$w['member_id']."&w[truename]=".$w['truename']."&w[shop_id]=".$w['shop_id']."&page=".$page."&w[page_size]=".$w['page_size'];
if($case_id&&$_SESSION['brandType']){
	$auth=$admin_obj->agent_auth("witkey_shop_case", 'case_id='.$case_id,'case_id');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url, 3, '', 'error' );
		die();
	}
}
if(isset($ac) && $ac=='del'){
	if($case_id){
		$res =$table_obj->del ( 'case_id', $case_id, $url );
		kekezu::admin_system_log('删除商铺案例'.':' . $case_id );//日志记录
		$res and kekezu::admin_show_msg ( "删除成功", $url,3,'','success' ) or kekezu::admin_show_msg ("删除失败", $url,3,'','warning' );
	}else {
		kekezu::admin_show_msg ( "删除失败", $url );
	}
} elseif (isset ( $sbt_action )) { //批量删除
	$ckb_string = $ckb;
	is_array ( $ckb_string ) and $ckb_string = implode ( ',', $ckb_string );
	if (count ( $ckb_string )) {
		$case_obj->setWhere ( 'case_id in (' . $ckb_string . ')' );
		$res = $case_obj->del_keke_witkey_shop_case();//删除
		kekezu::admin_system_log('删除多个商铺案例'.':' . $ckb_string );//日志记录
		$res and kekezu::admin_show_msg ( "批量操作成功", $url ,3,'','success') or kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );
	} else {
		kekezu::admin_show_msg ( "批量操作失败", $url,3,'','warning' );
	}
} else {
	$field="cases.*,space.brand";
	$table=array('cases'=>'witkey_shop_case','space'=>'witkey_space');
	$join=array("inner join");
	$on=array('cases.shop_id=space.shop_id');
	$where="space.brand in(".$_SESSION['brandType'].")";		
	$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
	$page and $page = intval ( $page ) or $page = '1';
	//组合查询条件
	$w ['case_id'] and $where .= " and cases.case_id = '".$w['case_id']."' ";
	$w ['case_name'] and $where .= " and cases.case_name like '%".$w['case_name']."%' ";
	$w ['shop_id'] and $where.=" and cases.shop_id = '".$w['shop_id']."' ";
	is_array($w['ord']) and $order=' cases.'.$w['ord']['0'].' '.$w['ord']['1'] or $order= 'cases.case_id desc';
	$res = $table_obj->get_multi_grid ($field,$table,$join,$on,$where,$order,$page, $page_size,$url);
	$case_arr = $res ['data'];
	$pages = $res ['pages'];
}

require $template_obj->template ( 'control/admin/tpl/admin_shop_' . $view );