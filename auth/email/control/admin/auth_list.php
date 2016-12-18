<?php
/**
 * @copyright keke-tech
 * @author Aqing
 * @version v 2.0
 * 2010-08-26 09:40:34
 * @desc 修改于2012-10-10，修改人：xxy
 */
defined ( "ADMIN_KEKE" ) or exit ( "Access Denied" ); //控制权限
kekezu::admin_check_role(71);
$url = "index.php?do=" . $do . "&view=" . $view . "&auth_code=" . $auth_code . "&w[page_size]=" . $w [page_size] . "&w[realname_a_id]=" . $w [realname_a_id] . "&w[username]=" . $w [username] . "&w[auth_status]=" . $w [auth_status]; //跳转地址
$email_obj = new keke_table_class('witkey_auth_email'); //实例化邮箱认证表对象
if(($auth_id||$email_a_id)&&$_SESSION['brandType']){
	$email_a_id?$auth_id=$email_a_id:$auth_id=$auth_id;
	$auth=$admin_obj->agent_auth("witkey_auth_email", 'email_a_id='.$auth_id,'email_a_id');
	if(!$auth){//无权限则跳出
		kekezu::admin_show_msg ( $_lang ['no_auth'], $url, 3, '', 'error' );
		die();
	}
}
if (isset ( $ac ) && $ac == 'del') {
	kekezu::admin_system_log($obj.$_lang['email_auth_delete']);
	$auth_obj->del_auth ( $email_a_id ); //单条删除认证申请
} elseif (isset ( $sbt_action ) && $sbt_action == $_lang['mulit_delete']) {
	$keyids = $ckb;
	kekezu::admin_system_log($_lang['mulit_del_email_auth']);
	$auth_obj->del_auth ( $keyids ); //批量删除
} else {
	$url = "index.php?do=" . $do . "&view=" . $view . "&auth_code=" . $auth_code . "&w[page_size]=" . $w [page_size] . "&w[email_a_id]=" . $w [email_a_id] . "&w[username]=" . $w [username]; //跳转地址
	$field="email.*,brand.brand";
	$table=array('email'=>'witkey_auth_email','brand'=>'witkey_brand');
	isset($_SESSION['brandType'])?$join=array("inner join"):$join=array("left join");
	$on=array('email.uid=brand.uid');
	isset($_SESSION['brandType'])?$where="brand.brand in(".$_SESSION['brandType'].") and brand.app_status=1":$where = " 1 = 1 "; //默认查询条件
	intval ( $w ['email_a_id'] ) and $where .= " and email.email_a_id = " . intval ( $w ['email_a_id'] ) . ""; //搜索认证编号
	($w ['auth_status'] === "0" and $where .= " and email.auth_status = 0 ") or ($w ['auth_status'] and $where .= " and email.auth_status = '$w[auth_status]' "); //搜索认证状态
	$w ['username'] and $where .= " and email.username like '%" . $w ['username'] . "%' "; //搜索认证标题
	$order="email.email_a_id desc "; // order by auth_status asc 
	intval ( $w ['page_size'] ) and $page_size = intval ( $w ['page_size'] ) or $page_size = 10; //每页显示多少条，默认10
	intval ( $page ) or $page = 1 and $page = intval ( $page );
	/*$kekezu->_page_obj->setAjax(1);
	$kekezu->_page_obj->setAjaxDom("ajax_dom");*/
	$res = $email_obj->get_multi_grid ($field,$table,$join,$on,$where,$order,$page, $page_size,$url);
	$email_arr = $res ['data'];
	$pages = $res ['pages'];
}

if($action){
	$email_obj->_table_obj->setWhere("email_a_id = $auth_id"); 
	if($action=='pass'){
		$email_obj->_table_obj->setAuth_status(1);
		
	}elseif ($action=='not_pass'){
		$email_obj->_table_obj->setAuth_status(2); 
	}
	$res =  $email_obj->_table_obj->edit_keke_witkey_auth_email();
	kekezu::admin_show_msg($_lang['operate_success'],'index.php?do=auth&view=list&auth_code=email',3,'','warning');
}

require $template_obj->template ( 'auth/' . $auth_dir . '/control/admin/tpl/auth_list' );
