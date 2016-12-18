<?php

/**
 * 任务详细页、任务首页的入口文件
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-8-11上午08:05:04
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


if (isset($task_id)) {
	 //拥有查看屏蔽任务的权限的用户组编号
	$task_rule_group_id = array(1,3,8);
	
	$task_ext_obj = new Keke_witkey_task_ext_class ();
	$task_ext_obj->setWhere ( 'a.task_id=' . intval ( $task_id ) );
	$task_info = $task_ext_obj->query_keke_witkey_task ();
	$task_info = kekezu::k_stripslashes ( $task_info ['0'] );
	$task_info ['uid'] != $uid && $user_info['group_id'] == 0 && $task_info ['task_status'] == 1 and kekezu::show_msg ( $_lang ['friendly_notice'], $_K['siteurl'].'/index.php?do=task_list', 2, $_lang ['task_auditing'] );
	if (!in_array($user_info['group_id'],$task_rule_group_id))
	{
		$task_info ['uid'] != $uid && $task_info['model_id']!=4 && $task_info ['task_status'] == 0 and kekezu::show_msg ( $_lang ['friendly_notice'], $_K['siteurl'].'/index.php?do=task_list', 2, '任务未付款' );
	}
	
	
	if (! $task_info) {
		kekezu::show_msg ( $_lang ['operate_notice'], $_K['siteurl']."/index.php?do=index", '1', $_lang ['task_not_exsit_has_delete'], 'error' );
	}

	$union_hand = keke_union_class::hand_link ($task_info);
	if ($task_info ['task_union'] == 2 && $task_info ['r_task_id'] && intval ( $u ) == 1) {
		$union_obj = new keke_union_class ( $task_id );
		$union_obj->view_task ();
	}
	$model_info = $model_list [$task_info ['model_id']];
	$model_code = $model_info ['model_code'];
	keke_lang_class::package_init ( "task" );
	keke_lang_class::loadlang ( $model_info ['model_dir'] );
	keke_lang_class::loadlang ( "task_info" );

	function mark_echo($mark_status){
		switch($mark_status){
			case 1:
				echo '好评';
				break;
			case 2:
				echo '中评';
				break;
			case 3:
				echo '差评';
				break;
		}
	}
    
    // Breadcrumb
    $breadcrumb = $kekezu->findIndusWithParentById($task_info["indus_id"]);
    $breadcrumb = array_reverse($breadcrumb);
    
	 //SEO 
	$seo_indus = $indus_c_arr[$task_info['indus_id']]['indus_name'].' '.$indus_p_arr[$task_info['indus_pid']]['indus_name'];
	$page_title =  $task_info['task_title'].' '.$seo_indus.'_IT帮手网 ';
	$page_keyword = $task_info['task_title'].'，'.$indus_c_arr[$task_info['indus_id']]['indus_name'].'，'.$indus_p_arr[$task_info['indus_pid']]['indus_name']; //print_r($task_info);
	$page_description = '欢迎前来IT帮手网参加'.$task_info['task_title'].'，想了解更多类似'.$task_info['task_title'].'的威客任务信息，请进入'.$indus_p_arr[$task_info['indus_pid']]['indus_name'].'频道，它属于'.$indus_c_arr[$task_info['indus_id']]['indus_name'].'、'.$indus_p_arr[$task_info['indus_pid']]['indus_name'].'类项目,欢迎来报名参与,如果您获得了中标,将得到该项目悬赏金百分之九十以上并可增加积分。';
	
	$model_info and (require S_ROOT . "/task/" . $model_info ['model_dir'] . "/control/task_info.php") or kekezu::show_msg ( $_lang ['error'], "index.php?do=index", 3, $_lang ['task_model_not_exist'], 'error' );
	
} else{
	header("location:".$_K['siteurl']."/index.php?do=task_list");
}