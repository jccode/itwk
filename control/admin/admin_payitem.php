<?php
/**
 * 增值服务的后台路由
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-11-5-下午02:03:21
 * @version V2.0
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','buy','config');

(! empty ( $view ) && in_array ( $view, $views ))  or  $view = 'index';


require ADMIN_ROOT . 'admin_payitem_' . $view . '.php';
 
