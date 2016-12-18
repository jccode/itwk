<?php
/**
 * @copyright keke-tech
 * @author ch
 * @version v 2.0
 * 2012-05-28下午03:37:00
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$table_obj = new Keke_witkey_invoice_class();

$opps = array('apply','accept','refuse');
in_array($opp,$opps) or $opp='apply';
intval ( $page_size ) or $page_size = '10';
intval ( $page ) or $page = '1';
$url = $origin_url . "&op=$op&opp=$opp&page_size=$page_size&page=$page&task_title=$task_title&start_time=$start_time&end_time=$end_time";

/*三级横向菜单*/
$third_nav = array(
		array("1"=>"apply" , "2"=>$_lang['apply']),
		array("1"=>"accept" , "2"=>$_lang['accept']),
		array("1"=>"refuse" , "2"=>$_lang['refuse'])
	);
$iv_status = keke_glob_class::get_iv_status();

if($ac=='cancel'){
	if($iv_id){
		$table_obj->setWhere('iv_id='.intval($iv_id));
		$table_obj->setIv_status('-1');
		$table_obj->edit_keke_witkey_invoice();
		/**
		 * 放弃申请了啊。给我将单人、多人扣的税金还回来吧
		 */
		$iv = db_factory::get_one(' select a.task_id,a.model_id,b.task_cash,b.task_status from '
				.TABLEPRE.'witkey_invoice a left join '.TABLEPRE.'witkey_task b on a.task_id=b.task_id
				 where a.iv_id='.$iv_id);
		if($iv['task_status']<8&&($iv['model_id']==1||$iv['model_id']==2)){
			db_factory::execute(' update '.TABLEPRE.'witkey_task set real_cash = '.$iv['task_cash'].' where task_id='.$iv['task_id']);
		}
		
		kekezu::show_msg ( "操作提示", $url . "&opp=$opp", '1',$_lang['iv_cancel_success'], 'alert_right' ) ;
	}
}
if($opp){
	$invoice_obj = new Keke_witkey_vote_class();
	$page_obj = $kekezu->_page_obj;
	
	$sql=sprintf("select iv_id,task_id,task_title,iv_price,iv_fee,iv_status,iv_datetime from %switkey_invoice where ", TABLEPRE);
	/*条件*/
	$where = "uid=$uid";
	$opp == "accept" and $where .= " and iv_status=1";
	$opp == "refuse" and $where .= " and iv_status=2";
	if(isset($task_title)){
		$where.=" and task_title like '%".$task_title."%'";
	}
	if($start_time&&!$end_time){
		$where.=" and iv_datetime >= ".intval(strtotime($start_time));
	}elseif(!$start_time&&$end_time){
		$where.=" and iv_datetime <= ".intval(strtotime($end_time));
	}elseif($start_time&&$end_time){
		if($start_time==$end_time){
			$where.=" and iv_datetime = ".intval(strtotime($start_time));
		}else{
			$where.=" and iv_datetime between ".intval(strtotime($start_time))." and ".intval(strtotime($end_time));
		}
	}
	
	$count = db_factory::get_count ( sprintf ( "select count(iv_id) from %switkey_invoice where %s", TABLEPRE, $where ) );
	/*排序*/
	$where .= " order by iv_datetime desc";
	
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
	$invoice_info = db_factory::query($sql . $where . $pages['where']);
}

require keke_tpl_class::template("user/".$do."_".$view."_".$op);