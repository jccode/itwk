<?php
/**
 * 推广类
 * Enter description here ...
 * @author Administrator
 * 关系状态:relation_status 0未生效 1已生效
 * 事件状态:1已结算只有结算状态
 */
keke_lang_class::load_lang_class ( 'keke_prom_class' );
class keke_prom_class {
	public $_prom_open;
	
	public static function get_instance() {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_prom_class ();
		}
		return $obj;
	}
	public function __construct() {
		global $kekezu;
		$this->_prom_open = intval ( $kekezu->_sys_config ['prom_open'] ); //判断后台是否开启推广
	}
	/**
	 * 获取推广项配置
	 */
	public function get_prom_config($code='task_bid'){
		$info = db_factory::get_one('select * from '.TABLEPRE.'witkey_prom_rule where prom_code="'.$code.'"');
		$conf = unserialize($info['config']);
		$conf&&$info = array_merge($info,$conf);
		return $info;
	}
	/**
	 * 获取下线的注册关系
	 * (关系有效期类才会返回)
	 *@param int $uid 下线uid
	 */
	public function get_prom_relation($uid) {
		$sql = " select * from %switkey_prom_relation where uid='%d' and prom_type='reg'";
		return db_factory::get_one ( sprintf ( $sql, TABLEPRE, $uid ) );
	}
	/**
	 * 佣金计算
	 */
	public function cash_convert($code,$cash){
		$conf = $this->get_prom_config($code);
		$inc  = array();
		$inc['cash']   = 0;
		$inc['credit'] = 0;
		if($conf){
			switch($code){
				case 'pub_task':
					$rate = intval($conf['rate']);
					$inc['cash'] = $cash*$rate/100;
					break;
				case 'task_bid':
					$rate = intval($conf['rate']);
					$inc['cash'] = $cash*$rate/100;
					break;
				case  'auth':
					$inc['cash']   = $conf['cash'];
					$inc['credit'] = $conf['credit'];
					break;
			}
		}
		return $inc;
	}
	/**
	 * 推广验证，判断是否符合推广配置
	 */
	public function valid_event($code,$uid,$task_id,$work_id){
		$pass = FALSE;
		$conf = $this->get_prom_config($code);
		if($conf){
			switch($code){
				case 'pub_task'://发布任务,验证模型
					$model_id = db_factory::get_count(' select model_id from '.TABLEPRE.'witkey_task where task_id='.$task_id);
					$model_ids = explode(',',$conf['model']);
					if(in_array($model_id,$model_ids)){
						$pass = TRUE;
					}
					break;
				case 'task_bid'://中标,验证状态
					$work_status = db_factory::get_count(' select work_status from '.TABLEPRE.'witkey_task_work where uid='.$uid.' and work_id='.$work_id);
					if($work_status==11){
						$pass=TRUE;
					}
					break;
				case  'auth'://
					//检查认证推广是否已经发放
					$settlement = db_factory::get_count(' select event_id from '.TABLEPRE.'witkey_prom_event where uid='.$uid.' and event_status=2 and action="auth"');
					if(!$settlement){
						$codes = $conf['config'];//认证代码 realname,mobile
						$codes = explode(',',$codes);
						$i  = count($codes);
						if($i){
							$str = '';
							foreach($codes as $v){
								$str.='"'.$v.'",';
							}
							$str = rtrim($str,',');
							$auth_c =  db_factory::get_count(' select count(record_id) from '.TABLEPRE.'witkey_auth_record where 
													auth_code in ('.$str.') and auth_status=1 and uid='.$uid);
							
							if($i==$auth_c){
								$pass = TRUE;
							}
						}
					}
					break;
			}
		}
		return $pass;
	}
	/**
	 * 推广关系产生 
	 *@param int $uid 推广下线id
	 *@param string 下线名称
	 * @param $u 推广员UID===epi
	 */
	function create_prom_relation($uid, $username, $u) {
		global $_lang;
		$relate_obj = new Keke_witkey_prom_relation_class ();
		if ($this->_prom_open) {
			$u = intval ( $u );
			if ($u == $uid) { //无法推广自己
				kekezu::notify_user ( $_lang ['prom_fail'], $_lang ['you_can_not_prom_self'], $u );
				return false;
			} else {
				$prom_relation = $this->get_prom_relation ( $uid ); //获取此下线推广关系
				if ($prom_relation) { //已有推广关系
					kekezu::notify_user ( $_lang ['prom_fail'], $_lang ['your_prom_user_has_promer'], $u );
				} else {
					$p_info = kekezu::get_user_info ( $u ); //上线用户信息
					$relate_obj->setUid ( $uid );
					$relate_obj->setUsername ( $username );
					$relate_obj->setProm_uid ( $p_info ['uid'] );
					$relate_obj->setProm_username ( $p_info ['username'] );
					$relate_obj->setProm_type ( 'reg' );
					$relate_obj->setRelation_status ( 0 );//认证完后才生效
					 $relate_obj->setOn_time ( time () );
					$res = $relate_obj->create_keke_witkey_prom_relation ();
					$this->prom_feed('reg',$this->get_prom_relation($uid),"成功邀请会员");
					return true;
				}
			}
		} else {
			kekezu::notify_user ( $_lang ['prom_fail'], $_lang ['prom_system_closed'], $u );
			return false;
		}
	}
	/**
	 * 推广事件产生
	 * @param $action 事件动作  task_bid=>中标,pub_task=>发布,auth=>认证
	 * @param $p_arr 推广关系数组
	 * @param $event_desc  事件描述。为任务标题或其他
	 * @param $uid 下线id
	 * @param float $cash 可得现金
	 * @param float $credit 可得金币
	 * @param $task_id 任务编号
	 * @param $work_id 稿件编号
	 * @return boolen 
	 */
	function create_prom_event($action,$p_arr,$event_desc,$uid,$cash = 0,$credit=0,$task_id='', $work_id='') {
		$result = FALSE;
		if ($this->_prom_open) {
				$type_arr = self::get_prom_type();//事件类型
				//创建推广事件
				$event_obj = new Keke_witkey_prom_event_class ();
				$event_obj->setEvent_desc ( $type_arr[$action].'#'.$event_desc );
				$event_obj->setUid ( $uid );
				$event_obj->setUsername ( $p_arr ['username'] );
				$event_obj->setParent_uid ( $p_arr ['prom_uid'] );
				$event_obj->setParent_username ( $p_arr ['prom_username'] );
				$event_obj->setTask_id ( $task_id );
				$event_obj->setWork_id ( $work_id );
				$event_obj->setAction ($action);
				$event_obj->setRake_cash ( $cash);
				$event_obj->setRake_credit ( $credit );
				$event_obj->setEvent_time ( time () );
				$event_obj->setEvent_status ( 2 );
				$result = $event_obj->create_keke_witkey_prom_event ();
		}
		return $result;
	}
	/**
	 * 事件结算
	 * @param $action 事件动作  task_bid=>中标,pub_task=>发布,auth=>认证
	 * @param $event_desc  事件描述。。。为任务标题或其他
	 * @param $uid 下线id
	 * @param float $cash 获得佣金总额
	 * @param $task_id 任务编号
	 * @param $work_id 稿件编号
	 * @return boolen
	 */
	function dispose_prom_event($action,$event_desc,$uid,$cash = 0, $task_id='', $work_id='') {
		if ($this->_prom_open) {
			$p_relation = $this->get_prom_relation ( $uid );
			$inc_arr  = $this->cash_convert($action,$cash);//收入换算
			if ($p_relation&&$this->valid_event($action,$uid,$task_id,$work_id)) { //关系+事件验证
				//产生推广事件
				$p_relation['relation_status']==1 and $pass=true or $pass = false;
				$p_relation['relation_status']==0&&$action=='auth' and $pass=true;
				//发布任务的推广关系，不要求认证，只要存在关系就算
				if($action=='pub_task'){
					$pass=true;
				}
				$action=='auth' and $event_desc=$this->get_auth_desc();
				$pass&&$prom_event = $this->create_prom_event ($action,$p_relation,$event_desc,$uid,$inc_arr['cash'],$inc_arr['credit'],$task_id, $work_id); 
			}
			if ($prom_event) {
				keke_finance_class::cash_in ( $p_relation ['prom_uid'], $inc_arr['cash'],$inc_arr['credit'], 'prom_'.$action, '', 'task', $task_id );
				//认证通过更改状态为生效
				$action=='auth'&&db_factory::execute('update '.TABLEPRE.'witkey_prom_relation set relation_status=1 where relation_id='.$p_relation['relation_id']);
				$this->prom_feed($action,$p_relation,$event_desc,$inc_arr['cash'],$inc_arr['credit'],$task_id);
			}
		}
	}
	/**
	 * 获取认证的推广描述
	 */
	function get_auth_desc(){
		$str = '';
		$conf = $this->get_prom_config('auth');
		$item_list = keke_auth_base_class::get_auth_item('','',true);
		$codes = explode(',',$conf['config']);
		foreach($codes as $v){
			$str.=$item_list[$v]['auth_title'].',';
		}
		return rtrim($str,',');
	}
	/**
	 * 产生推广feed+站内信通知
	 * @param $action 事件动作  task_bid=>中标,pub_task=>发布,auth=>认证
	 * @param $p_arr 关系数组
	 * @param $event_desc  事件描述。。。为任务标题或其他
	 * @param float $cash 可得现金
	 * @param float $credit 可得金币
	 * @param $task_id 任务编号
	 */
	public function prom_feed($action,$p_arr,$event_desc,$cash=0,$credit=0,$task_id=0){
		global $_K;
	      $title = '推广金结算通知';
			switch($action){
				case 'reg':
					$title = '推广用户注册通知';
					$f_title='成功邀请会员';
					$content = '您成功推广了一位新的会员;用户名:'.$p_arr['username'].',该会员发布、承接单人悬赏、认证后您都将有机会获得推广员收益.';
					break;
				case 'pub_task':
					$f_title='成功发布任务';
					$f_url  = $_K['siteurl'].'/index.php?do=task&task_id='.$task_id;
					$content = "您推广的用户【".$p_arr['username']."】成功发布并完任务,您获得了 ".$cash." 元推广佣金";
					break;
				case 'task_bid':
					$f_title='成功中标';
					$f_url  = $_K['siteurl'].'/index.php?do=task&task_id='.$task_id;
					$content = "您推广的用户【".$p_arr['username']."】成功中标了,您获得了 ".$cash." 元推广佣金";
					break;
				case 'auth'://
					$title   = '推广用户完成相关认证通知';
					$f_title = $event_desc;
					$content = "您推广的用户【".$p_arr['username']."】完成了".$event_desc.',您获得了 '.$cash.' 元';
					$credit and $content.=','.$credit.CREDIT_NAME;
					break;
			}
			kekezu::notify_user($title,$content,$p_arr['prom_uid'],$p_arr['prom_username']);
			//feed
			$feed_arr = array (
			"feed_username" =>array (
					"content" => $p_arr['prom_username'], 
					"url" => "index.php?do=shop&u_id={$p_arr['prom_uid']}" )
			,
	 		"action" => array ("content" =>$f_title,
								"url" => "",
								'cash'=>$cash,
								'credit'=>$credit
			),
			 "event" => array ("content" => $event_desc,
			 				"url" =>$f_url
			));
			kekezu::save_feed ( $feed_arr,$p_arr['prom_uid'], $p_arr['prom_username'], 'prom_'.$action, $task_id);
	}
	/**
	 * 推广收益查询
	 */
	static function prom_income($uid) {
		if($uid){
		return db_factory::get_count(' select sum(fina_cash) cash from '.TABLEPRE.'witkey_finance where fina_type="in"
			 and fina_action="prom_task_bid" and uid='.$uid.' and YEAR(DATE(FROM_UNIXTIME(fina_time)))='.date('Y',time())
			.' and MONTH(DATE(FROM_UNIXTIME(fina_time)))='.date('m',time()),0,'cash',600);
		}
	}
	/**
	 * 创建推广cookie
	 * 参与点击结算。、登录时的关系创建
	 * @param $u 推广员UID
	 */
	function create_prom_cookie($u,$q) {
		global $_K,$dos;
		$u = intval ( $u );
		if ($u&&$this->_prom_open) { //是推广.且推广系统开启
			//记录推广COOKIE
			setcookie ( "user_prom_event", $u, time () + 24 * 3600, COOKIE_PATH, COOKIE_DOMAIN );
			parse_str($q,$arr);
			in_array($q['p'],$dos) or $q['p']='register';
			unset($arr['epi']);
			$arr['do'] = $arr['p'];
			header ( "Location:" . $_K ['siteurl'] . "/index.php?".http_build_query($arr)); //重定向至注册页
		}
	}
	/**
	 * 反序列推广cookie
	 */
	function extract_prom_cookie() {
		isset ( $_COOKIE ['user_prom_event'] ) and $u = intval ( $_COOKIE ['user_prom_event'] );
		return $u;
	}
	/**
	 * 清理推广cookie
	 */
	static function clear_prom_cookie() {
		if (isset ( $_COOKIE ['user_prom_event'] )) {
			setcookie ( 'user_prom_event', '', - 9999 );
			unset ( $_COOKIE ['user_prom_event'] );
		}
	}
	/**
	 * 获取推广关系状态
	 */
	public static function get_prelation_status() {
		global $_lang;
		return array ("未生效",'已生效');
	}
	/**
	 * 获取推广事件状态
	 */
	public static function get_pevent_status() {
		global $_lang;
		return array ("1" =>'已结算');
	}
	/**
	 * 获取推广事件类型
	 */
	public static function get_prom_type(){
		return array(
				'task_bid'=>'任务中标',
				'pub_task'=>'发布任务',
				'auth'=>'通过认证',
		);
	}
	
}