<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$auth_blank_error_arr = keke_glob_class::get_auth_blank_error_mes();

require $kekezu->_tpl_obj->template ( "auth/" . $auth_dir . "/control/admin/tpl/auth_nopass_box" );