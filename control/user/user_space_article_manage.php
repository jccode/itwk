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
if(isset($art_id) && isset($operate)){
	$art_obj =  new Keke_witkey_article_flagship_class();
	$art_obj->setWhere("art_id=".$art_id);
	switch ($operate) {
		case 'del':
			$res = $art_obj->del_keke_witkey_article_flagship();
			kekezu::show_msg ( "操作提示", $url, '1', '删除成功！', 'alert_right' );
	
		break;
	}
}

//读取信息
$page_obj = $kekezu->_page_obj;
$sql = sprintf("select art_id,art_title,shop_id,art_type,pub_time from %switkey_article_flagship where ",TABLEPRE);
$where = "  uid=". intval($uid);

if(intval($art_type)){
	$url.="&art_type=".intval($art_type);
	$where.=" and art_type = ".intval($art_type);
}

if(isset($art_title)){
	$url.="&art_title=".$art_title;
	$where.=" and art_title like '%".$art_title."%'";
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
		$where.=" and pub_time = ".intval(strtotime($start_time));
	}else{
		$where.=" and pub_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}

$count = db_factory::get_count ( sprintf ( "select count(art_id) from %switkey_article_flagship where %s", TABLEPRE, $where ) );
$where .= " order by art_id desc";
$pages = $page_obj->getPages($count, $page_size, $page, $url, '#userCenter');
$art_info = db_factory::query($sql . $where . $pages['where']);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );