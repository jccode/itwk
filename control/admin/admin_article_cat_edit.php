<?php
 /**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 */
defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );
kekezu::admin_check_role(22);
$art_cat_obj = new Keke_witkey_article_category_class();//实例化文章分类表对象

$types =  array ('help', 'art','case');
$type = (! empty ( $type ) && in_array ( $type, $types )) ? $type : 'art';

//验证关键词
if($ac=='validseoname'){
	
	$seocatname or die();
	if (db_factory::get_table_data('*',"witkey_industry","seo_catname = '$seocatname'")){
		echo 'exsit';
	}
	else if (db_factory::get_table_data('*',"witkey_article_category","seo_catname = '$seocatname'")){
		echo 'exsit';
	}
	else if (db_factory::get_table_data('*',"witkey_special","seo_catname = '$seocatname'")){
		echo 'exsit';
	}
	die();
}
 
//分类结果数组
if($type=='art'){
	kekezu::admin_check_role(39);
	$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{1}%'","  listorder asc",'','','art_cat_id',null);
}elseif($type=='help'){
	kekezu::admin_check_role(44);
	$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{100}%'"," listorder asc",'','','art_cat_id',null);
}elseif($type=='case'){
	kekezu::admin_check_role(14);
	$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{500}%'"," listorder asc",'','','art_cat_id',null);
}


//单条分类信息
if($art_cat_id){
	$art_cat_obj->setWhere('art_cat_id='.intval($art_cat_id));
	$art_cat_info = $art_cat_obj->query_keke_witkey_article_category();
	$art_cat_info = $art_cat_info[0];
	$art_cat_pid = $art_cat_info[art_cat_pid];
}


//编辑分类
if($sbt_edit){
	if(!$txt_seo_catname){
		$txt_seo_catname = kekezu::get_pinyin(kekezu::escape($txt_cat_name),'UTF-8');
	} 
	$flag = null;
	if($hdn_art_cat_id){
		db_factory::get_table_data('indus_id',"witkey_industry","seo_catname = '{$txt_seo_catname}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
		db_factory::get_table_data('art_cat_id',"witkey_article_category","seo_catname = '{$txt_seo_catname}'  and art_cat_id!='$hdn_art_cat_id'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
		db_factory::get_table_data('sp_id',"witkey_special","seo_catname = '{$txt_seo_catname}' ") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
		$art_cat_obj->setWhere('art_cat_id='.intval($hdn_art_cat_id));
		$art_cat_info = $art_cat_obj->query_keke_witkey_article_category();
		$art_cat_info = $art_cat_info[0];
		if($art_cat_info['art_cat_pid']>0){
			$art_cat_obj->setArt_cat_pid($slt_cat_id);
		}
	}else{
		
			db_factory::get_table_data('*',"witkey_industry","seo_catname = '{$txt_seo_catname}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
			db_factory::get_table_data('*',"witkey_article_category","seo_catname = '{$txt_seo_catname}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
			db_factory::get_table_data('*',"witkey_special","seo_catname = '{$txt_seo_catname}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
		
		$art_cat_obj->setArt_cat_pid($slt_cat_id);
	}
	
	$art_cat_obj->setCat_name(kekezu::escape($txt_cat_name));
	$art_cat_obj->setListorder($txt_listorder?$txt_listorder:0);
	$art_cat_obj->setIs_show(intval($chk_is_show));
	$art_cat_obj->setOn_time(time());
	//seo
	if($txt_seo_catname){
		$art_cat_obj->setSeo_catname($txt_seo_catname);
	}else{
		$art_cat_obj->setSeo_catname(kekezu::get_pinyin($txt_seo_catname),'UTF-8');
	}
	
	$art_cat_obj->setSeo_keywords($txt_seo_keywords);
	$art_cat_obj->setSeo_title($txt_seo_title);
	$art_cat_obj->setSeo_desc($tar_seo_desc);
		
	if($type=="art"){
		$art_cat_obj->setCat_type("article");
	}else if($type=="help"){
		$art_cat_obj->setCat_type("help");
	}else if ($type=="single"){
		$art_cat_obj->setCat_type("single");
	}else if ($type=='case'){
		$art_cat_obj->setCat_type("case");
	}
	$art_index = "";
	$art_index = "{{$slt_cat_id}}".$art_index;
	$flag = $art_cat_arr[$slt_cat_id];
	
	while ($flag['art_cat_pid']){
		$art_index = "{{$flag['art_cat_pid']}}".$art_index;
		$flag = $art_cat_arr[$flag['art_cat_pid']];
	}
	
	if($hdn_art_cat_id){
		$art_cat_obj->setArt_cat_id($hdn_art_cat_id);
		$art_index = $art_index."{{$hdn_art_cat_id}}";
		$art_cat_obj->setArt_index($art_index);

		$res = $art_cat_obj->edit_keke_witkey_article_category();//编辑文章分类
		if($res){
			//生成新的.htacess文件
			epweike_seo_class::create_htacess();
			kekezu::admin_system_log($_lang['edit_article_cat'].$txt_cat_name);
			kekezu::admin_show_msg('','index.php?do='.$do.'&view=cat_list&type='.$type,3,'','success');
		}
	}else{
		$res = $art_cat_obj->create_keke_witkey_article_category();//添加文章分类
		$art_index = $art_index."{{$res}}";
		if($res){
			$art_cat_obj->setWhere("art_cat_id='$res'");
			$art_cat_obj->setArt_index($art_index);
			$art_cat_obj->edit_keke_witkey_article_category();
			//生成新的.htacess文件
			epweike_seo_class::create_htacess();
			kekezu::admin_system_log($_lang['add_article_cat'] . $txt_cat_name);
			kekezu::admin_show_msg($_lang['add_article_cat_success'],'index.php?do='.$do.'&view=cat_list&type='.$type,3,'','success');
		}
	}
}
 
//递归分类列表
$temp_arr = array();
kekezu::get_tree($art_cat_arr,$temp_arr,'option',$art_cat_pid,'art_cat_id','art_cat_pid','cat_name');
$cat_arr = $temp_arr;
unset($temp_arr);
 

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);