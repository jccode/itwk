<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-6-26 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//$story_arr = db_factory::query("select a.art_title,a.content,a.art_id,b.uid,b.shop_id from ".TABLEPRE."witkey_article a left join ".TABLEPRE."witkey_space b on a.r_uid = b.uid  where art_cat_id = 599 ");

$page and $page = intval($page) or $page = 1;
$url = $_K['siteurl']."/index.php?do=vip&view=story";

$where = " where art_cat_id = 599 order by a.art_id desc";
$sql_count = "select count(*) from ".TABLEPRE."witkey_article a left join ".TABLEPRE."witkey_space b on a.r_uid = b.uid";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

$sql = "select a.art_title,a.content,a.art_cat_id,a.r_uid,a.art_id,b.uid,b.shop_id,b.isvip from ".TABLEPRE."witkey_article a left join ".TABLEPRE."witkey_space b on a.r_uid = b.uid";
$pages = $kekezu->_page_obj->getPages ( $count, 10, $page, $url );
$sql .= $where.$pages['where'];
$story_arr = db_factory::query($sql);

$page_title = 'VIP会员赚钱故事 同城VIP情人 1对1服务 满意100%赚钱好项目_IT帮手网';
$page_keyword = 'VIP会员，同城VIP，VIP情人，赚钱故事，赚钱好项目';
$page_description ='IT帮手网赚钱故事，优秀威客分享在家赚钱的方法，如何寻找赚钱好项目。与优秀威客交流学习，通过优秀威客赚钱故事了解学生怎么赚钱，怎样在家赚钱，做一名成功的威客。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );