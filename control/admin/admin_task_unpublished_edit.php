<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-5-17上午9:17
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 161 );
$url = "index.php?do=$do&view=unpublished_list";

$task_obj = new Keke_witkey_task_class(); 

if (isset ( $ac ) && $is_submit == 1) { 
			
	if($ac == 'add'){ //添加
		$task_obj->setWhere( " task_id = " . $task_id );
		$task_obj->setTask_title($fds['task_title']);
		$task_obj->setTask_desc($fds['task_desc']);
		$task_obj->setIndus_id($fds['indus_id']);
		
		$res = $task_obj->edit_keke_witkey_task();
		$res and kekezu::admin_show_msg ( '任务修改成功', $url,3,'',' success' ) or kekezu::admin_show_msg ( '任务修改失败', $url,3,'','warning' );
	}
	
} else {//界面	
	
	$task_obj->setWhere ( " task_id = " . $task_id );
	$task_arr = $task_obj->query_keke_witkey_task ();	
	$task_arr = $task_arr[0];  
	
	 //附件列表
	$file_obj = new Keke_witkey_file_class(); 
	$file_obj->setWhere ( " obj_type = 'task' and task_id = " . $task_id ); 
	$file_list = $file_obj->query_keke_witkey_file ();
	
	//行业
	$industry_obj = new Keke_witkey_industry_class();
	$industry_obj->setWhere ( " 1=1" ); 
	$industry_arr = $industry_obj->query_keke_witkey_industry();
	$industry_arr = list_to_tree($industry_arr);
	
	require $template_obj->template ( 'control/admin/tpl/admin_task_' . $view);
}	

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