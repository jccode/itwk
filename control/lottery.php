<?php
/**
  * 摇奖页面
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-21 16:37
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','list','info');
! in_array ( $view, $views ) && $view = 'index';

require $do.'/'.$do.'_' . $view . '.php';