<?php
/**
 * 品牌馆首页@copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-31 10:44
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_title = '品牌馆首页';
$page_keyword = '品牌馆首页';
$page_description ='品牌馆首页';

/**推荐列表*/
$hk_recomm_list = db_factory::query(' select * from '.TABLEPRE.'witkey_brand where  brand="hk" and is_recommend = 1 order by is_recommend desc limit 0,27',1,3600);



//var_dump($hk_recomm_list);
/*访谈列表*/
$interview = db_factory::query(' select * from '.TABLEPRE.'witkey_article where art_cat_id=594 order by pub_time desc limit 0,3',1,3600);
/**优质服务**/
$s_list  = db_factory::query(' select b.title,b.service_id,b.service_type,b.price,b.unite_price,b.pic,b.shop_id from '.TABLEPRE.'witkey_brand a '
			.' left join '.TABLEPRE.'witkey_service b on a.uid = b.uid where a.app_status=1 group by a.uid order by b.on_time desc limit 0,8',1,3600);
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );