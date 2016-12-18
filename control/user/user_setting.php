<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$ops = array ('basic', 'picture','credit_record', 'credit','safe','fina_account', 'account_bind','space','trans','auth','collect','prom','corp_site','index');

in_array ( $op, $ops ) or $op = "index";

require 'user_' . $op . '.php';