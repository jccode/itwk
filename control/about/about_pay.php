<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//$page_title=$_lang['mobi_version'].'- '.$_K['html_title'];
$ops = array("online","offline");
in_array($op, $ops) or $op="offline";
$nav_list = array("online"=>"在线支付方式","offline"=>"线下支付方式");
require $do.'_' . $view .'_'.$op .'.php';