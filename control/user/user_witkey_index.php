<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-06-25 9:03
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

switch ($opp){
	case 'ajax_content':  //建议栏留言
    	$comm_obj = new Keke_witkey_comment_class();
    	$comm_obj->setObj_type('suggest');
    	$comm_obj->setOrigin_id(0);
    	$comm_obj->setUid($uid);
    	$comm_obj->setUsername($username);
    	$comm_obj->setContent($shop_comment_tent);
    	$comm_obj->setStatus(0);
    	$comm_obj->setOn_time(time());
    	$res = $comm_obj->create_keke_witkey_comment();
    	$res and kekezu::echojson ( $_lang['add_success'], "1" ) or kekezu::echojson ( $_lang['add_fail'], "0" );
    	die();
		break;
	case 'ajax_homepage':
		$res=keke_user_class::set_homepage($uid,$tag);
		$res and kekezu::echojson ( $res, "1" ) or kekezu::echojson ( $_lang['add_fail'], "0" );
		die();
		break;
}


/**************处理信息查询↓↓↓↓↓↓**************/
//待上传作品
$needupload_count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_task a right join ".TABLEPRE."witkey_task_work b on a.task_id=b.task_id where b.uid='$uid' and b.work_status=11 and a.task_status = 4 and a.cash_status=1 and a.model_id = 4");

//待传评价
$workmark_count = db_factory::get_count("select 'count(*)' from ".TABLEPRE."witkey_mark where by_uid='$uid' and mark_status=0 and mark_type = 2");

//待雇主确认付款
$paywork_count = db_factory::get_count("select 'count(*)' from ".TABLEPRE."witkey_task_work where uid='$uid' and exec_time>0");
/**************处理信息查询↑↑↑↑↑↑**************/


 //商铺信息
$shop_info = $work_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where shop_id='$user_info[shop_id]'");

 //劳务服务
$service_count_1 = db_factory::get_count ( " select 'count(*)' from " . TABLEPRE . "witkey_service where uid='$uid' AND service_type = 1" );
$service_count_1 or $service_count_1 = 0;

 //创意服务
$service_count_2 = db_factory::get_count ( " select 'count(*)' from " . TABLEPRE . "witkey_service where uid='$uid' AND service_type = 1" );
$service_count_2 or $service_count = 0;

 //出售中的案例总数
$case_count = db_factory::get_count ( " select 'count(*)' from " . TABLEPRE . "witkey_shop_case where shop_id='$shop_info[shop_id]'" );
$case_count or $case_count = 0;

 //成功威客
$art_witkey = db_factory::query("select a.art_id,a.art_cat_id,a.art_title,a.art_pic,a.user_desc,s.username,s.uid from ".TABLEPRE."witkey_article as a left outer join ".TABLEPRE."witkey_space as s on a.r_uid=s.uid where a.art_cat_id=594 LIMIT 3",1,86400);

 //平台公告
$bulletin = kekezu::get_table_data ( "art_id,art_cat_id,art_title,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "1", "", 86400 );

 //任务关注
$attention_task_arr = get_attention_task( $user_info[uid] );

//交易安全
$art_safe_arr = db_factory::query(" select art_id,art_cat_id,art_title,pub_time from " . TABLEPRE . "witkey_article where art_type='help' and is_delineas=0 and art_cat_id in(617,618) order by art_id desc limit 6",1,86400);

 //威客帮助
$art_help_arr = db_factory::query(" select art_id,art_cat_id,art_title,pub_time from " . TABLEPRE . "witkey_article where art_type='help' and is_delineas=0 and art_cat_id in(606,607,608,609,610,611) order by art_id desc limit 8",1,86400);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" .$op );
 
function get_attention_task($uid){	 
	global $indus_p_arr,$indus_arr; 
	$task_attention_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_task_attention where uid='$uid'");
	if( $task_attention_info['indus_id'] ){
		$industry_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_industry where indus_id IN(".$task_attention_info[indus_id].")");
	}

	if( $industry_arr ){
		$re_arr = array();
		foreach($industry_arr as $v){
			$re_arr['name'][$v['indus_id']] = $indus_arr[$v['indus_id']]['indus_name'];
			$re_arr['list'][$v['indus_id']] = db_factory::query(" select * from " . TABLEPRE . "witkey_task where indus_id =".$v[indus_id]." order by task_id desc limit 6", 1, 7200);
		}

		return $re_arr;
	}
}