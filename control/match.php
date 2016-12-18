<?php
/**
  * 创意大赛
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-8-6下午02:57:33
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','case','prom','ask');
! in_array ( $view, $views ) && $view = 'index';
require $do.'/'.$do.'_' . $view . '.php';