<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-6-26 9:50
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (164);

switch($ac){
	case 'close': //关闭
		$close_user_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_space where shop_id='$shop_id'");
		if($close_user_info){
			$res = db_factory::execute (" update ".TABLEPRE."witkey_shop set is_close='1' where shop_id = '$shop_id'" );
			$res = db_factory::execute (" update ".TABLEPRE."witkey_space set is_close='1' where shop_id = '$shop_id'" );
			
			 //发送信息
			$msg_arr = array();
			$msg_arr['用户名'] = $close_user_info['username'];
			$title = '您的IT帮手网账号已被冻结'; 
			$msg_obj = new keke_msg_class();
			$msg_obj->send_message ( $close_user_info['uid'], $close_user_info['username'], 'freeze', $title, $msg_arr,  $close_user_info['email']); 
			$res and kekezu::show_msg('消息提示', "index.php?do=$do&view=$view" ,3, '关闭商铺成功', 'success');
		}
	break;
	case 'open': //开启
		$res = db_factory::execute (" update ".TABLEPRE."witkey_shop set is_close='0' where shop_id = '$shop_id'" );
		$res = db_factory::execute (" update ".TABLEPRE."witkey_space set is_close='0' where shop_id = '$shop_id'" );
		kekezu::show_msg('消息提示', $_SERVER['HTTP_REFERER'] ,3, '开启商铺成功', 'success');
	break;
}

$table_obj = new keke_table_class ( "witkey_shop" );
$shop_obj = new Keke_witkey_shop_class();
$url="index.php?do=$do&view=$view&w[shop_id]=".$w['shop_id']."&w[shop_name]=".$w['shop_name']."&page=$page&w[page_size]=".$w['page_size'];

$where = '  1 = 1'; //查询
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
$page and $page = intval ( $page ) or $page = '1';
//组合查询条件
$w ['shop_id'] and $where .= " and shop_id = '".$w['shop_id']."' ";
$w ['shop_name'] and $where .= " and shop_name like '%".$w['shop_name']."%' ";
$w['username'] and $where.=" and username like '%".$w['username']."%' ";
if($start_time&&!$end_time){
	$url.="&start_time=".$start_time;
	$where.=" and on_time >= ".intval(strtotime($start_time));
}elseif(!$start_time&&$end_time){
	$url.="&end_time=".$end_time;
	$where.=" and on_time <= ".intval(strtotime($end_time));
}elseif($start_time&&$end_time){
	$url.="&start_time=$start_time&end_time=$end_time";
	if($start_time==$end_time){
		$where.=" and on_time = ".intval(strtotime($start_time));
	}else{
		$where.=" and on_time between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
	}
}elseif($op == 'listorder') {
	$shop_obj->setWhere ( "shop_id='$shop_id'" );
	$shop_obj->setListorder ( $value ? $value : 0 );
	$shop_obj->edit_keke_witkey_shop ();
	die ();
}
is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $where .= ' order by shop_id desc';

$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null);
$shop_arr = $r [data];
$pages = $r [pages];

require $template_obj->template ( 'control/admin/tpl/admin_shop_' . $view );