<?php
/**
 * 文章模块入口路由页
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-12-5-上午10:37:07
 * @version V2.0
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//$views = array ("list", "info" );
//! in_array ( $view, $views ) && $view = 'list';

if ( ! $art_cat_id && ! $i ) {
	$art_cat_id = db_factory::get_count( sprintf("select art_cat_id from %switkey_article where art_id = %d ", TABLEPRE, $art_id) );
}
(!$art_cat_id&&$i) and $art_cat_id = $i;//栏目
$art_cat_arr = kekezu::get_table_data("*","witkey_article_category","",'','','','art_cat_id',null);

$cat_info = $art_cat_arr[$art_cat_id];
switch ($cat_info['cat_type']){
	case "article":
	default:
		if($cat_info['art_cat_id']==594){
			$do = $_GET['do'] = "special";
			$art_id and $view = $_GET['view'] = "witkey_info" or $view = $_GET['view'] = "witkey_list";
			require "special.php";
		}
		elseif($cat_info['art_cat_id']==593){
			$do = $_GET['do'] = "special";
			$art_id and  $view = $_GET['view'] = "employer_info" or $view = $_GET['view'] = "employer_list";
			require "special.php";
		}
		else{
			if($art_id){
				require 'article/article_info.php';
			}
			else{
				require 'article/article_list.php';
			}
			$nav_active_index = "article";
		}
		break;
	case "case":
		$do = $_GET['do'] = "special";
		if($art_id){
			$view = $_GET['view'] = "case_info";
		}
		else{
			$view = $_GET['view'] = "case_list";
			$cate_id = $_GET['cate_id'] = $art_cat_id;
		}
		require "special.php";
		break;
	case "help":
		$do = $_GET['do'] = "help";
		if($art_id){
			$view = $_GET['view'] = "info";
		}
		else{
			$view = $_GET['view'] = "list";
		}
		require "help.php";
		break;
}




