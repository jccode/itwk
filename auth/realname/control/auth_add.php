<?php

/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01下午2:37:13
 */		
defined ( 'IN_KEKE' ) or exit('Access Denied');
$page_title = $_lang['realname_auth'];
$step_arr = array("step1"=>array( $_lang['step_one'], $_lang['auth_intro']),
				"step2"=>array( $_lang['step_two'], $_lang['fill_in_realname_auth_info']),
				"step3"=>array( $_lang['step_three'], $_lang['waiting_for_background_check']),
				"step4"=>array( $_lang['step_four'], $_lang['background_check_pass']));

$auth_step= keke_auth_realname_class::get_auth_step($auth_step,$auth_info);

$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver)."&zone=$zone";

$selected = array();
switch ($auth_step){
	case "step1": //步骤1
		$selected['step1'] = 'class="selectedLava"';		
		$realname_zone_arr = keke_glob_class::get_realname_zone(); //身份证所在地区
	break;
	case "step2": //步骤2
		$selected['step2'] = 'class="selectedLava"';
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			empty($fds['zone']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请先选择您的身份证所在地区！', 'alert_error' ) ;
			empty($_FILES['id_pic']['name']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请上传身份证复印件！', 'alert_error' ) ;
			empty($_FILES['id_pic_back']['name']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请上传身份证复印件(背面)！', 'alert_error' ) ;
			$auth_obj->add_auth($fds, 'id_pic', true, 'id_pic_back'); //认证申请提交
		}
	break;
	case "step3": //步骤3
		$selected['step3'] = 'class="selectedLava"';
	break;
	case "step4": //步骤4
		$selected['step4'] = 'class="selectedLava"';
	break;
}

require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );