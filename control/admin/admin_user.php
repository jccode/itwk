<?php
/**
后台用户路由
*/
defined ( 'ADMIN_KEKE' ) or 	exit ( 'Access Denied' );

$views = array("add","list","detail","integ","charge","custom_list","group_add","group_list","custom_add",
"track","track_edit","track_feed","track_filter");

$view = (!empty ( $view ) && in_array ( $view, $views )) ? $view : 'add';
require "admin_user_$view.php";