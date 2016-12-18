<?php
/**
 * 后台广告列表显示页面
 * @copyright keke-tech
 * @author hr
 * @version v 2.0
 * @date 2011-12-21 下午05:54:07
 * @encoding GBK
 */

	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
	kekezu::admin_check_role(32);
	
	$ad_obj = new Keke_witkey_ad_class();//广告数据
	//$target_obj = new Keke_witkey_ad_target_class(); //广告类型
	$table_obj = new keke_table_class('witkey_ad');
	
	$page = isset($page) ? intval($page) : '1' ;
	$url = "index.php?do={$do}&view={$view}&ad_id={$ad_id}&ad_type={$target_id}&ad_name={$ad_name}&target_id={$target_id}&ord={$ord}&page={$page}";
	//ajax修改排序
	if ($action && $action=='u_order'){
		!$u_id && exit();
		!$u_value && exit();
		$ad_obj -> setListorder( intval($u_value) );
		$ad_obj -> setWhere('ad_id='.intval($u_id));
		$ad_obj -> edit_keke_witkey_ad();
		exit();
	}
	
	//操作 删除,批量删除
	if (($sbt_action && $ckb) || ($ac=='del' && $ad_id)){
// 		if (!empty($ckb) || !empty($ad_id)) {
			$ids = $ckb ? implode(',', $ckb) : intval($ad_id) ;// echo $ids;
			$ad_obj -> setWhere('ad_id in ('.$ids.')');
			$result = $ad_obj -> del_keke_witkey_ad();
			kekezu::admin_system_log($_lang['delete_ads'].$ids);
			kekezu::admin_show_msg($result ? $_lang['ads_delete_success'] : $_lang['no_operation'] ,"index.php?do={$do}&view={$view}&target_id={$target_id}&ord={$ord}&page={$page}",3,'',$result?'success':'warning');
// 		} else {
// 			kekezu::admin_show_msg($_lang['choose_operate_item']);
// 		}
	}
	
	//广告类型调用
	$targets_arr =  kekezu::get_table_data('*','witkey_ad_target', '', '', '', '', 'target_id');
	//查询数据
	//$page = isset($page) ? intval($page) : '1' ;
	$pagesize = isset($page_size) ? intval($page_size) : '10' ;
	$where = '1=1';
	$where .= $ad_id ? ' and ad_id="'.intval($ad_id).'"' : '' ;
	$where .= $target_id && !$ad_id ? ' and target_id="'.intval($target_id).'"' : '';
	$where .= $ad_name && !$ad_id ? ' and ad_name like "%'.$ad_name.'%"' : '';

	is_array($w['ord']) and $where .=' order by '.$ord[0].' '.$ord[1];
	
	//is_array($ord) && $ord=$ord[0].' '.$ord[1];//implode(' ',$ord);
	//$where .= $ord ? ' order by '.$ord : ''; //echo $where;
	$ad_arr = $table_obj -> get_grid($where, $url, $page, $pagesize, null); //var_dump($ad_arr);
	$pages = $ad_arr['pages'];
	$ad_arr = $ad_arr['data'];
	
	require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view);
  