<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');

$ops = array('inbox','outbox','send');
$opps= array('system','inbox');
 $msg_obj = new keke_table_class('witkey_msg');

$sub_nav=array(
			array("write"=>array( $_lang['write_message'],"doc-edit")),
			array("accept"=>array( $_lang['inbox'],"contact-card"),
			   	"output"=>array( $_lang['outbox'],"cc")));
$msg_type = $msg_type?$msg_type:"system";
//系统消息
$sql = "select * from ".TABLEPRE."witkey_msg where ";
$where  = "1=1 ";

$s_sql .= $where." and uid<1 and to_uid=".intval($uid);
$a_sql .= $where." and to_uid = ".intval($uid)." and uid>0";
$o_sql .= $where." and uid = ".intval($uid)." and msg_status<1";


$s_n = db_factory::get_count("select count(msg_id) c from ".TABLEPRE."witkey_msg where ".$s_sql.' and view_status=0',0,'c',120);
$o_n = db_factory::get_count("select count(msg_id) c from ".TABLEPRE."witkey_msg where ".$o_sql.' and view_status=0',0,'c',120);
$a_n = db_factory::get_count("select count(msg_id) c from ".TABLEPRE."witkey_msg where ".$a_sql.' and view_status=0',0,'c',120);

switch ($msg_type){
	case "system"://系统消息
			$where=$s_sql;
		break;
	case "output"://已发短信
			$where=$o_sql;
		break;
	case "accept"://已收短信
			$where=$a_sql;
		break;

	case "write"://写短信
			require 'user_message_send.php';
			die();
		break;

}

//查询
$p_s = $p_s ? $p_s : 30;
$where .= " order by msg_id desc ";
$url = "index.php?do=$do&view=$view&op=$op&msg_type=$msg_type";
$page = $page ? $page : 1;
$count = db_factory::get_count("select count(msg_id) from ".TABLEPRE."witkey_msg where ".$where);
$pages = $kekezu->_page_obj->getPages ( $count, $p_s, $page, $url );
$data = db_factory::query($sql.$where.$pages[where]);

$pl_del and $msg_id =  $ckb;

switch ($op) {
	case 'del':  //删除和批量删除
		if($msg_type=='system'||$msg_type=='accept'){
				$res = $msg_obj->del("msg_id",$msg_id);
		}else{
			is_array($msg_id) and $msg_id = implode(",", $msg_id);
			$res = db_factory::execute("update ".TABLEPRE."witkey_msg set msg_status=1 where msg_id in ($msg_id)");//改变短信状态值
		}
		
	
		$res and	kekezu::show_msg ( "操作提示", "index.php?do=$do&view=$view&msg_type=$msg_type", '1', '删除成功！', 'alert_right' ) ;
		die();
	break;

	case "views"://查看

			$msg_id and $msg  = $msg_obj->get_table_info("msg_id", $msg_id);
			if($uid==$msg['to_uid']&&$msg['view_status']<1){
				db_factory::execute("update ".TABLEPRE."witkey_msg set view_status=1 where msg_id = ".intval($msg_id));
			}
			require keke_tpl_class::template ( "user/user_message_view");die();

		break;
}



if (isset ( $check_username ) && ! empty ( $check_username )) {
	$res =  keke_user_class::get_user_info($check_username,1);
	if($res){
		echo true;
	}else{
		echo "用户名不存在";
	}
	die ();
}

require keke_tpl_class::template ( "user/" . $do . "_".$view."_system");