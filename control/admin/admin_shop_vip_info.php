<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-09-29 15:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role (196 );
$user_info = db_factory::get_one(sprintf("select * from %switkey_space where shop_id=%s",TABLEPRE,$shop_id));
$shop_info = db_factory::get_one(sprintf("select s.*,l.level_name,l.level_name_tw from %switkey_shop as s, %switkey_vip_level as l where s.shop_level=l.level_id and s.shop_id=%s",TABLEPRE,TABLEPRE,$shop_id));
$shop_info['shop_info'] = unserialize($shop_info['shop_info']);

//保存编辑
if($sbt_edit){
	kekezu::admin_system_log($_lang['edit_user_shop_info']."[".$shop_info['shop_id']."]".$shop_info['shop_name']);

	$shop_obj = keke_table_class::get_instance('witkey_shop');
	$service=kekezu::escape($shop);
	$res = $shop_obj->save($shop,array("shop_id"=>$shop_id));
	if($res==0 || $res==1){
		//更新space表数据
		$space_obj = new Keke_witkey_space_class();
		$space_obj->setWhere("uid=".$shop_info['uid']);
		$space_obj->setShop_name($shop['shop_name']);
		$space_obj->edit_keke_witkey_space();
		kekezu::admin_show_msg($_lang['shop_edit_success'],"index.php?do=$do&view=vip_list",2,$_lang['shop_edit_success'],'success');
	}else{
		kekezu::admin_show_msg($_lang['shop_edit_fail'],"index.php?do=$do&view=vip_edit&shop_id=".$shop_id,2,$_lang['shop_edit_fail'],'warning');
	}
}elseif($op == 'listorder') {
	$shop_obj->setWhere ( "shop_id='$shop_id'" );
	$shop_obj->setListorder ( $value ? $value : 0 );
	$shop_obj->edit_keke_witkey_shop ();
	die ();
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );