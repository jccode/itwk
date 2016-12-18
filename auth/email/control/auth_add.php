<?php

/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01下午2:37:13
 */
defined ( 'IN_KEKE' ) or exit('Access Denied');

//异步检查
if (isset ( $check_email ) && ! empty ( $check_email )) {
	//$dt = db_factory::get_count(" select email from " . TABLEPRE . "witkey_member where email='".$check_email."' and uid!=".$uid);
	$dt = db_factory::get_count(" select email from " . TABLEPRE . "witkey_auth_email where email='".$check_email."' and uid!=".$uid." and auth_status = 1");
	if($dt){
		echo "该邮箱已被他人注册";
	}else{
		$dt = db_factory::get_count(" select email from " . TABLEPRE . "witkey_auth_email where email='".$check_email."' and uid!=".$uid);
		if($dt){
			echo "该邮箱已被他人申请认证";
		}else{
			echo 1;
		}
	}
	die ();
}

$page_title= $_lang['email_auth'];
if($del_auth_id){
	$email_obj = new Keke_witkey_auth_email_class();
	$email_obj->setWhere("email_a_id = " .intval($del_auth_id));
	$email_obj->del_keke_witkey_auth_email(); 
}
$step_arr=array("step1"=>array($_lang['step_one'], $_lang['input_email_address']),
				"step2"=>array($_lang['step_two'], $_lang['auth_email']),
				"step3"=>array($_lang['step_three'], $_lang['auth_pass']));

$auth_step= keke_auth_email_class::get_auth_step($auth_step,$auth_info);

//$verify = kekezu::reset_secode_session($ver?0:1);//安全码输入
$verify   = 0;
$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver);



$selected = array();
switch ($auth_step){
	case "step1":
		$selected['step1'] = '1';
	break;
	case "step2":
		if($user_info['auth_email']){
			header("location:".$ac_url."&auth_step=step3");
		}
		$selected['step2'] = '1';
		preg_match("/@(.*)/u",$auth_info['email'],$matches);
		$mail_ext=$matches[1];
		
		if($resend){
			$succ=$auth_obj->send_mail($auth_info['email_a_id'],$auth_info); 
			$succ and kekezu::echojson( $_lang['send_success_confirm_as_soon'],"1") or kekezu::echojson( $_lang['email_send_fail'],"0");
			die();
		}
		if($send_mail){
			$succ=$auth_obj->add_auth($email);//邮箱认证提交 
			$succ and kekezu::echojson( $_lang['send_success_confirm_as_soon'],"1") or kekezu::echojson( $_lang['email_send_fail'],"0");
			die();
		}
		
		break;
	case "step3":
		$selected['step3'] = '1';
		$email_a_id && $ac=='check_email' and $auth_obj->audit_auth($active_code,$email_a_id);//邮箱认证自审
	break;
}

require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );