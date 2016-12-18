<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

switch ($op){
	case "send"://发送消息
		if ($sbt_edit) {
			$tar_title= kekezu::escape($tar_title);
 			$tar_content = kekezu::escape($tar_content);
			keke_msg_class::send_private_message($tar_title,$tar_content,$to_uid, $to_username,'','json');
		} else{
			$title = $_lang['send_msg'];
			CHARSET=='gbk' and $to_username = kekezu::utftogbk($to_username);
			$to_user_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_space where uid='$to_uid'");
			
			$title = '发送站内信';
			require keke_tpl_class::template ( 'message' );
		}
		die ();
		break;
}