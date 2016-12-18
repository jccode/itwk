<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//


/**
 * @author       Administrator
 * std_obj 为类的成员对象。用来保存release_info发布信息,att_info附加项信息
 */
keke_lang_class::load_lang_class ( 'keke_task_release_class' );
abstract class keke_task_release_class {
	
	
	/**
	 * 发布权限判断
	 */
	public function check_pub_priv($url = '', $output = "normal") {
		global $_lang;
		$this->_priv ['pass'] and kekezu::keke_show_msg ( $url, $_lang ['can_pub'], '', $output ) or kekezu::keke_show_msg ( $url, $this->_priv ['notice'] . $_lang ['not_rights_pub_task'], "error", $output );
	}
	
	
	/**
	 * 获取任务绑定父级行业（没有则读取全行业）
	 * @return   void
	 */
	public function get_bind_indus() {
		global $kekezu;
		if ($this->_model_info ['indus_bid']) {
			$bind_indus = implode ( ',', array_filter ( explode ( ',', $this->_model_info ['indus_bid'] ) ) );
			return kekezu::get_table_data ( '*', "witkey_industry", "indus_id in (select indus_pid from " . TABLEPRE . "witkey_industry where indus_id in({$bind_indus}))", 'listorder desc', '', '', 'indus_id', null );
		} else {
			return $this->_indus_arr = $kekezu->_indus_p_arr;
		
		}
	}
	
	
	
	/**
	 * 任务发布状态设置以及任务花费金额与金币的计算
	 * 返回根据后台配置的最小金额得出的当前任务可发布状态
	 * @param $total_cash 任务总金额(含有增值费用)
	 * @param $task_cash  任务金额(不含增值费用)
	 * @param $is_trust 是否担保
	 */
	public function set_task_status($total_cash, $task_cash) {
		global $kekezu;
		$basic_config = $kekezu->_sys_config;
		$balance = $this->_user_info ['balance'];
		$credit = $this->_user_info ['credit'];
		if ($balance + $credit >= $total_cash) { //用户金额满足总花费的情况下
			$model_code = $this->_model_info ['model_code'];
			switch ($model_code) {
				case "tender" :
					$this->_task_config ['zb_audit'] == 2 and $task_status = "2" or $task_status = "1";
					break;
				case "match" :
					$task_status = "2";
					break;
				default :
					if ($task_cash >= $this->_task_config ['audit_cash']) { //发布金额满足最小金额限制，用户余额满足 不需审核
						$task_status = '2'; //任务状态可设置为成功发布
					} elseif ($task_cash < $this->_task_config ['audit_cash']) { //发布金额小于最小金额,需要审核
						$task_status = "1"; //任务状态设置为需要审核
					}
					if ($basic_config ['credit_is_allow'] == '2') { //金币关
						$cash_cost = $task_cash;
						$credit_cost = '0';
					} else { //开
						if ($credit >= $task_cash) { //满足总金
							$credit_cost = $task_cash;
							$cash_cost = '0';
						} else {
							$credit_cost = $credit;
							$cash_cost = $task_cash - $credit;
						}
					}
			}
		} else {
			$task_status = "0"; //延迟支付
		}
		$this->_task_obj->setTask_status ( $task_status ); //设置任务状态
		$this->_task_obj->setCash_cost ( $cash_cost ); //现金花费
		$this->_task_obj->setCredit_cost ( $credit_cost ); //金币花费
	}
	/**
	 * 任务发布通用块设置
	 * 用来处理各任务通用的设置
	 */
	public function public_pubtask() {
		$std_obj = $this->_std_obj; //成员对象
		$release_info = $std_obj->_release_info; //任务发布信息
		$task_obj = $this->_task_obj; //任务对象
		$user_info = $this->_user_info;
		
		$txt_task_title = kekezu::str_filter ( $release_info ['txt_title'] ); //任务标题
		$task_obj->setTask_title ( $txt_task_title );
		$task_obj->setModel_id ( $this->_model_id ); //设定任务类型
		$task_obj->setProfit_rate ( $this->_task_config ['task_rate'] ); //当前比例
		$task_obj->setTask_fail_rate ( $this->_task_config ['task_fail_rate'] ); //失败返金抽成比
		$task_obj->setTask_cash ( $release_info ['txt_task_cash'] ); //任务金额
		$task_obj->setReal_cash ( $release_info ['txt_task_cash'] * (100 - $this->_task_config ['task_rate']) / 100 ); //实际佣金
		$task_obj->setStart_time ( time () ); //任务开始时间
		$time_arr = getdate ();
		$rel_time = $time_arr ['hours'] * 3600 + $time_arr ['minutes'] * 60 + $time_arr ['seconds'];
		$task_obj->setSub_time ( strtotime ( $release_info ['txt_task_day'] ) + $rel_time ); //任务投稿期
		

		$task_obj->setEnd_time ( strtotime ( $release_info ['txt_task_day'] ) + $this->_task_config ['choose_time'] * 24 * 3600+ $rel_time); //任务选稿期
		$task_obj->setIndus_id ( $release_info ['indus_id'] ); //任务行业
		$task_obj->setIndus_pid ( $release_info ['indus_pid'] );
		
		$tar_content = kekezu::str_filter ( $release_info ['tar_content'] );
		$task_obj->setTask_desc ( $tar_content ); //任务需求
		$task_obj->setUid ( $this->_uid );
		$task_obj->setUsername ( $this->_username );
		/**增值服务项录入**/
		$att_info = array_filter ( $std_obj->_att_info ); //增值项信息
		

		$keys_arr = array_keys ( $att_info );
		//增值服务借宿时间设置
		$payitem_arr [top] = 1000000000;
		$payitem_arr [urgent] = 1000000000;
		foreach ( $att_info as $k => $v ) {
			$v [item_code] == 'top' and $payitem_arr [top] = time () + 3600 * 24 * $v [item_num];
			$v [item_code] == 'urgent' and $payitem_arr [urgent] = time () + 3600 * 24 * $v [item_num];
		}
		
		$payitem_time = serialize ( $payitem_arr );
		
		$att_ids = implode ( ",", array_keys ( $att_info ) ); //增值项编号串
		$task_obj->setPay_item ( $att_ids );
		$task_obj->setPayitem_time ( $payitem_time );
		$task_obj->setAtt_cash ( floatval ( $std_obj->_att_cash ) ); //增值项金额
		

		$contact = serialize ( $release_info ['cont'] ); //联系方式
		$task_obj->setContact ( $contact );
		$task_obj->setKf_uid ( $this->_kf_uid ); //指定客服
		//任务附件
		$file_arr = array_filter ( explode ( ',', $release_info ['file_ids'] ) );
		$file_s = implode ( ',', $file_arr );
		$task_obj->setTask_file ( $file_s );
	}
	
	/**
	 * 任务信息成功产生的追加操作
	 */
	public function update_task_info($task_id, $obj_name) {
		
		global $_K,$_lang;
		$std_obj = $this->_std_obj;
		$release_info = $std_obj->_release_info; //任务信息
		$att_info = $std_obj->_att_info; //增值信息
		

		$user_info = $this->_user_info; //用户信息
		$task_obj = $this->_task_obj; //任务对象
		if ($task_id) {
			db_factory::execute ( "update " . TABLEPRE . "witkey_space set pub_num = pub_num+1 where uid=$this->_uid " );
			//任务附件保存
			$release_info ['file_ids'] and $this->save_task_file ( $task_id, $release_info ['txt_title'] );
			$task_status = $task_obj->getTask_status (); //任务状态
			/**订单产生**/
			$task_title = $task_obj->getTask_title ();
			
			switch ($task_status) {
				case "2" :
					//产生订单+结算
					

					$this->create_task_order ( $task_id, $this->_model_id, $release_info, $att_info );
					
					$this->create_prom_event ( $task_id ); /*任务发布成功。产生推广事件*/
					
					$feed_arr = array ("feed_username" => array ("content" => $this->_username, "url" => "index.php?do=shop&u_id={$this->_uid}" ), "action" => array ("content" => $_lang['pub_task'], "url" => "" ), "event" => array ("content" => " $task_title", "url" => "index.php?do=task&task_id=$task_id" ) );
					kekezu::save_feed ( $feed_arr, $this->_uid, $this->_username, 'pub_task', $task_id );
					//发送消息
					$this->notify_user ( $task_id, '2' );
					$j_step = 'step4';
					$status = '2';
					break;
				case "1" : //进入审核
					//产生订单+结算
					$this->create_task_order ( $task_id, $this->_model_id, $release_info, $att_info );
					$this->create_prom_event ( $task_id ); /*任务发布成功。产生推广事件*/
					/*$feed_arr = array ("feed_username" => array ("content" => $this->_username, "url" => "index.php?do=shop&u_id= $this->_uid  " ), "action" => array ("content" => "发布了任务 ", "url" => "" ), "event" => array ("content" => " $task_title", "url" => "index.php?do=task&task_id=$task_id" ) );
					kekezu::save_feed ( $feed_arr, $this->_uid, $this->_username, 'pub_task', $task_id );*/
					//发送消息
					$this->notify_user ( $task_id, '1' );
					$j_step = 'step4';
					$status = '1';
					
					break;
				case "0" : //金额不够
					

					$total_cash = $this->get_total_cash ( $release_info ['txt_task_cash'] );
					$pay_cash = $total_cash - ($user_info ['balance'] + $user_info ['credit']);
					$pay_cash = ceil ( $pay_cash );
					$order_id = $this->create_task_order ( $task_id, $this->_model_id, $release_info, $att_info, 'wait' );
					//发送消息
					$this->notify_user ( $task_id, '0' );
					//担保交易
					if ($task_obj->getIs_trust ()) {
						$model_code = $this->_model_info ['model_dir'];
						$jump_url = keke_trust_fac_class::trust_task_request ( "create", $model_code, $task_id, $task_obj->getTrust_type () );
					} else {
						$jump_url = "index.php?do=pay&order_id=$order_id";
					}
					$this->del_task_obj ( $obj_name ); //清除缓存
					header ( "location:" . $jump_url );
					die ();
					break;
			}
		}
		
		$this->del_task_obj ( $obj_name ); //清除缓存
		header ( "Location:" . $_K ['siteurl'] . "/index.php?do=release&model_id=$this->_model_id&r_step=" . $j_step . "&task_id=$task_id&status=" . $status );
	}
	
	/**
	 * 产生推广事件
	 * @param $task_id 任务编号
	 */
	public function create_prom_event($task_id) {
		global $kekezu;
		$task_obj = $this->_task_obj;
		if ($this->_model_info ['model_code'] != 'tender') {
			$this->_model_info ['model_code'] == 'dtender' and $task_cash = $task_obj->getReal_cash () or $task_cash = $task_obj->getTask_cash ();
			$kekezu->init_prom ();
			$prom_obj = $kekezu->_prom_obj;
			if ($prom_obj->is_meet_requirement ( "pub_task", $task_id )) {
				$prom_obj->create_prom_event ( "pub_task", $this->_uid, $task_id, $task_cash );
			}
		}
	}
	/**
	 * 获取任务基本配置
	 * @return   void
	 */
	public abstract function get_task_config();
	/**
	 * 任务发布函数
	 */
	public abstract function pub_task();
	
	/**
	 * 发布模式进行信息
	 * @param $std_cache_name session名
	 * @param $data 外部传入参数
	 */
	public abstract function pub_mode_init($std_cache_name, $data = array());
	
	
	/**
	 * 获取任务增值费用信息 
	 *@return $att_info;
	 */
	public function get_pay_item() {
		return $this->_std_obj->_att_info; //增值服务信息
	}
	/**
	 * 移除付费项	 
	 * @param $item_id  待移除项id
	 * @param $obj_name 任务信息session 保存名
	 */
	public function remove_pay_item($item_id, $obj_name) {
		$att_info = $this->_std_obj->_att_info; //增值服务数组
		if ($att_info [$item_id]) {
			unset ( $att_info [$item_id] );
		}
		$this->_std_obj->_att_info = array_filter ( $att_info ); //重新保存增值服务信息
		$this->save_task_obj ( array (), $obj_name ); //重新保存任务session
		$total_cash = $this->get_total_cash ( $this->_std_obj->_release_info ['txt_task_cash'] );
		kekezu::echojson ( number_format ( $total_cash, 2 ), 1 );
		die ();
	}
	/**
	 * 计算增值服务费总额
	 * @param array $att_info 增值服务数组信息
	 * @return float 增值服务总金额
	 */
	public function solve_pay_item($att_info = array()) {
		$att_cash = '0';
		if (is_array ( $att_info )) {
			foreach ( $att_info as $v ) {
				$att_cash += floatval ( $v ['item_cash'] );
			}
		}
		
		return $att_cash;
	}
	/**
	 * 保存当前发布信息
	 * @param array $release_info 任务发布信息
	 * @param string $obj_name    外部传入的任务对象session名
	 * @return   void
	 */
	public function save_task_obj($release_info = array(), $obj_name) {
		global $kekezu;
		empty ( $release_info ) or $this->_std_obj->_release_info = $release_info; //将任务信息保存入成员对象中
		$this->_std_obj->_att_cash = $this->solve_pay_item ( $this->_std_obj->_att_info ); //增值服务总额
		$this->_model_info ['model_code'] == 'tender' and $this->_std_obj->_release_info ['txt_task_cash'] = $this->_task_config [zb_fees];
		$_SESSION [$obj_name] = serialize ( $this->_std_obj ); //将对象保存入session
	}
	/**
	 * 获取任务信息
	 * @param string $obj_name    外部传入的任务对象session名
	 * @return   void
	 */
	public function get_task_obj($task_id) {
		$task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where taks_id='$task_id'");
		$attach_info = kekezu::get_table_data("witkey_file","obj_type='task' and task_id='$task_id' ");
		$this->_std_obj = array();
		$this->_std_obj->_release_info = $task_info;
		$this->_std_obj->_att_info = $attach_info;
		
	}
	
	/**
	 * 销毁任务对象
	 * @return   void
	 */
	public function del_task_obj($obj_name) {
		if (isset ( $_SESSION [$obj_name] )) {
			unset ( $_SESSION [$obj_name] );
		}
	}
	/**
	 * 页面进入权限判断
	 * @param string $r_step 任务当前步骤
	 * @param int $model_id 任务模型
	 * @param array $relese_info 任务发布信息
	 * @param int $task_id  发布完成的任务编号  ，第四步有用
	 */
	public function check_access($r_step, $model_id, $release_info, $task_id = null, $output = 'normal') {
		global $_lang;
		switch ($r_step) {
			case "step1" :
				break;
			case "step2" : //没有进过第一步
				$release_info ['step1'] or kekezu::keke_show_msg ( "index.php?do=release&pub_mode=$this->_pub_mode&model_id=$model_id", $_lang ['you_not_choose_task_model'], "error", $output );
				break;
			case "step3" : //没有进过第二步
				if (! $release_info ['step2'] && ! $release_info ['step1']) { //没进过前2步
					kekezu::keke_show_msg ( "index.php?do=release&pub_mode=$this->_pub_mode&model_id=$model_id", $_lang ['you_not_choose_task_model_and_not_in'], "error", $output );
				} elseif (! $release_info ['step2']) {
					kekezu::keke_show_msg ( "index.php?do=release&pub_mode=$this->_pub_mode&model_id=$model_id&r_step=step2", $_lang ['you_not_fill_requirement_and_not_in'], "error", $output );
				}
				break;
			case "step4" : //无法查到刚才的任务记录。此页面10分钟类有效
				$sql = sprintf ( " select task_id from %switkey_task where task_id = '%d' and start_time>%d", TABLEPRE, $task_id, time () - 600 );
				$task_info = db_factory::get_one ( $sql );
				$task_info or kekezu::keke_show_msg ( "index.php?do=release&pub_mode=$this->_pub_mode", $_lang ['the_page_timeout_notice'], "error", $output );
				return $task_info;
				break;
		}
	}
	
	/**
	 * 根据金额获取最大天数
	 */
	public static function get_default_max_day($task_cash, $model_id, $min_day) {
		$max = kekezu::get_show_day ( floatval ( $task_cash ), $model_id );
		$max >= $min_day or $max += $min_day;
		return date ( 'Y-m-d', time () + $max * 24 * 3600 );
	}
}