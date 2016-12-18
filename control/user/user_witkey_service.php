<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2012-06-16 9:03
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$price_unit  = keke_glob_class::get_price_unit();//价格单位
intval($page_size) or $page_size=10;
intval($page) or $page=1;
$url = "index.php?do=$do&view=$view&op=$op&page=$page&page_size=$page_size&service_title=$service_title&start_time=$start_time&end_time=$end_time";

//删除信息
if(isset($service_id) && isset($operate)){
	if($operate=='del' && intval($service_id)){
		$service_obj =  new Keke_witkey_service_class();
		$service_obj->setWhere("service_id=".$service_id);
		$res = $service_obj->del_keke_witkey_service();
		if($res){
			kekezu::show_msg('操作提示',$url,2,'删除服务信息成功！','success');
		}else{
			kekezu::show_msg('操作提示',$url,2,'删除服务信息失败！','warning');
		}
	}
}

//读取信息
$page_obj = $kekezu->_page_obj;
$sql = sprintf("select service_id,title,price,unite_price,on_time from %switkey_service where ",TABLEPRE);
$where = "model_id=7 and service_status=1 and uid=". $_SESSION['uid'];
if(isset($service_title)){
	$where.=" and title like '%".$service_title."%'";
}
if($start_time&&!$end_time){
	$where.=" and on_time >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$where.=" and on_time <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
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

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" .$op );