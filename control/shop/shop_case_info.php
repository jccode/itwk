<?php
/**
 * @copyright keke-tech
 * @author Aaron
 * @version v 2.0
 * 2012-2-25早上9:50
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//案例分类
$shop_cate=db_factory::get_table_data('*','witkey_shop_cate',"shop_id=".$shop_info['shop_id'],'cate_id asc','','','cate_id',3600);


//当前案例详情
if(isset($case_id)){
	$case_info=db_factory::get_one("select * from ".TABLEPRE."witkey_shop_case where case_id=$case_id");
}else{
	kekezu::show_msg("操作提示",$_K['siteurl']."/index.php?do=shop&sid=$sid&view=case_list",3,"案例参数有误，请返回列表页重试！","warning");
}
//读取前后案例数据
$where = "and shop_id=".$shop_info['shop_id']." and case_name is not null";
$case_up_info = db_factory::get_one(sprintf("select case_id,cate_id,case_name,case_pic from %switkey_shop_case  where case_id<'$case_id'  %s order by case_id desc limit 0,1",TABLEPRE,$where));
$case_down_info = db_factory::get_one(sprintf("select case_id,cate_id,case_name,case_pic from %switkey_shop_case  where case_id>'$case_id' %s order by case_id asc limit 0,1",TABLEPRE,$where));
//var_dump(sprintf("select case_id,cate_id,case_name,case_pic from %switkey_shop_case  where case_id<'$case_id'  %s order by case_id desc limit 0,1",TABLEPRE,$where));
$cate_id = $case_info['cate_id'];
if($shop_info['shop_level']==3){
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}_flagship" );
}else{
	require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
}