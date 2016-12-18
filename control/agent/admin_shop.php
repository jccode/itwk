<?php

/**
 * 后台商铺管理入口
* @copyright keke-tech
* @author ch
* @version v 2.0
* 2012-6-26	
*/
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$views = array('list','info','service_list','service_edit','brand','brand_info','member_list','case_list','cate_list','match','upgrade','vip_list','vip_info','add_vip','domain');
(isset($view) && in_array($view,$views)) and $view or $view='list';

///VIP开关
if(in_array($ac,array('rm_vip','add_vip'))&&$u&&$s){
	$u = intval($u);//用户UID
	$s = strval($s);//店铺ID
	switch($ac){
		case 'rm_vip':
			$res = db_factory::execute(' update '.TABLEPRE.'witkey_shop set shop_level=1,isvip=0 where shop_id='.$s);
			if($res){
				db_factory::execute(' update '.TABLEPRE.'witkey_space set isvip=0,vip_start_time=0,vip_end_time=0,shop_level=1 where uid='.$u);
				kekezu::admin_system_log('客服#'.$admin_info['username'].'关闭店铺#'.$s.'的VIP属性');
			}
			$res and kekezu::echojson('VIP关闭成功',1) or kekezu::echojson('VIP关闭失败',0);
			break;
		case 'add_vip':
			$l or $l=2;
			$d or $d=1;
			$d *= 365;
			
			$h_obj = new Keke_witkey_vip_history_class();
			$h_obj->setRemark('客服#'.$admin_info['username'].'于后台开通店铺</br>#'.$s.'的VIP服务');
			$h_obj->setH_status( 1 );
			$h_obj->setUsername( '管理员添加' );
			$h_obj->setLevel_id( $l );
			$h_obj->setDay( $d );
			$h_obj->setStart_time( time() );
			$h_obj->setEnd_time( $d*24*3600 );
			$res = $h_obj->create_keke_witkey_vip_history();
			if($res){
				db_factory::execute(' update '.TABLEPRE.'witkey_shop set shop_level='.$l.',isvip=1 where shop_id='.$s);
				db_factory::execute(' update '.TABLEPRE.'witkey_space set isvip=1,shop_level='.$l.',vip_start_time='.time().',vip_end_time='.intval(time()+$d*24*3600).' where uid='.$u);
				kekezu::admin_system_log('客服#'.$admin_info['username'].'开通店铺#'.$s.'的VIP服务');
			}
			$res and kekezu::echojson('VIP开启成功',1) or kekezu::echojson('VIP开启失败',0);
			break;	
	}
}
if (file_exists ( ADMIN_ROOT . 'admin_shop_' . $view . '.php' )) {
	require ADMIN_ROOT . 'admin_shop_' . $view . '.php';
} else {
	kekezu::admin_show_msg ( $_lang['404_page'],'index.php?do=shop&view=list',3,'','warning' );
}