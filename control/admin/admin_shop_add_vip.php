<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-09-29 15:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

 //保存编辑
if($is_submit){	 
	$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$fds[uid]'");
	$space_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_space where uid='$fds[uid]'");

	if(!$space_info){
		kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,5, '该用户不存在', 'warning');
	}
	if(!$shop_info){
		kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,5, '该用户还未开通商铺，无法升级', 'warning');
	}

	$fds['day'] = intval($fds['day']);	
	$fds['cash_cost'] = intval($fds['cash_cost']);
	$fds['day'] or kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,5, '请填写VIP期限', 'warning');
	$fds['shop_level'] or kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,5, '请选择要升级的VIP商铺类型', 'warning');
	
	 //已是VIP
	if($space_info['isvip'] == 1){
		 //相同等级续费、拓展升级旗舰
		if($space_info['shop_level'] <= $fds['shop_level']){ 
			$space_vip_start_time = $space_info['vip_start_time']; //sapce表有效期
			$space_vip_end_time = ($space_info['vip_end_time']+($fds['day']*86400));  //sapce表有效期			
			$history_vip_start_time = $space_info['vip_end_time'];
			$history_vip_end_time = ($space_info['vip_end_time']+($fds['day']*86400));
		}else{ //VIP降级暂未开通
			kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,5, 'VIP降级功能暂未开通', 'warning');
		}
	
	}else{ //普通成为VIP
		$space_vip_start_time = time();
		$space_vip_end_time = (time()+($fds['day']*86400));
		$history_vip_start_time = $space_vip_start_time;
		$history_vip_end_time = $space_vip_end_time;
	}
	
	/*echo '$vip_start_time:'.date('Y-m-d H:i:s',$space_info['vip_start_time']).'</br>';
	echo '$vip_end_time:'.date('Y-m-d H:i:s',$space_info['vip_end_time']).'</br>';		
	echo '$space_vip_start_time:'.date('Y-m-d H:i:s',$space_vip_start_time).'</br>';
	echo '$space_vip_end_time:'.date('Y-m-d H:i:s',$space_vip_end_time).'</br>';
	echo '$history_vip_start_time:'.date('Y-m-d H:i:s',$history_vip_start_time).'</br>';
	echo '$history_vip_end_time:'.date('Y-m-d H:i:s',$history_vip_end_time).'</br>';
	*/
	
     //添加到升级记录表	
	$vip_history_obj = new Keke_witkey_vip_history_class();
	$vip_history_obj->setUid($shop_info['uid']);
	$vip_history_obj->setUsername($shop_info['username']);
	$vip_history_obj->setStart_time($history_vip_start_time);
	$vip_history_obj->setEnd_time($history_vip_end_time);
	$vip_history_obj->setDay($fds['day']);
	$vip_history_obj->setCash_cost($fds['cash_cost']);
	$vip_history_obj->setCredit_cost(0);
	$vip_history_obj->setH_status(1);
	$vip_history_obj->setLevel_id($fds['shop_level']);
	$res_1 = $vip_history_obj->create_keke_witkey_vip_history();
	
	 //修改用户表
	$space_obj = new Keke_witkey_space_class();
	$space_obj->setWhere("uid = '$fds[uid]'");
	$space_obj->setShop_level($fds['shop_level']);
	$space_obj->setIsvip('1');
	$space_obj->setVip_start_time($space_vip_start_time);
	$space_obj->setVip_end_time($space_vip_end_time);
	$res_2 = $space_obj->edit_keke_witkey_space();
	
	 //修改商铺表
	$shop_obj = new Keke_witkey_shop_class();
	$shop_obj->setWhere("uid = '$fds[uid]'");
	$shop_obj->setShop_level($fds['shop_level']);
	$shop_obj->setIsvip('1');
	$res_3 = $shop_obj->edit_keke_witkey_shop();
	
	if($res_1){		
		kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,3, '操作成功，请检查下详细记录', 'success');
	}else{
		kekezu::show_msg('消息提示', "index.php?do=$do&view=$view&t_uid=$fds[uid]" ,3, '操作有误', 'warning');
	}
}

if( $t_username ){
	$shop_info = db_factory::get_one("select * from ".TABLEPRE."witkey_shop where username='$t_username'");
	$space_info = db_factory::get_one("select * from ".TABLEPRE."witkey_space where username='$t_username'");
	$shop_info['shop_info'] and $shop_info['shop_info'] = unserialize($shop_info['shop_info']); 	
	$vip_history_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_vip_history where username = '$t_username' ORDER BY h_id DESC");
	
}elseif($t_uid ){
	$shop_info = db_factory::get_one(sprintf("select * from %switkey_shop where uid=%d",TABLEPRE,$t_uid));
	$space_info = db_factory::get_one(sprintf("select * from %switkey_space where uid=%d",TABLEPRE,$t_uid));
	$shop_info['shop_info'] and $shop_info['shop_info'] = unserialize($shop_info['shop_info']); 	
	$vip_history_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_vip_history where uid = '$t_uid' ORDER BY h_id DESC");
}

$vip_level_arr = db_factory::query("select * from " . TABLEPRE . "witkey_vip_level where 1"); 
$vip_level_arr = format_vip_level($vip_level_arr);

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );

function format_vip_level($vip_arr){
	$new_list = array();
	foreach($vip_arr as $v){
		$new_list[$v['level_id']] = $v;
	}
	
	return $new_list;
}