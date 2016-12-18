<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');

intval($page) or $page = 1;
$url_str = "index.php?do=$do&view=$view&op=$op&opp=$opp";
$msg_obj = keke_table_class::get_instance("witkey_msg");
$msg_m = new Keke_witkey_msg_class();


/*删除动作*/
if($ac=='del'&&$msg_id){
     $msg_m->setMsg_status(1);
	 $msg_m->setMsg_id($msg_id);
	 $res = $msg_m->edit_keke_witkey_msg();

    $res and kekezu::show_msg ( "操作提示", $url_str."&page=$page", '1', '删除成功！', 'alert_right' ) ; 
}elseif($ckb){
   $res = $msg_obj->del("msg_id", array_filter($ckb));
   if($ckb){
	   	$sql = "update ".TABLEPRE."witkey_msg set msg_status=1 where msg_id in(".implode(',', $ckb).")";
		$res = db_factory::execute($sql);
	    $res and  kekezu::show_msg ( "操作提示", $url_str."&page=$page", '1',$_lang['delete_selected_success'], 'alert_right' ) ;
	  
   }else{

    	kekezu::show_msg ( "操作提示", $url_str."&page=$page", '1', '系统繁忙请稍后！', 'alert_error' ) ;
   }
}else{
	$where.="and uid=$uid and msg_status!=1 ";
	$res = $msg_obj->get_grid($where, $url_str, $page,12);
	$data = $res['data'];
	$pages = $res['pages'];
}
require keke_tpl_class::template ( "user/" . $do . "_".$view."_$op");


