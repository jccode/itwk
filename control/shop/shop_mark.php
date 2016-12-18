<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$sq  = " where a.uid={$shop_info['uid']} and a.mark_type=2 ";
$mt&&$sq.=' and a.mark_status='.intval($mt); 
//var_dump($shop_info);
/**统计**/
$mark_count=kekezu::get_table_data(" count(mark_id) count,mark_status","witkey_mark"," uid={$shop_info['uid']} and mark_type=2 and mark_status>0","","mark_status ","","mark_status");
$total_mark = $mark_count[1]['count'] +$mark_count[2]['count']+$mark_count[3]['count'];


$count = intval(db_factory::get_count(' select count(a.mark_id) c from '.TABLEPRE.'witkey_mark a '.$sq,0,'c',600));

$url = $_K['siteurl']."/index.php?do=shop&sid={$shop_info['shop_id']}&view=mark";
$page=max($page,1);
$limit = max($limit,10);

$pages = $kekezu->_page_obj->getPages($count,$limit,$page,$url);

$mark_list = db_factory::query(' select a.*,b.task_title,b.task_cash,b.task_cash_coverage from '.TABLEPRE
			.'witkey_mark a left join '.TABLEPRE.'witkey_task b on a.origin_id=b.task_id '.$sq.' order by mark_time desc '.$pages['where']);	
$cash_cove_arr = kekezu::get_cash_cove();
$aids = keke_user_mark_class::get_mark_aid(2);

if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}