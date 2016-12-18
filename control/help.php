<?php
/**
  * 关于我们
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-12 13:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','answer','list','info','self','service','get_username','get_password','get_securitycode','wanzhuanyipin');
! in_array ( $view, $views ) && $view = 'index';
	
require $do.'/'.$do.'_' . $view . '.php';