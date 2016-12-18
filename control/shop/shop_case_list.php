<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//案例分类
$shop_cate=db_factory::get_table_data('*','witkey_shop_cate',"shop_id=".$shop_info['shop_id'],'cate_id asc','','','cate_id',0);

$url = $ac_url.'&view=case_list&cate_id='.$cate_id;
$where = "shop_id=".$shop_info['shop_id']." and case_name is not null";
isset($cate_id) && intval($cate_id) and $where .= " and cate_id=".$cate_id;
$case_obj = keke_table_class::get_instance ( "witkey_shop_case" );
$where .= " order by case_id DESC ";
//$case_list = db_factory::get_table_data("case_id,cate_id,case_name,case_pic",'witkey_shop_case',$where,'case_id desc','','','case_id',3600);
//var_dump($shop_cate);
$page and $page=intval ( $page ) or $page = 1;
$d = $case_obj->get_grid ( $where, $url, $page,16, null);
$case_list = $d [data];
$pages = $d [pages]; 
if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}