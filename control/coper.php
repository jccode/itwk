<?php
/**
 * 
 * @author xxy
 * @version v 2.0
 * 2012-09-24
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title=$_lang['coper'].'- '.$_K['html_title'];
if(isset($formhash)&&kekezu::submitcheck($formhash)){
	//初始化对象
	$table_name="witkey_coper";
	$table_obj = keke_table_class::get_instance($table_name);
	$pk=array();
	$data['on_time']=time();
	$ep_a&&$data['address']=$ep_a;
	$ep_d&&$data['description']=$ep_d;
	$ep_e&&$data['email']=$ep_e;
	$ep_m&&$data['mobile']=$ep_m;
	$ep_n&&$data['name']=$ep_n;
	$ep_q&&$data['qq']=$ep_q;
	$ep_r&&$data['region']=$ep_r;
	$ep_s&&$data['status']=$ep_s;
	$ep_t&&$data['telephone']= $ep_t;
	$ep_tp&&$data['type']=$ep_tp;
	$ep_b&&$data['brand']=$ep_b;
	$result=$table_obj->save ( $data, $pk );
	$result and kekezu::echojson ( $_lang['add_success'], "1" ) or kekezu::echojson ( $_lang['add_fail'], "0" );
	die();
}
require  keke_tpl_class::template ($do);