<?php
/**
 * 品牌馆-台湾馆-品牌入驻
 * 
 * @author xxy
 * 2012-9-26
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '品牌馆-台湾馆-品牌库-IT帮手网';
$page_keyword = '品牌馆-台湾馆-品牌库-IT帮手网';
$page_description ='品牌馆-台湾馆-品牌库-IT帮手网';
$op_name="品牌库";
//推荐品牌
$table=TABLEPRE.'witkey_space as b,'.TABLEPRE.'witkey_shop as s';
$field="s.shop_id,s.logo,s.shop_name,b.uid";
$where="b.shop_id=s.shop_id and s.istop=1  and b.brand='tw' and s.isvip=1";
$order="s.listorder asc,s.shop_id asc";
$limit="0,33";
$recmdbrand=db_factory::query(sprintf("select %s from %s where %s order by %s limit %s",$field,$table,$where,$order,$limit));
//新加会员
$where=" b.shop_id=s.shop_id and b.brand='tw' and s.isvip=1";
$newbrand=db_factory::query(sprintf("select %s from %s where %s order by %s limit %s",$field,$table,$where,$order,$limit));
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );