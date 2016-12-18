<?php
/**
 * 行业管理
 * @copyright keke-tech
 * @author Tao
 * @version v 2.0
 * 2011-8-24 16:28
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
if($type==1){
	kekezu::admin_check_role ( 7 );	
}elseif ($type==2){
	kekezu::admin_check_role ( 6 );
}


$indus_obj = new Keke_witkey_industry_class ();
$file_obj = new keke_file_class();
$table_obj = new keke_table_class ( "witkey_industry" );
$url = "index.php?do=$do&view=$view&type=$type&w[indus_pid]={$w[indus_pid]}&w[indus_name]={$w[indus_name]}
		&$ord[0]={$ord[1]}";

if ($ac == 'del') { //删除
	$table_obj->del ( 'indus_id', $indus_id, $url );
	kekezu::admin_show_msg($_lang['delete_success'],'index.php?do=task&view=industry',3,'','success');
} elseif (isset ( $sbt_action )) {
	if ($edit_indus_name_arr) { //编辑
		foreach ( $edit_indus_name_arr as $k => $v ) {
			$indus_obj->setWhere ( "indus_id = $k" );
			$indus_obj->setIndus_name ( $v );
			$indus_obj->edit_keke_witkey_industry ();
		
		}
	
		kekezu::admin_system_log ( $_lang['edit_industry'] );
	} elseif ($add_indus_name_arr) { //删除
		 
		foreach ( $add_indus_name_arr as $k => $aindarr ) {
			foreach ( $aindarr as $kk => $v ) {
				if (! $v)
					continue;
				$indus_obj->_indus_id = null;
				$indus_obj->setIndus_name ( $v );
				$indus_obj->setIndus_pid ( $k );
				$indus_obj->setSeo_catname(kekezu::get_pinyin(kekezu::escape($v),'UTF-8'));
				$indus_obj->setIndus_type(intval($type));
				$indus_obj->setListorder ( $add_indus_name_listarr [$k] [$kk] ? $add_indus_name_listarr [$k] [$kk] : 0 );
				$indus_obj->setOn_time ( time () );
				$indus_obj->create_keke_witkey_industry ();
				epweike_seo_class::create_htacess();//更新htacess
			}
		}
		kekezu::admin_system_log ( $_lang['delete_industry'] );
	}
		$file_obj->delete_files(S_ROOT."./data/data_cache/");
		$file_obj->delete_files(S_ROOT.'./data/tpl_c/');
	kekezu::admin_show_msg ( $_lang['operate_success'], 'index.php?do=' . $do . '&view=' . $view.'&type='.intval($type),3,'','success' );
} elseif ($ac === 'editlistorder') { //改排序
	if ($iid) {
		$indus_obj->setWhere ( 'indus_id=' . $iid );
		$indus_obj->setListorder ( $val );
		$indus_obj->edit_keke_witkey_industry ();
	}
} else {
	$where = ' 1 = 1 and indus_type = '.intval($type);
	//查询条件
	if (isset ( $sbt_search )) {
		intval ( $w [indus_pid] ) and $where .= " and indus_pid = $w[indus_pid]";
		strval ( $w [indus_name] ) and $where .= " and indus_name like '%$w[indus_name]%'";
		$ord [1] and $where .= " order by $ord[0] $ord[1]";
	}
	$indus_arr = kekezu::get_table_data ( "*", "witkey_industry", $where, "", "", "", "", 0 );
	sort ( $indus_arr );
	
	if (! $w) {
		$t_arr = array ();
		kekezu::get_tree ( $indus_arr, $t_arr, 'cat', NULL, 'indus_id', 'indus_pid', 'indus_name' );
		$indus_show_arr = $t_arr;
		//var_dump($t_arr);die(); 
		unset ( $t_arr );
	} else {
		//sort($indus_arr);
		$indus_show_arr = $indus_arr;
	}
	//搜索行业下拉菜单
	$temp_arr = array ();
	$indus_option_arr = kekezu::get_industry ();
	kekezu::get_tree ( $indus_option_arr, $temp_arr, "option", $w [indus_pid] );
	$indus_option_arr = $temp_arr;
	unset ( $temp_arr );
	$indus_index_arr = kekezu::get_indus_by_index ();
}

function sortTree($nodeid, $arTree) {
	$res = array ();
	for($i = 0; $i < sizeof ( $arTree ); $i ++)
		if ($arTree [$i] ["indus_pid"] == $nodeid) {
			array_push ( $res, $arTree [$i] );
			$subres = sortTree ( $arTree [$i] ["indus_id"], $arTree );
			for($j = 0; $j < sizeof ( $subres ); $j ++)
				array_push ( $res, $subres [$j] );
		}
	return $res;
}

require $kekezu->_tpl_obj->template ( "control/admin/tpl/admin_task_$view" );
