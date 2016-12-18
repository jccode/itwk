<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/*获取绑定列表*/
$bank_arr=keke_glob_class::get_bank();//银行列表
$ac_url=$origin_url."&op=$op&opp=$opp";
if($rebind){
	if(intval($bank_id)){
		if($bank_a_id){
			 db_factory::execute(sprintf(" delete from %switkey_auth_bank where bank_a_id='%d'",TABLEPRE,intval($bank_a_id)));
			 db_factory::execute(sprintf(" delete from %switkey_auth_record where ext_data='%d'",TABLEPRE,intval($bank_a_id)));
		}
		db_factory::execute(sprintf(" delete from %switkey_member_bank where bank_id='%d'",TABLEPRE,intval($bank_id)));
		
		kekezu::show_msg ( "操作提示", $ac_url, '1', '解绑成功！', 'alert_right' ) ;
	}else{
		kekezu::show_msg ( "操作提示", $ac_url, '1', '系统繁忙请稍后！', 'alert_error' ) ;
	}
}else{
	$account_list = db_factory::query(sprintf(" select * from %switkey_member_bank where uid = '%d' and bind_status='1'",TABLEPRE,$uid));
	$auth_list    = kekezu::get_table_data('bank_a_id,bank_id',"witkey_auth_bank"," uid='$uid' and auth_status!=2",'','','','bank_id',null);


}
require keke_tpl_class::template ( "user/" . $do . "_" . $op."_".$opp );