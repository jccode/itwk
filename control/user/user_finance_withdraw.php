<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$step_list = array ("step1" => array ($_lang['step_one'], $_lang['input_withdraw_money'] ), "step2" => array ($_lang['step_two'], $_lang['choose_withdraw_account'] ), "step3" => array ($_lang['step_three'], $_lang['confirm_account_info'] ), "step4" => array ($_lang['step_four'], $_lang['complete_withdraw'] ) );
$step or $step = 'step1';
$verify = kekezu::reset_secode_session ( $ver ? 0 : 1 ); //安全码输入
$ac_url = $origin_url . "&op=$op&ver=" . intval ( $ver );
$pay_config = kekezu::get_table_data ( "*", "witkey_pay_config", '', '', "", '', 'k' );

switch ($step) {
	case "step1" :
		if ($reset) {
			$_SESSION ['withdraw_cash'] = '';
		}elseif ($choose_cash) {
			if ($withdraw_cash) {
				$user_info['balance']<$withdraw_cash&&kekezu::show_msg ( $_lang['have_no_enough_money'], $ac_url . "#userCenter", "3", "", "warning" );
				$withdraw_cash < $pay_arr ['withdraw_min'] ['v'] || $withdraw_cash > $pay_arr ['withdraw_max'] ['v'] and kekezu::show_msg ( $_lang['day_withdraw_money_is'] . "{$pay_arr['withdraw_min']['v']}-{$pay_arr['withdraw_max']['v']}," . $_lang['you_withdraw_money_error'], $ac_url . "#userCenter", "3", "", "warning" );
				$_SESSION ['withdraw_cash'] = $withdraw_cash;
				header ( "Location:" . $ac_url . "&step=step2&withdraw_cash=$withdraw_cash#userCenter" );
			} else {
				kekezu::show_msg ( $_lang['not_choose_recharge_money'], $ac_url . "#userCenter", "3", "", "warning" );
			}
		}
		break;
	case "step2" :
		$withdraw_cash != $_SESSION ['withdraw_cash'] and kekezu::show_msg ( $_lang['alert_return_rewrite'], $ac_url . "&step=step1&reset=1#userCenter", "3", "", "warning" );
		/**银行认证检测**/
		$bank_auth = keke_auth_fac_class::auth_check ( "bank", $uid );
		$bind_list = kekezu::get_table_data("*","witkey_auth_bank","uid='$uid' and auth_status!=2","","","","bank_id",null); 
		//$bind_list = kekezu::get_table_data("*","witkey_member_bank","uid='$uid' and bind_status='1'","","","","bank_id",null); 
		$bank_arr=keke_glob_class::get_bank();//银行列表
		$offline_list = kekezu::get_payment_config('','offline',1);
		//判断是否绑定爽购网，有则显示
		//$tw_arr=kekezu::get_table_data("*","witkey_corp_site","uid='$uid'","","","","corp_site_id",null);
	case "step3" :
		switch ($paymode) { //提现方式
			case "online" : //在线提现
				$pay_info = $payment_list [$pay_type]; //当前支付方式信息
			
				break;
			case "offline" : //线下提现
				//$bank_info = keke_auth_fac_class::auth_check ( "bank", $uid );
			    $user_bank_info = kekezu::get_table_data ( "*", "witkey_member_bank", 'uid='.$uid.' and bank_id='.$pay_type, '', "", '', '' );
				/**认证银行**/
				break;
			case "corp_site"://合作网站帐号
				switch($_SESSION['brand']){
					case 'tw'://台湾馆用户则添加爽购网帐户
						$corp_site=kekezu::get_table_data('*','witkey_corp_site','uid='.$uid,'','','','');
						break;
				}
				break;
		}
		break;
	case "step4" :
		if ($sbt_withdraw) {
			$withdraw_obj = new Keke_witkey_withdraw_class ();
			if (kekezu::submitcheck ($formhash)) {
				floatval ( $withdraw_cash ) > $user_info ['balance'] and kekezu::show_msg ( $_lang['submit_error'], $ac_url . "&step=step1&reset=1#userCenter", 2, $_lang['withdraw_money_too_big'], 'warning' );
				$withdraw_obj->setWithdraw_cash ( floatval ( $withdraw_cash ) );
				$withdraw_obj->setUid ( $uid );
				$withdraw_obj->setUsername ( $username );
				$withdraw_obj->setPay_username($pay_username);
				$withdraw_obj->setWithdraw_status ( 1 );
				$withdraw_obj->setApplic_time ( time () );
				$withdraw_obj->setPay_type ( $pay_type );
				$withdraw_obj->setPay_account ( $pay_account );
				$withdraw_obj->setBank_address($bank_address.'-'.$bank_sum_name);
				//根据银行卡所在地址来区分品牌馆
				if(strpos($bank_address,'台湾')!==false){
					$brand='tw';
				}
				$brand&$withdraw_obj->setBrand( $brand );
				$withdraw_id = $withdraw_obj->create_keke_witkey_withdraw ();
				
				if ($withdraw_id) {
					unset ( $_SESSION ['withdraw_cash'] );
					keke_finance_class::cash_out ( $uid, abs ( floatval ( $withdraw_cash ) ), 'withdraw', 0, 'withdraw', $withdraw_id );
					switch ($brand){
						case 'tw':
							//发送台湾爽购网
							//$params='order_id='.$withdraw_id.'&username='.$_SESSION['username']."&uid=".$_SESSION['uid']."&cash=".$withdraw_cash."&songogoID=".;
							//$tw_result=file_get_contents("http://www.epweike.cn/phone.php?".$params);
						    //if($tw_result==1){//返回错误
								//kekezu::show_msg ( $_lang['submit_fail'], $ac_url . "&step=step1&withdraw_cash=$withdraw_cash#userCenter", 2, $_lang['withdraw_apply_subit_fail'], 'warning' );
							//}
						    //$sec=5;
							$msg=$_lang['withdraw_apply_success_wait_audit_tw'];
							kekezu::echojson ( $msg, 1,array('order_id'=>$withdraw_id));
							die ();
							break;
						default:
							$sec=2;
							$msg=$_lang['withdraw_apply_success_wait_audit'];
							break;
					}
					kekezu::show_msg ( $_lang['submit_success'], $ac_url . "&step=step4&withdraw_cash=$withdraw_cash#userCenter", $sec, $msg.$brand,'success' );
				} else {
					if($brand=='tw'){
						kekezu::show_msg ( $_lang['submit_fail'], $ac_url . "&step=step1&withdraw_cash=$withdraw_cash#userCenter", 2, $_lang['withdraw_apply_subit_fail'], 'warning' );
					}else{
					    kekezu::show_msg ( $_lang['submit_fail'], $ac_url . "&step=step3&withdraw_cash=$withdraw_cash#userCenter", 2, $_lang['withdraw_apply_subit_fail'], 'warning' );
					}
				}
			}
		}
		break;
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );


