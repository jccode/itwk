<?php
/**
 * 行业编辑
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-18早上22:20:00
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

if($type==1){
	kekezu::admin_check_role ( 7 );	
}elseif ($type==2){
	kekezu::admin_check_role ( 6 );
}

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

$indus_table_obj = new Keke_witkey_industry_class();
$indus_obj = keke_table_class::get_instance ( "witkey_industry" ); //实例化行业表对象
$file_obj = new keke_file_class();

//行业结果数组
$indus_arr = kekezu::get_table_data ( '*', "witkey_industry", '  indus_pid = 0 and indus_type='.intval($type), "listorder", '', '', 'indus_id', $cache );
(isset ( $indus_id ) and intval ( $indus_id ) > 0) and $indus_info = $indus_obj->get_table_info ( 'indus_id', $indus_id );
empty ( $art_info ) or extract ( $art_info );
//单条行业信息
if (isset ( $indus_id ) && intval ( $indus_id ) > o) {
	$indus_info = $indus_obj->get_table_info ( 'indus_id', $indus_id );
	$indus_pid = $indus_info ['indus_pid'];
}
//添加行业
if($sbt_edit){
	//栏目seo标识验证

	if(!$fs[seo_catname]){
		$fs[seo_catname] = kekezu::get_pinyin(kekezu::escape($fs[indus_name]),'UTF-8');
	}
	$indus_id=$pk['indus_id'];
	db_factory::get_table_data('*',"witkey_industry","seo_catname = '{$fs[seo_catname]}' and indus_id!='$indus_id'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
	db_factory::get_table_data('*',"witkey_article_category","seo_catname = '{$fs[seo_catname]}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
	db_factory::get_table_data('*',"witkey_special","seo_catname = '{$fs[seo_catname]}'") and kekezu::admin_show_msg($_lang['operate_fail'],"index.php?do=task&view=industry_edit&indus_id=$indus_id",3,'该标识已存在','warning');
	
	$indus_table_obj->setWhere("indus_name = '".$fs['indus_name']."'");
	$res  = $indus_table_obj->count_keke_witkey_industry();
	!$pk&&$res and kekezu::admin_show_msg($_lang['operate_fail'],$url,3,$_lang['indus_has']);
	$fs['on_time'] = time();
	isset($fs['is_recommend']) or $fs['is_recommend']=0;
	$fs=kekezu::escape($fs);	
	$res = $indus_obj->save($fs,$pk);
	$indus_info = $indus_obj->get_table_info ( 'indus_id', $pk['indus_id'] ); 
	$url = "index.php?do=task&view=industry&type=".intval($type);
	!$pk and kekezu::admin_system_log($_lang['add_industry']) or kekezu::admin_system_log($_lang['edit_industry'].':'.$indus_info['indus_name']);
	$file_obj->delete_files(S_ROOT."./data/data_cache/");
	$file_obj->delete_files(S_ROOT.'./data/tpl_c/');

	//生成新的.htacess文件
	epweike_seo_class::create_htacess();
	
	$res and kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success') or kekezu::admin_show_msg($_lang['operate_fail'],$url,3,'','warning');	
}

 

//递归分类列表

$temp_arr = array();

kekezu::get_tree($indus_arr,$temp_arr,'option',$indus_pid,'indus_id'); 
$indus_arr = $temp_arr;

require $template_obj->template ( 'control/admin/tpl/admin_task_' . $view );