<?php
/**
  * 关于我们
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-4下午02:57:33
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index','about','invite','service','release','pay','contact','reg','invoice');
! in_array ( $view, $views ) && $view = 'index';

$about_title = array(
	'index' => '关于我们 ',
	'about' => '走进一品 ',
	'service' => '服务协议 ',
	'release' => '发布协议 ',
	'pay' => '支付方式 ',
	'contact' => '联系我们 '
);
$page_title = $about_title[$view].' - 关于我们 '.$_lang['mobi_version'].'- '.$_K['html_title'];  
$nav_style[$view] = 'class="selected"';
require $do.'/'.$do.'_' . $view . '.php';