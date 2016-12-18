<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');


$opp or $opp='task';//收藏类型

$title ="收藏夹";

$favor_obj=new Keke_witkey_favorite_class();//收藏对象
$page_obj=$kekezu->_page_obj;//分页对象

$where=" uid = ".intval($uid);

intval($page) or $page=1;
intval($page_size) or $page_size='10';

$url=$origin_url."&op=$op&opp=$opp&obj_type=$obj_type&ord=$ord&page=$page&page_size=$page_size";
if($ac=='del'&&$fid){
	$res = db_factory::execute('delete from '.TABLEPRE.'witkey_favorite where f_id='.$fid);
	$res and kekezu::show_msg('操作提示',$url,1,'删除成功','alert_right') or kekezu::show_msg('操作提示',$url,1,'删除失败','alert_error');
}

$opp and $where.=" and keep_type ='".$opp."'";

$obj_title and $where.=" and obj_name 	like '%".$obj_title."%'";

$ord and $where.=" order by $ord " or $where.=" order by f_id desc ";

$favor_obj->setWhere($where);
$count=intval($favor_obj->count_keke_witkey_favorite());
$pages=$page_obj->getPages($count, $page_size, $page, $url,"#userCenter");

$favor_obj->setWhere($where.$pages['where']);
$favor_arr=$favor_obj->query_keke_witkey_favorite();

require keke_tpl_class::template('user/user_'.$op);

