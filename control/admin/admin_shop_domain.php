<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-6-26 9:50
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (204);

if ($ac&&$d_id){
	$d_info = db_factory::get_one("select * from ".TABLEPRE."witkey_shop_domain where d_id = '$d_id'");
	if($d_info)
	switch($ac){
		case 'accept': //关闭
			db_factory::execute("update ".TABLEPRE."witkey_shop_domain set d_status = 1,op_time=".time()." where d_id = '$d_id'");
			db_factory::execute("update ".TABLEPRE."witkey_shop set domain ='{$d_info['d_key']}' where shop_id = '{$d_info['shop_id']}'");
			kekezu::notify_user("域名设置已生效","您申请的域名设置已生效",$d_info['uid'],$d_info['username']);
			kekezu::admin_show_msg('审核成功',"index.php?do=$do&view=$view&page=$page");
		break;
		case 'deny': //开启
			db_factory::execute("update ".TABLEPRE."witkey_shop_domain set d_status = -1,op_time=".time()." where d_id = '$d_id'");
			db_factory::execute("update ".TABLEPRE."witkey_shop set domain ='' where shop_id = '{$d_info['shop_id']}'");
			kekezu::notify_user("域名设置审核失败","您申请的域名设置审核失败，请重新设置",$d_info['uid'],$d_info['username']);
			kekezu::admin_show_msg('已拒绝审核',"index.php?do=$do&view=$view&page=$page");
		break;
		case 'delete'://删除
			db_factory::execute("delete from ".TABLEPRE."witkey_shop_domain where d_id = '$d_id'");
			db_factory::execute("update ".TABLEPRE."witkey_shop set domain ='' where shop_id = '{$d_info['shop_id']}'");
			kekezu::admin_show_msg('已删除',"index.php?do=$do&view=$view&page=$page");
		break;
	}
}
if ($ac=='config'){
	epweike_seo_class::create_domain_config();
	kekezu::admin_show_msg('配置已生成',"index.php?do=$do&view=$view&page=$page");
}

$table_obj = new keke_table_class ( "witkey_shop_domain" );

$url="index.php?do=$do&view=$view&page=$page&w[page_size]=".$w['page_size'];

$where = '  1 = 1'; //查询
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size =10;
$page and $page = intval ( $page ) or $page = '1';
//组合查询条件
$w ['shop_id'] and $where .= " and shop_id = '".$w['shop_id']."' ";
//$w ['shop_name'] and $where .= " and shop_name like '%".$w['shop_name']."%' ";
$w['username'] and $where.=" and username like '%".$w['username']."%' ";
strlen($w['d_status']) and $where.=" and d_status='{$w['d_status']}' ";
strlen($w['d_type']) and $where.=" and d_type='{$w['d_type']}' ";
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
}
is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $where .= ' order by on_time desc';



$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null);
$domain_arr = $r [data];
$pages = $r [pages];

require $template_obj->template ( 'control/admin/tpl/admin_shop_' . $view );