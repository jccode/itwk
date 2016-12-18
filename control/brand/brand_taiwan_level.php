<?php
/**
 * 品牌馆-台湾馆-品牌入驻
 * 
 * @author xxy
 * 2012-9-28
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
// open_vip
$vip_level_arr = db_factory::query ( "select * from " . TABLEPRE . "witkey_vip_level" );
$vip_level_arr or kekezu::show_msg ( '消息提示', $_K ['siteurl'] . '/index.php?do=vip&view=index', 3, '暂时未开通VIP商铺！', 'warning' );
$uid and $brand_info = db_factory::get_one ( " select brand from " . TABLEPRE . "witkey_space where uid='$uid'" ) and $brand_info=$brand_info['brand'];
$uid and $shop_info = db_factory::get_one ( " select * from " . TABLEPRE . "witkey_shop where uid='$uid'" );
if ($uid&&$level_id=='') {
	//未开通VIP商铺，显示台湾VIP扩展版
	if($shop_info['shop_level']==1) $level_id=2;
	// 已开通
	if ($shop_info ['isvip'] && $level_id == '') {
		$vip_level_id = $shop_info ['shop_level'];
		$my_vip_level = list_search ( $vip_level_arr, array (
				'level_id' => $vip_level_id 
		), 1 );
		if($_SESSION['brand']=='tw'){
			$my_vip_level ['rule_config_tw'] and $my_vip_level ['rule_config_tw'] = unserialize ( $my_vip_level ['rule_config_tw'] );
			$my_vip_level ['price_config_tw'] and $my_vip_level ['price_config_tw'] = unserialize ( $my_vip_level ['price_config_tw'] );
			if (is_array ( $my_vip_level ['price_config_tw'] )) {
				foreach ( $my_vip_level ['price_config_tw'] as $k => $v ) {
					unset ( $my_vip_level ['price_config_tw'] [$k] );
					$my_vip_level ['price_config_tw'] [$v ['month']] = $v;
				}
			}
		}else{
			$my_vip_level ['rule_config'] and $my_vip_level ['rule_config_tw'] = unserialize ( $my_vip_level ['rule_config'] );
			$my_vip_level ['price_config'] and $my_vip_level ['price_config_tw'] = unserialize ( $my_vip_level ['price_config'] );
			if (is_array ( $my_vip_level ['price_config'] )) {
				foreach ( $my_vip_level ['price_config'] as $k => $v ) {
					unset ( $my_vip_level ['price_config'] [$k] );
					$my_vip_level ['price_config'] [$v ['month']] = $v;
				}
				$my_vip_level ['price_config_tw']=$my_vip_level ['price_config'];
			}
		}
		
	}
}
//未登陆时初显示台湾VIP扩展版
!$shop_info['shop_level']&&!$level_id&&$level_id=2;

if ($level_id) {
	in_array ( $level_id, array (2,3) ) or $level_id = $vip_level_arr [0] ['level_id']; // 当前选择的VIP类型
	$vip_level = list_search ( $vip_level_arr, array (
			'level_id' => $level_id 
	), 1 );
	$vip_level ['rule_config_tw'] and $vip_level ['rule_config_tw'] = unserialize ( $vip_level ['rule_config_tw'] );
	$vip_level ['price_config_tw'] and $vip_level ['price_config_tw'] = unserialize ( $vip_level ['price_config_tw'] );
	if (is_array ( $vip_level ['price_config_tw'] )) {
		foreach ( $vip_level ['price_config_tw'] as $k => $v ) {
			unset ( $vip_level ['price_config_tw'] [$k] );
			$vip_level ['price_config_tw'] [$v ['month']] = $v;
		}
	}
}
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
function list_search($list, $condition, $row = '') {
	if (is_string ( $condition ))
		parse_str ( $condition, $condition );
	
	$resultSet = array ();
	foreach ( $list as $key => $data ) {
		$find = false;
		foreach ( $condition as $field => $value ) {
			if (isset ( $data [$field] )) {
				if (0 === strpos ( $value, '/' )) {
					$find = preg_match ( $value, $data [$field] );
				} elseif ($data [$field] == $value) {
					$find = true;
				}
			}
		}
		if ($find) {
			if ($row == 1) {
				$resultSet = &$list [$key];
				break;
			} else {
				$resultSet [] = &$list [$key];
			}
		}
	}
	return $resultSet;
}