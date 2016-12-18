<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-09-02 11:40:30
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(61);
$event_id or kekezu::admin_show_msg ( $_lang['param_error'], "index.php?do=$do&view=event",3,'','warning');

$event_id and $event_info= db_factory::get_one(" select * from ".TABLEPRE."witkey_prom_event where event_id = '$event_id'");

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );