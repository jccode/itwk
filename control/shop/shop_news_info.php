<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if($art_id){
//获取当前文章的上一篇和下一篇文章信息
$art_up_info = db_factory::get_one(sprintf("select  art_id ,art_title  from %switkey_article_flagship   where art_id<'%d' order by art_id desc limit 0,1",TABLEPRE,$art_id));
$art_down_info = db_factory::get_one(sprintf("select art_id ,art_title  from %switkey_article_flagship   where art_id>'%d' order by art_id asc limit 0,1",TABLEPRE,$art_id));

if(!$_COOKIE["article_".$art_id]){
	//更改浏览数
	$sqlplus = "update %switkey_article_flagship set views = views+1 where art_id = %d";
	db_factory::execute(sprintf($sqlplus,TABLEPRE,$art_id));
}
//设置cookie过期时间
setcookie("article_".$art_id,"exist_".$art_id,time()+3600*24);
$art_info = db_factory::get_one(sprintf("select * from %switkey_article_flagship where art_id=%d",TABLEPRE,$art_id));
$art_type = $art_info['art_type'];
}else{
kekezu::show_msg ( $_lang ['oprerate_notice'], $kekezu->_sys_config ['website_url'] . "/index.php?do=index", 2, '参数错误', "warning" );
}
require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );