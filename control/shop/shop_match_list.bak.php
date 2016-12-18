<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-2 16:06
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

kekezu::check_login();
if($user_info['uid']!=$shop_info['uid']){
	kekezu::show_msg("操作提示","index.php?do=shop&view=index&sid=".$shop_info['shop_id'],2,"您没有权限查看该页面。","warning");
}
$ops = array(1,2,3);
!in_array($op,$ops) || !$op and $op = 1;
$page and $page=intval($page) or $page=1;
$page_size = 10;
$url=$_K['siteurl']."/index.php?do=shop&view=match_list&sid=".$shop_info['shop_id']."&page=".$page."&op=".$op;

$cash_cove_arr = kekezu::get_cash_cove();
$area = explode(',',$shop_info['residency']);
$area = $area[0];
//同城速配短信时间
$match_info = db_factory::get_one(sprintf("select * from %switkey_shop_match where uid=%d order by m_id desc",TABLEPRE,$shop_info['uid']));

if(!$user_info['skill_ids']){
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=user&view=space&op=skill",2,"请先设置您的同城速配标签。","warning");
}
$task_obj = keke_table_class::get_instance ( "witkey_task" );
$page_obj = $kekezu->_page_obj;
$status_arr = tender_task_class::get_task_status();
if(isset($op) && $shop_info['city_match']==1){
	$where = " where model_id=4 and task_type!=3 and indus_id in (".$shop_info['skill_ids'].") and FIND_IN_SET('".$area."',city)>0 ";
	$w     = ' and a.uid != '.$shop_info['uid'].' and a.task_status = 2 and sub_time<'.time();
	
	//=---------------------统计------------------=//
	//未报价
	$w_csql = " select count(a.task_id) c from ".TABLEPRE."witkey_task a ".$where
				.$w.' and a.task_id not in (select CONCAT(c.task_id) from '
				.TABLEPRE.'witkey_task_work c left join '.TABLEPRE
				.'witkey_task d on c.task_id=d.task_id '.$where.' and d.task_status=2
				  and c.uid='.$shop_info['uid'].')';
	$w_c    = intval(db_factory::get_count($w_csql,0,'c',600));
	//已报价
	$y_csql = " select count(a.task_id) c from ".TABLEPRE."witkey_task a left join ".TABLEPRE
				."witkey_task_work b on a.task_id=b.task_id ".$where
				." and b.uid=".$shop_info['uid'].' and a.uid != '.$shop_info['uid'];
	$y_c    = intval(db_factory::get_count($y_csql,0,'c',600));
	//已中标
	$b_csql = sprintf("select count(a.task_id) c from %switkey_task a",TABLEPRE).$where
			.' and a.w_uid='.$shop_info['uid'];
	$b_c    = intval(db_factory::get_count($b_csql,0,'c',600));
	switch ($op){
		case 1://未报价
			$sql = " select a.* from ".TABLEPRE."witkey_task a ".$where
				.$w.' and a.task_id not in 
				 (select CONCAT(c.task_id) from '.TABLEPRE.'witkey_task_work c left join '
				.TABLEPRE.'witkey_task d on c.task_id=d.task_id '.$where.' and d.task_status=2
				  and c.uid='.$shop_info['uid'].')';
			$count   = $w_c;
			break;
		case 2://已报价
			$sql=" select a.* from ".TABLEPRE."witkey_task a left join ".TABLEPRE
				."witkey_task_work b on a.task_id=b.task_id ".$where
				." and b.uid=".$shop_info['uid'].' and a.uid != '.$shop_info['uid'];
			$count   = $y_c;
			break;
		case 3://中标
			$sql = sprintf("select a.* from %switkey_task a",TABLEPRE).$where.' and a.w_uid='.$shop_info['uid'];
			$count   = $b_c;
			break;
	}
			$sql .= " order by task_id desc";
	if($sql&&$where){
		$pages = $page_obj->getPages($count, $page_size, $page, $url);
		$task_arr = db_factory::query($sql.$pages['where']);
	}
}

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );