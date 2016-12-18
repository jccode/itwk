<?php
/**
 * 退出后台
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-5-19上午08:10:16
 */

$_SESSION ['uid'] = "";
$_SESSION ['username'] = "";
$_SESSION ['auid'] = "";
$_SESSION ['user_info'] = "";
if (isset ( $_COOKIE ['user_login'] )) {
	setcookie ( 'user_login', '' );
}

?>
<script> parent.location.href="index.php?do=login" </script>
