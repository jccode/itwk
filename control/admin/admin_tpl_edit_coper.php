<?php
/**
 * 友连编辑
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-9-1
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

kekezu::admin_check_role ( 30 );
//实例化合作对象
$table_name="witkey_coper";
$coper_obj = keke_table_class::get_instance($table_name);
//单条信息
if ($coper_id) {
	$coper_info = $coper_obj->get_table_info ( 'coper_id',$coper_id );
} 
//编辑
if ($sbt_edit) { //print_r($txt_location);exit;
	$txt_obj_type or $txt_obj_id = '';
	$status = $status ? $status : 0;
	$address&$data['address']=$address;
	$description&&$data['description']=$description;
	$email&&$data['email']=$email;
	$mobile&&$data['mobile']=$mobile;
	$name&&$data['name']=$name;
	$qq&&$data['qq']=$qq;
	$region&&$data['region']=$region;
	$status&&$data['status']=$status;
	$telephone&&$data['telephone']=$telephone;
	$type&&$data['type']=$type;
	$brand&&$data['brand']=$brand;
	if ($hdn_coper_id) {
		$pk=array('coper_id'=>$hdn_coper_id);
	 	$res = $coper_obj->save($data,$pk); //更新
		if ($res) {
			kekezu::admin_system_log ( $_lang['copers_edit'] . $hdn_coper_id );
			kekezu::admin_show_msg ( $_lang['copers_edit_success'], 'index.php?do=' . $do . '&view=coper&page='.$page,3,'','success' );
		}
	} else { 	
		$data['on_time']=time();
		$pk=array();
	 	$res = $coper_obj->save($data,$pk); //更新
		if ($res) {
			kekezu::admin_system_log ( $_lang['copers_add'] . $res );
			kekezu::admin_show_msg ( $_lang['copers_edit_success'], 'index.php?do=' . $do . '&view=coper&page='.$page,3,'','success' );
		}
	}
}
require $kekezu->_tpl_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );