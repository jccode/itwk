<?php
/**
 * @copyright keke-tech
 * @author Chen 品牌馆
 * 
 * 2012-8-05 
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 200 );
$t_obj = keke_table_class::get_instance ( "witkey_brand" );
$page and $page = intval ( $page ) or $page = 1;
$slt_page_size and $slt_page_size = intval ( $slt_page_size ) or $slt_page_size = 10;
$url = "index.php?do=$do&view=$view&page=$page&slt_page_size=$slt_page_size&brand=$brand&app_status=$app_status&ord[]=$ord[0]&ord[]=$ord[1]";
$brand_status = keke_glob_class::get_brand_status ();
$brand_type = keke_glob_class::get_brand_type ();

if ($ac && $brand_id) {
	$brand_info = db_factory::get_one ( ' select a.*,b.shop_name from ' . TABLEPRE . 'witkey_brand a left join ' . TABLEPRE . 'witkey_shop b on a.uid=b.uid where a.brand_id=' . $brand_id );
	switch ($ac) {
		case 'del' :
			$res = db_factory::execute ( 'delete  from ' . TABLEPRE . 'witkey_brand  where brand_id=' . $brand_id );
			if ($res) {
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set brand="" where uid=' . $brand_info ['uid'] );
				//kekezu::notify_user('品牌馆申请不通过','客服已通过您的品牌馆申请',$brand_info[uid],$brand_info['username']);
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '删除编号#' . $brand_id . '品牌馆申请' );
			}
			break;
		case 'pass' :
			$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_brand set app_status=1 where brand_id=' . $brand_id );
			if ($res) {
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set brand="' . $brand_info ['brand'] . '" where uid=' . $brand_info ['uid'] );
				kekezu::notify_user ( '品牌馆申请通过', '客服已通过您的品牌馆申请', $brand_info [uid], $brand_info ['username'] );
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '通过编号#' . $brand_id . '品牌馆申请' );
			}
			break;
		case 'nopass' :
			$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_brand set app_status=2 where brand_id=' . $brand_id );
			if ($res) {
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set brand="" where uid=' . $brand_info ['uid'] );
				kekezu::notify_user ( '品牌馆申请不通过', '客服未通过您的品牌馆申请', $brand_info [uid], $brand_info ['username'] );
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '不通过编号#' . $brand_id . '品牌馆申请' );
			}
			break;
		case 'is_recomm' :
			$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_brand set is_recommend=1 where brand_id=' . $brand_id );
			if ($res) {
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '推荐编号#' . $brand_id . '品牌馆申请' );
			}
			break;
		case 'no_recomm' :
			$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_brand set is_recommend=0 where brand_id=' . $brand_id );
			if ($res) {
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '取消推荐编号#' . $brand_id . '品牌馆申请' );
			}
			break;
	}
	$res and kekezu::admin_show_msg ( '处理成功', 'index.php?do=shop&view=brand&page=' . $page, 3, '', 'success' ) or kekezu::admin_show_msg ( '处理失败', 'index.php?do=shop&view=brand&page=' . $page, 3, '', 'warning' );
}
if ($sbt_action && $ckb) {
	$ids = implode ( ',', array_filter ( $ckb ) );
	$infos = db_factory::query ( ' select brand_id,uid,brand,app_status,is_recommend from ' . TABLEPRE . 'witkey_brand where brand_id in (' . $ids . ')' );
	switch ($sbt_action) {
		case '批量删除' :
			$uids = array ();
			foreach ( $infos as $v ) {
				$uids [] = $v ['uid'];
			}
			$uids = implode ( ',', array_filter ( $uids ) );
			if ($ids && $uids) {
				$res = db_factory::execute ( ' delete from ' . TABLEPRE . 'witkey_brand where brand_id in (' . $ids . ')' );
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_space set brand="" where uid in (' . $uids . ')' );
			}
			break;
		case '批量通过' :
			$nids = array ();
			foreach ( $infos as $v ) {
				if ($v ['app_status'] == 1) {
					continue;
				}
				$nids [] = $v ['brand_id'];
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_space set brand="' . $v ['brand'] . '" where uid =' . $v ['uid'] );
			}
			$nids = implode ( ',', array_filter ( $nids ) );
			if ($nids) {
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_brand set app_status=1 where brand_id in (' . $nids . ')' );
				$ids = $nids;
			}
			break;
		case '批量不通过' :
			$nids = array ();
			foreach ( $infos as $v ) {
				if ($v ['app_status'] == 2) {
					continue;
				}
				$nids [] = $v ['brand_id'];
				db_factory::execute ( ' update ' . TABLEPRE . 'witkey_space set brand="" where uid =' . $v ['uid'] );
			}
			$nids = implode ( ',', array_filter ( $nids ) );
			if ($nids) {
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_brand set app_status=2 where brand_id in (' . $nids . ')' );
				$ids = $nids;
			}
			break;
		case '批量推荐' :
			$nids = array ();
			foreach ( $infos as $v ) {
				if ($v ['is_recommend'] == 1) {
					continue;
				}
				$nids [] = $v ['brand_id'];
			}
			$nids = implode ( ',', array_filter ( $nids ) );
			if ($nids) {
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_brand set is_recommend=1 where brand_id in (' . $nids . ')' );
				$ids = $nids;
			}
			break;
		case '批量取消推荐' :
			$nids = array ();
			foreach ( $infos as $v ) {
				if ($v ['is_recommend'] == 0) {
					continue;
				}
				$nids [] = $v ['brand_id'];
			}
			$nids = implode ( ',', array_filter ( $nids ) );
			if ($nids) {
				$res = db_factory::execute ( ' update ' . TABLEPRE . 'witkey_brand set is_recommend=0 where brand_id in (' . $nids . ')' );
				$ids = $nids;
			}
			break;
	}
	if ($res) {
		kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . $sbt_action . '编号#' . $ids . '品牌馆记录' );
		kekezu::admin_show_msg ( '处理成功', 'index.php?do=shop&view=brand&page=' . $page, 3, '', 'success' );
	} else {
		kekezu::admin_show_msg ( '处理失败', 'index.php?do=shop&view=brand&page=' . $page, 3, '', 'warning' );
	}
}

//条件
$where = ' 1 = 1  ';
$txt_brand_id and $where .= " and brand_id = '" . $txt_brand_id . "' ";
$txt_username and $where .= " and username like '%" . $txt_username . "%' ";
$brand and $where .= " and brand = '" . $brand . "' ";
$app_status and $where .= ' and app_status=' . $app_status;
if ($ord [1]) {
	$where .= " order by brand_id $ord[1] ";
} else {
	$where .= " order by brand_id desc";
}

$d = $t_obj->get_grid ( $where, $url, $page, $slt_page_size, null );
$brand_arr = $d [data];
$pages = $d [pages];

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );