<?php

/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01下午2:37:13
 */
defined ( 'IN_KEKE' ) or exit('Access Denied');

 //异步检查
if (isset ( $check_mobile ) && ! empty ( $check_mobile )) {
	$dt = db_factory::get_count(" select mobile from " . TABLEPRE . "witkey_auth_mobile where mobile='".$check_mobile."' and uid != '$uid' and auth_status =1");
	if($dt){
		echo "该手机号码已被他人占用";
	}else{
		$dt = db_factory::get_count(" select mobile from " . TABLEPRE . "witkey_auth_mobile where mobile='".$check_mobile."' and uid != '$uid'");
		if($dt){
			echo "该手机号码已被他人申请认证";
		}else{
			echo 1;
		}		
	}

	die ();
}

$page_title= $_lang['mobi_auth'];
$step_arr=array("step1"=>array( $_lang['step_one'], $_lang['enter_cellphone_num']),
				"step2"=>array( $_lang['step_two'], $_lang['check_in_cellphone_num']),
				"step3"=>array( $_lang['step_three'], $_lang['auth_pass']));

$auth_step= keke_auth_mobile_class::get_auth_step($auth_step,$auth_info);
$verify = kekezu::reset_secode_session($ver?0:1);//安全码输入
$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver);
switch ($auth_step){
	case "step1": 
		$selected['step1'] = '1'; 
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$auth_obj->add_auth($fds);//认证申请提交
		}
	break;
	case "step2":
		//获取手机
		$sql="select mobile from keke_witkey_auth_mobile where uid=".$_SESSION['uid'];
		$dt = db_factory::get_one($sql);
		//开始验证
		$selected['step2'] = '1'; 
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$auth_obj->valid_auth($fds);
		}
	break;
	case "step3":
		$selected['step3'] = '3';
	break;
}
require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );