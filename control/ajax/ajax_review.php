<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if($sbt_action == '1'){	
	if($txt_comment_name && $txt_comment_content){
		$comment_obj = keke_table_class::get_instance("witkey_comment");
		$fds['username'] = $txt_comment_name;
		$fds['content'] = $txt_comment_content;	
		$fds['uid'] = $user_info['uid'] ? $user_info['uid'] : 0;	
		$fds['origin_id'] = 0;	
		$fds['obj_type'] = 'ask';	
		$fds['p_id'] = 0;		
		$fds['on_time'] = time();
		$fds['status'] = 0;
	
		$res = $comment_obj->save($fds);	
		$res and kekezu::echojson ( '点评成功', "1" ) or kekezu::echojson ( '点评失败', "0" );
	}
	
	die();
}

$title = '一句话点评';
require $template_obj->template ( 'ajax/ajax_review' );