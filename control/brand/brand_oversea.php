<?php
/**
 * 品牌馆-海外馆@copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-31 10:44
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$page_title = '品牌馆-海外馆';
$page_keyword = '品牌馆-海外馆';
$page_description ='品牌馆-海外馆';
		
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );