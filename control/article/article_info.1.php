<?php
/**
 * 文章模块--文章首页
 * this not free,powered by keke-tech
 * @author xl
 * @charset:GBK  last-modify 2011-12-5-上午10:37:07
 * @version V2.0
 */
defined ( 'IN_KEKE' ) or exit('Access Denied');

$ncat_ids = array('587','597','589','590','591');
$ncat_names =  array('587'=>'最新动态','597'=>'平台公告','589'=>'媒体报道','590'=>'频道热门','591'=>'互联网资讯');
in_array($art_cat_id,$ncat_ids) or $art_cat_id = '587';

$art_id = intval($art_id)+0;
$year and $year = intval($year);
$art_id or kekezu::show_msg(kekezu::lang("operate_notice"),$_K['siteurl']."/index.php?do=article&view=list",2,kekezu::lang("test"),"warning");

$static and $pre_url = $_K['siteurl'].'/';

$sql  = "select a.* ,b.cat_name from %switkey_article as a
              left join %switkey_article_category as b  on a.art_cat_id = b.art_cat_id where a.art_id='%d'";

$art_info = db_factory::get_one(sprintf($sql,TABLEPRE,TABLEPRE,$art_id));

if(!$art_info){
	kekezu::show_msg('网页错误','/index.php?do=article&view=list',2,'您查看的文章不存在，您可以返回文章列表查看其它文章。','warning');
}

if($art_info['jump_link']){
	header("location:{$art_info['jump_link']}");
}

//取得文章关键字数据
$art_keyword_arr = db_factory::query("select * from keke_witkey_article_keyword where keyword_status=1");
if(is_array($art_keyword_arr)){
	foreach ($art_keyword_arr as $v){
		$art_info['content'] = str_replace($v['word'], "<a href='".$v['url']."' target='_blank'>".$v['word']."</a>", $art_info['content']);
	}
}
$art_info['content'];

$where = " and 1=1 and art_type='art' and art_cat_id=".$art_info['art_cat_id'];

//获取当前文章的上一篇和下一篇文章信息
$art_up_info = db_factory::get_one(sprintf("select  art_id ,art_cat_id,art_title  from %switkey_article  where art_id>'%d'  %s order by art_id asc limit 0,1",TABLEPRE,$art_id,$where));
$art_down_info = db_factory::get_one(sprintf("select art_id ,art_cat_id,art_title  from %switkey_article  where art_id<'%d' %s order by art_id desc limit 0,1",TABLEPRE,$art_id,$where));

if(!$_COOKIE["article_".$art_id]){
	//更改浏览数
	$sqlplus = "update %switkey_article set views = views+1 where art_id = %d";
	db_factory::execute(sprintf($sqlplus,TABLEPRE,$art_id));
}
//设置cookie过期时间
setcookie("article_".$art_id,"exist_".$art_id,time()+3600*24);
 
 //SEO
$page_title=$art_info['seo_title'].' 新闻中心_IT帮手网';
$page_keyword = $art_info['seo_keywords'];
$page_description = $art_info['seo_desc'];

//读取推荐和热门内容
$recommend_info = kekezu::get_table_data('art_id,art_cat_id,art_title,pub_time,art_pic','witkey_article',"art_type='art' and is_show=1 and is_recommend=1  and art_pic !=''",'art_id desc','','0,4','art_id',3600);
$hot_info = kekezu::get_table_data('art_id,art_cat_id,art_title,pub_time','witkey_article',"art_type='art' and is_show=1",'views desc','','0,10','art_id',3600);
$about_info = kekezu::get_table_data('art_id,art_cat_id,art_title','witkey_article',"art_type='art' and is_show=1 and art_cat_id=".$art_info['art_cat_id']." and art_id <> ".$art_id,'art_id desc','','0,8','art_id',3600);
$about_count = count($about_info);
//案例展示默认内容
$case_628_info = db_factory::query("select art_id,art_cat_id,art_title,art_type,art_pic,pub_time, ".TABLEPRE."witkey_article.task_id as task_id,task_title,task_cash,view_num,focus_num,work_num,w_username from ".TABLEPRE."witkey_article left join  ".TABLEPRE."witkey_task on ".TABLEPRE."witkey_article.task_id  = ".TABLEPRE."witkey_task.task_id WHERE art_type='case' AND art_cat_id=628 ORDER BY art_id DESC LIMIT 5");
$case_629_info = db_factory::query("select art_id,art_cat_id,art_title,art_type,art_pic,pub_time, ".TABLEPRE."witkey_article.task_id as task_id,task_title,task_cash,view_num,focus_num,work_num,w_username from ".TABLEPRE."witkey_article left join  ".TABLEPRE."witkey_task on ".TABLEPRE."witkey_article.task_id  = ".TABLEPRE."witkey_task.task_id WHERE art_type='case' AND art_cat_id=629 ORDER BY art_id DESC LIMIT 5");
$case_630_info = db_factory::query("select art_id,art_cat_id,art_title,art_type,art_pic,pub_time, ".TABLEPRE."witkey_article.task_id as task_id,task_title,task_cash,view_num,focus_num,work_num,w_username from ".TABLEPRE."witkey_article left join  ".TABLEPRE."witkey_task on ".TABLEPRE."witkey_article.task_id  = ".TABLEPRE."witkey_task.task_id WHERE art_type='case' AND art_cat_id=630 ORDER BY art_id DESC LIMIT 5");

require keke_tpl_class::template ( SKIN_PATH . "/article/article_info" );
