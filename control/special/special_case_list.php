<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-24 17:36
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page = intval ( $page );
$page or $page = 1;
$page_size = intval ( $page_size );
$year = intval($year);
$page_size or $page_size = 20;
$opp=='remote' and $page_size = 8;
$url=$_K['siteurl']."/index.php?do=$do&view=$view&op=$op&page=$page&pagesize=$pagesize&art_cat_id=$art_cat_id";

$cate_id or $cate_id = $art_cat_id;
$cate_arr = kekezu::get_table_data ( "art_cat_id,art_cat_pid,cat_type,cat_name", "witkey_article_category", "cat_type='case' and art_cat_pid = 500 ", " listorder asc", "", "", "art_cat_id", 3600 );

$page_obj = $kekezu->_page_obj;
$sql = "select art_id,art_cat_id,art_title,art_type,art_pic,pub_time, ".TABLEPRE."witkey_article.task_id as task_id,task_title,task_cash,view_num,focus_num,work_num,w_username from ".TABLEPRE."witkey_article left join  ".TABLEPRE."witkey_task on ".TABLEPRE."witkey_article.task_id  = ".TABLEPRE."witkey_task.task_id";
$where = " where art_type='case' and is_show=1 and art_cat_id !=637  ";

if(intval($art_cat_id)){
	$where.=" and art_cat_id =  ".intval($art_cat_id);
}else{
	$where.=" and ".TABLEPRE."witkey_article.is_recommend=1 ";
}

$count = db_factory::get_count(sprintf("select count(art_id) from %switkey_article%s",TABLEPRE,$where));
$where .= " order by art_id desc";
$pages = $page_obj->getPages($count, $page_size, $page, $url); 
$art_arr = db_factory::query($sql.$where.$pages['where']);

//友情链接
if($page < 2){
	$art_cat_id and $link_where = " and obj_id = '$art_cat_id'" or $link_where = " and obj_id = ''";
	$link_tag = keke_core_class::link_make_tag( array(7=>1) );
	$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) $link_where order by listorder limit 0,30", TABLEPRE ), 1, 0 );
}

//SEO
$seo_info = case_page_seo($cate_id, $page);
$seo_title = $cate_arr[$cate_id] ? '' : '优秀案例 '; 
$page_title = $seo_title.$seo_info['page_title'];
$page_keyword = $seo_info['page_keyword'];
$page_description = $seo_info['page_description'];

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );

function case_page_seo($cate_id,$page){
	$seo_info = array(		
		1 => array( //默认
			1 => array( 
				'page_title' => '成功案例|品牌故事|金点子|好点子|点子超市＿IT帮手网',
				'page_keyword' => '成功案例|品牌故事|金点子|好点子|点子超市＿IT帮手网',
				'page_description' => '欢迎您来到IT帮手网,成功案例包括品牌故事,金点子,好点子,点子超市,创新,创业,创意的展示，是用户最信赖的威客站,是激发人们创新,创意,创业的知识与智慧,创意无处不精彩！'
			),
		),
		628 => array(//平面设计
			1 => array( 
				'page_title' => '平面设计,平面设计网 ,平面设计欣赏 ,平面广告设计素材,成功案例＿IT帮手网',
				'page_keyword' => '平面设计,平面设计网 ,平面设计欣赏 ,平面广告设计素材,成功案例＿IT帮手网',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括平面设计,平面设计网 ,平面设计欣赏 ,平面广告设计素材等创意交易类威客任务,是中国最有价值的创意交易平台'
			),
			2 => array( 
				'page_title' => '包装设计欣赏,包装设计网,产品包装设计欣赏,食品包装设计,成功案例＿IT帮手网',
				'page_keyword' => '包装设计欣赏,包装设计网,产品包装设计欣赏,食品包装设计,成功案例＿IT帮手网',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括包装设计欣赏,包装设计网,产品包装设计欣赏,食品包装设计等创意交易类威客任务,是中国最有价值的创意交易平台'
			),
			3 => array( 
				'page_title' => '封面设计图片,封面设计欣赏,封面设计素材,封面设计模板,成功案例＿IT帮手网',
				'page_keyword' => '封面设计图片,封面设计欣赏,封面设计素材,封面设计模板,成功案例＿IT帮手网',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括封面设计图片,封面设计欣赏,封面设计素材,封面设计模板等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '动漫设计与制作专业介绍,动漫制作,动漫图片,动漫图片下载,成功案例＿IT帮手网',
				'page_keyword' => '动漫设计与制作专业介绍,动漫制作,动漫图片,动漫图片下载,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括动漫设计与制作专业介绍,动漫制作,动漫图片,动漫图片下载等创意交易类威客任务,是中国最有价值的创意交易平台'
			),
			5 => array( 
				'page_title' => '搞笑四格漫画,乌龙院四格漫画,乌龙院四格漫画全集,四格漫画欣赏,成功案例＿IT帮手网',
				'page_keyword' => '搞笑四格漫画,乌龙院四格漫画,乌龙院四格漫画全集,四格漫画欣赏,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括搞笑四格漫画,乌龙院四格漫画,乌龙院四格漫画全集,四格漫画欣赏等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			6 => array( 
				'page_title' => '平面最新qq表情大全,平面设计素材，应用设计素材,成功案例＿IT帮手网',
				'page_keyword' => '平面最新qq表情大全,平面设计素材，应用设计素材,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括平面最新qq表情大全,平面设计素材，应用设计素材,成功案例等创意交易类威客任务,是中国最有价值的创意交易平台'
			),
			7 => array( 
				'page_title' => '工业设计在线,工业设计公司,工业设计手绘,中国工业设计在线,成功案例＿IT帮手网',
				'page_keyword' => '工业设计在线,工业设计公司,工业设计手绘,中国工业设计在线,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括工业设计在线,工业设计公司,工业设计手绘,中国工业设计在线等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		629 => array( //品牌设计
			1 => array( 
				'page_title' => '品牌设计,品牌形象设计,品牌形象设计,效果图设计,成功案例＿IT帮手网',
				'page_keyword' => '品牌设计,品牌形象设计,品牌形象设计,效果图设计,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括品牌设计,品牌形象设计,品牌形象设计,效果图设计,成功案例等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '厦门品牌设计,公司logo设计,品牌logo设计,网站logo设计＿IT帮手网',
				'page_keyword' => '厦门品牌设计,公司logo设计,品牌logo设计,网站logo设计',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括LOGO设计,公司厦门品牌设计,品牌logo设计,网站logo设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '品牌标志设计，企业品牌设计,品牌卡通形象设计,经典品牌logo设计＿IT帮手网',
				'page_keyword' => '品牌标志设计，企业品牌设计,品牌卡通形象设计,经典品牌logo设计',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括品牌标志设计，企业品牌设计,品牌卡通形象设计,经典品牌logo设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '标志设计欣赏,品牌标志设计欣赏,企业品牌设计,网站标志设计＿IT帮手网',
				'page_keyword' => '标志设计欣赏,品牌标志设计,公司标志设计,网站标志设计',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括标志设计欣赏,品牌标志设计,企业品牌设计,网站标志设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		630 => array( //建筑装修
			1 => array( 
				'page_title' => '建筑设计,建筑设计图,建筑设计规范,建筑设计效果图,成功案例＿IT帮手网',
				'page_keyword' => '建筑设计,建筑设计图,建筑设计规范,建筑设计效果图',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括建筑设计,建筑设计图,建筑设计规范,建筑设计效果图等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站,成功案例＿IT帮手网',
				'page_keyword' => '建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计,成功案例＿IT帮手网',
				'page_keyword' => '家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修,成功案例＿IT帮手网',
				'page_keyword' => '店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏,成功案例＿IT帮手网',
				'page_keyword' => '办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏等创意交易类威客任务,是中国最有价值的创意交易平台。
				'
			),
		),
		630 => array( //建筑装修
			1 => array( 
				'page_title' => '建筑设计,建筑设计图,建筑设计规范,建筑设计效果图,成功案例＿IT帮手网',
				'page_keyword' => '建筑设计,建筑设计图,建筑设计规范,建筑设计效果图',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括建筑设计,建筑设计图,建筑设计规范,建筑设计效果图等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站,成功案例＿IT帮手网',
				'page_keyword' => '建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括建筑抗震设计规范，住宅建筑设计规范，建筑设计防火规范，建筑设计网站等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计,成功案例＿IT帮手网',
				'page_keyword' => '家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括家装设计效果图，免费家装设计欣赏，办公装修设计，经典家装设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修,成功案例＿IT帮手网',
				'page_keyword' => '店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括店面装修图片，店面装修效果图，店面装修设计效果图，淘宝店面装修等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏,成功案例＿IT帮手网',
				'page_keyword' => '办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括办公室装修效果图，办公楼装修，办公装修等方案效果图欣赏等创意交易类威客任务,是中国最有价值的创意交易平台。
				'
			),
		),
		633 => array( //创意点子
			1 => array( 
				'page_title' => '创意产品,创意礼品,创意点子,创意产品案例,成功案例＿IT帮手网',
				'page_keyword' => '创意产品,创意礼品,创意点子,创意产品案例,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括创意产品,创意礼品,创意点子,创意产品案例等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '爱情表白词,爱情表白短信,爱情表白仪式,爱情表白策划,成功案例＿IT帮手网',
				'page_keyword' => '爱情表白词,爱情表白短信,爱情表白仪式,爱情表白策划,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括爱情表白词,爱情表白短信,爱情表白仪式,爱情表白策划等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '圣诞祝福短信，圣诞祝福语，圣诞节祝福短信，圣诞节的祝福语,成功案例＿IT帮手网',
				'page_keyword' => '圣诞祝福短信，圣诞祝福语，圣诞节祝福短信，圣诞节的祝福语',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括圣诞祝福短信，圣诞祝福语，圣诞节祝福短信，圣诞节的祝福语等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '新年祝福语，新年祝福词，领导生日祝福短信,朋友生日祝福短信,成功案例＿IT帮手网',
				'page_keyword' => '新年祝福语，新年祝福词，领导生日祝福短信,朋友生日祝福短信',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括新年祝福语，新年祝福词，领导生日祝福短信,朋友生日祝福短信等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		632 => array( //文案策划
			1 => array( 
				'page_title' => '文案策划,策划方案,文案策划案例,文案策划范文,成功案例＿IT帮手网',
				'page_keyword' => '文案策划,策划方案,文案策划案例,文案策划范文,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括文案策划,策划方案,文案策划案例,文案策划范文等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '宝宝起名网,宝宝起名打分,给宝宝免费起名,宝宝免费起名打分,成功案例＿IT帮手网',
				'page_keyword' => '宝宝起名网,宝宝起名打分,给宝宝免费起名,宝宝免费起名打分,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括宝宝起名网,宝宝起名打分,给宝宝免费起名,宝宝免费起名打分等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '公司起名网,公司起名网站,公司起名大全,公司起名测算,成功案例＿IT帮手网
				',
				'page_keyword' => '公司起名网,公司起名网站,公司起名大全,公司起名测算,成功案例
				',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括公司起名网,公司起名网站,公司起名大全,公司起名测算等创意交易类威客任务,是中国最有价值的创意交易平台。
				'
			),
			4 => array( 
				'page_title' => '经典广告语,公益广告语,汽车广告语,房地产广告语,成功案例＿IT帮手网',
				'page_keyword' => '经典广告语,公益广告语,汽车广告语,房地产广告语,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括经典广告语,公益广告语,汽车广告语,房地产广告语等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '经典广告语大全,公益广告语大全,搞笑广告语大全,食品广告语大全,成功案例＿IT帮手网',
				'page_keyword' => '经典广告语大全,公益广告语大全,搞笑广告语大全,食品广告语大全,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括经典广告语大全,公益广告语大全,搞笑广告语大全,食品广告语大全等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		631 => array( //案例展示
			1 => array( 
				'page_title' => '案例分析,案例展示,展示设计,商业展示设计,成功案例＿IT帮手网',
				'page_keyword' => '案例分析,案例展示,展示设计,商业展示设计,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括案例分析,案例展示,展示设计,商业展示设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '展示展览设计欣赏 ,展示设计手绘效果图,展示设计模型,展示设计平面图,成功案例＿IT帮手网',
				'page_keyword' => '展示展览设计欣赏 ,展示设计手绘效果图,展示设计模型,展示设计平面图,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括展示展览设计欣赏 ,展示设计手绘效果图,展示设计模型,展示设计平面图等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '展示设计说明, 展示设计效果图,展览展示设计案例,展示设计网,成功案例＿IT帮手网',
				'page_keyword' => '展示设计说明, 展示设计效果图,展览展示设计案例,展示设计网,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括展示设计说明, 展示设计效果图,展览展示设计案例,展示设计网等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '服装展示设计,橱窗展示设计,户外广告展示设计,世博会展示设计,成功案例＿IT帮手网',
				'page_keyword' => '服装展示设计,橱窗展示设计,户外广告展示设计,世博会展示设计,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括服装展示设计,橱窗展示设计,户外广告展示设计,世博会展示设计等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '网站案例展示,logo案例展示,封面设计案例展示,动漫设计案例展示,成功案例＿IT帮手网',
				'page_keyword' => '网站案例展示,logo案例展示,封面设计案例展示,动漫设计案例展示,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网站案例展示,logo案例展示,封面设计案例展示,动漫设计案例展示等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			6 => array( 
				'page_title' => '网店装修案例展示,店招设计案例展示,包装设计案例展示,海报设计案例展示,成功案例＿IT帮手网',
				'page_keyword' => '网店装修案例展示,店招设计案例展示,包装设计案例展示,海报设计案例展示,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网店装修案例展示,店招设计案例展示,包装设计案例展示,海报设计案例展示等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		634 => array( //营销方案
			1 => array( 
				'page_title' => '营销方案,营销策划书,网络营销方案,营销策划方案,成功案例＿IT帮手网',
				'page_keyword' => '营销方案,营销策划书,网络营销方案,营销策划方案,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括营销方案,营销策划书,网络营销方案,营销策划方案等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '市场营销方案,市场营销策划方案,市场营销策划,市场营销策划书,成功案例＿IT帮手网',
				'page_keyword' => '市场营销方案,市场营销策划方案,市场营销策划,市场营销策划书,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括市场营销方案,市场营销策划方案,市场营销策划,市场营销策划书等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '营销策划方案案例,营销策划案例,营销策划案例分析,营销与策划,成功案例＿IT帮手网',
				'page_keyword' => '营销策划方案案例,营销策划案例,营销策划案例分析,营销与策划,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括营销策划方案案例,营销策划案例,营销策划案例分析,营销与策划等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => '网络营销与策划,网络营销策划方案,网络营销策划书,网络营销技巧,成功案例＿IT帮手网',
				'page_keyword' => '网络营销与策划,网络营销策划方案,网络营销策划书,网络营销技巧,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网络营销与策划,网络营销策划方案,网络营销策划书,网络营销技巧等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略,成功案例＿IT帮手网',
				'page_keyword' => '网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			6 => array( 
				'page_title' => '整合营销传播,整合营销传播案例,成功案例＿IT帮手网',
				'page_keyword' => '整合营销传播,整合营销传播案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括市场营销,整合营销传播,整合营销传播案例等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
		635 => array( //网站软件
			1 => array( 
				'page_title' => '网站建设方案,网站建设策划,网站制作软件,软件开发文档,成功案例＿IT帮手网',
				'page_keyword' => '网站建设方案,网站建设策划,网站制作软件,软件开发文档,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网站建设方案,网站建设策划,网站制作软件,软件开发文档等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			2 => array( 
				'page_title' => '网站设计案例，制作网站软件，广告设计网站欣赏,成功案例＿IT帮手网',
				'page_keyword' => '网站设计案例，制作网站软件，广告设计网站欣赏',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网站设计案例，制作网站软件，广告设计网站欣赏等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			3 => array( 
				'page_title' => '厦门网站建设，网站建设方案书，企业网站建设方案，动态网站制作,成功案例＿IT帮手网',
				'page_keyword' => '厦门网站建设，网站建设方案书，企业网站建设方案，动态网站制作',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网站建设方案书，企业网站建设方案，动态网站制作等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			4 => array( 
				'page_title' => 'wap网站，wap网站开发，wap网站建设，wap网站设计欣赏,成功案例＿IT帮手网',
				'page_keyword' => 'wap网站，wap网站开发，wap网站建设，wap网站设计欣赏',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括wap网站，wap网站开发，wap网站建设，wap网站设计欣赏等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			5 => array( 
				'page_title' => '网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略,成功案例＿IT帮手网',
				'page_keyword' => '网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略,成功案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括网络营销策划案例,网络营销的策略,网络整合营销,网络营销策略等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
			6 => array( 
				'page_title' => '整合营销传播,整合营销传播案例,成功案例＿IT帮手网',
				'page_keyword' => '整合营销传播,整合营销传播案例',
				'page_description' => '欢迎您来到IT帮手网,IT帮手网的成功案例包括市场营销,整合营销传播,整合营销传播案例等创意交易类威客任务,是中国最有价值的创意交易平台。'
			),
		),
	);

	if($seo_info[$cate_id]){
		$count = count($seo_info[$cate_id]);
		if($page > $count){
			$page = $page%$count;
		}
		
		return $seo_info[$cate_id][$page];
	}else{
		
		return $seo_info[1][1];
	}
	
}
