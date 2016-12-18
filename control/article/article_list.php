<?php
/**
 * 文章模块--文章首页
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-12-5-上午10:37:07
 * @version V2.0
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$ncat_ids = array('587','597','589','590','591');
$ncat_names =  array('587'=>'最新动态','597'=>'平台公告','589'=>'媒体报道','590'=>'频道热门','591'=>'互联网资讯');
// in_array($art_cat_id,$ncat_ids) or $art_cat_id = '587';
$page = intval ( $page );
$page or $page = 1;
$page_size = intval ( $page_size );
$year = intval($year);
$page_size or $page_size = 20;
$url=$_K['siteurl']."/index.php?do=$do&art_cat_id=$art_cat_id&page=$page&pagesize=$pagesize&keyword=$keyword";

$page_obj = $kekezu->_page_obj;
$sql = sprintf("select art_id,art_cat_id,art_title,pub_time from %switkey_article",TABLEPRE);
$where = " where art_type='art' and is_show=1";

$art_cat_id and $where .= " and art_cat_id='$art_cat_id' ";
	
$keyword and $where .= " and art_title like '%".$keyword."%'";

$count = db_factory::get_count(sprintf("select count(art_id) from %switkey_article%s",TABLEPRE,$where));
$where .= " order by art_id desc";
$pages = $page_obj->getPages($count, $page_size, $page, $url);
$art_info = db_factory::query($sql.$where.$pages['where']);

//读取推荐和热门内容
$recommend_info = kekezu::get_table_data("art_id,art_cat_id,art_title,pub_time,art_pic","witkey_article","art_type='art' and is_show=1 and is_recommend=1 and art_pic !=''","art_id desc","","0,4","art_id",3600);
$hot_info = kekezu::get_table_data("art_id,art_cat_id,art_title,pub_time","witkey_article","art_type='art' and is_show=1","views desc","","0,10","art_id",3600);

//友情链接
$link_tag = keke_core_class::link_make_tag( array(5=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

 //SEO
switch($art_cat_id){
	case '597': //平台公告
		$page_title = '平台公告 公告栏 IT帮手网公告栏了解IT帮手网最新动向_IT帮手网';
		$page_keyword = '平台公告，公告栏，IT帮手网公告栏';
		$page_description ='IT帮手网平台公告，实时公布网站最新公告、通知与网站政策变化，是面向IT帮手网广大威客雇主用户的公告栏。IT帮手网公告栏，雇主威客第一时间了解IT帮手网最新动向的地方。';
	break;
	case '589': //媒体报道
		$page_title = '媒体报道 新闻媒体与网络媒体对IT帮手网的深度报道_IT帮手网';
		$page_keyword = '媒体报道，新闻媒体，网络媒体，深度报道';
		$page_description ='新闻媒体、网络媒体等众多实力媒体对IT帮手网的深入报道，用户用媒体的视角看IT帮手网的优劣，雇主与威客用媒体的声音说出自己对IT帮手网的心声。';
	break;
	case '587': //最新动态
		$page_title = '新闻公告 最新新闻热点 近期新闻等威客资讯热点_IT帮手网';
		$page_keyword = '新闻公告,最新新闻热点,近期新闻,威客资讯热点';
		$page_description ='IT帮手网新闻公告频道，盘点IT帮手网近期新闻、最新新闻热点，及时掌握威客资讯热点、把握威客行业动向，实时掌控IT帮手网最新动态，让你更加了解IT帮手网。';
	break;
	case '590': //热门频道
		$page_title = '频道热门 IT帮手网的互联网新闻中心，游戏资讯、互联网新闻、资讯快报一网打尽_IT帮手网';
		$page_keyword = '频道热门，互联网新闻中心，游戏资讯，互联网新闻，资讯快报';
		$page_description ='频道热门，IT帮手网的互联网新闻中心，掌握最新的互联网动态，收听最前沿、最劲爆的互联网新闻，寻找最有趣的游戏资讯，IT帮手网频道热门，最快最全的互联网新闻中心，资讯快报一网打尽。';
	break;
	case '591': //互联网资讯
		$page_title = '互联网资讯 互联网新闻里报道互联网公司在互联网那些事儿_IT帮手网';
		$page_keyword = '互联网资讯，互联网新闻，互联网公司，互联网那些事儿';
		$page_description ='互联网资讯频道，来自互联网络、互联网媒体的报道，通过IT帮手网互联网资讯频道这个窗口，了解中国互联网状况，掌握互联网资讯，看看互联网新闻里报道互联网公司在互联网那些事儿。';
	break;
}

require keke_tpl_class::template ( SKIN_PATH . "/article/article_list" );