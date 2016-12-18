<?php
/**
 * 品牌馆-台湾馆@copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-31 10:44
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$page_title = '品牌馆-台湾馆-IT帮手网';
$page_keyword = '品牌馆-台湾馆-IT帮手网';
$page_description ='品牌馆-台湾馆-IT帮手网';
//店铺推荐16个，位于搜索下方
$tw_list = db_factory::query('select shop.uid,shop.shop_id,shop.logo from '.TABLEPRE.'witkey_brand as brand,'.TABLEPRE.'witkey_shop as shop where brand.uid=shop.uid and brand.brand="tw" and shop.istop = 1 and shop.isvip=1 order by shop.listorder asc,shop.shop_id desc  limit 0,16');
$unit_price = keke_glob_class::get_price_unit();
$tw_list[0] and $ser_info = db_factory::get_one(' select * from '.TABLEPRE.'witkey_service where shop_id='.$tw_list[0]['shop_id'].' order by views desc,price desc limit 0,1');
//人气版top10
$top_10 = db_factory::query(' select a.views,a.shop_name,a.shop_id from '.TABLEPRE.'witkey_brand b '
				.' left join '.TABLEPRE."witkey_shop a on a.shop_id=b.shop_id where b.app_status=1 and b.brand='tw' and a.is_close=0 and a.isvip=1 order by a.views desc limit 0,10");
//出售服务,会员为台湾vip才显示
$field='service.shop_id,service.pic,service.service_id,service.title,service.price,service.unite_price,service.service_type';
$table=TABLEPRE.'witkey_service service, '.TABLEPRE.'witkey_space space';
$where='service.uid=space.uid and space.isvip=1 and space.brand="tw" and service.is_top=1 and service.is_stop=0';
$order='service.service_id desc ';
$limit='0,10';
$sql='select '.$field.' from '.$table.' where '.$where.' order by '.$order.' limit '.$limit;
$service_list = db_factory::query($sql);
//精彩案例作品赏析，在brand.php中

//设计品牌最新上传案例展示
$sql='select scase.case_id,scase.shop_id,scase.case_name,scase.case_pic,space.username from '.TABLEPRE.'witkey_shop_case as scase,'.TABLEPRE.'witkey_space as space where space.isvip=1  and scase.shop_id=space.shop_id and space.brand='."'tw'".' order by scase.on_time desc limit 0,10';
$new_case = db_factory::query($sql );
//台湾优秀品牌访谈,调用新闻中心->优秀威客，栏目ID为594
$field="a.art_cat_id,a.art_id,a.art_title,a.content,a.art_pic,s.shop_id";
$table="keke_witkey_article as a,keke_witkey_shop as s,keke_witkey_brand as b";
$where="a.r_uid=s.uid and a.is_show=1 and a.art_cat_id=594 and s.isvip=1 and a.is_recommend=1 and b.brand='tw' and b.uid=a.r_uid and b.brand='tw'";
$order="a.pub_time desc";
$limit="0,1";
$sql=sprintf("select %s from %s where %s order by %s limit %s",$field,$table,$where,$order,$limit);
$weike_list=db_factory::query($sql);
//台湾原创设计师,调用新闻中心->台湾原创设计师，栏目ID为640
$ycsjs_list=kekezu::get_table_data('art_id,art_title', "witkey_article", 'art_cat_id=640 and is_recommend=1', 'art_id desc', '', '0,10','art_id', 3600);
//一品官方资讯，调用新闻中心->最新动态，栏目ID为587
$artical_list=kekezu::get_table_data('art_id,art_title', "witkey_article", 'art_cat_id=587', 'art_id desc', '', '0,10','art_id', 3600);
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
