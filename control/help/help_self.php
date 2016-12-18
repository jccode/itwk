<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = '自助服务 用户遇到常见问题的解答平台_IT帮手网';
$page_keyword = '自助服务，常见问题，解答平台';
$page_description = 'IT帮手网自助服务频道，用户遇到常见问题的解答平台。雇主和威客在账号安全、一品认证、交易管理等遇到的常见问题，都可以发起自助服务，在IT帮手网自助服务频道的解答平台上得到帮助。';

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );