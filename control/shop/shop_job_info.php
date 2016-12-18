<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//当前职位详情
if(isset($job_id)){
	$job_info=db_factory::get_one("select * from ".TABLEPRE."witkey_job where job_id=$job_id");
	$local = explode(',',$job_info['job_address']);
}else{
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&view=case_list",3,"职位参数有误，请返回列表页重试！","warning");
}
if($ac=='apply'){
   //消息通知
  kekezu::notify_user ( "职位应聘", $username.'应聘了您发布的职位<a href="' . $ac_url. '&view=job_info&job_id=' . $job_id. '" target="_blank">' . $job_info ['job_title'] . '</a>', $job_info['uid'], $job_info['username'] );
  kekezu::echojson( '职位应聘成功',"1") or kekezu::echojson( '职位应聘失败',"0");					
}
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );