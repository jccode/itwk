<?php
/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' )&&defined('ISWAP')&&ISWAP or kekezu::echojson ($wap_msg, 0);




if($ac=='change'){
	if($old_password && $new_password){
		$user_info = kekezu::get_user_info($uid);
		if(kekezu::utf8_strlen(kekezu::gbktoutf($new_password))<6 ||kekezu::utf8_strlen(kekezu::gbktoutf($new_password))>16){
			kekezu::echojson ( array ('r' => '密码长度4-16字符' ), 2 );
		}
		
		if(md5($old_password)!=$user_info['password']){
			kekezu::echojson ( array ('r' => '旧密码输入有误,请重试！' ), 0 );
		}

		if($old_password==$new_password){
			kekezu::echojson ( array ('r' => '旧密码和新密码不能相同！' ), 0 );
		}
		
		$message_obj = new keke_msg_class ();
		$v = array ("新密码" => $new_pass );
		$message_obj->send_message ( $user_info ['uid'], $user_info ['username'], 'update_password', "修改密码", $v, $user_info ['email'], $user_info ['mobile'] );
		
		$user_obj = new Keke_witkey_space_class ();
		$user_obj->setWhere ( "uid='$uid'" );
		$user_obj->setPassword ( md5 ( $new_password ) );
		$user_obj->edit_keke_witkey_space ();
		$member_obj = new Keke_witkey_member_class ();
		$member_obj->setWhere ( "uid='$uid'" );
		$member_obj->setPassword ( md5 ( $new_password ) );
		$res = $member_obj->edit_keke_witkey_member ();
		
		$flag = keke_user_class::user_edit ( $username, $old_password, $new_password, '', 0 ) > 0 ? 1 : 0;
		
		if ($flag && $res == 1){
			unset ( $_SESSION );
			kekezu::echojson ( array ('r' => '密码修改成功！' ), 1);
		}
		
	}else{
		kekezu::echojson ( array ('r' => '旧密码或新密码不能为空！' ), 0 );
	}
}

