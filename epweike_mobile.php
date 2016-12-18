<?php

define ( "IN_KEKE", true );

require ('app_comm.php');

$config_basic_obj = new Keke_witkey_basic_config_class ();

$config_basic_arr = $config_basic_obj->query_keke_witkey_basic_config ();

foreach ( $config_basic_arr as $k => $v ) {
	$config_arr [$v ['k']] = $v ['v'];
}

file_put_contents('epweike_mobile.log', ''.date("Y-m-d H:i:s").''.chr(13), FILE_APPEND);

//header("Location: ".$config_arr['apk_url']);
header("Location: /epweike_mobile.apk");
exit;
