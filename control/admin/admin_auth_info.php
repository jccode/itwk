<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-9-01 11:35:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$auth_code or kekezu::admin_show_msg ( $_lang['error_param'], "index.php?do=auth",3,'','warning');
$auth_code and require S_ROOT.'./auth/'.$auth_dir.'/control/admin/auth_info.php';
