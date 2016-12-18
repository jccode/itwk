<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 11:45
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$views = array ('index','s_list','n_list','info');
! in_array ( $view, $views ) && $view = 'index';

require $do.'/'.$do.'_' . $view . '.php';