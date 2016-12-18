<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-9-18 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
intval($page_size) or $page_size=10;
intval($page) or $page=1;
$url = "index.php?do=$do&view=$view&op=$op&page=$page&page_size=$page_size";
//var_dump($shop_info);
$shop_id = $shop_info['shop_id'];
//删除信息
if(isset($job_id) && isset($operate)){
	$job_obj =  new Keke_witkey_job_class();
	$job_obj->setWhere("job_id=".$job_id);
	switch ($operate) {
		case 'del':
			$res = $job_obj->del_keke_witkey_job();
			kekezu::show_msg ( "操作提示", $url, '1', '删除成功！', 'alert_right' );
	
		break;
	}
}

//读取信息
$page_obj = $kekezu->_page_obj;
$sql = sprintf("select job_id,job_title,job_type,job_address,cut_time from %switkey_job where ",TABLEPRE);
$where = "  uid=". intval($uid);

if(isset($job_type)&&$job_type){
	$url.="&job_type=".$job_type;
	$where.=" and job_type = '$job_type'";
}

if(isset($job_title)){
	$url.="&job_title=".$job_title;
	$where.=" and job_title like '%".$job_title."%'";
}

if($start_time&&!$end_time){
	$url.="&start_time=".$start_time;
	$where.=" and pub_time >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$url.="&end_time=".$end_time;
	$where.=" and pub_time <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
	$url.="&start_time=$start_time&end_time=$end_time";
	if($start_time==$end_time){
		$where.=" and pub_time = ".intval(strtotime($start_time));
	}else{
		$where.=" and pub_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}

$count = db_factory::get_count ( sprintf ( "select count(job_id) from %switkey_job where %s", TABLEPRE, $where ) );
$where .= " order by job_id desc";
$pages = $page_obj->getPages($count, $page_size, $page, $url, '#userCenter');

$job_info = db_factory::query($sql . $where . $pages['where']);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
