<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/**
 * 推广，登录/注册页面
 */
$uid and header ( "location:index.php" );
//添加登录与注册的语言包
keke_lang_class::loadlang ( 'login', 'index' );
keke_lang_class::loadlang ( 'register', 'index' );

if ($is_login == 1) {
	//登录成功后的地址
	if ($task_id) {
		$t_id = explode ( '-', $task_id );
		$_K ['refer'] = "index.php?do=task&task_id=" . $t_id [2];
	} else {
		$_K ['refer'] = "index.php?do=release";
	}
	//登录失败后指定跳转到本页面
	$_K ['do'] = $do;
	
	//用户登录
	$login_obj = new keke_user_login_class ();
	$user_info = $login_obj->user_login ( $txt_account, md5 ( $pwd_password ), "", 2 );
	
	//请求接口
	if ($releation_id) {
		//向联盟请求登录用户信息
		$service = 'keke_login';
		$param = array ('releation_id' => $releation_id,
						 'to_uid' => $user_info ['uid'],
						 'to_username' =>$user_info ['username'],
						 'r_task_id' => intval($r_task_id),
						 'login_type'=>max($login_type,1),
						 'app_uid'=>$app_uid);
		keke_union_class::union_request ( $service, $param );
		$login_type==2 and db_factory::execute('update '.TABLEPRE.'witkey_space set `union_user`=1,`union_rid`='.$releation_id.' where uid='.$user_info['uid']);
	}
	//登录成功跳转到任务详细页
	if ($user_info) {
		$login_obj->save_user_info ( $user_info );
		header ( 'location:' . $_K ['refer'] );
	}
} elseif ($is_register == 1) {
	//注册失败后的跳转页面
	$_K ['do'] = $do;
	//登录成功后的跳转地址
	if ($task_id) {
		$_K ['refer'] = "index.php?do=task&task_id=$task_id";
	} else {
		$_K ['refer'] = "index.php?do=task&release";
	}
	$reg_obj = new keke_register_class ();
	//用户注册
	$reg_uid = $reg_obj->user_register ( $txt_account, md5 ( $pwd_password ), $txt_email, '', 0, $pwd_password );
	$user_info = keke_user_class::get_user_info ( $reg_uid );

	//请求接口
	if ($releation_id) {
		//向联盟请求登录用户信息
		$service = 'keke_login';
		$param = array ('releation_id' => $releation_id,
						 'to_uid' => $user_info ['uid'],
						 'to_username' => $user_info ['username'],
						 'r_task_id' => $r_task_id,
						 'login_type'=>max($login_type,1),
						 'app_uid'=>$app_uid);
		keke_union_class::union_request ( $service, $param );
		$login_type==2 and db_factory::execute('update '.TABLEPRE.'witkey_space set `union_user`=1,`union_rid`='.$releation_id.' where uid='.$user_info['uid']);
	}
	//用户登录
	$reg_obj->register_login ( $user_info );

}

require keke_tpl_class::template ( 'login_p' );
