<?php
keke_lang_class::load_lang_class('sreward_task_agreement');
class sreward_task_agreement extends keke_task_agreement {
	
	protected $_inited = false;
	public static function get_instance($agree_id) {
		static $obj = null;
		if ($obj == null) {
			$obj = new sreward_task_agreement ( $agree_id );
		}
		return $obj;
	}
	
	public function __construct($agree_id) {
		parent::__construct ( $agree_id );
	}
	/**
	 * 阶段操作权限判断
	 * @see keke_task_agreement::process_can()
	 */
	public function process_can() {
		global $uid;
		$agree_status = $this->_agree_status; //阶段状态
		$buyer_status = $this->_buyer_status; //雇主状态
		$seller_status = $this->_seller_status; //威客状态
		$buyer_uid = $this->_buyer_uid; //雇主uid
		$seller_uid = $this->_seller_uid; //威客uid 
		$process_arr = array ();
		if ($uid == $buyer_uid || $uid == $seller_uid) {
			switch ($agree_status) {
				case "1" : //双方签署
					$uid == $buyer_uid && $buyer_status == '1' and $process_arr ['buyer_sign'] = true;
					$uid == $seller_uid && $seller_status == '1' and $process_arr ['seller_sign'] = true;
					break;
				case "2" : //进入实际流程
					$process_arr ['rights'] = true; //维权
					if ($uid == $seller_uid && $seller_status == '2') { //威客状态为2,可以上传附件
						$process_arr ['upload'] = true;
					}
					if ($uid == $buyer_uid && $seller_status == '3') { //威客已上传、雇主可确认下载
						$process_arr ['confirm'] = true;
					}
					if ($uid == $seller_uid || ($uid == $buyer_uid && $buyer_status == '3')) { //威客或者已确认附件的雇主
						$process_arr ['download'] = true;
					}
					break;
				case "3" : //协议完成
					$process_arr ['mark'] = true;
					break;
			}
		}
		return $process_arr;
	}
	
	/**
	 * 任务结算
	 */
	public function dispose_task() {
		global $kekezu,$_lang;
		$trust_info  = $this->_trust_info;
		$kekezu->init_prom ();
		$prom_obj = $kekezu->_prom_obj;
		$model_code = $this->_model_code; //模型code
		$agree_info = $this->_agree_info;
		/**双方所得金额**/
		$cash_info = db_factory::get_one ( sprintf ( " select task_cash,real_cash from %switkey_task where task_id = '%d'", TABLEPRE, $this->_task_id ) );
		/** 评价数+2***/
		$this->plus_mark_num ();
		/**威客记录**/
		keke_user_mark_class::create_mark_log ( $model_code, '1', $agree_info ['seller_uid'], $agree_info ['buyer_uid'], $agree_info ['work_id'], $cash_info ['task_cash'], $this->_task_id,$this->_seller_username, $this->_buyer_username);
		/**雇主记录**/
		keke_user_mark_class::create_mark_log ( $model_code, '2', $agree_info ['buyer_uid'], $agree_info ['seller_uid'], $agree_info ['work_id'], $cash_info ['real_cash'], $this->_task_id, $this->_buyer_username,$this->_seller_username);
		$site_profit = $cash_info ['task_cash'] - $cash_info ['real_cash']; //网站利润
		keke_finance_class::cash_in ( $agree_info ['seller_uid'], $cash_info ['real_cash'], 0, 'task_bid', '', 'task', $agree_info ['work_id'], $site_profit ); //打钱给威客
		$task_title = db_factory::get_count(sprintf(" select task_title from %switkey_task where task_id='%d'",TABLEPRE,$this->_task_id));
		//feed
		$feed_arr = array ("feed_username" => array ("content" =>$this->_seller_uid, "url" => $_K['siteurl']."/index.php?do=space&member_id={$this->_seller_uid}" ), "action" => array ("content" => $_lang ['success_bid_haved'], "url" => "" ), "event" => array ("content" =>$task_title, "url" => $_K['siteurl']."/index.php?do=task&task_id=$this->_task_id",'cash'=>$cash_info ['real_cash']));
		kekezu::save_feed ( $feed_arr,$this->_seller_uid,$this->_seller_username, 'work_accept', $this->_task_id );
		
		/** 威客上线结算*/
		$prom_obj->dispose_prom_event ( "bid_task", $agree_info ['seller_uid'],$this->_task_id);
		/** 雇主上线结算*/
		$prom_obj->dispose_prom_event ( "pub_task", $agree_info ['buyer_uid'], $this->_task_id );
	}
	/**
	 * 交付阶段2时的状态列表
	 * 当前用户能进入step2 说明他绝对签署了协议。所以只需判断对方状态
	 * @param int $user_type 用户类型  1=>威客(威客),2=>雇主(雇主)
	 */
	public function agreement_stage_list($user_type = '1') {
		global $_lang;
		$buyer_status = $this->_buyer_status; //雇主状态
		$seller_status = $this->_seller_status; //威客状态
		$agree_status = $this->_agree_status; //协议状态
		$agree_info = $this->_agree_info; //协议信息
		$stage_list = array ();
		switch ($user_type) {
			case "1" : //威客(威客)
				if (in_array ( $seller_status, array ('2', '3', '4' ) )) { //已签署协议、源文件上传、等待源文件接收
					if ($buyer_status == '1') { //此时雇主未签署协议
						$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_accepttime'] ) . $_lang['agree_jf_agreement'] );
						$stage_list [] = array ('orange', 'waring', $_lang['wain_each_not_agree_and_wait_jf'] );
					} else {
						if ($agree_info ['buyer_accepttime'] >= $agree_info ['seller_accepttime']) { //雇主先签署
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_accepttime'] ) . $_lang['agree_jf_agreement'] );
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_accepttime'] ) . $_lang['agree_jf_agreement'] );
						} else {
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_accepttime'] ) . $_lang['agree_jf_agreement'] );
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_accepttime'] ) . $_lang['agree_jf_agreement'] );
						}
						if ($agree_info ['seller_confirmtime']) { //我提交了源文件
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_confirmtime'] ) . $_lang['confirm_jf_resource_file'] );
							if ($agree_info ['buyer_confirmtime']) { //雇主确认接收
								$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_confirmtime'] ) . $_lang['confirm_recept_resource_file'] );
							} else { //未确认接收
								$stage_list [] = array ('blue', 'info', $_lang['waiting_each_confirm_jf_resource_file'] );
							}
						}
					}
				}
				break;
			case "2" : //雇主(雇主)
				if (in_array ( $buyer_status, array ('2', '3', '4' ) )) { //已签署协议、源文件上传、等待源文件接收
					if ($seller_status == '1') { //此时威客未签署协议
						$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_accepttime'] ) . $_lang['agree_jf_agreement'] );
						$stage_list [] = array ('orange', 'waring', $_lang['wain_each_not_agree_and_wait_jf'] );
					} else {
						if ($agree_info ['seller_accepttime'] >= $agree_info ['buyer_accepttime']) { //威客家先签署
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_accepttime'] ) . $_lang['agree_jf_agreement'] );
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_accepttime'] ) . $_lang['agree_jf_agreement'] );
						} else {
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_accepttime'] ) . $_lang['agree_jf_agreement'] );
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_accepttime'] ) . $_lang['agree_jf_agreement'] );
						}
						if ($agree_info ['seller_confirmtime']) { //对方提交源文件
							$stage_list [] = array ('green', 'successful', $_lang['congratulate_each_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['seller_confirmtime'] ) . $_lang['confirm_jf_resource_file']);
							if ($agree_info ['buyer_confirmtime']) { //雇主确认接收
								$stage_list [] = array ('green', 'successful', $_lang['congratulate_you_yu'] . date ( 'Y-m-d H:i:s', $agree_info ['buyer_confirmtime'] ) . $_lang['confirm_recept_resource_file'] );
							}
						} else {
							$stage_list [] = array ('blue', 'info', $_lang['waiting_each_confirm_jf_resource_file'] );
						}
					}
				}
				break;
		}
		return $stage_list;
	}
	/**
	 * 协议阶段进入权限判断
	 * @param int $user_type 用户角色 1=>威客(威客),2=>雇主(雇主)
	 * @return step  返回当前阶段值
	 */
	public function stage_access_check($user_type = '1') {
		$agree_status = $this->_agree_status; //协议状态
		$buyer_status = $this->_buyer_status; //雇主状态
		$seller_status = $this->_seller_status; //威客状态
		

		switch ($agree_status) {
			case "1" :
				if ($buyer_status == '1' && $seller_status == '1') { //双方均未签署
					$step = 'step1';
				} elseif ($buyer_status == '1' && $seller_status == '2') { //威客(威客)签署
					$user_type == '1' and $step = 'step2' or $step = 'step1';
				} elseif ($buyer_status == '2' && $seller_status == '1') { //雇主(雇主)签署
					$user_type == '2' and $step = 'step2' or $step = 'step1';
				}
				break;
			case "2" :
				$step = 'step2';
				break;
			case "3" :
				$step = 'step3';
				break;
		}
		return $step;
	}
	/**
	 * 获取协议阶段导航
	 */
	public function agreement_stage_nav() {
		global $_lang;
		return array ("1" => array ("step1", $_lang['read_hand_work_agreement'], $_lang['each_agree_agreement_start_jf'] ), "2" => array ("step2", $_lang['recept_resource_file'], $_lang['bid_work_to_pub_name'] ), "3" => array ("step3", $_lang['complete_file_jf'], $_lang['work_resource_file_complete'] ) );
	}
	
	/**
	 * 获取雇主交付状态
	 */
	public function get_buyer_status() {
		global $_lang;
		return array ("1" => $_lang['wait_agreement'], "2" => $_lang['wait_resource_file_upload'], "3" => $_lang['confirm_recept_source_file'], "4" => $_lang['each_mark'], "5" => $_lang['jf_complete'] );
	}
	/**
	 * 获取威客交付状态
	 */
	public function get_seller_status() {
		global $_lang;
		return array ("1" => $_lang['wait_agreement'], "2" => $_lang['confirm_resource_file_upload'], "3" => $_lang['wait_resource_file_recept'], "4" => $_lang['each_mark'], "5" => $_lang['jf_complete'] );
	}
}