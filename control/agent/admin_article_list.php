<?php
/**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 * @example art => cate_id <100 ,help =>cate_id<200 and cate_id >=100,singel =>cate_id >=200
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$art_obj = new Keke_witkey_article_class ();

$table_obj = new keke_table_class ( "witkey_article" );
$types = array ('help', 'art', 'single','case' );
$type = (! empty ( $type ) && in_array ( $type, $types )) ? $type : 'art';
$url = "index.php?do=$do&view=$view&w[username]=$w[username]&w[art_title]=$w[art_title]&w[art_cat_id]=$w[art_cat_id]&page_size=$page_size&page=$page&type=$type";

if ($ac == 'del') {
	$res = $table_obj->del ( 'art_id', $art_id, $url );
	$res and kekezu::admin_show_msg ( $_lang['operate_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['operate_fail'], $url,3,'','warning' );
} elseif (isset ( $sbt_action )) {
	sizeof ( $ckb ) or kekezu::admin_show_msg ( $_lang['choose_operate_item'], $url,3,'','warning' );
	is_array ( $ckb ) and $ids = implode ( ',', array_filter ( $ckb ) );
	$art_obj->setWhere ( "art_id in ($ids)" );
	switch ($sbt_action) {
		case $_lang['recycle'] ://放入回收站
			$art_obj->setIs_show ( 2 );
			$res = $art_obj->edit_keke_witkey_article ();
			kekezu::admin_system_log ( $_lang['mulit_delete_articles'] );//批量删除文章
			break;
		case $_lang['recovery_articles'] ://恢复文章
			$art_obj->setIs_show ( 1 );
			$res = $art_obj->edit_keke_witkey_article ();
			kekezu::admin_system_log ( $_lang['mulit_recovery_articles']);
			break;
		case $_lang['mulit_delete'] ://批量删除
			$res = $art_obj->del_keke_witkey_article ();
			kekezu::admin_system_log ( $_lang['mulit_recovery_articles'] );
			break;
		default :
			break;
	}
	$res and kekezu::admin_show_msg ( $_lang['mulit_operate_success'], $url,3,'','success' ) or kekezu::admin_show_msg ( $_lang['mulit_operate_fail'], $url,3,'','warning' );

} elseif ($op == 'listorder') {
	$art_obj = new Keke_witkey_article_class();
	$art_obj->setWhere ( "art_id='$art_id'" );
	$art_obj->setListorder (intval($value));
	$art_obj->edit_keke_witkey_article(); 
	kekezu::admin_system_log($_lang['edit_art_order'].$art_id);
	die ();
} else {
	$field="a.art_cat_id,a.art_id,a.art_title,a.content,a.art_pic,s.shop_id";
	$table="keke_witkey_article as a,keke_witkey_shop as s";
	$where="a.r_uid=s.uid and a.is_show=1 and a.art_cat_id in(594,640)";
	$order="a.pub_time desc";
	$limit="0,1";
	$sql=sprintf("select %s from %s where %s order by %s limit %s",$field,$table,$where,$order,$limit);
	$weike_list=db_factory::query($sql);
	
	$field="a.*,b.brand";
	$table=array('a'=>'witkey_article','b'=>'witkey_brand');
	$join=array("left join");
	$on=array('a.r_uid=b.uid');
	$where = " a.art_cat_id in(594,640) and case a.art_cat_id
               when 594 then b.brand='".$_SESSION['brandType']."' end";
	switch ($type) {
		case 'art' :
			kekezu::admin_check_role ( 16 );
			$art_cat_arr = kekezu::get_table_data ( '*', "witkey_article_category", "art_index like '%{1}%'", " listorder asc", '', '', 'art_cat_id', null );
			//$where .= " and art_cat_id in (select art_cat_id from " . TABLEPRE . "witkey_article_category where art_index like '%{1}%') ";
			$where.=" and a.art_type = 'art' ";
			break;
			;
	}
	
	//文章分类
	$temp_arr = array ();
	kekezu::get_tree ( $art_cat_arr, $temp_arr, 'option', $w['art_cat_id'], 'art_cat_id', 'art_cat_pid', 'cat_name' );
	$cat_arr_list = $temp_arr;
	unset ( $temp_arr );
	
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 10;
	$page and $page = intval ( $page ) or $page = 1;
	$w [art_id] and $where .= " and a.art_id = ".intval ( $w [art_id] );
	$w [art_title] and $where .= " and a.art_title like '%$w[art_title]%'";

	
	$w [art_cat_id] and $where .= " and a.art_cat_id in  (select a.art_cat_id from " . TABLEPRE . "witkey_article_category where a.art_index like '%{{$w [art_cat_id] }}%')";
	$w [username] and $where .= " and a.username like '%$w[username]%' ";
	$w [content] and $where .= " and a.content like '%$w[content]%'";
	$ord[0]&&$ord[1] and $order =' '.$ord[0].' '.$ord[1] or $order="  a.art_id desc ";
	$r = $table_obj->get_multi_grid ($field,$table,$join,$on,$where,$order,$page, $page_size,$url);
	$art_arr = $r [data];
	$pages = $r [pages];
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );