<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];
$ops = array("contact","map");
in_array($op, $ops) or $op="contact";
$nav_list = array("contact"=>"联系方式","map"=>"来访地图");
require $do.'_' . $view .'_'.$op .'.php';