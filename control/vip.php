<?php
/**
  * 关于我们
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-4下午02:57:33
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','desc','story','open','help');
! in_array ( $view, $views ) && $view = 'index';

require $do.'/'.$do.'_' . $view . '.php';