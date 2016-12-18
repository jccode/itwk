<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//SEO
$page_title = '直接雇佣 威客任务平台 1对1服务，满意付款，赏金任务悬赏金100%_IT帮手网';
$page_keyword = '直接雇佣，威客任务平台，威客平台，威客任务';
$page_description ='IT帮手网直接雇佣模式，威客任务平台首创独有模式。威客雇主1对1服务，第三方托管赏金，满意付款，赏金100%归威客所有，真正让利于雇主与威客，诚心为雇主威客们考虑的专业的威客服务平台。';

$success_info = kekezu::get_table_data('task_id,model_id,task_status,task_title,uid,username,task_cash,task_type,w_uid,w_username,w_bid_time','witkey_task','model_id=4 and task_type!=2 and task_status in (4,5,7)','task_id desc','','0,10','task_id',3600);
$wait_info = kekezu::get_table_data('task_id,model_id,task_status,task_title,uid,username,task_cash,sub_time,task_type,w_uid,w_username','witkey_task','model_id=4 and task_type=1 and task_cash_coverage <1 and task_status=2','task_id desc','','0,10','task_id',3600);
$recommend_info = kekezu::get_table_data('task_id,model_id,task_status,task_title,uid,username,task_cash,task_type,w_uid,w_username,w_bid_time','witkey_task','model_id=4 and (task_type=3 or ( task_type = 1 and task_cash_coverage<1) ) and task_status=4 and task_cash>2000','task_id desc','','0,10','task_id',3600);
//优秀VIP推荐
$rec_shop = kekezu::get_table_data('shop_id,uid,username,shop_name','witkey_shop','isvip=1 and istop=1','listorder asc','','0,8','shop_id',3600);
//最新VIP商铺
$new_shop = kekezu::get_table_data('uid,username,shop_id,shop_name','witkey_space','isvip=1','vip_start_time desc','','0,8','uid',3600);


//雇佣交易数：9997 个
$hire_count = db_factory::get_one(" select count(task_id) as task_count ,sum(task_cash) as task_cash from ".TABLEPRE."witkey_task where model_id = 4 and task_cash_coverage<1   ",1,3600);

//疑难解答
$help_answer = kekezu::get_table_data ( "art_id,art_cat_id,art_title,pub_time", "witkey_article", " art_type = 'help'  and is_recommend =1 ", " pub_time desc", "", "5", "", 86400 );

//提现公告
$with_draw = kekezu::get_table_data( "uid,username,withdraw_cash,applic_time", "witkey_withdraw", "withdraw_status =2 ", " applic_time desc", "", "5", "", 86400 );

//赚钱故事会
$story_arr = db_factory::query("select a.art_title,a.r_uid,a.content,a.art_cat_id,art_pic,a.art_id,b.uid,b.shop_id,b.isvip from ".TABLEPRE."witkey_article a left join ".TABLEPRE."witkey_space b on a.r_uid = b.uid where art_cat_id = 598 order by a.art_id desc limit 10",1,86400);

//周雇佣冠军
$week_kemp  = db_factory::query("
select sum(fina_cash) as fina_cash,fina_type,fina_action,fina_time ,a.uid ,b.username,w_level from keke_witkey_finance a left join keke_witkey_space b on a.uid=b.uid
  where yearweek(from_unixtime(fina_time),1)=yearweek(from_unixtime(".time()."),1) and fina_type ='in' and fina_action = 'task_bid'
	group by a.uid  order by fina_cash desc limit 0,3;",1,3600);
//友情链接
$link_tag = keke_core_class::link_make_tag( array(12=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

require keke_tpl_class::template ( $do );