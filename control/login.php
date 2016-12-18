<?php

/**
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-27早上9:55:00
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title=$_lang['login'].'- '.$_K['html_title'];
$uid and header ( "location:index.php" ); 
$open_api_arr = $kekezu->_api_open;
$api_name = keke_glob_class::get_open_api();
//初始化对象 
$login_obj = new keke_user_login_class();   
$inter = $kekezu->_sys_config ['user_intergration'];

if (kekezu::submitcheck(isset($formhash))|| isset($login_type) ==3) {
	//登录之前的地址
	 isset($hdn_refer) and $_K['refer'] = $hdn_refer;  
	 $txt_code = isset($txt_code)?$txt_code:"";
	 $login_type = isset($login_type)?$login_type:"";
	 $ckb_cookie = isset($ckb_cookie)?$ckb_cookie:"";
	//用户登录 
 	$user_info = $login_obj->user_login($txt_account, md5($pwd_password),$txt_code,$login_type); 
 	//存储用户信息 
	$login_obj->save_user_info($user_info, $ckb_cookie,$login_type); 
}
require  keke_tpl_class::template ( $do );