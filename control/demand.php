<?php
/**
  * 出售服务
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-20 9:30
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title= "提交需求".'--' . $_K ['html_title'];
kekezu::check_login();
$basic_url = $_K['siteurl']."/index.php?do=$do&step={$step}&service_type=".intval($service_type);
$ext = '*.jpg;*.jpeg;*.gif;*.png;*.bmp';
$price_unit  = keke_glob_class::get_price_unit();//价格单位


$model_info = $model_list[4];
$model_info['config'] and $task_config = unserialize($model_info['config']); //任务配置

if(kekezu::submitcheck($formhash)){
		if(intval($txt_day)<1||intval($txt_price)<0){//时间为空,金额为负数。防止页面修改注入
			kekezu::show_msg('操作提示',$_SERVER['HTTP_REFERER'],2,'非法注入','warning');
		}
		$task_obj = new Keke_witkey_task_class();
		$task_obj->setIndus_pid ( $indus_pid );
		$task_obj->setIndus_id( $indus_id );//设定服务分类
		$task_obj->setTask_title( kekezu::str_filter ( $txt_title ) );	//标题
		$task_obj->setTask_desc( kekezu::str_filter ( $tar_content ));//描述
		$task_obj->setModel_id(4);
		$task_obj->setStart_time(time());
		$sub_time = time()+($txt_day*24*3600);
		$end_time = $sub_time + ($task_config['choose_time']*24*3600);
		$task_obj->setEnd_time($end_time);
		$task_obj->setSub_time($sub_time);
		$task_obj->setTask_cash($txt_price);
		$task_obj->setTask_cash_coverage(0);
		$task_obj->setTask_type(2);
		$task_obj->setCash_status(0);
		
		$task_obj->setUnite_price($unite_price);
		
		$task_obj->setCity($province . "," . $city . "," . $area);//地区
		$task_obj->setAddress($txt_address);
		$task_obj->setPoint($point);

		$task_obj->setTask_status(1);

		$task_obj->setUid ($uid);//用户信息
		$task_obj->setUsername ($username);
		
		//联系方式保存
		$contact_arr = array();
		$contact['mobile'] and $contact_arr['mobile'] = $cont['mobile'];
		$contact['email'] and $contact_arr['email'] = $cont['email'];
		$contact['qq'] and $contact_arr['qq'] = $cont['qq'];
		$contact['msn'] and $contact_arr['msn'] = $cont['msn'];
		$task_obj->setContact(serialize($contact_arr));

		if(intval($task_id)){
			$task_obj->setWhere('task_id='.intval($service_id));
			$res = $task_obj->edit_keke_witkey_task();
		}else{
			$task_id = $res = $task_obj->create_keke_witkey_task();
		}
		
		kekezu::show_msg('操作提示',$_K['siteurl'].'/index.php?do=task&task_id='.$task_id,2,'恭喜您，需求信息编辑成功。','success');
	}
	

	if(isset($task_id)){
		if(intval($task_id)){
			$task_info = db_factory::get_one(sprintf("select * from %switkey_task where task_id=%d",TABLEPRE,intval($task_id)));
		}
		if($task_info['city']){
			$loca=explode(',',$task_info['city']);
		}
		if($task_info['contact']){
			$task_info['cont'] = unserialize($task_info['contact']);
		}
	}

require  keke_tpl_class::template ($do);