<?php
/**
 * @copyright keke-tech
 * @author yj
 * @version v 2.0
 * 2012-9-17早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//取得商店的信息
//var_dump($shop_info);

if(isset($art)){
  switch($art){
  	case 1:
  		$title = '公司简介';
  		$about_info = $shop_info['shop_intro'];
  		break;
  	case 2:
  		$title = '荣誉资质';
  		$about_info = $shop_info['shop_honor'];
  		break;
  	case 3:
  		$title = '团队实力';
  		$about_info = $shop_info['shop_power'];
  		break;
  	case 4:
  		$title = '公司采风';
  		$about_info = $shop_info['shop_report'];
  		break;
  	case 5:
  		$title = '联系方式';
  		$about_info = $shop_info['contact_type'];
  		break;
  }
}else{
  $title = '公司简介';
  $about_info = $shop_info['shop_intro'];
}
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );