<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$cate_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_type,cat_name", "witkey_article_category", "cat_type='case' and art_cat_pid = 500 ", " listorder asc", "", "", "art_cat_id", 3600 );

$art_id = intval($art_id)+0;
$year and $year = intval($year);
$art_id or kekezu::show_msg(kekezu::lang("operate_notice"),$_K['siteurl']."/index.php?do=special&view=case_list",2,kekezu::lang("test"),"warning");

$art_info = db_factory::get_one(sprintf("select a.* ,b.* from %switkey_article as a
left join %switkey_task as b  on a.task_id = b.task_id where a.art_id='%d'",TABLEPRE,TABLEPRE,$art_id));
if(!$art_info){
	kekezu::show_msg('网页错误','/index.php?do=special',2,'您查看的案例不存在，您可以返回案例列表查看其它案例。','warning');
}
//获取当前文章的上一篇和下一篇文章信息
$art_up_info = db_factory::get_one(sprintf("select  art_id ,art_cat_id,art_title  from %switkey_article where 1 =1 and art_type='case' and art_cat_id=%d and  art_id<'%d' order by art_id desc limit 0,1",TABLEPRE,$art_info['art_cat_id'],$art_id));
$art_down_info = db_factory::get_one(sprintf("select art_id ,art_cat_id,art_title  from %switkey_article where 1 =1 and art_type='case' and art_cat_id=%d and  art_id>'%d' order by art_id asc limit 0,1",TABLEPRE,$art_info['art_cat_id'],$art_id));

if(!$_COOKIE["article_".$art_id]){
	//更改浏览数
	db_factory::execute(sprintf("update %switkey_article set views = views+1 where art_id = %d",TABLEPRE,$art_id));
}
//设置cookie过期时间
setcookie("article_".$art_id,"exist_".$art_id,time()+3600*24);

$page_title=$art_info['art_title'].$art_info['seo_title'].'- '.$_K['html_title'];
$page_keyword = $art_info['seo_keywords'];
$page_description = $art_info['seo_desc'];

//相关案例
$about_info = kekezu::get_table_data('art_id,art_cat_id,art_title','witkey_article',"art_type='case' and is_show=1 and art_cat_id=".$art_info['art_cat_id']." and art_id <> ".$art_id,'art_id desc','','0,8','art_id',3600);
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );