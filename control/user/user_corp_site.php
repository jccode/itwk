<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$user_info = kekezu::get_user_info ( $uid );
 
 //判断是否实名认证
//$user_info['auth_realname']!=1 and kekezu::show_msg('必须先通过身份认证才能进行此操作',"index.php?do=user&view=setting&op=auth&auth_code=realname",'3','','warning');
$table_obj = keke_table_class::get_instance ( "witkey_corp_site" );
$step_list = array (
		"step1" => array (
				$_lang ['step_one'],
				$_lang ['complete_bank_account_info'] 
		),
		"step2" => array (
				$_lang ['step_two'],
				$_lang ['account_setting_successful'] 
		) 
);

$ac_url = $origin_url . "&op=$op&opp=$opp";
$step=$step?$step:'step1';
switch ($step) {
	case "step1":
		$field="*";
		$table="witkey_corp_site";
		$where="uid=".$uid;
		$sql="select ".$field." from ". TABLEPRE ."witkey_corp_site where ".$where;
	    //提交验证表单
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			if($corp_site=db_factory::execute($sql)){
				kekezu::show_msg ( "操作提示", $ac_url . "&step=step1", '1', '您已经认证过！', 'alert_right' ) ;
			}
		    $params="email=".$conf['ep_username'];
		    //发送至爽购网认证
		    ob_start();
		    $songogo_rs=file_get_contents("http://www.epweike.cn/phone.php?".$params);
		    ob_clean();
		    if($songogo_rs){			
		    	$data['uid'] = $uid;
				$data['corp_site'] = $opp;
				$data['corp_site_username'] = $conf['ep_username'];
				$data ['on_time'] = time ();
				$data = kekezu::escape ( $data );
				$corp_site_id = $table_obj->save ( $data );
				if ($corp_site_id) {
					kekezu::show_msg ( "操作提示", $ac_url. "&step=step1", '1', '认证成功！', 'alert_right' ) ;
				} else {
					kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '系统繁忙请稍后！', 'alert_error' ) ;
				}
			}else{
		    	kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '认证失败请重新认证！', 'alert_error' ) ;
		   }
		}else{//如果添加，显示已添加的帐号
			$corp_site=db_factory::query($sql);
		}
}
require keke_tpl_class::template ( "user/" . $do . "_" . $op . "_" . $opp );