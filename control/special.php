<?php
/**
 * 特色专栏
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-06-19 14:10:00
 */
defined ( 'IN_KEKE' ) or exit('Access Denied');

$views = array ("case_list","case_info","special_list","video_list","video_info","employer_list","employer_info","witkey_list","witkey_info");
! in_array ( $view, $views ) && $view = "case_list";

require $do.'/'.$do.'_' . $view . '.php';