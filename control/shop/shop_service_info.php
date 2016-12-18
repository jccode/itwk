<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

 //服务信息
//$service_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_service where service_id = '$service_id'");
$service_indus_p_arr = $kekezu->_service_indus_p_arr;
$indus_p_arr = $kekezu->_indus_p_arr;
$indus_arr = array_merge($service_indus_p_arr,$indus_p_arr);
$indus_new_arr = array();
foreach($indus_arr as $k=>$v){
     $indus_new_arr[$v[indus_id]] = $v; 
}
$indus_ids = db_factory::query("select indus_pid from ".TABLEPRE."witkey_service where shop_id=".$sid." order by service_id desc");
$indus_ids = array_unique_fb($indus_ids); 
//收藏次数
$favor_num = db_factory::get_count("select count(f_id) count from ".TABLEPRE."witkey_favorite where obj_id=$service_info[service_id]");

//服务链接
$url = $ac_url.'&view=service_list';
//推广链接
//$prom_url = $_K['siteurl']."/index.php?do=shop&amp;view=service_info&amp;sid=$service_info[shop_id]&amp;service_id=$service_info[service_id]";
$prom_url = $_K['siteurl'].'/shop-view-service_info-sid-'.$service_info[shop_id].'-service_id-'.$service_info[service_id].'.html';
$prom_content = "#微博赚钱#来接活吧，这里有一个 {$service_info['title']}服务，还等什么？轻轻一点，轻松赚钱！[耶] (来自@IT帮手网)";

 //其他推荐服务 
$istop_service_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_service where shop_id = '$shop_info[shop_id]' AND is_top = 1 ORDER BY service_id DESC LIMIT 4");

//更新浏览次数
if($shop_info['uid'] != $uid){
	db_factory::execute ( " update " . TABLEPRE . "witkey_service set views = views + 1 where uid = '$shop_info[uid]'" );
}
$indus = $service_info['indus_pid'];
//关注最多的10条服务
$focus_service_arr = db_factory::query("select * from ".TABLEPRE."witkey_service where shop_id = '$shop_info[shop_id]' order by focus_num desc limit 0,10");

if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}

//获取行业分类名
function get_industry_name($indus_id){	
	$indus_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_industry where indus_id = '$indus_id'");
	
	return $indus_info['indus_name'];
}
function array_unique_fb($array2D){    
     foreach ($array2D as $v){       
         $v = join(",",$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串  
         $temp[] = $v;  
     }        
     $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组  
     foreach ($temp as $k => $v)  
     {  
         $temp[$k] = explode(",",$v);   //再将拆开的数组重新组装  
     }  
    return $temp;  
}