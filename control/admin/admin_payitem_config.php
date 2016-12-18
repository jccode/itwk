<?php
/**
 * 增值服务的后台配置路由,跳到对应服务项文件夹中的control层中
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-11-5-下午02:03:21
 * @version V2.0
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$payitem=keke_payitem_class::get_payitem_config(null,null,$item_code,0);
$payitem_type = keke_glob_class::get_payitem_type();
$ac=='download' and keke_file_class::file_down($file_name, $file_path);
if($sbt_edit){
	$payitem_obj=keke_table_class::get_instance("witkey_payitem");
	is_array($model_code) and $fds['model_code']=implode(",",$model_code);
	
	$fds['small_pic']=$hdn_small_pic ? $hdn_small_pic : '';
	$fds['small_pic']!=$payitem['small_pic'] and keke_file_class::del_file($payitem['small_pic']);
	$fds['big_pic']= $hdn_big_pic ? $hdn_big_pic : '';
	
	$item_allow_cash and $fds['item_cash'] or $fds['item_cash'] = 0; //余额扣除
	$item_vipfree and $fds['vipfree']=1 or $fds['vipfree'] = 0; //vip免费
	$item_integral_cost and $fds['integral_cost'] or $fds['integral_cost'] = 0; //积分抵消
	
	$fds['big_pic']!=$payitem['big_pic'] and keke_file_class::del_file($payitem['big_pic']);
	$res=$payitem_obj->save($fds,$pk);
	if($res){ 
		 kekezu::admin_system_log($_lang['edit'].$payitem['item_name']);
		 kekezu::admin_show_msg($payitem['item_name'].$_lang['edit_success'],$_SERVER['HTTP_REFERER'],"3",'','success');
	}else 
		kekezu::admin_show_msg($payitem['item_name'].$_lang['edit_fail'],$_SERVER['HTTP_REFERER'],"3",'','warning');
}else{
	$model_list=$kekezu->_model_list;
    $code_arr=explode(",",$payitem['model_code']);
	
}
$kekezu->_cache_obj->gc();
require keke_tpl_class::template("/control/payitem/$item_code/tpl/admin_config");
function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){ return false;}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}
