<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-8-6 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title ="创意大赛问题解答" . '-' . $_K ['html_title'];

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );