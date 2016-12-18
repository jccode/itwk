<?php
/**
 * 行业栏目页
 * power for  epweike
 * @author tank
 * @charset:GBK  last-modify 2012-5-57-上午10:37:07
 * @version V2.0
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

!$indus_id&&$i and $indus_id = $i;

if(intval($indus_id)){
    $indus_info = $kekezu->findIndusById($indus_id);
    
	//格式化数据
	if(!$indus_info['children']){
		$p = $indus_info['indus_pid'];
		$c = $indus_id;
	}
	else{
		$p = $indus_id;
       	$pids = $kekezu->get_indus_ids_below_except_leaf($indus_info);
	}
	
	
	if($indus_info['indus_type']==2){
		$do = $_GET['do'] = 'service';
		$view or $view = $_GET['view'] = 's_list';
		require 'service.php';
	}
	else{
		$do = $_GET['do'] = 'task_list';
		require 'task_list.php';
	}
}
else{
	$page_title = '任务分类 创意任务大全_IT帮手网';
	$page_keyword = '任务分类，创意任务大全';
	$page_description = 'IT帮手网是威客和雇主最信赖的威客网站，中国最有价值的创意交易平台,提供LOGO设计、包装设计、微博营销、网店推广、网站建设、装修设计、广告语征集等创意交易类威客任务，是领先的新型威客网站！';
	require keke_tpl_class::template ( $do );
}
