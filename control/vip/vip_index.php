<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");

$istop_shop_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop where istop = 1 and isvip = 1 order by shop_id DESC LIMIT 8",1,7200);

$page_title = 'VIP服务 加入VIP商铺即可享受更多特权，赢得更多订单！_IT帮手网';
$page_keyword = 'VIP商铺，VIP特权，VIP服务';
$page_description ='IT帮手网VIP商铺拥有更多VIP特权。VIP店铺个性主页，VIP身份标志，同城任务速配，黄金广告位展示。享受更多VIP特权，更多的VIP商铺展示，更多的威客任务推荐，加入一品VIP。';
		
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );