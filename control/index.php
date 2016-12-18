<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-18 19:33:00
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = "index";

$cash_cove_arr = kekezu::get_cash_cove();

//SEO
$page_title = '威客-创意,IT帮手网,中国专业威客网站';
$page_keyword = '威客，威客网，免费威客网，中国威客网，威客任务，威客网络兼职，任务中国，IT帮手网';
$page_description ='IT帮手网是中国首家全免费威客平台，是威客和雇主最信赖的威客网站，中国最有价值的创意交易平台,提供LOGO设计、包装设计、平面设计、微博营销、网店推广、论坛推广、网站建设、装修设计、宝宝起名、广告语征集等创意交易类威客任务，是继猪八戒威客网之后领先新型威客网站！';

//网站指数
/**
 * 最新任务
 * 最新商品
 */
 $task_new1 = db_factory::query ( sprintf ( "select task_id,task_title,task_cash,view_num,work_num,task_cash_coverage from %switkey_task  where  task_status='2' order by start_time desc limit 0,30", TABLEPRE ), true, 86400 );
 $task_new2 = db_factory::query ( sprintf ( "select task_id,task_title,task_cash,view_num,work_num,task_cash_coverage from %switkey_task  where task_status='2' and ((task_type=1 and task_cash_coverage<1) or task_type=3) order by start_time desc limit 0,30", TABLEPRE ), true, 86400 );
 $task_new3 = db_factory::query ( sprintf ( "select task_id,task_title,task_cash,view_num,work_num,task_cash_coverage from %switkey_task  where  task_status='2' and model_id between 1 and 3 order by start_time desc limit 0,30", TABLEPRE ), true, 86400 );
 $task_new4 = db_factory::query ( sprintf ( "select task_id,task_title,task_cash,view_num,work_num,task_cash_coverage from %switkey_task  where  task_status='2' and is_recommend='1' order by start_time desc limit 0,30", TABLEPRE ), true, 86400 );
 
if (isset ( $ajax )) {
	if($ajax=='shop'){
		$shop_new = db_factory::query ( sprintf ( "select shop_id,uid,shop_name,isvip from %switkey_shop where is_close!=1 and isvip=1  order by on_time desc limit 0,44", TABLEPRE ), 1, 86400 );
	}elseif($ajax=='integrity_shop'){
		$shop_new = db_factory::query ( "select a.shop_id,a.uid,a.shop_name,a.isvip from " . TABLEPRE . "witkey_shop a, " . TABLEPRE . "witkey_space b where a.uid = b.uid and a.is_close!=1 and b.Integrity=1  order by on_time desc limit 0,44", 1, 86400 );
	}elseif($ajax=='integrity_member'){
		$shop_new = db_factory::query ( "select a.shop_id,a.uid,a.shop_name,a.isvip from " . TABLEPRE . "witkey_shop a, " . TABLEPRE . "witkey_space b where a.uid = b.uid and a.is_close!=1 and b.Integrity=1  order by on_time desc limit 0,44", 1, 86400 );
	}	
	require keke_tpl_class::template ( "ajax/index" );
	die ();
}

//作弊时间天数
$day_count =  (time()-strtotime('2011-04-01 00:00:00'))/24/3600;
$day_count = intVal($day_count);
/**
 * 交易额
 */
$task_add_cash = 3488000+40000+$day_count*200000;
$task_cash = intval(db_factory::get_count("select sum(task_cash) from ".TABLEPRE."witkey_task ",0,null,86400));
$task_cash += $task_add_cash;
$task_cash = number_format($task_cash);
/**
 * 交易数量
 */
$task_add_num = 5000+120+$day_count*50;
$task_num = intval(db_factory::get_count("select count(task_id) from ".TABLEPRE."witkey_task ",0,null,86400));
$task_num+= $task_add_num;
$task_num = number_format($task_num);

/**
 * 人才
 */

$user_add_num = 433620+15000+$day_count*3000+180000+780000;
$user_num = intval(db_factory::get_count("select count(uid) from ".TABLEPRE."witkey_member",0,null,86400));
$user_num+= $user_add_num;
$user_num = number_format($user_num);
/**
*平台公告
 */
$bulletin = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 媒体报道
 */
$media = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=589 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 中标公告
 */
$bid = db_factory::query("select a.task_id,a.uid,a.username,a.work_status,a.op_time,b.model_id from ".TABLEPRE
				."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id=b.task_id "
				." where a.work_status between 1 and 12 order by  a.op_time desc limit 0,4",1,86400 );

/**
 * 重金任务 2012-7-27 产品要求限制金额修改为1000元 ch
 */
/*
$heavily = kekezu::get_table_data("task_id,task_title,task_cash,model_id","witkey_task"," (model_id=1 or model_id = 2) and task_cash >=500 and task_status=2  ","start_time desc ","","10","",86400);
if(count($heavily)<3){
	$heavily = kekezu::get_table_data("task_id,task_title,task_cash,model_id","witkey_task"," (model_id=1 or model_id = 2 or (model_id=4 and task_type=1 and ifnull(task_cash_coverage,0)=0)) and task_cash >=1000 and task_status=2  ","model_id asc,start_time desc ","","3","",86400);
    } */
$heavily = kekezu::get_table_data("task_id,task_title,task_cash,model_id,task_cash_coverage","witkey_task"," (model_id=4 and task_type=1) and (task_cash >= 2000 or ifnull(task_cash_coverage,0) >= 3) and task_status=2  ","start_time desc ","","10","",86400);
if(count($heavily)<3){
	$heavily = kekezu::get_table_data("task_id,task_title,task_cash,model_id,task_cash_coverage","witkey_task"," (model_id=1 or model_id = 2 or model_id = 3 or (model_id=4 and task_type=1)) and (task_cash >=1000 or ifnull(task_cash_coverage,0) >= 2) and task_status=2  ","model_id asc,start_time desc ","","3","",86400);
}
/* print_r($heavily); */


/**
 * 推荐案例
 */
$case = kekezu::get_table_data("art_id,art_title,art_cat_id,task_id,art_pic,is_recommend,pub_time", "witkey_article", " art_type='case' and art_pic !='' and is_recommend=1  ", " pub_time desc", "", "10", "", 86400 );

/**
 * 推荐任务
 */
$task_recomm = db_factory::query ( sprintf ( " select task_id,uid,username,task_title,task_cash,model_id,view_num,work_num,task_cash_coverage from %switkey_task where ((model_id in (1,2,3) ) or (model_id=4 and  task_type!=3 ) ) and task_status='2' and task_cash>1000 order by start_time desc limit 0,30", TABLEPRE ), 1, 86400 );

/**
 * 推荐店铺
 */
$shop_recomm = db_factory::query ( sprintf ( "select shop_id,uid,shop_name,shop_level,isvip from %switkey_shop where   is_close!=1 and isvip = 1   order by istop desc,shop_level desc,listorder asc limit 0,44", TABLEPRE ), 1, 86400 );

/**
 *	首页友情链接
 */
$link_tag = keke_core_class::link_make_tag( array(1=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag	) order by listorder limit 0,50", TABLEPRE ), 1, 86400 );

require keke_tpl_class::template ( $do );