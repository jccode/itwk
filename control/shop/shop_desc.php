<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


//获取保密
$sect_info = kekezu::get_table_data ( "*", "witkey_member_ext", " type='sect' and uid='$shop_info[uid]' ", "", "", "", "k" );

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );