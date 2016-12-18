<?php
/**
 * @copyright keke-tech
 * @author tank
 * 
 * 2012-6-28 11:56
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//检查登录
kekezu::check_login();
//检查参数
$tuid or kekezu::show_msg("参数错误");
$tuid==$uid and kekezu::show_msg('操作提示',$_K['siteurl'].'/index.php?do=talent',3,'您无法雇佣自己,请前往人才大厅.','warning');
$tuserinfo = kekezu::get_user_info($tuid);
$income    = db_factory::get_one(' select sum(fina_cash) cash,sum(fina_credit) credit from '.TABLEPRE.'witkey_finance where uid='.$tuid.' and fina_type="in" and fina_action="task_bid" ');

$service_id and $service_info = db_factory::get_one("select * from ".TABLEPRE."witkey_service where service_id = '$service_id' ");

if(kekezu::submitcheck($formhash)){
		if(intval($txt_task_day)<1||intval($txt_task_cash)<0){//时间为空,金额为负数。防止页面修改注入
			kekezu::show_msg('操作提示',$_SERVER['HTTP_REFERER'],2,'非法注入','warning');
		}
		
		// set indus_id, indus_pid. 取最后一个非空的id为indus_id, 倒数第二个非空为indus_pid
		$i = count($indus_ids)-1;
		while ($i >= 0 && $indus_ids[$i] == "") {
			$i--;
		}
		$indus_id = $i >= 1 ? $indus_ids[$i] : 0;
		$indus_pid = $i >= 1 ? $indus_ids[$i-1] : 0;
		
		$task_obj = new Keke_witkey_task_class();
		$task_obj->setIndus_pid ( $indus_pid );
		$task_obj->setIndus_id( $indus_id );//设定服务分类
		$txt_task_title = kekezu::str_filter ( $txt_task_title );
		$task_obj->setTask_title($txt_task_title);	//标题
		$task_obj->setTask_desc( kekezu::str_filter ( $tar_content ));//描述
		$task_obj->setModel_id(4);
		$task_obj->setStart_time(time());
		$task_obj->setEnd_time(time()+($txt_task_day*24*3600));
		$task_obj->setTask_cash($txt_task_cash);
		$task_obj->setReal_cash($txt_task_cash);
		$task_obj->setTask_cash_coverage(0);
		$task_obj->setTask_type(3);
		$task_obj->setTask_status(0);
		$task_obj->setCash_status(0);
		//联系方式保存
		$contact_arr = array();
		$contact['mobile'] and $contact_arr['mobile'] = $cont['mobile'];
		$contact['email'] and $contact_arr['email'] = $cont['email'];
		$contact['qq'] and $contact_arr['qq'] = $cont['qq'];
		$contact['msn'] and $contact_arr['msn'] = $cont['msn'];
		$task_obj->setContact(serialize($contact_arr));
		$task_obj->setTask_status(-1);
		$task_obj->setUid ($uid);//用户信息
		$task_obj->setUsername ($username);
		$task_id = $task_obj->create_keke_witkey_task();
		//附件赋值
		$file_ids and db_factory::execute("update ".TABLEPRE."witkey_file set task_id = '$task_id',obj_id='$task_id' where file_id in ($file_ids)");
		
		$msg_obj = new keke_msg_class();
		$msg_obj->setTitle("任务雇佣邀请");
		$msg_obj->config_init('hire_invite');
		$msg_obj->setUid($tuid);
		$msg_obj->setUsername($tuserinfo['username']);
		$msg_obj->setValue("雇主名称", $tuserinfo['username']);
		$msg_obj->setValue('雇主链接', '<a href="index.php?do=shop&u_id='.$uid.'" target="_blank">'.$username.'</a>');
		//走消息机制发送邀请
		if ($service_info){
			$msg_obj->setValue("订单标题", $txt_task_title);
			$msg_obj->setValue("订单链接", '<a href="index.php?do=task&task_id='.$task_id.'" target="_blank">'.$txt_task_title.'</a>');
		}
		else{
			$msg_obj->setValue("任务标题", $txt_task_title);
			$msg_obj->setValue("任务链接", '<a href="index.php?do=task&task_id='.$task_id.'" target="_blank">'.$txt_task_title.'</a>');
		}
		$msg_obj->send();
		
		//建立稿件
		$work_obj = new Keke_witkey_task_work_class();
		$work_obj->setTask_id($task_id);
		$work_obj->setUid($tuid);
		$work_obj->setUsername($tuserinfo['username']);
		$work_obj->setCycle($txt_task_day);
		$work_obj->setQuote($txt_task_cash);
		$work_obj->create_keke_witkey_task_work();
		
		kekezu::show_msg('操作提示','index.php?do=task&task_id='.$task_id,2,'已向威客发送邀请，等待威客确认接受。','success');
}
//允许的附件格式
$ext_types   = kekezu::get_ext_type ();



require keke_tpl_class::template ('hiretask');