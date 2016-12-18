<?php
/**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$job_age_arr = keke_glob_class::get_job_age();
$job_salary_arr = keke_glob_class::get_job_salary();
$job_experience_arr = keke_glob_class::get_job_experience();
$job_obj_arr = keke_glob_class::get_job_obj();
$job_education_arr = keke_glob_class::get_job_education();
$job_obj = keke_table_class::get_instance ( "witkey_job" );
(isset ( $job_id ) and intval ( $job_id ) > 0) and $job_info = $job_obj->get_table_info ( 'job_id', $job_id );
empty ( $job_info ) or extract ( $job_info );
$loca = explode(',',$job_info['job_address']);

$ac_url = $origin_url . "&op=".$op;

/**
 * 处理页面表单的提交
 */

if (isset($formhash)&&kekezu::submitcheck($formhash)) {  
	//文章发布时间
	$fields ['pub_time'] = time ();
	$fields['cut_time'] = strtotime($cut_time);
	$fields['uid'] = $uid;
	$province&&$city and $fields['job_address'] = $province.','.$city;
	$fields['username'] = $user_info['username'];
	$fields['shop_id'] = $shop_info['shop_id'];
	$fields=kekezu::escape($fields);
	
	$res = $job_obj->save ( $fields, $pk );
	if($pk['job_id']){
	  $msg = '编辑';
	}else{
	  $msg = '发布';
	} 
	if($res){
		
		$res and kekezu::show_msg ( "操作提示", $origin_url."&op=job_manage" , '1', $msg.'成功！', 'alert_right' ) ;
	}else{
	
		$res and kekezu::show_msg ( "操作提示", $ac_url."&op=job_manage" , '1', $msg.'失败！', 'alert_error' ) ;
	}
}


require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op);
