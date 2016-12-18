<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if(isset($art_id) && intval($art_id)){
	$sql=sprintf("select * from %switkey_article where art_id=%d",TABLEPRE,$art_id);
	$art_info=db_factory::get_one($sql);
	//跳转处理
	if(!$art_info){
		kekezu::show_msg('网页错误','/index.php?do=article&art_cat_id=593',2,'您查看的文章不存在，您可以返回列表查看其它文章。','warning');
	}
	if($art_info['jump_link']){
		header("location:{$art_info['jump_link']}");
	}
	
	if(!$_COOKIE["article_".$art_id]){
		//更改浏览数
		$sqlplus = "update %switkey_article set views = views+1 where art_id = %d";
		db_factory::execute(sprintf($sqlplus,TABLEPRE,$art_id));
	}
	//设置cookie过期时间
	setcookie("article_".$art_id,"exist_".$art_id,time()+3600*24);

	//SEO
	$page_title=$art_info['art_title'].$art_info['seo_title'].'_IT帮手网 ';
	$page_keyword = $art_info['seo_keywords'];
	$page_description = $art_info['seo_desc'];
	
	$where = " and art_type='art' and is_show=1 and art_cat_id=".$art_info['art_cat_id'];
	
	//获取当前文章的上一篇和下一篇文章信息
	$art_up_info = db_factory::get_one(sprintf("select  art_id ,art_cat_id,art_title  from %switkey_article  where art_id<'%d'  %s order by art_id desc limit 0,1",TABLEPRE,$art_id,$where));
	$art_down_info = db_factory::get_one(sprintf("select art_id ,art_cat_id,art_title  from %switkey_article  where art_id>'%d' %s order by art_id asc limit 0,1",TABLEPRE,$art_id,$where));
	//读取相关文章
	$about_info = kekezu::get_table_data('art_id,art_cat_id,art_title','witkey_article',"art_type='art' and is_show=1 and art_cat_id=".$art_info['art_cat_id']." and art_id <> ".$art_id,'art_id desc','','0,10','art_id',3600);
}else{
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=special&view=employer_list",3,"文章参数错误，请返回列表页重试！","warning");
}

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );