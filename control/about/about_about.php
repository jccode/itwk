<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '走进一品 '.$_lang['mobi_version'].'- '.$_K['html_title'];  

$ops = array ('index','founder','team','history','lawer');
(!empty($op) && in_array($op, $ops)) and $op or $op='index';

$menu_style[$op] = 'class="current"';
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_{$op}" );
