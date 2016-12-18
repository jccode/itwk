<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_obj = $kekezu->_page_obj;
$action or $action = 'basic';
/**三级菜单**/
$third_nav=array("basic"=>array($_lang['finance_detail'],$_lang['finance_record_stats']),
				"charge"=>array($_lang['recharge_record'],$_lang['recharge_record_stats']),
				"withdraw"=>array($_lang['withdraw_record'],$_lang['withdraw_record_stats']));

$where = " uid = '$uid' ";

$fina_date_arr = array(
		'three_day'=>array('最近三天','DATE_SUB(CURDATE(), INTERVAL 3 DAY) <='),
		'one_week'=>array('最近一周','DATE_SUB(CURDATE(), INTERVAL 7 DAY) <='),
		'one_month'=>array('最近一个月','DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <='),
		'three_month'=>array('最近三个月','DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <='),
		'half_year'=>array('最近半年','DATE_SUB(CURDATE(), INTERVAL 6 MONTH) <='),
		'one_year'=>array('最近一年','DATE_SUB(CURDATE(), INTERVAL 1 YEAR) <='),
		'three_year'=>array('最近三年','DATE_SUB(CURDATE(), INTERVAL 3 YEAR) <='),
		'three_year_ago'=>array('三年以前','DATE_SUB(CURDATE(), INTERVAL 3 DAY) >')
		);

intval ( $page_size ) or $page_size = '10';
intval ( $page ) or $page = '1';
$url = $origin_url . "&op=$op&action=$action&page_size=$page_size&page=$page";

switch ($action) {
	case "basic" :		
		$fina_obj = new Keke_witkey_finance_class ();
		$action_arr = keke_glob_class::get_finance_action (); //财务用途
	
		//$where .="  and fina_action not in ('withdraw','offline_recharge','online_charge')";
        
		$fina_type and $where .= " and fina_type = '$fina_type' ";
		$fina_date and $where .=" and  ".$fina_date_arr[$fina_date][1]." date(from_unixtime(fina_time)) ";
		
		
		$ord and $where .= " order by $ord " or $where .= " order by fina_id desc ";

		/**搜索条件 end**/
		$fina_obj->setWhere ( $where );
		$count = intval ( $fina_obj->count_keke_witkey_finance () );
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
		$fina_obj->setWhere ( $where . $pages ['where'] );
	
		$fina_arr = $fina_obj->query_keke_witkey_finance ();  //var_dump($fina_arr);
		break;
	case "charge" :
		$charge_obj=new Keke_witkey_order_charge_class();//充值记录表
		$order_type_arr=keke_glob_class::get_charge_type();/*充值订单类型*/
		$bank_arr=keke_glob_class::get_bank();
		$status_arr = keke_order_class::get_order_status();
	
		/**搜索条件 start**/
		$charge_type and $where.=" charge_type =   '$charge_type' ";
		$fina_date and $where .=" and  ".$fina_date_arr[$fina_date][1]." date(from_unixtime(pay_time)) ";
		$ord and $where .= " order by $ord " or $where .= " order by order_id desc ";
		/**搜索条件 end**/
		$charge_obj->setWhere ( $where );
		$count = intval ( $charge_obj->count_keke_witkey_order_charge());
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
		$charge_obj->setWhere ( $where . $pages ['where'] ); 
		$charge_arr=$charge_obj->query_keke_witkey_order_charge();
		break;
	case "withdraw" :
		$status_arr  = keke_glob_class::withdraw_status();
		$withdraw_obj=new Keke_witkey_withdraw_class();//提现记录表
		
		$ord and $where .= " order by $ord " or $where .= " order by withdraw_id desc ";
		/**搜索条件 end**/
		$withdraw_obj->setWhere ( $where );
		$count = intval ( $withdraw_obj->count_keke_witkey_withdraw());
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
		$withdraw_obj->setWhere ( $where . $pages ['where'] );
		$withdraw_arr=$withdraw_obj->query_keke_witkey_withdraw();
		$bank_arr = keke_glob_class::get_bank();
		break;
}

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );


