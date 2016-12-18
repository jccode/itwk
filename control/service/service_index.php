<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 14:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


 //TA出售了服务
$sale_service_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_service where is_top = 1 GROUP BY uid ORDER BY service_id DESC LIMIT 12",1,86400);
foreach ($sale_service_arr as $k=>$v){
    if($sale_service_arr[$k]['pic']){
	$service_pic[$k]=explode('/', $sale_service_arr[$k]['pic']);
	$sale_service_arr[$k]['pict']=$service_pic[$k][0]."/".$service_pic[$k][1]."/".$service_pic[$k][2]."/".$service_pic[$k][3]."/".$service_pic[$k][4]."/s_".$service_pic[$k][5];
    $url=$_K['siteurl'].'/'.$sale_service_arr[$k]['pict'];
    if( @fopen( $url, 'r' ) ) {   	
    	$sale_service_arr[$k]['pic']=$sale_service_arr[$k]['pict'];
    }
    }
}
 //TA发布了需求
$demand_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_task where is_top = 1 and model_id = 4 and task_type =2 ORDER BY task_id DESC LIMIT 12",1,86400);
 
 //友情链接
$link_tag = keke_core_class::link_make_tag( array(9=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

 //服务单位
$price_unit_arr = keke_glob_class::get_price_unit();

//疑难解答(常见问题)
$help_answer = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_type = 'help'  and is_recommend =1 ", " pub_time desc", "", "3", "", 86400 );


/**
 *平台公告
 */
$bulletin = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "3", "", 86400 );

//SEO
$page_title = '劳务大厅  为人民服务提供生活旅游互联网等一条龙服务_IT帮手网';
$page_keyword = '劳务大厅，服务悬赏，为人民服务，一条龙服务';
$page_description ='IT帮手网劳务大厅，专为人民服务的频道，包括招聘找人、生活服务、商务服务、生活服务、旅游服务等，你都可以在一品劳务大厅频道发布需求或供给信息。一品劳务大厅频道，为人民服务提供生活旅游互联网等一条龙服务。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );