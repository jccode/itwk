<?php

class time_fac_class {
	protected $_basic_config;
	
	function __construct() {
		global $kekezu;
		$this->_basic_config = $kekezu->_sys_config;
	}
	function run() {
		global $model_list;
		global $kekezu;
		$model_list or $model_list = kekezu::get_table_data ( '*', 'witkey_model', 'model_status=1', 'model_id' );
		
		//任务到期执行判断
		$task_list = kekezu::get_table_data ( '*', 'witkey_task', "ifnull(exec_time,0)>0 and exec_time<" . time () );
		
		if ($task_list)
			foreach ( $task_list as $task ) {
				$model_dir = $model_list [$task ['model_id']] ['model_dir'];
				if (file_exists ( S_ROOT . "./task/$model_dir" ))
					$m = strtolower ( $model_dir ) . "_time_class";
				if (class_exists ( $m )) {
					$time_obj = new $m ();
					$time_obj->exec_task ( $task ['task_id'], $task );
				}
			}
		
		//稿件确认期执行判断   无视任务模型  稿件只有打款期到了才需要执行的可能
		$work_list = kekezu::get_table_data ( '*', 'witkey_task_work', "exec_time between 1 and " . time () );
		
		if ($work_list)
			$kekezu->init_prom ();
		foreach ( $work_list as $work ) {
			if (! $work ['is_confirmed']&&!$work['pay_status']) { //雇主没确认的才给，防止有人改exec_time
				//财务结算
				keke_finance_class::cash_in ( $work ['uid'], $work ['work_price'], 0, 'task_bid', null, 'task', $work ['work_id'] ); //支付费用
				
				//触发时间中断
				db_factory::execute ( "update " . TABLEPRE . "witkey_task_work set exec_time = 0,is_confirmed=1,pay_status=1 where work_id = '{$work['work_id']}'" );
				
				//消息通知
				kekezu::notify_user ( "任务#{$work['task_id']}佣金结算", '您参与的任务<a href="index.php?do=task&task_id=' . $work ['task_id'] . '">#' . $work ['task_id'] . '</a>雇主未确认付款，系统自动确认，您获得 ' . $work ['work_price'] . '元。', $work ['uid'], $work ['username'] );
				/**
				 * 推广结算。。。。单人
				 */
				$t_info = db_factory::get_one ( 'select model_id,task_title,task_cash from ' . TABLEPRE . 'witkey_task where task_id=' . $work ['task_id'] );
				if ($t_info ['model_id'] == 1) {
					$kekezu->_prom_obj->dispose_prom_event ( 'task_bid', $t_info ['task_title'], $work ['uid'], $t_info ['task_cash'], $work ['task_id'], $work ['work_id'] );
				}
				//动态生成
				$feed_arr = array ("feed_username" => array ("content" => $work ['username'], "url" => "index.php?do=shop&u_id={$work['uid']}" ), "action" => array ("content" => "中标了", "url" => "" ), "event" => array ("content" => "任务#" . $work ['task_id'], "url" => "index.php?do=task&task_id=" . $task_info ['task_id'], 'cash' => $work ['work_price'] ) );
				kekezu::save_feed ( $feed_arr, $work ['uid'], $work ['username'], 'task_bid', $work ['task_id'] );
			
			}
		}
		
		$mark_list = db_factory::query ( ' select a.mark_id,a.mark_type from ' . TABLEPRE . 'witkey_mark a left join '
							.TABLEPRE.'witkey_task b on a.origin_id=b.task_id where b.task_status=8 and a.mark_max_time<' . time () . ' and a.mark_status=0' );
		if ($mark_list) {
			foreach ( $mark_list as $v ) {
				$t = $v ['mark_type'];
				$i = $v ['mark_id'];
				$c = '评价到期,系统自动好评';
				if ($t == 1) {
					$aid = '4,5';
					$star = '5.0,5.0';
				} else {
					$aid = '1,2,3';
					$star = '5.0,5.0,5.0';
				}
				//keke_user_mark_class::exec_mark ( $i, $c, 1, $aid, $star );
				$res = keke_user_mark_class::exec_mark_process ( $i, $c, 1, $aid, $star );
			}
		}
		$vip_list = db_factory::query ( 'select uid,username from ' . TABLEPRE . 'witkey_space where vip_end_time<' . time () . ' and shop_level>1' );
		if ($vip_list) {
			foreach ( $vip_list as $v ) {
				$u = $v ['uid'];
				$n = $v ['username'];
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_vip_history set h_status=-1 where h_status=1 and uid=' . $u );
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_space set isvip=0,shop_level=1 where uid=' . $u );
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_shop set isvip=0,shop_level=1 where uid=' . $u );
				kekezu::notify_user ( 'VIP到期通知', '您购买的VIP特权已到期,您可以前往<a href="index.php?do=vip" class="red">VIP柜台</a>续订', $u, $n );
			}
		}
		$match_list = db_factory::query ( ' select m_id,uid,username from ' . TABLEPRE . 'witkey_shop_match where m_status=1 and end_time<' . time () . ' order by m_id desc' );
		if ($match_list) {
			foreach ( $match_list as $v ) {
				$i = $v ['m_id'];
				$u = $v ['uid'];
				$n = $v ['username'];
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_shop_match set m_status=-1 where m_id=' . $i );
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set city_match=0 where uid=' . $u );
				kekezu::notify_user ( '同城速配短信到期通知', '您购买的同城速配短信通知功能已到期,您可以进行续订', $u, $n );
			}
		}
		/**同城消息队列**/
//		$h = intval ( date ( 'H', time () ) );
//		if ($h == 0 || $h == 1) { //0点-1点.把前一天的未发送成功的消息发送掉(可能客服会主动后台发送失败)
//			$que_list = $kekezu->_cache_obj->get ( 'citymatch_time_lock' );
//			if (! $que_list) {
//				$que_list = db_factory::query ( ' select * from ' . TABLEPRE . 'witkey_citymatch_queue where que_day="' . date ( 'Y-m-d', time () - 24 * 3600 ) . '" and que_status!=1 ' );
//				$kekezu->_cache_obj->set ( 'citymatch_time_lock', $que_list, 3600 );
//			}
//			if ($que_list) {
//				$msg_obj = new keke_msg_class ();
//				foreach ( $que_list as $v ) {
//					kekezu::notify_user ( '同城任务速递', $v ['content'], $v ['uid'], $v ['username'] ); //站内信
//					$msg_res = $msg_obj->send_phone_sms ( $v ['mobile'], $v ['content'] );
//					if ($msg_res == '发送成功') {
//						$sqlarr = array ('que_status' => 1 );
//					} else {
//						$sqlarr = array ('error_status' => $msg_res, 'que_status' => 2 );
//					}
//					db_factory::updatetable ( TABLEPRE . 'witkey_citymatch_queue', $sqlarr, array ('que_id' => $v ['que_id'] ) );
//				}
//			}
//		}

		/**站点统计信息**/
		if (($h == 0)&&IS_CACHE!=0) { //把前一天的统计信息发送
			$stat_lock = $kekezu->_cache_obj->get ( 'statistics_time_lock' );
			$kekezu->_cache_obj->set ( 'statistics_time_lock', 1, 3600 );
			if (! $stat_lock) {
				db_factory::query("update keke_witkey_space set track_uid=0 , track_username = NULL, track_type=0 where track_uid>0 and isvip=0 and last_track_time<UNIX_TIMESTAMP(now()) - 20 * 24 * 3600");
				
				keke_admin_class::daily_statistics ( - 1 );
				
				$stat_list = db_factory::get_one ( ' select * from ' . TABLEPRE . 'witkey_statistics where date(from_unixtime(add_time))="' . date ( 'Y-m-d', time () - 24 * 3600 ) . '" and status<1' );
				//获取服务器IP
				$serverip = gethostbyname($_SERVER["HTTP_HOST"]);
				//
				if ($stat_list && $serverip == '223.4.179.70') {
					
					$mail_content = '统计开始时间' . date ( 'Y-m-d 00:00:00', time () - 24 * 3600 ) . ',统计结束时间' . date ( 'Y-m-d 24:00:00', time () - 24 * 3600 ) . '<br>';
					
					$submit = array ();
					$stat_list ['submit'] and $submit = unserialize ( $stat_list ['submit'] );
					$success = array ();
					$stat_list ['success'] and $success = unserialize ( $stat_list ['success'] );
					$conversion = array ();
					$stat_list ['conversion'] and $conversion = unserialize ( $stat_list ['conversion'] );
					$complate = array ();
					$stat_list ['complete'] and $complate = unserialize ( $stat_list ['complete'] );
					$additional = array ();
					$stat_list ['additional'] and $additional = unserialize ( $stat_list ['additional'] );
					
					$mail_content .= '<table border="1">';
					$mail_content .= '<tr><td></td><td>提交任务数（个）</td><td>提交任务金额（元）</td></tr>';
					$mail_content .= '<tr><td>提交任务数量（总）</td><td>' . $submit ['total'] ['count'] . '</td><td>' . $submit ['total'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>悬赏</td><td>' . $submit ['reward'] ['count'] . '</td><td>' . $submit ['reward'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>招标</td><td>' . $submit ['tender'] ['count'] . '</td><td>' . $submit ['tender'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>计件</td><td>' . $submit ['preward'] ['count'] . '</td><td>' . $submit ['preward'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>雇佣</td><td>' . $submit ['hire'] ['count'] . '</td><td>' . $submit ['hire'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>直接雇佣</td><td>' . $submit ['taskhire'] ['count'] . '</td><td>' . $submit ['taskhire'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>劳务服务</td><td>' . $submit ['service'] ['count'] . '</td><td>' . $submit ['service'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td colspan="3">&nbsp;</td></tr>';
					$mail_content .= '<tr><td></td><td>成功任务数（个）</td><td>成功任务金额（元）</td></tr>';
					$mail_content .= '<tr><td>成功任务数量（总）</td><td>' . $success ['total'] ['count'] . '</td><td>' . $success ['total'] ['host'];
					$mail_content .= $success ['total'] ['host'] ? "(今日托管:{$success['total']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>悬赏</td><td>' . $success ['reward'] ['count'] . '</td><td>' . $success ['reward'] ['cash'];
					$mail_content .= $success ['reward'] ['host'] ? "(今日托管:{$success['reward']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>招标</td><td>' . $success ['tender'] ['count'] . '</td><td>' . $success['tender']['host'];
					$mail_content .= $success ['tender'] ['host'] ? "(今日托管:{$success['tender']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>计件</td><td>' . $success ['preward'] ['count'] . '</td><td>' . $success ['preward'] ['cash'];
					$mail_content .= $success ['preward'] ['host'] ? "(今日托管:{$success['preward']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>雇佣</td><td>' . $success ['hire'] ['count'] . '</td><td>' . $success['hire']['host'];
					$mail_content .= $success ['hire'] ['host'] ? "(今日托管:{$success['hire']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>直接雇佣</td><td>' . $success ['taskhire'] ['count'] . '</td><td>' . $success ['taskhire'] ['cash'];
					$mail_content .= $success ['taskhire'] ['host'] ? "(今日托管:{$success['taskhire']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>劳务服务</td><td>' . $success ['service'] ['count'] . '</td><td>' . $success ['service'] ['cash'];
					$mail_content .= $success ['service'] ['host'] ? "(今日托管:{$success['service']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td colspan="3">&nbsp;</td></tr>';
					$mail_content .= '<tr><td></td><td>任务转化率（个数）</td><td>任务转化率（金额）</td></tr>';
					$mail_content .= '<tr><td>转化率（总）</td><td>' . $conversion ['total'] ['count'] . '%</td><td>' . $conversion ['total'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>悬赏</td><td>' . $conversion ['reward'] ['count'] . '%</td><td>' . $conversion ['reward'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>招标</td><td>' . $conversion ['tender'] ['count'] . '%</td><td>' . $conversion ['tender'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>计件</td><td>' . $conversion ['preward'] ['count'] . '%</td><td>' . $conversion ['preward'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>雇佣</td><td>' . $conversion ['hire'] ['count'] . '%</td><td>' . $conversion ['hire'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>直接雇佣</td><td>' . $conversion ['taskhire'] ['count'] . '%</td><td>' . $conversion ['taskhire'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td>劳务服务</td><td>' . $conversion ['service'] ['count'] . '%</td><td>' . $conversion ['service'] ['cash'] . '%</td></tr>';
					$mail_content .= '<tr><td colspan="3">&nbsp;</td></tr>';
					$mail_content .= '<tr><td></td><td>完成任务数（个）</td><td>完成任务金额（元）</td></tr>';
					$mail_content .= '<tr><td>完成任务数量（总）</td><td>' . $complate ['total'] ['count'] . '</td><td>' . $complate ['total'] ['cash'];
					$mail_content .= $success ['total'] ['host'] ? "(今日托管:{$success['total']['host']})" : '';
					$mail_content .= '</td></tr>';
					$mail_content .= '<tr><td>悬赏</td><td>' . $complate ['reward'] ['count'] . '</td><td>' . $complate ['reward'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>招标</td><td>' . $complate ['tender'] ['count'] . '</td><td>' . $complate ['tender'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>计件</td><td>' . $complate ['preward'] ['count'] . '</td><td>' . $complate ['preward'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>雇佣</td><td>' . $complate ['hire'] ['count'] . '</td><td>' . $complate ['hire'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>直接雇佣</td><td>' . $complate ['taskhire'] ['count'] . '</td><td>' . $complate ['taskhire'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td>劳务服务</td><td>' . $complate ['service'] ['count'] . '</td><td>' . $complate ['service'] ['cash'] . '</td></tr>';
					$mail_content .= '<tr><td colspan="3">&nbsp;</td></tr>';
					$mail_content .= '<tr><td></td><td>新增数</td><td>总数</td></tr>';
					$mail_content .= '<tr><td>注册会员统计</td><td>' . $additional ['register'] ['count'] . '</td><td>' . $additional ['register'] ['total'] . '</td></tr>';
					$mail_content .= '<tr><td>VIP商铺销售统计</td><td>' . $additional ['vipbuy'] ['new_count'] . '个(' . $additional ['vipbuy'] ['new_buy'] . '元)</td><td>' . $additional ['vipbuy'] ['total_count'] . '个(' . $additional ['vipbuy'] ['total_buy'] . '元)</td></tr>';
					$mail_content .= '<tr><td>社会化登录统计</td><td>' . $additional ['oatuh'] ['count'] . '</td><td>' . $additional ['oatuh'] ['total'] . '</td></tr>';
					$mail_content .= '</table>';
					
					/* 旧短信内容
					 * $mobile_content .= '昨发布' . $success ['total'] ['count'] . '个，';
					$mobile_content .= '全赏' . $success ['reward'] ['count'] . '个' . $success ['reward'] ['cash'] . '元，';
					$mobile_content .= '计件' . $success ['preward'] ['count'] . '个' . $success ['preward'] ['cash'] . '元，';
					$mobile_content .= '招标' . $success ['tender'] ['count'] . '个' . $success['tender']['host'] . '元，';
					$hire_count = $submit ['hire'] ['count'] + $submit ['taskhire'] ['count'];
					$hire_success = $success ['hire'] ['count'] + $success ['taskhire'] ['count'];
					$hire_cash = $success['hire']['host'] + $success ['taskhire'] ['cash'];
					$mobile_content .= '雇佣' . $hire_success . '个' . $hire_cash . '元，'; */
					//新短信内容 2012-08-22 ch 按黄总要求修改统计短信模板
					$mobile_content = '昨日全部任务'.$success['total']['count'].'个，';
					$mobile_content .= '总金额'.$success['total']['host'].'元，';
					$mobile_content .= '悬赏类'.strval($success ['reward'] ['count']+$success ['preward'] ['count']).'个（'.strval($success['reward']['host']+$success['preward']['host']).'元），';
					$mobile_content .= '非悬赏类'.strval($success['total']['count']-$success ['reward'] ['count']-$success ['preward'] ['count']).'个（'.strval($success['total']['host']-$success['reward']['host']-$success['preward']['host']).'元）';
					//$mobile_content .= '直接雇佣'.$submit['taskhire']['count'].'个托管'.$success['taskhire']['count'].'个'.$success['taskhire']['cash'].'元';

					$msg_obj = new keke_msg_class ();
					//发送短信
					$mobile_arr = array('13695965999','13063000000','13666035133','18259406917','15980927884','18605028393','15980935617','18205989302','13950007515','13400639717','18605028393');
					$msg_res = $msg_obj->send_phone_sms ( $mobile_arr, $mobile_content );
					//发送邮件
					kekezu::send_mail ( '415642680@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '279330000@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '2159858@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '348085753@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '1904469@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '2214647619@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '258399652@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '1125211269@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '1648877074@qq.com', '站点统计信息详情', $mail_content );
					kekezu::send_mail ( '85164568@qq.com', '站点统计信息详情', $mail_content );

					db_factory::execute ( 'update ' . TABLEPRE . 'witkey_statistics set status=1 where date(from_unixtime(add_time))="' . date ( 'Y-m-d', time () - 24 * 3600 ) . '"' );
				}
			}
		}
	}
}

?>