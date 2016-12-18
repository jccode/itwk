<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$p_space_style = keke_glob_class::get_p_space_style (); //获取个人空间风格
$p_style_arr = keke_glob_class::get_p_space_name (); //获取个人空间名字
$e_space_style = keke_glob_class::get_e_space_style (); //获取企业空间风格
$e_style_arr = keke_glob_class::get_e_space_name (); //获取企业空间名字

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );