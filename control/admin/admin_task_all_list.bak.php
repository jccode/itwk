<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 176 );

$task_obj = new Keke_witkey_task_class();  
$where = " task_status != 10 ";   //默认查询条件 

//分页
$page_obj = $kekezu->_page_obj;  //分页实例化
$page_size and $page_size = intval($page_size) or $page_size = 10;
$page and $page = intval($page) or $page = 1;

$url = "index.php?do=task&view=$view&page=$page&page_size=$page_size";
$condit  = keke_task_config::condit_format($where,$url,$ord);
$url     = $condit['url'];
$where   = $condit['w'];
if (isset ( $ac ) && $task_id) { 

	switch( $ac ){
		case 'del':  //删除单条
			$task_obj->setWhere ( 'task_id= ' . $task_id );
			$res = $task_obj->del_keke_witkey_task ();
			kekezu::admin_system_log ( $_lang['delete_task_unpublished_log'] . "_" . $task_id );
			$res and kekezu::admin_show_msg ( $_lang['list_task_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' );
		break;
		case 'close': //关闭
			$task_obj->setWhere ( 'task_id= ' . $task_id );
			$task_obj->setTask_status(10);
			$res = $task_obj->del_keke_witkey_task ();
			$res and kekezu::admin_show_msg ( $_lang['list_task_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['list_finance_delete_fail'], $url,3,'','warning' );
		break;
	}
	
} elseif (isset ( $ckb )) { 
	
	$ckb_string = implode ( ',', $ckb );
	$task_obj->setWhere ( 'task_id in (' . $ckb_string . ')' );	
	switch ($sbt_action) {
		case $_lang['mulit_delete'] : //批量删除
			$res = $task_obj->del_keke_witkey_task ();
			kekezu::admin_system_log ( $_lang['mulit_delete_task_log'] . "_" .$ckb_string);
		break;
	}
	
	$res and kekezu::admin_show_msg ( $_lang['mulit_operate_delete_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['mulit_operate_delete_fail'], $url,3,'','warning' );

} else {
	//查询统计
	$task_obj->setWhere ( $where ); 
	$count = $task_obj->count_keke_witkey_task ();
	$page_obj->setAjax(1);
	$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	//查询结果数组
	$task_obj->setWhere ( $where . $pages [where] );
	$task_arr = $task_obj->query_keke_witkey_task ();  
	
	//行业
	$industry_obj = new Keke_witkey_industry_class();
	$industry_obj->setWhere ( " 1=1" ); 
	$industry_arr = $industry_obj->query_keke_witkey_industry();
	$industry_name_arr = func_industry_arr($industry_arr);
	$industry_arr = list_to_tree($industry_arr);
}

require $template_obj->template ('control/admin/tpl/admin_task_unpublished_list');

function func_industry_arr($arr){
	$arr_new = array();
	foreach($arr as $val){
		$arr_new[$val['indus_id']] = $val['indus_name'];
	}
	
	return $arr_new;
}

function list_to_tree($list, $pk='indus_id',$pid = 'indus_pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}