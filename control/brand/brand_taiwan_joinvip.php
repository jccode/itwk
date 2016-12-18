<?php
/**
 * 品牌馆-台湾馆-品牌入驻
 * 
 * @author xxy
 * 2012-9-26
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '品牌馆-台湾馆-品牌入驻-IT帮手网';
$page_keyword = '品牌馆-台湾馆-品牌入驻-IT帮手网';
$page_description ='品牌馆-台湾馆-品牌入驻-IT帮手网';
$op_name="品牌入驻";
$istop_shop_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop where istop = 1 and isvip = 1 order by shop_id DESC LIMIT 8",1,7200);
//open_vip
$vip_level_arr = db_factory::query("select * from " . TABLEPRE . "witkey_vip_level");
$vip_level_arr or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=vip&view=index', 3, '暂时未开通VIP商铺！', 'warning');
$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );