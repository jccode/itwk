<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = '客服中心 提供网上客服中心 在线客服中心 qq在线客服中心 24小时人工电话_IT帮手网';
$page_keyword = '客服中心，网上客服中心，在线客服中心，qq在线客服中心，24小时人工电话';
$page_description = 'IT帮手网客服中心频道，一品会员解决问题与矛盾的网上客服中心。qq在线客服中心，并提供24小时人工电话，一品会员在使用网站服务的过程中有什么疑问或不满，都可以到IT帮手网网上客服中心进行反应，在第一时间得到解决。';

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );