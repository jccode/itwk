<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('list', 'edit' );

( ! empty($view) && in_array ($view, $views) ) or $view = 'list';

$filename = ADMIN_ROOT . 'admin_' . $do . '_' . $view . '.php';

if (file_exists ( $filename )) {
	require $filename;
} else {
	kekezu::admin_show_msg ( $_lang['404_page'], '', 3, '', 'warning' );
}

