<?php
/**
 * 品牌馆-香港馆@copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-31 10:44
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$page_title = '品牌馆-香港馆';
$page_keyword = '品牌馆-香港馆';
$page_description ='品牌馆-香港馆';
$hk_list = db_factory::query(' select uid,shop_id from '.TABLEPRE.'witkey_brand where  app_status=1 and brand="hk" order by is_recommend desc,brand_id desc limit 0,16');


$unit_price = keke_glob_class::get_price_unit();
$hk_list[0] and $ser_info = db_factory::get_one(' select * from '.TABLEPRE.'witkey_service where shop_id='.$hk_list[0]['shop_id'].' order by views desc,price desc limit 0,1');
$top_10 = db_factory::query(' select a.views,a.shop_name,a.shop_id from '.TABLEPRE.'witkey_brand b '
				.' left join '.TABLEPRE.'witkey_shop a on a.shop_id=b.shop_id where b.app_status=1 and a.is_close=0 order by a.views desc limit 0,10');

				
				
				


require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );