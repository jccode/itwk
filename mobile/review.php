<?php
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );
/**
 * 留言建议接口
 */
$comment_obj = keke_table_class::get_instance ( "witkey_comment" );
$fds ['username'] = kekezu::gbktoutf($comment_name);
$fds ['content'] = kekezu::gbktoutf($content);
$fds ['uid'] = $user_info ['uid'] ? $user_info ['uid'] : 0;
$fds ['origin_id'] = 0;
$fds ['obj_type'] = 'ask';
$fds ['p_id'] = 0;
$fds ['on_time'] = time ();
$fds ['status'] = 0;

$res = $comment_obj->save ( $fds );
$res and kekezu::echojson ('',1) or kekezu::echojson (array('r'=>'review failed!'),0);