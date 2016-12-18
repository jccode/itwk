<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if($uid!=$shop_info['uid']){
   kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&sid=".$sid."&view=index",3,"您无权进入此页面","warning");
}
//案例分类
$shop_cate = db_factory::get_table_data('*','witkey_shop_cate',"shop_id=".intval($shop_info['shop_id']),'cate_id asc','','','cate_id',3600);

if($shop_info['shop_type']==3){
	//推荐链接
	$shop_link_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_link where obj_id = ".intval($shop_info['shop_id'])." AND obj_type = 'shop' ORDER BY link_id DESC LIMIT 6",1,3600);
	
	//资质证书
	$cert_ext_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_member_ext where uid = ".intval($shop_info['uid'])." AND type = 'cert' ORDER BY ext_id DESC LIMIT 4",1,3600);
}elseif($shop_info['shop_type']==2){
	//团队成员
	$shop_member_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_member where shop_id = ".intval($shop_info['shop_id'])." ORDER BY member_id DESC LIMIT 4",1,3600);
}

//出售服务 
$service_istop_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_service where shop_id = ".intval($shop_info['shop_id'])." AND is_top = 1 ORDER BY service_id DESC LIMIT 10",1,3600);

//案例展示 
$case_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_case where shop_id='".$shop_info['shop_id']."' and case_name is not null ORDER BY case_id desc LIMIT 10",1,3600);

//最近来访
$visit_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_visit where shop_id=".$shop_info['shop_id']." ORDER BY on_time desc LIMIT 5");

 //幻灯片
$slide_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_slide where shop_id=".$shop_info['shop_id']." ORDER BY listorder asc LIMIT 5");


//旗舰首页公司新闻
$news_list = db_factory::query("select art_id,art_title,pub_time from ".TABLEPRE."witkey_article_flagship where shop_id=".$sid." and art_type=1 order by pub_time desc limit 0,10"); 
//旗舰首页行业资讯
$zixun_list = db_factory::query("select art_id,art_title,pub_time from ".TABLEPRE."witkey_article_flagship where shop_id=".$sid." and art_type=2 order by pub_time desc limit 0,10");


if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}
