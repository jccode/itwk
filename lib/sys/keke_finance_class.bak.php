<?php

/**
 * @example 财务结算
 * @access class
 * @author shangk
 *
 *
 */
keke_lang_class::load_lang_class ( 'keke_finance_class' );
class keke_finance_class {
	
	public static $_basic_config;
	//金额
	public static $_cash;
	/**
	 * 方向:   in ,out
	 */
	public static $_type;
	//动作
	public static $_action;
	//task,service,null
	public static $_obj_type;
	public static $_obj_id;
	public static $_userinfo;
	
	public static function init($uid = null) {
		global $kekezu;
		global $_lang;
		self::$_basic_config = $kekezu->_sys_config;
		if ($uid) {
			try {
				self::$_userinfo = kekezu::get_user_info ( $uid );
			} catch ( Exception $e ) {
				keke_exception::handler ( $e );
			}
		} else {
			die ( $_lang ['uid_parameter_no_value'] );
		}
	}
	/**
	 *
	 * 威客用户支出的计算处理
	 * @param int $uid
	 * @param float $cash
	 * @param string $action
	 * realname_auth=>实名认证(out)
	 * bank_auth=>银行认证(out)
	 * company_auth=>公司认证(out)
	 * email_auth=>电子邮箱认证(out)
	 * mobile_auth=>手机认证(out)
	 * buy_vip=>购买vip 身份(out)
	 * buy_service=>购买服务(out)
	 * prom_task=>推广任务(out)
	 * hide_task=>隐藏任务(out)
	 * hide_work=>隐藏搞件(out)
	 * tj_task=>推荐任务(out)
	 * pub_task=>发布任务(out)
	 * withdraw=>提现(out)
	 * task_ext=>任务延期(out)
	 * report=>仲裁处理(out)
	 * pay_item=>增值服务(out)
	 * buy_mobile=>购买短信(out)
	 * admin_correction=>管理员后台纠错(out)
	 * @param float $profit - 站长收入
	 * @param string $obj_type   -task,service,order
	 * @param id $obj_id
	 * @return boolen true or false 成功   or 失败
	 */
	public static function cash_out($uid, $cash, $action, $profit = 0, $obj_type = null, $obj_id = null) {
		self::init ( $uid );
		$res = false;
		$sys_config = self::$_basic_config;
		$user_info = self::$_userinfo;
		$fo = new Keke_witkey_finance_class ();
		$fo->setFina_action ( $action );
		$fo->setFina_type ( "out" );
		$fo->setObj_type ( $obj_type );
		$fo->setObj_id ( $obj_id );
		$fo->setSite_profit ( $profit );
		$fo->setUid ( $user_info [uid] );
		$fo->setUsername ( $user_info [username] );
		$user_balance = $user_info ['balance'];
		$user_credit = $user_info ['credit'];
		$credit_allow = intval ( $sys_config ['credit_is_allow'] ) + 0;
		
		if ($cash <= 0) {
			return false;
		}
		
		if ($cash && $action) {
			try {
				//判断积分是否开启
				$credit_allow == 2 and $user_credit = 0;
				//是否有钱付款
				if ($user_balance + $user_credit < $cash) {
					return false;
				}
				// 提现判断,代金券不能用提现
				if ($action == 'withdraw') {
					//扣除人个帐户
					db_factory::execute ( "update " . TABLEPRE . "witkey_space set balance = balance-" . abs ( floatval ( $cash ) ) . " where uid ='{$user_info['uid']}'" );
					$fo->setFina_cash ( $cash );
					$fo->setFina_credit ( 0 );
					$fo->setUser_balance ( $user_balance - abs ( $cash ) );
				} else {
					//计算剩余积分，先扣代金券
					$sy_credit = $user_credit - $cash;
					if ($sy_credit > 0) {
						//更新用户积分
						db_factory::execute ( "update " . TABLEPRE . "witkey_space set credit = credit-{$cash} where uid ='{$user_info['uid']}'" );
						$fo->setFina_credit ( $cash );
						$fo->setFina_cash ( 0 );
						$fo->setUser_balance ( $user_balance );
						$fo->setUser_credit ( $user_credit - $cash );
					
					} else {
						//更新余额与积分
						db_factory::execute ( "update " . TABLEPRE . "witkey_space set credit = credit-{$user_credit},balance = balance-" . abs ( $sy_credit ) . " where uid ='{$user_info['uid']}'" );
						$fo->setFina_credit ( $user_credit );
						$fo->setFina_cash ( abs ( $sy_credit ) );
						$fo->setUser_balance ( $user_balance - abs ( $sy_credit ) );
						$fo->setUser_credit ( 0 );
					
					}
				}
				$fo->setFina_time ( time () );
				$res = $fo->create_keke_witkey_finance ();
			} catch ( Exception $e ) {
				keke_exception::handler ( $e );
			}
		
		//unique_num 这个字段现在已经不用了
		/* $sql = "update " . TABLEPRE . "witkey_finance set unique_num = CONCAT('88',LPAD(LAST_INSERT_ID(),8,'0')) where fina_id = last_insert_id() ";
			db_factory::execute ( $sql ); */
		}
		return $res;
	}
	/**
	 * 威客用户收入计算处理
	 * @param int $uid
	 * @param float $cash      -现金
	 * @param float $credit  - 积分
	 * @param string $action   - 动作
	 * online_recharge=>再线充值(in)
	 * line_recharge=>下线充值(in)
	 * task_bid=>任务中标(in)
	 * task_mark=>任务入围(in)
	 * task_lettory=>任务摇奖(in)
	 * task_fail=>任务失败退款(in)
	 * task_prom=>任务推广成功的佣金(in)
	 * task_prom_fail=>任务推广失败退款(in)
	 * rights_return=>维权返款(in)
	 * sale_service=>卖服务费用(in)
	 * admin_recharge=>管理员充值(in)
	 * withdraw_fail=>提现失败(in)
	 * ucenter_change=>ucenter 兑换(in)
	 * @param string $source  - zfb,cft,line,paypal,admin
	 * @param string $obj_type  - task,service,order,vip
	 * @param id $obj_id
	 * @param float $profit  - 利润
	 * @return boolen true or false
	 */
	public static function cash_in($uid, $cash, $credit = 0, $action, $source = null, $obj_type = null, $obj_id = null, $profit = 0, $charge = null) {
		//用户收入 来源  冲值   任务中标,任务失败退款，推广失败退款,卖出服务(作品),卖服务失败退款,提现失败退款
			
		self::init ( $uid );

		if ($cash <= 0 && $action != 'admin_charge') {
			return false;
		}
		
		if($action == 'admin_charge' && $cash <= 0){			
			$fina_type = "out";
		}else{			
			$fina_type = "in";
		}
		
		$user_info = self::$_userinfo;  //var_dump($user_info);exit;
		$sys_config = self::$_basic_config;
		$fo = new Keke_witkey_finance_class ();
		$fo->setFina_action ( $action );
		$fo->setFina_type ( $fina_type ); //"in"
		$fo->setObj_type ( $obj_type );
		$fo->setObj_id ( $obj_id );
		$fo->setFina_credit ( $credit );
		$fo->setFina_cash ( $cash );
		$fo->setUser_balance ( $user_info ['balance'] + $cash );
		$fo->setUser_credit ( $user_info ['credit'] + $credit );
		$fo->setUid ( $user_info [uid] );
		$fo->setUsername ( $user_info [username] );
		$fo->setFina_source ( $source );
		$fo->setSite_profit ( $profit );
		$fo->setRecharge_cash ( $charge !== null ? floatval ( $charge ) : null );
		 //扣除
		$sql = "update " . TABLEPRE . "witkey_space set credit = credit+{$credit},balance = balance+" . $cash . " where uid ='{$user_info['uid']}'";
		$res = db_factory::execute ( $sql ); 
		if ($res) {
			$fo->setFina_time ( time () );
			$row = $fo->create_keke_witkey_finance (); //var_dump($row);exit;
			//unique_num 字段已经没有用了
			/* $sql2 = "update " . TABLEPRE . "witkey_finance set  unique_num=CONCAT('88',LPAD(LAST_INSERT_ID(),8,'0')) where fina_id = last_insert_id()";
			db_factory::execute ( $sql2 );
			 */ //var_dump($res);exit;
			return $row;
		} else {
			return false;
		}
	
	}
	
	/**
	 * 威客用户收入计算处理
	 * @param int $uid
	 * @param float $cash      -现金
	 * @param float $credit  - 积分
	 * @param string $action   - 动作
	 * online_recharge=>再线充值(in)
	 * line_recharge=>下线充值(in)
	 * task_bid=>任务中标(in)
	 * task_lettory=>任务摇奖(in)
	 * task_fail=>任务失败退款(in)
	 * task_prom=>任务推广成功的佣金(in)
	 * task_prom_fail=>任务推广失败退款(in)
	 * rights_return=>维权返款(in)
	 * sale_service=>卖服务费用(in)
	 * admin_recharge=>管理员充值(in)
	 * withdraw_fail=>提现失败(in)
	 * ucenter_change=>ucenter 兑换(in)
	 * @param string $source  - zfb,cft,line,paypal,admin
	 * @param string $obj_type  - task,service,order,vip
	 * @param id $obj_id
	 * @param float $profit  - 利润
	 * @return boolen true or false
	 */
	public static function cash_in_two($uid, $cash, $credit = 0, $action, $source = null, $obj_type = null, $obj_id = null, $profit = 0, $charge = null, $remark = null) {
		//用户收入 来源  冲值   任务中标,任务失败退款，推广失败退款,卖出服务(作品),卖服务失败退款,提现失败退款
		self::init ( $uid );

		if($cash <= 0){			
			$action = 'admin_recharge_out';	
			$fina_type = "out";
		}else{			
			$fina_type = "in";
		}
				
		$user_info = self::$_userinfo; 
		$sys_config = self::$_basic_config;
		$fo = new Keke_witkey_finance_class ();
		$fo->setFina_action ( $action );
		$fo->setFina_type ( $fina_type ); //"in"
		$fo->setObj_type ( $obj_type );
		$fo->setObj_id ( $obj_id );
		$fo->setFina_credit (abs($credit)); //$credit
		$fo->setFina_cash (abs($cash)); //$cash
		$fo->setUser_balance ( $user_info ['balance'] + $cash ); 
		$fo->setUser_credit ( $user_info ['credit'] + $credit );
		$fo->setUid ( $user_info [uid] );
		$fo->setUsername ( $user_info [username] );
		$fo->setFina_source ( $source );
		$fo->setSite_profit ( $profit );
		$fo->setRemark( $remark );
		$fo->setRecharge_cash ( $charge !== null ? floatval ( $charge ) : null );
		 //扣除
		$sql = "update " . TABLEPRE . "witkey_space set credit = credit+{$credit},balance = balance+" . $cash . " where uid ='{$user_info['uid']}'";
		$res = db_factory::execute ( $sql ); 
		if ($res) {
			$fo->setFina_time ( time () );
			$row = $fo->create_keke_witkey_finance (); 

			return $row;
		} else {
			return false;
		}
	
	}
	
	/**
	 * 担保 财务记录
	 * 不改变用户余额
	 * @param array $data  数据
	 * @param string $trust_type 担保类型
	 * @param string $fina_type 财务类型 in、out
	 */
	public static function finance_trust($data = array(), $trust_type = 'alipay_trust', $fina_type = 'in') {
		$fina_obj = keke_table_class::get_instance ( "witkey_finance" );
		$data ['is_trust'] = 1;
		$data ['trust_type'] = $data ['fina_source'] = $trust_type;
		$data ['fina_type'] = $fina_type;
		$fina_id = $fina_obj->save ( $data );
		$sql = "update " . TABLEPRE . "witkey_finance set unique_num = CONCAT('88',LPAD(LAST_INSERT_ID(),8,'0')) where fina_id = last_insert_id() ";
		db_factory::execute ( $sql );
		return $fina_id;
	}
	
	/**
	 * 获取威客实际所得的金额 
	 * @param  $cash ----用户提现金额
	 * @return $real_cash  -----用户可获得的实际金额
	 */
	public static function get_to_cash($cash,$type) {
		//调试
		$type = trim($type);
		if ($type=='alipayjs'){
			if ($cash < 1) {
				return 0;
			}
			if ($cash <= 200) {
				$fee = 1;
			} elseif ($cash > 200 && $cash <= 5000) {
				$fee = $cash*0.005;
			} elseif ($cash > 5000) {
				$fee = 25;
			}
		}else{
			if ($cash < 1.5) {
				return 0;
			}
			if($cash<=300){
				$fee= 1.5;
			}elseif($cash>300&&$cash<=500){
				$fee = 3;
			}elseif($cash>500&&$cash<=5000){
				$fee =5;
			}elseif($cash>5000){
				$fee = 10;
			}
		}
		return $fee;
	}
	
	/**
	 * 后台站长支付宝打款
	 *
	 */
	
	public static function alipayjs_format_moneys($cash) {
		$website_cash = keke_finance_class::get_to_cash ( $cash );
		$alipay_per_charge = 0.5;
		$alipay_per_low = 1;
		$alipay_per_high = 25;
		
		//调试
		if ($website_cash <= 1) {
			return $website_cash;
		}
		if ($website_cash <= 200) {
			$real_website_cash = $website_cash + $alipay_per_low;
		
		} elseif ($website_cash > 200 && $website_cash <= 5000) {
			$real_website_cash = $website_cash + $website_cash * $alipay_per_charge / 100;
		} elseif ($website_cash > 5000) {
			$real_website_cash = $website_cash + $alipay_per_high;
		}
		return $real_website_cash;
	}

}

?>