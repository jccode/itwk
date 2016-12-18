<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = 'VIP商铺常见问题解答 收费的VIP商铺和普通商铺，服务有什么不同？_IT帮手网';
$page_keyword = '常见问题，普通商铺，如何解决问题，常见问题解答';
$page_description ='IT帮手网VIP商铺常见问题，在使用IT帮手网VIP商铺的过程中遇到问题，可以在常见问题中知道如何解决问题，获得解决问题的策略，更好的享受IT帮手网VIP商铺的贴心服务与一品VIP特权。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );