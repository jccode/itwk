<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title="玩转一品,".$_K['html_title'];

require keke_tpl_class::template ( SKIN_PATH . "/help/{$do}_{$view}" );