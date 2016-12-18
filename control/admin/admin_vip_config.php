<?php
/**
 * @copyright keke-tech
 * @author tank
 * 
 * 2012-5-23 
 */
defined ( 'ADMIN_KEKE' ) or die ( 'Access Denied' );
kekezu::admin_check_role ( 156 );

$type or $type = 'level';

//等级模式
if ($type=="level"){
	switch ($op){
		case 'edit':
			$level_id and $level_info = db_factory::get_one("select * from ".TABLEPRE."witkey_vip_level where level_id='$level_id'");
			$price_config = array();
			$rule_config = array();
			$level_info['price_config'] and $price_config = unserialize($level_info['price_config']);
			$level_info['rule_config'] and $rule_config = unserialize($level_info['rule_config']);
			
			$special_list = kekezu::get_table_data("*","witkey_vip_special");
			
			//编辑提交
			if ($sbt_edit){
				$level_obj = new Keke_witkey_vip_level_class();
				$level_obj->setLevel_name($txt_level_name);
				$level_obj->setAllow_buy($rdo_allow_buy);
				$level_obj->setListorder($txt_listorder);
				$level_obj->setBrand($brand);
				if ($price_arr){
					$temp = array();
					foreach ($price_arr as $p){
						if ($p['month']&&$p['price'])
						{
							$temp[] = $p;
						}
					}
					
					$level_obj->setPrice_config(serialize($temp));
				}
				$rule_list or $rule_list = array();
				$level_obj->setRule_config(serialize($rule_list));
				
				if ($level_id){//编辑
					$level_obj->setWhere("level_id = '$level_id'");
					$level_obj->edit_keke_witkey_vip_level();
				}
				else {//添加
					$level_obj->create_keke_witkey_vip_level();
				}
				//$file_obj->delete_files(S_ROOT."./data/data_cache/");
				kekezu::show_msg('操作成功',"index.php?do=vip&view=config&type=level");
			}
			require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view.'_'.$type.'_edit' );die();
			
		break;
		case 'delete':
			$level_obj = new Keke_witkey_vip_level_class();
			$level_obj->setWhere("level_id = '$level_id'");
			$level_obj->del_keke_witkey_vip_level();
			//$file_obj->delete_files(S_ROOT."./data/data_cache/");
			kekezu::show_msg('删除成功',"index.php?do=vip&view=config&type=level");
		break;
	}
	
	$level_list = db_factory::get_table_data("*","witkey_vip_level");//列表数据
	require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view.'_'.$type );
}
//权限项管理
elseif($type=="special"){
switch ($op){
		case 'edit':
			$sp_id and $sp_info = db_factory::get_one("select * from ".TABLEPRE."witkey_vip_special where sp_id='$sp_id'");
			
			//编辑提交
			if ($sbt_edit){
				$temp_str = '';
				$sp_id and $temp_str.=" and sp_id!='$sp_id' ";
				kekezu::get_table_data('sp_id',"witkey_vip_special","sp_key = '{$txt_sp_key}' $temp_str") and kekezu::admin_show_msg('操作失败',"index.php?do=$do&view=$view&type=$type&sp_id=$sp_id&op=$op",3,'该标识已存在','warning');
				
				$sp_obj = new Keke_witkey_vip_special_class();
				$sp_obj->setSp_name($txt_sp_name);
				$sp_obj->setSp_desc($tar_sp_desc);
				$sp_obj->setSp_key($txt_sp_key);
				//$sp_obj->setSp_key($txt_sp_key);
				if ($sp_id){//编辑
					$sp_obj->setWhere("sp_id = '$sp_id'");
					$sp_obj->edit_keke_witkey_vip_special();
				}
				else {//添加
					$sp_obj->create_keke_witkey_vip_special();
				}
				$file_obj->delete_files(S_ROOT."./data/data_cache/");
				kekezu::show_msg('操作成功',"index.php?do=vip&view=config&type=special");
			}
			require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view.'_'.$type.'_edit' );die();
			
		break;
		case 'valid_sp_key':
			if (kekezu::get_table_data('*','witkey_vip_special',"sp_key = '$sp_key'")){
				echo 'exsit';
			}
			die();
			break;
		case 'delete':
			$sp_obj = new Keke_witkey_vip_special_class();
			$sp_obj->setWhere("sp_id = '$sp_id'");
			$sp_obj->del_keke_witkey_vip_special();
			$file_obj->delete_files(S_ROOT."./data/data_cache/");
			kekezu::show_msg('删除成功',"index.php?do=vip&view=config&type=special");
		break;
	}
	
	$special_list = db_factory::get_table_data("*","witkey_vip_special");
	require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view .'_'.$type);
}


