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
//$where = " task_status != 10 ";   //默认查询条件 
$where = " 1 = 1";

if ( ! $ord ) {
	$ord[] = 'start_time';
	$ord[] = 'desc';
}
//分页
$page_obj = $kekezu->_page_obj;  //分页实例化
$page_size and $page_size = intval($page_size) or $page_size = 10;
$page and $page = intval($page) or $page = 1;

$url = "index.php?do=task&view=$view&page=$page&page_size=$page_size";
$condit  = keke_task_config::condit_format($where,$url,$ord);
$url     = $condit['url'];
$where   = $condit['w'];

//var_dump( $condit );
if (isset ( $ac ) && $task_id) { 

	switch( $ac ){
		case 'del':  //删除单条		
			$task_title = db_factory::get_count ( sprintf ( "select task_title from %switkey_task where task_id='%d' ", TABLEPRE, $task_id ) );
			$task_title = addslashes( $task_title );
			kekezu::admin_system_log ( $_lang ['task_del'] . "：{$task_title}" );
			$task_obj->setWhere ( 'task_id= ' . $task_id );
			$res = $task_obj->del_keke_witkey_task ();
			$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $_SERVER['HTTP_REFERER'], 2, '删除成功', 'success' ) 
			or kekezu::admin_show_msg ( $_lang ['operate_notice'], $_SERVER['HTTP_REFERER'], 2, '删除失败', "warning" );
		break;
		case 'close': //关闭
			$task_obj->setWhere ( 'task_id= ' . $task_id );
			$task_obj->setTask_status(10);
			$res = $task_obj->edit_keke_witkey_task ();
			$res and kekezu::admin_show_msg ( '任务关闭成功', $_SERVER['HTTP_REFERER'],3,'','success' ) or kekezu::admin_show_msg ( '任务关闭失败', $_SERVER['HTTP_REFERER'],3,'','warning' );
		break;
		 /** 新增 start **/
		case "pass"://通过	
			$task_audit_arr = get_task_info ( $task_id );	
			$res=keke_task_config::task_audit_pass ( array($task_id)); 	
			kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'审核通过','success');
			break;
		case "nopass"://不通过
			$task_audit_arr = get_task_info ( $task_id );	
			$res = keke_task_config::task_audit_nopass ( $task_id );
			$v_arr = array ("用户名" => "{$task_audit_arr['username']}", $_lang ['task_title'] => $url, "网站名称" => "$kekezu->_sys_config['website_name']" );
			keke_shop_class::notify_user ( $task_audit_arr ['uid'], $task_audit_arr ['username'], 'task_auth_fail', $task_audit_arr ['task_title'], $v_arr );
			$res and kekezu::admin_show_msg ( $_lang ['operate_notice'], $_SERVER['HTTP_REFERER'], 2, $_lang ['operate_success'], 'success' ) 
			or kekezu::admin_show_msg ( $_lang ['operate_notice'], $_SERVER['HTTP_REFERER'], 2, $_lang ['operate_fail'], "warning" );
			break;	
		case "recommend": //任务推荐
			$res =keke_task_config::task_recommend($task_id);
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'任务推荐成功','success') 
			or kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'任务推荐失败',"warning");
			break;
		case "unrecommend": //取消任务推荐
			$res = keke_task_config::task_unrecommend($task_id);
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'取消推荐成功','success') 
			or kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'取消推荐失败',"warning");
			break;
		case "unfreeze" : //任务解冻
			$res =keke_task_config::task_unfreeze ( $task_id );
			$res and kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'任务解除冻结成功','success') 
			or kekezu::admin_show_msg($_lang['operate_notice'],$_SERVER['HTTP_REFERER'],2,'任务解除冻结失败',"warning");
			break;				
		/** 新增 end **/
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
	//$page_obj->setAjax(1);
	//$page_obj->setAjaxDom("ajax_dom");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );

	//echo $where . $pages [where];
	
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

//任务状态（普通招标）
$tender_status_arr = tender_task_class::get_task_status ();

//任务状态（单人悬赏）
$sreward_status_arr = sreward_task_class::get_task_status ();

//任务状态（计件悬赏）
$preward_status_arr = preward_task_class::get_task_status ();

//任务状态（多人悬赏）
$mreward_status_arr = mreward_task_class::get_task_status ();

require $template_obj->template ('control/admin/tpl/admin_task_unpublished_list');

function func_industry_arr($arr){
	$arr_new = array();
	foreach($arr as $val){
		$arr_new[$val['indus_id']] = $val['indus_name'];
	}
	
	return $arr_new;
}

function get_task_info($task_id) {
	$task_obj = new Keke_witkey_task_class ();
	$task_obj->setWhere ( "task_id = $task_id" );
	$task_info = $task_obj->query_keke_witkey_task ();
	$task_info = $task_info ['0'];
	return $task_info;
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