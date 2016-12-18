<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
//echo 'sasa';exit;
//检查用户信息是否完善
kekezu::check_user_info();

 //异步检查
if (isset ( $check_id_card ) && ! empty ( $check_id_card )) {
	$dt = db_factory::get_count(" select id_card from " . TABLEPRE . "witkey_auth_realname where id_card='".$check_id_card."' and uid != '$uid' and auth_status =1");
	if($dt){
		echo "该身份证号码已被他人占用";
	}else{
		$dt = db_factory::get_count(" select id_card from " . TABLEPRE . "witkey_auth_realname where id_card='".$check_id_card."' and uid != '$uid'");
		if($dt){
			echo "该身份证号码已被他人申请认证";
		}else{
			echo 1;
		}		
	}

	die ();
}

if (isset ( $check_shop_name ) && ! empty ( $check_shop_name )) {
	$dt = db_factory::get_count(" select shop_name from " . TABLEPRE . "witkey_shop where shop_name='".$check_shop_name."' and uid != '$uid'");
	if($dt){
		echo "该名称已被他人占用";
	}else{
		echo 1;
	}

	die ();
}

if (kekezu::submitcheck($formhash)) { 
	if( $shop_type && $shop_info['shop_type'] && $shop_info['shop_type'] > $shop_type ) {
		kekezu::show_msg ( "非法操作", $_SERVER['HTTP_REFERER'], '1', '您要降级商铺请与管理员联系。', 'alert_error' ) ;
	}
	
	if($shop_info['shop_type'] < $shop_type){
	
		 //商铺类型
		$shop_type and $shop_type = intval($shop_type);
		if( in_array($shop_type, array(1,2)) ){
			$conf ['shop_type'] = $shop_type; 
		}
		
		 //填写个人、工作室
		
		if(in_array($shop_type, array(1,2))){
			empty($fds['zone']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请先选择您的身份证所在地区！', 'alert_error' ) ;
			
			empty($_FILES['id_pic']['name']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请上传身份证复印件！', 'alert_error' ) ;
			($_FILES['id_pic']['error'] > 0) and kekezu::show_msg ( "操作提示", '', '1', '文件上传有误,可能是您的文件过大！', 'warning' ) ;
			empty($_FILES['id_pic_back']['name']) and kekezu::show_msg ( "操作提示", $_SERVER['HTTP_REFERER'], '1', '请上传身份证复印件(背面)！', 'alert_error' ) ;
			($_FILES['id_pic']['error'] > 0) and kekezu::show_msg ( "操作提示", '', '1', '文件上传有误,可能是您的文件过大！', 'warning' ) ;
			
		 	 //未申请认证情况下可添加进去
		 	$auth_realname_log = db_factory::get_one(" select * from " . TABLEPRE . "witkey_auth_realname where uid='$uid'");
			if(!$auth_realname_log){
				keke_lang_class::package_init ( 'auth' );
				keke_lang_class::loadlang ( 'auth_add' );  
				$auth_obj = new keke_auth_realname_class ( 'realname' ); //初始化认证对象				 
				$auth_info = $auth_obj->get_user_auth_info ( $uid,0,$show_id); //用户认证信息;
				$auth_obj->add_auth($fds, 'id_pic', '', 'id_pic_back'); //认证申请提交
			}
			
			 //插入到商铺表
			$_FILES['id_pic']['name'] and $fds['id_pic'] = keke_file_class::upload_file('id_pic');  //身份证正面
			$_FILES['id_pic_back']['name'] and $fds['id_pic_back'] = keke_file_class::upload_file('id_pic_back');  //身份证反面
			$fds['sex'] = $sex;
			$conf['shop_info'] = serialize($fds);	
				
		}elseif($shop_type == 3){ //公司企业
			$_FILES['company_card_pic']['name'] and $company['company_card_pic'] = keke_file_class::upload_file('company_card_pic'); //营业执照图片
			$company['company_card_pic'] and keke_file_class::del_att_file('', $shop_info['shop_info']['company_card_pic']); 
			$company['company_card_pic'] or $company['company_card_pic'] = $shop_info['shop_info']['company_card_pic'];
			$conf['shop_info'] = serialize($company);
		}
	}
	
	if(!$shop_info['shop_id']){		
		$conf ['uid'] = $uid;
		$conf ['username'] = $username;
		$conf ['shop_level'] = 1; 
		$conf ['on_time'] = time();
	}

	$conf ['shop_name'] = kekezu::escape($shop_name);	
	$conf ['shop_desc'] = kekezu::escape($shop_desc);	
	
	/*
	 *  //删除原图
	$auth_realname = db_factory::get_one(" select * from " . TABLEPRE . "witkey_auth_realname where uid='$uid'");
	($shop_info['shop_info']['id_pic'] != $auth_realname['id_pic']) and keke_file_class::del_att_file('', $shop_info['shop_info']['id_pic']); 
	($shop_info['shop_info']['id_pic_back'] != $auth_realname['id_pic_back']) and keke_file_class::del_att_file('', $shop_info['shop_info']['id_pic_back']); 
	 * */
	
	 //保存到shop表
	$shop_obj = keke_table_class::get_instance ( "witkey_shop" );
	$res = $shop_obj->save ($conf, array('shop_id'=>$shop_info['shop_id']) );

	 //保存到space表
	if(!$shop_info['shop_id']){
		$info_arr['shop_id'] = $res;
		$info_arr['shop_level'] = 1;
	}
	
	  //如果申请的是公司商铺，那么先不填写
	$conf['shop_type'] and $info_arr['user_type'] = $conf['shop_type'];
	$info_arr['shop_name'] = $conf['shop_name'];
	db_factory::updatetable(TABLEPRE."witkey_space", $info_arr, array('uid'=>$uid));
	
	
	 //添加升级成为公司记录
	$upgrade_arr = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop_upgrade where uid = '$uid'");
	if(!$upgrade_arr && $shop_type == 3){
		$shop_info['shop_id'] and $res = $shop_info['shop_id'];
		$obj_shop_upgrade = new Keke_witkey_shop_upgrade_class();
		$obj_shop_upgrade->setUid($uid);
		$obj_shop_upgrade->setUsername($username);
		$obj_shop_upgrade->setUp_status(0);
		$obj_shop_upgrade->setOn_time(time());
		$obj_shop_upgrade->setShop_id($res);
		$obj_shop_upgrade->setCancel_shop_type($shop_info['shop_type'] ? $shop_info['shop_type'] : 1);
		$obj_shop_upgrade->setCancel_shop_info($shop_info['shop_info'] ? serialize($shop_info['shop_info']) : '');
		$obj_shop_upgrade->create_keke_witkey_shop_upgrade();
	}
	
	if(!$shop_info['shop_id']){
		kekezu::show_msg ( "操作提示", $ac_url , '1', '商铺开通成功！', 'alert_right' ) ;
	}else{				
		kekezu::show_msg ( "操作提示", $ac_url , '1', '商铺编辑成功！', 'alert_right' ) ;
	}
}

$upgrade_log = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop_upgrade where uid='$uid'"); 
if($upgrade_log){ 
	$shop_info['shop_type'] = 3; 
}

($shop_info['shop_type'] != 3) and $realname_zone_arr = keke_glob_class::get_realname_zone();  //身份证所在地区

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );