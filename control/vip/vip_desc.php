<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = 'VIP会员特权 具有同城vip服务与任务vip情人服务让你赢得更多商机_IT帮手网';
$page_keyword = 'VIP会员，同城vip，vip情人';
$page_description ='IT帮手网VIP商铺拥有更多VIP特权。VIP店铺个性主页，VIP身份标志，同城任务速配，黄金广告位展示。享受更多VIP特权，更多的VIP商铺展示，更多的威客任务推荐，加入一品VIP。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );