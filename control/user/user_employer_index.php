<?php
/**
 * @copyright keke-tech
 * @author Aaron
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
//待付款任务 - 悬赏
$nopay_count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_task where uid='$uid' and (task_status = 0 or (model_id = 4 and task_status = 4 and ifnull(cash_status,0)=0) )");
//待选稿任务
$needchoose_count = db_factory::get_count("select 'count(*)' from ".TABLEPRE."witkey_task where uid='$uid' and task_status = 3");
//待评价
$workmark_count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_mark where by_uid='$uid' and mark_status=0 and mark_type = 1");
//待确认付款 
$paywork_count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id = b.task_id where b.uid='$uid' and a.exec_time>0");

/**************处理信息查询↑↑↑↑↑↑**************/




 //商铺信息
$shop_info = $work_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where shop_id='$user_info[shop_id]'");

 //人才关注
$talent_attention = get_attention_skill($user_info['skill_ids']);
 
 //平台公告
$bulletin = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "1", "", 86400 );

 //成功雇主
$art_employer = db_factory::query(sprintf("select a.art_id,a.art_title,a.art_cat_id,a.art_pic,a.user_desc,s.username,s.uid from %switkey_article as a left outer join %switkey_space as s on a.r_uid=s.uid where a.art_cat_id=593 LIMIT 3",TABLEPRE,TABLEPRE));

 //雇主帮助
$art_help_arr = db_factory::query(" select art_id,art_title,art_cat_id,pub_time from " . TABLEPRE . "witkey_article where art_type='help' and is_delineas=0 and art_cat_id in(603,604,605) order by art_id desc limit 8",1,86400);

//交易安全
$art_safe_arr = db_factory::query(" select art_id,art_title,art_cat_id,pub_time from " . TABLEPRE . "witkey_article where art_type='help' and is_delineas=0 and art_cat_id in(617,618) order by art_id desc limit 6",1,86400);

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" .$op );

function get_attention_skill($skill_ids){
	global $indus_arr; 
	$skill_ids = explode(',', $skill_ids);
	
	$i = 1;
	$talent = array();
	foreach($skill_ids as $v){
		if($i >=6 ) break;
		$v and $talent['name'][$v] = $indus_arr[$v]['indus_name'];
		$v and $talent['list'][$v] = db_factory::query(" select sk.*,s.username from " . TABLEPRE . "witkey_member_skill sk left join 
	  	   " . TABLEPRE . "witkey_space s on sk.uid = s.uid where sk.skill_id = '$v' ORDER BY sk.on_time DESC LIMIT 6");

		$i++;
	}
	
	return $talent;
}