<?php
/**
 * 技能管理
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19早上0:54:00
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 8);
$table_obj = new keke_table_class ( "witkey_skill" );

//搜索行业下拉菜单
$temp_arr = array ();
$indus_option_arr = kekezu::get_industry ();
kekezu::get_tree ( $indus_option_arr, $temp_arr,"option",$w[indus_pid] );
$indus_option_arr = $temp_arr;
unset ( $temp_arr );
is_array($indus_arr)&&sort ( $indus_arr );
$indus_show_arr = array();
kekezu::get_tree($indus_arr, $indus_show_arr,'cat',NULL,'indus_id','indus_pid','indus_name');
$indus_show_arr = kekezu::get_table_data('*',"witkey_industry","",'indus_id','','','indus_id');
$where = ' 1 = 1';

$url = "index.php?do=$do&view=$view&w[indus_pid]={$w[indus_pid]}&w[skill_name]={$w[skill_name]}
		&page_size=$page_size&page=$page
		&$ord[0]={$ord[1]}";

intval ( $page_size ) and $page_size = intval ( $page_size ) or $page_size = 10;
intval ( $page ) and $page = intval ( $page ) or $page = 1;

if(isset($sbt_search)){	
	intval ( $w [indus_id] ) and $where .= " and indus_id = $w[indus_id]";
	strval ( $w [skill_name] ) and $where .= " and skill_name like '%$w[skill_name]%'";	
	$ord [1] and $where .= " order by $ord[0] $ord[1]";	
}
$r = $table_obj->get_grid ( $where, $url, $page, $page_size );
$skill_arr = $r [data];
$pages = $r [pages];

if ($ac == 'del') { 
	$skill_log = keke_table_class::all_table_info("witkey_skill", array("skill_id"=>$skill_id));
	$res = $table_obj->del('skill_id', $skill_id); 
	kekezu::admin_system_log($_lang['delete_skill'].":".$skill_log[skill_name]);
	$res and kekezu::admin_show_msg($_lang['delete_success'], $url,'3','','success') or kekezu::admin_show_msg($_lang['delete_fail'], $url,'3','','warning');
}
//批量删除
if ($sbt_action) {
	if (! count($ckb)){
		kekezu::admin_show_msg ($_lang['choose_operation'], $url ,'3','','warning');
	}else{
		$res = $table_obj->del ('skill_id',$ckb);
		
		kekezu::admin_system_log($_lang['mulit_delete_skill']);
		$res and kekezu::admin_show_msg($_lang['delete_success'], $url,'3','','success') or kekezu::admin_show_msg($_lang['delete_fail'], $url,'3','','warning');
	}
}
//递归分类列表
$temp_arr = array ();
kekezu::get_tree ( $indus_arr, $temp_arr, 'option', NULL, 'indus_id', 'indus_pid', 'indus_name' );
$indus_arr = $temp_arr;

unset ( $temp_arr );
require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_task_' . $view );


