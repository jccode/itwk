<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$price_unit  = keke_glob_class::get_price_unit();//价格单位
intval($page_size) or $page_size=10;
intval($page) or $page=1;
$url = "index.php?do=$do&view=$view&op=$op&page=$page&page_size=$page_size";

//删除信息
if(isset($service_id) && isset($operate)){
	$service_obj =  new Keke_witkey_service_class();
	$service_obj->setWhere("service_id=".$service_id);
	switch ($operate) {
		case 'del':
			$res = $service_obj->del_keke_witkey_service();
			kekezu::show_msg ( "操作提示", $url, '1', '删除成功！', 'alert_right' );
		;
		break;
		case 'rec':
			$service_obj->setIs_top(1);
			$res = $service_obj->edit_keke_witkey_service();
			kekezu::show_msg ( "操作提示", $url, '1', '推荐成功！', 'alert_right' );
			;
		break;
		case 'cancel_rec':
			$service_obj->setIs_top(0);
			$res = $service_obj->edit_keke_witkey_service();
			kekezu::show_msg ( "操作提示", $url, '1', '取消推荐成功！', 'alert_right' );
		;
		break;
	}
}

//读取信息
$page_obj = $kekezu->_page_obj;
$sql = sprintf("select service_id,title,price,unite_price,pic,indus_id,indus_pid,service_type,is_top,on_time from %switkey_service where ",TABLEPRE);
$where = "  uid=". intval($uid);

if(intval($service_type)){
	$url.="&service_type=".intval($service_type);
	$where.=" and service_type = ".intval($service_type);
}

if(isset($service_title)){
	$url.="&service_title=".$service_title;
	$where.=" and title like '%".$service_title."%'";
}

if($start_time&&!$end_time){
	$url.="&start_time=".$start_time;
	$where.=" and on_time >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$url.="&end_time=".$end_time;
	$where.=" and on_time <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
	$url.="&start_time=$start_time&end_time=$end_time";
	if($start_time==$end_time){
		$where.=" and on_time = ".intval(strtotime($start_time));
	}else{
		$where.=" and on_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}

$count = db_factory::get_count ( sprintf ( "select count(service_id) from %switkey_service where %s", TABLEPRE, $where ) );
$where .= " order by service_id desc";
$pages = $page_obj->getPages($count, $page_size, $page, $url, '#userCenter');
$service_info = db_factory::query($sql . $where . $pages['where']);



require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );