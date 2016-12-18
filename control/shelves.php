<?php
/**
  * 出售服务
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-20 9:30
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title= "出售服务".'--' . $_K ['html_title'];
kekezu::check_login();
$basic_url = $_K['siteurl']."/index.php?do=$do&step={$step}&service_type=".intval($service_type);
$ext = '*.jpg;*.jpeg;*.gif;*.png;*.bmp';
$price_unit  = keke_glob_class::get_price_unit();//价格单位


$steps = array ('step1','step2');
! in_array ( $step, $steps ) && $step = 'step1';



if($step=='step2'){
	if(isset($formhash)&&kekezu::submitcheck($formhash)){
		$service_obj = new Keke_witkey_service_class();
		
		
		// set indus_id, indus_pid. 取最后一个非空的id为indus_id, 倒数第二个非空为indus_pid
		$i = count($indus_ids)-1;
		while ($i >= 0 && $indus_ids[$i] == "") {
			$i--;
		}
		$indus_id = $i >= 1 ? $indus_ids[$i] : 0;
		$indus_pid = $i >= 1 ? $indus_ids[$i-1] : 0;
		
			
		$service_obj->setIndus_pid ( $indus_pid );
		$service_obj->setIndus_id ( $indus_id );//设定服务分类
		$service_obj->setTitle ( kekezu::str_filter ( $txt_title ) );	//标题
		$service_obj->setContent (  kekezu::str_filter ( $tar_content ));//描述
		
		//服务配图
		$service_obj->setPic($pic);
		$service_obj->setPrice($txt_price);//价格
		$service_obj->setUnite_price($unite_price);
		
		$service_obj->setCity($province . "," . $city . "," . $area);//地区
		$service_obj->setAddress($txt_address);
		$service_obj->setPoint($point);

		$service_obj->setService_type($service_type);
		$service_obj->setService_status(1);
		$service_obj->setOn_time(time());
		
		$service_obj->setShop_id ( $user_info['shop_id'] ); //店铺编号
		$service_obj->setUid ($uid);//用户信息
		$service_obj->setUsername ($username);
		
		//联系方式保存
		$contact_arr = array();
		$contact['mobile'] and $contact_arr['mobile'] = $cont['mobile'];
		$contact['email'] and $contact_arr['email'] = $cont['email'];
		$contact['qq'] and $contact_arr['qq'] = $cont['qq'];
		$contact['msn'] and $contact_arr['msn'] = $cont['msn'];
		$service_obj->setContact(serialize($contact_arr));
		
		
		
		if(intval($service_id)){
			$service_obj->setWhere('service_id='.$service_id);
			$res = $service_obj->edit_keke_witkey_service();
		}else{
			$res = $service_obj->create_keke_witkey_service();
		}
		
		//附件赋值
		$file_ids and db_factory::execute("update ".TABLEPRE."witkey_file set obj_type='service',obj_id='$task_id' where file_id = $file_ids");
		
		kekezu::show_msg ( "操作提示", $_K['siteurl'].'/index.php?do=user&view=space&op=service', '1', '恭喜您，服务信息编辑成功！', 'alert_right' ) ;
		
	}


	if(isset($service_id)){
		if(intval($service_id)){			
			$service_info = db_factory::get_one("select * from ".TABLEPRE."witkey_service where service_id=".$service_id);
		/* var_dump($service_info); */
		}
		if($service_info['city']){
			$loca=explode(',',$service_info['city']);
		}
		if($service_info['contact']){
			$service_info['cont'] = unserialize($service_info['contact']);
		}
	}
	
	if($service_type==2||$service_info['service_type']==2){
		$service_indus_p_arr = $indus_p_arr;
		$service_indus_arr = $indus_arr;
	}
	
}




require  keke_tpl_class::template ($do);
