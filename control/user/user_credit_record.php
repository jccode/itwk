<?php
 /**
 * @copyright keke-tech
 * @author shangk	
 * @version v 2.0
 * 2012-5-29
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');
/**
 * 子集菜单
 */
$title="我的金币";

$credit_use = keke_glob_class::get_credit_use();

$table_obj=new Keke_witkey_credit_record_class();//收藏对象
$page_obj=$kekezu->_page_obj;//分页对象

$where=" uid = ".intval($uid);

intval($page) or $page=1;
intval($page_size) or $page_size=10;

$url=$origin_url."&op=$op&obj_type=$obj_type&ord=$ord&page=$page&page_size=$page_size";

$c_use and $where.=" and c_use = '".$c_use."' ";

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

$ord and $where.=" order by $ord " or $where.=" order by r_id desc ";

$table_obj->setWhere($where);
$count=intval($table_obj->count_keke_witkey_credit_record());
$pages=$page_obj->getPages($count, $page_size, $page, $url,"#userCenter");

$table_obj->setWhere($where.$pages['where']);
$data_arr=$table_obj->query_keke_witkey_credit_record();

require keke_tpl_class::template ( "user/" . $do . "_" . $op );


