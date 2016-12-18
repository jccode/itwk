<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


$service_indus_p_arr = $kekezu->_service_indus_p_arr;
$indus_p_arr = $kekezu->_indus_p_arr;
$indus_arr = array_merge($service_indus_p_arr,$indus_p_arr);
$indus_new_arr = array();
foreach($indus_arr as $k=>$v){
     $indus_new_arr[$v[indus_id]] = $v; 
}
//推荐服务
$service_recommed_arr = db_factory::query("select * from ".TABLEPRE."witkey_service where shop_id=".$sid." and is_top=1 order by service_id desc 
limit 0,3");
$indus_ids = db_factory::query("select indus_pid from ".TABLEPRE."witkey_service where shop_id=".$sid." order by service_id desc");

$indus_ids and $indus_ids = array_unique_fb($indus_ids); 
$page and $page=intval ( $page ) or $page = 1;
$url = $ac_url.'&view=service_list&indus='.$indus;
$where = " shop_id = '$shop_info[shop_id]'";
$indus and $where.=" and indus_pid=".$indus;
$service_obj = keke_table_class::get_instance ( "witkey_service" );
$service_type and $where .= "  and service_type = ".$service_type;
$where .= " order by service_id DESC ";

$d = $service_obj->get_grid ( $where, $url, $page,12, null);
$service_arr = $d [data];
$pages = $d [pages]; 
//}
if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
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
 
