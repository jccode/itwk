<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


//异步检查
if (isset ( $check_domain ) && ! empty ( $check_domain )) {
//	$res = keke_shop_class::check_domain ( $check_domain );
//	echo  $res;
//	die ();
	if (!epweike_seo_class::domain_key_check($check_domain)){
		echo "该域名名称为系统目录，不可使用";
	}
	elseif(db_factory::get_one("select * from ".TABLEPRE."witkey_shop_domain where d_key='$check_domain' and uid != '$uid' ")){
		echo "该域名已被别人使用";
	}
	else{
		echo true;
	}
	die();
}

$userinfo = kekezu::get_user_info($uid);


//读取当前用户可能有过的域名申请
$mydomain_record = db_factory::get_one("select * from ".TABLEPRE."witkey_shop_domain where uid = '$uid'");

if (kekezu::submitcheck($formhash)) {
	//$shop_obj = keke_table_class::get_instance ( "witkey_shop" );
	//$conf['domain'] = kekezu::escape($domain);
	//$res = $shop_obj->save ($conf, $pk );
	
	$domain = $conf['domain'] = kekezu::escape($domain);
	
	//验证是否是禁用词
	if (!epweike_seo_class::domain_key_check($domain)){
		kekezu::show_msg("不合法的域名设置","index.php?do=user&view=space&op=domain",3,"该域名名称为系统目录，不可使用");
	}
	
	$domain_exsit = db_factory::get_one("select * from ".TABLEPRE."witkey_shop_domain where d_key='$domain' ");
	if ($domain_exsit){
		kekezu::show_msg("已被占用","index.php?do=user&view=space&op=domain",3,"该域名已被别人使用");
	}
	
	$d_type >0 and !$userinfo['isvip'] and kekezu::show_msg("权限不在","index.php?do=user&view=space&op=domain",3,"只有vip用户能设置2级域名");;
	
	if ($mydomain_record){
		//编辑
		$res = db_factory::updatetable(TABLEPRE."witkey_shop_domain", array('d_key'=>$domain,'d_status'=>0,'d_type'=>$d_type,'on_time'=>time(),'op_time'=>0), array('uid'=>$uid));
	}
	else{
		//创建新记录
		$insertsqlarr = array(
			'd_key'=>$domain,
			'shop_id'=>$userinfo['shop_id'],
			'uid'=>$uid,
			'username'=>$username,
			'd_status'=>0,
			'd_type'=>$d_type,
			'on_time'=>time(),
			'op_time'=>0
		);
		$res = db_factory::inserttable(TABLEPRE."witkey_shop_domain", $insertsqlarr,1);
	}
	
	
	
	if($res){
		kekezu::show_msg ( $_lang['operate_notice'],"index.php?do=user&view=space&op=domain", 3,'个性域名设置成功！', 'success');
	}
		
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );