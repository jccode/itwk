<?php
/**
 * 诚信保障
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 201 );
$ops = array ('apply', 'refund', 'mark' );
in_array ( $op, $ops ) or $op = 'apply';
if ($op != 'mark') {
	//初始化 
	$page_obj = $kekezu->_page_obj;
	$page = max ( $page, 1 );
	$page_size = max ( $page_size, 10 );
	//查询
	$url = "index.php?do=$do&view=$view&op=$op&condit=$condit&page_size=$page_size&ord[0]=$ord[0]&$ord[1]=$ord[1]&slt_static=$static";
	if ($ac && $ini_id) {
		$ini_info = db_factory::get_one ( 'select id,uid,username,pay_cash from ' . TABLEPRE . 'witkey_integrity where id=' . $ini_id );
		switch ($ac) {
			case "pay" : //点亮
				$log = '客服#' . $admin_info ['username'] . '点亮用户#' . $ini_info ['username'] . '的诚信保障图标';
				$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_integrity set status=1,remark="' . $remark . '" where id=' . $ini_info ['id'] );
				if ($res) {
					kekezu::admin_system_log ( $log );
					db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set integrity=1 where uid=' . $ini_info ['uid'] );
					kekezu::notify_user ( '诚信保障图标点亮通知', '您的诚信保障图标已被点亮', $ini_info ['uid'], $ini_info ['username'] );
				}
				break;
			case "pass" : //熄灭
				$log = '客服#' . $admin_info ['username'] . '通过用户#' . $ini_info ['username'] . '的诚信保障图标熄灭申请';
				$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_integrity set status=1,remark="' . $remark . '" where id=' . $ini_info ['id'] );
				if ($res) {
					$notice = '您的诚信保障图标已被熄灭,诚信保障金已经退还,如未收到退款请联系客服';
					kekezu::admin_system_log ( $log );
					db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set integrity=0 where uid=' . $ini_info ['uid'] );
					keke_finance_class::cash_in($ini_info['uid'],2000,0,'integrity_refund');
					kekezu::notify_user ( '诚信保障图标熄灭申请处理通知', $notice, $ini_info ['uid'], $ini_info ['username'] );
				}
				break;
		}
		$res and kekezu::admin_show_msg ( '处理成功', $url . '&close=1', 2, '', 'success' ) or kekezu::admin_show_msg ( '处理失败', $url . '&close=1', 2, '', 'warning' );
	} else {
		$where = ' where 1=1 ';
		$op == 'apply' and $where .= ' and type=1 ' or $where .= ' and type = 2 ';
		if ($condit && $txt_val) {
			switch ($condit) {
				case 'uid' :
					$where .= ' and ' . $condit . '=' . intval ( $txt_val );
					break;
				case 'username' :
					$where .= "and " . $condit . " like '%{$txt_val}%' ";
					break;
			}
		}
		$count = db_factory::get_count ( ' select count(id) from ' . TABLEPRE . 'witkey_integrity' . $where );
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
		$list = db_factory::query ( ' select * from ' . TABLEPRE . 'witkey_integrity' . $where );
	
	}
} else {
	$ini_info = db_factory::get_one ( 'select id,uid,username,remark,pay_cash from ' . TABLEPRE . 'witkey_integrity where id=' . $ini_id );
	if ($t != 'show') {
		$remark = '客服#' . $admin_info ['username'];
		$t == 'pay' and $remark .= '点亮' or $remark .= '熄灭';
		$remark .= '用户#' . $ini_info ['username'] . '的诚信保障图标;原因:';
	} else {
		$remark = $ini_info ['remark'];
	}
}

require $template_obj->template ( 'control/admin/tpl/admin_user_integ' );