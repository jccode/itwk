<?php
/**
 * @copyright keke-tech
 * @author Chen 品牌馆详情
 * 
 * 2012-8-05 
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 200 );
if (! $op) {
	$brand_type = keke_glob_class::get_brand_type ();
	if ($brand_id) {
		$brand_info = db_factory::get_one ( ' select a.*,b.shop_name from ' . TABLEPRE . 'witkey_brand a left join ' . TABLEPRE . 'witkey_shop b on a.uid=b.uid where a.brand_id=' . $brand_id );
		$brand_status = keke_glob_class::get_brand_status ();
		if ($brand_info ['app_files']) {
			$file_list = db_factory::query ( ' select file_name,save_name from ' . TABLEPRE . 'witkey_file where file_id in (' . $brand_info ['app_files'] . ')' );
		}
		if ($ac) {
			switch ($ac) {
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
			$res and kekezu::admin_show_msg ( '处理成功', 'index.php?do=shop&view=brand_info&brand_id=' . $brand_id, 3, '', 'success' ) or kekezu::admin_show_msg ( '处理失败', 'index.php?do=shop&view=brand_info&brand_id=' . $brand_id, 3, '', 'warning' );
		
		}
	} else {
		if ($sbt_edit) {
			//添加判断是否从后台购买台湾vip后插入，先检查会员是否已经加入品牌库
			if($vip_update){
				$vip_result=db_factory::get_one('select uid from '.TABLEPRE . 'witkey_brand where uid='.$txt_uid);
				if($vip_result){ return false;die();};
			}
			
			$txt_uid and $id = db_factory::inserttable ( TABLEPRE . 'witkey_brand', array ('uid' => $txt_uid, 'username' => $txt_username, 'shop_id' => $txt_shop_id, 'on_time' => time (), 'app_status' => 1, 'brand' => $brand, 'app_desc' => '客服#' . $admin_info ['username'] . '#后台添加用户#' . $txt_username . '#品牌馆记录' ));
			if ($id) {
				db_factory::execute ( 'update ' . TABLEPRE . 'witkey_space set brand="' . $brand. '" where uid=' . $txt_uid );
				if($vip_update){ return $id;die();}
				kekezu::admin_system_log ( '客服#' . $admin_info ['username'] . '#后台添加用户#' . $txt_username . '#品牌馆记录' );
				kekezu::admin_show_msg ( '添加成功', 'index.php?do=shop&view=brand_info&brand_id=' . $id, 3, '', 'success' );
			} else {
				if($vip_update){ return false;die();}
				kekezu::admin_show_msg ( '添加失败', 'index.php?do=shop&view=brand_info', 3, '', 'warning' );
			}
		}
	}
} else {
	$page = max ( $page, 1 );
	$sql = ' select a.uid,a.username,a.shop_id,a.shop_name from ' . TABLEPRE . 'witkey_space a ' . 'left join ' . TABLEPRE . 'witkey_brand b on a.uid=b.uid where a.shop_id!=0 and a.uid not in (' . ' select CONCAT(c.uid) from ' . TABLEPRE . 'witkey_brand c) and a.group_id<1';
	$c_sql = ' select count(a.uid) from ' . TABLEPRE . 'witkey_space a ' . 'left join ' . TABLEPRE . 'witkey_brand b on a.uid=b.uid where a.shop_id!=0 and a.uid not in (' . ' select CONCAT(c.uid) from ' . TABLEPRE . 'witkey_brand c) and a.group_id<1';
	$txt_uid and ($sql .= ' and a.uid=' . $txt_uid and $c_sql .= ' and a.uid=' . $txt_uid);
	$txt_username and ($sql .= ' and a.username like "%' . $txt_username . '%"' and $c_sql .= ' and  a.username like "%' . $txt_username . '%"');
	$count = db_factory::get_count ( $c_sql );
	$url = 'index.php?do=shop&view=brand_info&op=user&page=' . $page;
	$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );
	$list = db_factory::query ( $sql . ' order by uid asc' . $pages ['where'] ); //echo $sql.$pages['where'];// var_dump($list); echo (time()-86400*2); echo date('Y-m-d',1343025497);
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );