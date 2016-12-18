<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$job_age_arr = keke_glob_class::get_job_age();
$job_salary_arr = keke_glob_class::get_job_salary();
$job_experience_arr = keke_glob_class::get_job_experience();
$job_obj_arr = keke_glob_class::get_job_obj();
//招聘职位列表
$page and $page=intval ( $page ) or $page = 1;
$url = $ac_url.'&view=job_list';
$where = " shop_id = '$shop_info[shop_id]'";
$job_obj = keke_table_class::get_instance ( "witkey_job" );
$where .= " order by is_top desc,job_id DESC ";

$d = $job_obj->get_grid ( $where, $url, $page, 5, null);
$job_arr = $d [data];
$pages = $d [pages]; 
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );