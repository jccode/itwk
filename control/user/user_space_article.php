<?php
/**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$art_obj = keke_table_class::get_instance ( "witkey_article_flagship" );
(isset ( $art_id ) and intval ( $art_id ) > 0) and $art_info = $art_obj->get_table_info ( 'art_id', $art_id );
empty ( $art_info ) or extract ( $art_info );
$ac_url = $origin_url . "&op=".$op;
/**
 * 处理页面表单的提交
 */

if (isset($formhash)&&kekezu::submitcheck($formhash)) {  
	//文章发布时间
	$fields ['pub_time'] = time ();
	$fields['uid'] = $uid;
	$fields['username'] = $user_info['username'];
	$fields['shop_id'] = $shop_info['shop_id'];
	$fields=kekezu::escape($fields);
	
	$res = $art_obj->save ( $fields, $pk );
	if($pk['art_id']){
	  $msg = '编辑';
	}else{
	  $msg = '添加';
	} 
	if($res){
		
		$res and kekezu::show_msg ( "操作提示", $origin_url."&op=article_manage" , '1', $msg.'成功！', 'alert_right' ) ;
	}else{
	
		$res and kekezu::show_msg ( "操作提示", $ac_url."&op=article_manage" , '1', $msg.'失败！', 'alert_error' ) ;
	}
}


require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op);
