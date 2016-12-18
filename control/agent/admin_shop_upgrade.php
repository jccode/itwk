<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-6-26 9:50
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 192 );

switch($ac){
	case 'pass': //通过
		//$upgrade_arr = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop_upgrade where up_id = '$up_id'");
		$obj_tab = new Keke_witkey_shop_upgrade_class();
		$obj_tab->setWhere("up_id = '$up_id'");
		$obj_tab->setUp_status(1);
		$res = $obj_tab->edit_keke_witkey_shop_upgrade();
		
		if($res){ //修改成为公司商铺
			 //修改商铺表
			$obj_shop = new Keke_witkey_shop_class();
			$obj_shop->setWhere("uid = '$up_uid'");
			$obj_shop->setShop_type(3);
			$obj_shop->edit_keke_witkey_shop();
			 
			 //修改用户表
			$obj_space = new Keke_witkey_space_class();
			$obj_space->setWhere("uid = '$up_uid'");
			$obj_space->setUser_type(3);
			$obj_space->edit_keke_witkey_space();
		}

		kekezu::show_msg('消息提示', $_SERVER['HTTP_REFERER'] ,3, '审核通过', 'success');
	break;
	case 'nopass': //未通过
		$upgrade_arr = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop_upgrade where up_id = '$up_id'");
		$upgrade_arr['cancel_shop_type'] or $upgrade_arr['cancel_shop_type'] = 1;
		$obj_tab = new Keke_witkey_shop_upgrade_class();
		$obj_tab->setWhere("up_id = '$up_id'");	
		$res = $obj_tab->del_keke_witkey_shop_upgrade();		
		if($res){ //修改为公司商铺			
			$obj_shop = new Keke_witkey_shop_class();
			$obj_shop->setWhere("uid = '$up_uid'");
			$obj_shop->setShop_type($upgrade_arr['cancel_shop_type']);
			$obj_shop->setShop_info($upgrade_arr['cancel_shop_info']);
			$obj_shop->edit_keke_witkey_shop();
			
			 //修改用户表
			$obj_space = new Keke_witkey_space_class();
			$obj_space->setWhere("uid = '$up_uid'");
			$obj_space->setUser_type($upgrade_arr['cancel_shop_type']);
			$obj_space->edit_keke_witkey_space();
		}
		
		kekezu::show_msg('消息提示', $_SERVER['HTTP_REFERER'] ,3, '审核未通过', 'success');
	break;
}


$w [page_size] and $page_size = intval($w [page_size]) or $page_size = 10;
$page and $page = intval($page) or $page = 1;

 //组合查询条件
$where = " where 1 =1 ";
$w ['shop_id'] and $where .= " and a.shop_id = '".$w['shop_id']."' ";
$w ['shop_name'] and $where .= " and b.shop_name like '%".$w['shop_name']."%' ";
$w['username'] and $where .= " and a.username like '%".$w['username']."%' ";
$start_time and $where .= " and a.on_time >= '".strtotime($start_time)."' ";  
$end_time and $where .= " and a.on_time <= '".strtotime($end_time)."' ";

$page and $page = intval($page) or $page = 1;
$url="index.php?do=$do&view=$view&w[shop_id]=".$w['shop_id']."&w[shop_name]=".$w['shop_name']."&w[username]=$w[username]&start_time=$start_time
	&end_time=$end_time&page=$page&w[page_size]=".$w['page_size'];


$sql_count = "select count(*) from ".TABLEPRE."witkey_shop_upgrade as a left join ".TABLEPRE."witkey_shop as b on a.shop_id = b.shop_id ";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

is_array($w['ord']) and $where .= ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $where .= ' order by a.up_id desc';
$sql = "select a.*,b.*,a.shop_id from ".TABLEPRE."witkey_shop_upgrade as a left join ".TABLEPRE."witkey_shop as b on a.shop_id = b.shop_id ";
$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
$sql .= $where.$pages['where'];
$shop_arr = db_factory::query($sql);	

require $template_obj->template ( 'control/agent/tpl/admin_shop_' . $view );