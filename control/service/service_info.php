<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 14:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );