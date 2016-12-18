<?php
/**
 * 用户详情展示
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$notice = '不存在的用户';
if($u_id){
	$user_info = kekezu::get_user_info($u_id);
	if($user_info){
		switch ($code){
			case 'auth_realname'://认证系列
			case 'auth_mobile':
			case 'auth_bank':
			case 'auth_email':
				$bank_arr=keke_glob_class::get_bank();//银行列表
				$tb_name = TABLEPRE.'witkey_'.$code;
				$code=='auth_realname' and $i = 1 or $code=='auth_mobile' and $i=2 or $code=='auth_email' and $i=3 or $i=4;
				$auth_info = db_factory::get_one(' select * from '.$tb_name.' where uid='.$u_id);
				$auth_code = ltrim($code,'auth_');
				break;
			case 'finance'://资金明细
				//区间列表
				$cash_cove = kekezu::get_cash_cove('tender');
				$fina_action_arr = keke_glob_class::get_finance_action();
				$page = max($page,1);
				$sql   = " select a.*,t.task_id,t.task_title,t.task_cash_coverage,t.task_cash,c.task_id as wtask_id,c.task_title as wtask_title,c.task_cash_coverage wtask_cash_coverage,c.task_cash wtask_cash from ".TABLEPRE.'witkey_finance a left join '.
						TABLEPRE.'witkey_task_work b on a.obj_id=b.work_id left join '.TABLEPRE.'witkey_task c '
						.' on b.task_id=c.task_id left join '.TABLEPRE.'witkey_task t on a.obj_id = t.task_id where a.uid='.$u_id.' order by fina_time desc';
				$c_sql = " select count(fina_id) from ".TABLEPRE.'witkey_finance where uid='.$u_id;
				$count =  db_factory::get_count($c_sql);
				$url   = 'index.php?do=user&view=detail&code=finance&u_id='.$u_id.'&page='.$page;
				$pages = $kekezu->_page_obj->getPages($count, 10, $page, $url);
				$list  = db_factory::query($sql.$pages['where']); //echo $sql.$pages['where'];// var_dump($list); echo (time()-86400*2); echo date('Y-m-d',1343025497);
				break;
			case 'credit'://积分明细
				$page = max($page,1);
				$sql   = " select a.*,b.task_title from ".TABLEPRE.'witkey_mark a left join '.
						TABLEPRE.'witkey_task b on a.origin_id=b.task_id where a.uid='.$u_id.' order by mark_time desc';
				$c_sql = " select count(mark_id) from ".TABLEPRE.'witkey_mark where uid='.$u_id;
				$count =  db_factory::get_count($c_sql);
				$url   = 'index.php?do=user&view=detail&code=credit&u_id='.$u_id.'&page='.$page;
				$pages = $kekezu->_page_obj->getPages($count, 10, $page, $url);
				$list  = db_factory::query($sql.$pages['where']);
				break;
		}
	}
}
require $template_obj->template ( 'control/admin/tpl/admin_user_detail' );

function get_auth_bank_real_name($uid){
	$info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_member_bank where uid='$uid'");
	return $info['real_name'];
}