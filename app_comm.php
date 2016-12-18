<?php
/**
 * this not free,powered by keke-tech
 * @version kppw 2.0
 * @auther 九江
 * 
 */
//error_reporting(E_ALL|E_STRICT);
error_reporting(E_ERROR);

require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib/thirdparty/underscore.php');
require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib/inc/keke_base_class.php'); 
require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib/inc/keke_core_class.php');

$basic_config  = $kekezu->_sys_config;
$model_list = $kekezu->_model_list;
$nav_list = $kekezu->_nav_list;
//$exec_time_traver = (! $exec_time_traver || $exec_time_traver < time ()) ? true : false;

$exec_time_traver = $kekezu->_cache_obj->get ( 'time_traveler_last_exec_cache' );
(!$exec_time_traver||$exec_time_traver<time()) and $exec_time_traver = true or $exec_time_traver = false;
$_K['refer'] = $_SERVER['HTTP_REFERER']; 

$_R = $_REQUEST;
$_R = kekezu::k_input($_R);  
$_R and extract ($_R,EXTR_SKIP);
$refer and $_K['refer']=$refer;
isset($inajax) and $_K['inajax']= $inajax; 
unset ( $uid, $username);
