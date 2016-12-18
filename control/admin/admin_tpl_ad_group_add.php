<?php
/**
 * 广告组编辑页面
 * @copyright keke-tech
 * @author hr
 * @version v 2.0
 * @date 2011-12-26 上午11:18:06
 * @encoding GBK
 */
kekezu::admin_check_role ( 32 );
$tag_obj = new Keke_witkey_tag_class ();
//添加,编辑
if ($sbt_edit) {	
	$tpl_type = $ckb ? implode ( ',', $ckb ) : $_K ['template'];
	$fds ['tagname'] = $fds['hdn_tagname'] ? $fds['hdn_tagname'] : $fds ['tagname'];
	$tag_obj->setTagname ( $fds ['tagname'] );
	$tag_obj->setCache_time ( intval ( $fds ['cache_time'] ) );
	$tag_obj->setTag_code ( $fds [tag_code] );
	$tag_obj->setTpl_type ( 'default' );
	$tag_obj->setTag_type ( 9 );
	$sbt_edit= strip_tags($sbt_edit);
	
	if(intval($tag_id)){
		$tag_obj->setWhere ( 'tag_id=' . intval($tag_id ));
		$result = $tag_obj->edit_keke_witkey_tag ();
	}else{
		$result = $tag_obj->create_keke_witkey_tag ();
	}

	kekezu::admin_system_log ( $sbt_edit.$fds ['tagname'] );
	$result_msg = $result ? $sbt_edit . $_lang['success'] : $sbt_edit . $_lang['fail'];
	if ($target_id){
		$jump_url = $result ? 'index.php?do=tpl&view=ad_add&ac=add&tagname='.$tagname.'&target_id='.$target_id 
							: 'index.php?do=tpl&view=ad_group_add&ac=add&tag_id'.$tag_id.'tagname='.$tagname.'&target_id='.$target_id ;
		$result_msg .= $result ? $_lang['jump_to_advertisement_page'] : $_lang['before_jump_to_page'];
	}
	$jump_url = $jump_url ? $jump_url : $_SERVER [HTTP_REFERER] ;
	kekezu::admin_show_msg ( $result_msg, $jump_url,'3','','success' );

}
$page_tips = $_lang['add'];
//查询
$ad_info = array();
$tagname && $ad_info['tagname']=$tagname;
if ($ac && $ac == 'edit') {	
	empty ( $tag_id ) && kekezu::admin_show_msg ($_lang['edit_parameter_error'], 'index.php?do=tpl&view=ad_group_add&ac=add',3,'','warning' ); //die
	$page_tips = $_lang['edit'];
	$tag_id = intval ( $tag_id );
	$tag_obj->setWhere ( 'tag_id="' . $tag_id . '"' );
	$ad_info = $tag_obj->query_keke_witkey_tag ();
	$ad_info = $ad_info [0];
}
//模板
$template_style_obj = new Keke_witkey_template_class ();  
$template_arr = $template_style_obj->query_keke_witkey_template ();

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );