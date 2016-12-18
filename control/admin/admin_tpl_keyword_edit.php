<?php
/**
 * 友连编辑
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-08-29
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 202 );
//实例化文章关键字对象
$keyword_obj = new Keke_witkey_article_keyword_class();

//文章关键字信息
if ($keyword_id) {
	$keyword_info = $keyword_obj->setWhere ( 'keyword_id=' . $keyword_id );
	$keyword_info = $keyword_obj->query_keke_witkey_article_keyword();
	$keyword_info = $keyword_info [0];
} 

//编辑文章关键字
if ($sbt_edit) { //print_r($txt_location);exit;
	$keyword_obj->setWord($word);
	$keyword_obj->setUrl($url);
	$keyword_obj->setKeyword_status($keyword_status);
	$keyword_obj->setUid($admin_info['uid']);
	$keyword_obj->setUsername($admin_info['username']);
	$keyword_obj->setEdit_time(time());
	if ($keyword_id) {
		$keyword_obj->setKeyword_id($keyword_id);
		$res = $keyword_obj->edit_keke_witkey_article_keyword(); //编辑文章关键字
		if ($res) {
			kekezu::admin_system_log ( '文章关键字编辑' . $keyword_id );
			kekezu::admin_show_msg ( '文章关键字编辑成功', 'index.php?do=' . $do . '&view=keyword&page='.$page,3,'','success' );
		}
	} else {
		$keyword_obj->setAdd_time(time());
		$res = $keyword_obj->create_keke_witkey_article_keyword(); //添加文章关键字
		if ($res) {
			kekezu::admin_system_log ( '文章关键字添加' . $res );
			kekezu::admin_show_msg ( '文章关键字添加成功', 'index.php?do=' . $do . '&view=keyword&page='.$page,3,'','success' );
		}
	}
}

require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );