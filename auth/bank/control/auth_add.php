<?php

/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01下午2:37:13
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$user_info['auth_realname']!=1 and kekezu::show_msg($_lang['check_auth_realname_and_auth_blank'],"index.php?do=user&view=setting&op=auth&auth_code=realname",'3','','warning');

$page_title = $_lang['bank_auth'];
$step_arr = array ("step1" => array ($_lang['step_one'], $_lang['choose_account'] ), "step2" => array ($_lang['step_two'], $_lang['payment_of_charges'] ), "step3" => array ($_lang['step_three'], $_lang['enter_throw_into_cash'] ), "step4" => array ($_lang['step_four'], $_lang['auth_pass'] ) );
$auth_step= keke_auth_bank_class::get_auth_step($auth_step,$auth_info);
//$verify = kekezu::reset_secode_session($ver?1:0);//安全码输入

$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver);
/*关联账号列表*/
$account_list = kekezu::get_table_data("*","witkey_member_bank"," uid = '$uid' and bind_status='1'",'','','','bank_id');

$bank_arr=keke_glob_class::get_bank();//银行列表
$auth_info[1] and $show='list';//存在多条认证记录。显示列表

$selected = array();
switch ($auth_step) {
	case "step1" :
		$selected['step1'] = '1';
		$bind_list = kekezu::get_table_data("bank_id","witkey_auth_bank","uid='$uid' and auth_status!=2","","","","bank_id",null);
		if($reset){
			unset($_SESSION['auth_bank_id']);
		}elseif($bank_id){
			$_SESSION['auth_bank_id']=$bank_id;
			header("Location:$ac_url&auth_step=step2&bank_id=$bank_id#userCenter");
		}
		break;
	case "step2" :
		$selected['step2'] = '1';
		if($bank_id){//检测是否选取过关联账户
			$bank_id!=$_SESSION['auth_bank_id'] and kekezu::show_msg($_lang['warn_different_account'],$ac_url."&auth_step=step1",'3','','warning');
		}else{
			kekezu::show_msg($_lang['warn_need_selected_the_associative_account'],$ac_url."&auth_step=step1",'3','','warning');
		}
		
		$account_info=$account_list[$bank_id];//当前选取的关联账户
		$account_info or kekezu::show_msg($_lang['warn_associated_bank_account_inexistent'],$ac_url."&auth_step=step1",'3','','warning');
		
		if (isset($formhash)&&kekezu::submitcheck($formhash)) { //认证申请提交
			$data['bank_name']=$account_info['bank_name'];
			$data['bank_account']=$account_info['card_num'];
			$data['deposit_area']=$account_info['bank_address'];
			$data['deposit_name']=$account_info['bank_sub_name'];
			$data['bank_id']=$account_info['bank_id'];
			$auth_obj->add_auth ($data); //认证申请提交
		}
		break;
	case "step3" :
		$selected['step3'] = '1';
		$auth_info or kekezu::show_msg($_lang['warn_illegal_entry'],$ac_url."&auth_step=step1",'3','','warning');
		$account_info=$account_list[$auth_info['bank_id']];//当前认证的关联账户
		$user_get_cash and $auth_obj->auth_confirm($auth_info,$user_get_cash);//认证确认
		break;
	case "step4":
		$selected['step4'] = '1';
		if($show=='list'){
			$auth_info[1] and $auth_list = $auth_info or $auth_list=array($auth_info);
		}else{
			if($show_id||!$auth_info[1]){
				$account_info=$account_list[$auth_info['bank_id']];//当前认证的关联账户
				$account_info or kekezu::show_msg($_lang['tips_about_bank_account_inexistent'],$ac_url."&auth_step=step1",'3','','warning');
			}
		}
		if($ac=='reauth'&&$bank_a_id){
			$ac_url = "{$origin_url}&op={$op}&auth_code={$auth_code}&step=step4&show=list&ver=1";
			$res = db_factory::execute(sprintf(" delete from %switkey_auth_bank where bank_a_id='%d'",TABLEPRE,$bank_a_id));
			$res .=db_factory::execute(sprintf(" delete from %switkey_auth_record where ext_data='%d'",TABLEPRE,$bank_a_id));
			$res and kekezu::show_msg($_lang['unbind_successful'],$ac_url."#userCenter",3,'','success') or kekezu::show_msg($_lang['unbind_fail'],$ac_url."#userCenter",3,'','warning');
		}
		break;
}

require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );