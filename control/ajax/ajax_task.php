<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 任务相关的ajax调用
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
switch ($ajax) {
	case "work_comment" : //稿件回复
		$comment_info = keke_task_class::get_comment ( 'work', $task_id, $work_id, $work_uid );
		break;
	case "load_comment" : //加载新增的留言
		$comment_info = db_factory::get_one ( "select * from " . TABLEPRE . "witkey_comment where comment_id = '$comment_id'" );
		break;
	case "mark_aid" : //辅助评价
		$aid_info = keke_user_mark_class::get_user_aid ( $auid, $mark_type, $mark_status, 1, null, $obj_id );
		break;
	case 'tao_goods' :
		$page_no or $page_no = 1;
		$data = keke_taobaoke_class::get_items_info ( $nick, $page_no );
		break;
	case 'getmaxday' :
		
		$maxday = kekezu::get_show_day ( $task_cash, $model_id );
		$task_config = kekezu::get_task_config ( $model_id );
		$minday = $task_config ['min_day'];
		kekezu::echojson ( '', 1, array ('maxday' => $maxday, 'minday' => $minday, 'mincash' => $task_config ['min_cash'] ) );
		die ();
		break;
	case 'edit' : //编辑时通过这里去取不同任务模型的配置
		$model_id or $model_id = 1;
		$task_config = unserialize ( $kekezu->_model_list [$model_id] ['config'] );
		switch ($model_id) {
			case 1 :
				$model_code = 'sreward';
				break;
			case 2 :
				$model_code = 'mreward';
				break;
			case 4 :
				$model_code = 'tender';
				$cash_cove_arr = kekezu::get_cash_cove ();
				break;
		}
		require keke_tpl_class::template ( 'task/' . $model_code . '/tpl/' . $_K ['template'] . '/task_edit' );
		break;
	case 'upload_source' :
		$ext_types = kekezu::get_ext_type ();
		$work_info = db_factory::get_one ( ' select uid,work_file from ' . TABLEPRE . 'witkey_task_work where work_id=' . $work_id );
		$work_info ['uid'] != $user_info ['uid'] && kekezu::show_msg ( "权限不足", $_K ['siteurl'] . "/index.php?do=task&task_id=$task_id" );
		$t_info = db_factory::get_one ( ' select uid,username,task_title from ' . TABLEPRE . 'witkey_task where task_id=' . $task_id );
		if ($sbt_edit) {
			//保存到稿件属性
			if ($file_ids) {
				$o_fids = ''; //旧附件列表
				$work_info ['work_file'] and $o_fids = $work_info ['work_file'];
				$n_fids = ltrim ( $o_fids . ',' . implode ( ',', array_unique ( explode ( ',', $file_ids ) ) ), ',' ); //新附件列表
				//更新稿件表信息
				db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set work_file = '$n_fids' where work_id = '{$work_id}' " );
				
				//更新附件表信息
				db_factory::execute ( "update " . TABLEPRE . "witkey_file set obj_type='work' ,task_id='$task_id' ,work_id='{$work_id}' where file_id in ($file_ids) " );
			
			}
			//消息通知雇主
			kekezu::notify_user ( "附件上传通知", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_id . '" target="_blank">' . $t_info ['task_title'] . '</a>威客上传了新的附件，您随时可以查看', $t_info ['uid'], $t_info ['username'] );
			kekezu::echojson ( '附件上传成功', 1 );
			die ();
		}
		require keke_tpl_class::template ( "task/upload_source" );
		die ();
		break;
	case 'upload_bidsource' :
		$title = "上传源文件";
		$ext_types = kekezu::get_ext_type ();
		$work_info = db_factory::get_one ( ' select uid,work_file from ' . TABLEPRE . 'witkey_task_work where work_id=' . $work_id );
		$work_info ['uid'] != $user_info ['uid'] && kekezu::show_msg ( "权限不足", $_K ['siteurl'] . "/index.php?do=task&task_id=$task_id" );
		$t_info = db_factory::get_one ( ' select uid,username,task_title from ' . TABLEPRE . 'witkey_task where task_id=' . $task_id );
		if ($sbt_edit) {
			//更新附件表信息
			db_factory::execute ( "update " . TABLEPRE . "witkey_file set obj_type='worksource',task_id='$task_id' ,work_id='{$work_id}' where file_id in ($file_ids) " );
			//消息通知雇主
			kekezu::notify_user ( "源文件上传通知", '您的任务<a href="' . $_K ['siteurl'] . '/index.php?do=task&task_id=' . $task_id . '" target="_blank">' . $t_info ['task_title'] . '</a>威客上传了新的源文件，您随时可以查看', $t_info ['uid'], $t_info ['username'] );
			kekezu::echojson ( '源文件上传成功', 1 );
			die ();
		}
		require keke_tpl_class::template ( "task/upload_bidsource" );
		die ();
		break;
	case 'back_upload' : //后台附件上传
		if ($task_id) {
			$file_ids   = explode(',',$file_ids);
			$task_file  = array_filter ( array_unique ( $file_ids ) );
			$del_ids= array_filter(explode(',',$del_ids));
			foreach($task_file as $k=>$v){
				if(in_array($v,$del_ids)){
					unset($task_file[$k]);
				}
			}
			$task_file  = implode ( ',', $task_file );
			$res = db_factory::execute(" update ".TABLEPRE."witkey_task set task_file='{$task_file}' where task_id={$task_id}");
			if($del_ids){
				foreach($del_ids as $v){//删除附件
					keke_file_class::del_att_file($v);
				}
			}
			kekezu::admin_system_log('编辑任务#'.$task_id.'的附件');
			$res and kekezu::echojson('附件编辑成功',1) or kekezu::echojson('附件编辑失败',0);
		}
		die();
		break;
}
require keke_tpl_class::template ( "ajax/ajax_" . $view );



