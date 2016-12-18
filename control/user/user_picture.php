<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$pic_id and $pic_id= intval($pic_id);
/**
 * 三级菜单
 */
$third_nav=array('choose'=>$_lang['select_head_pic'],'upload'=>$_lang['upload_head_pic']);
if($kekezu->_sys_config['user_intergration']==2){
	unset($third_nav['choose']);
	$show="upload";
	include 'keke_client/ucenter/client.php';
	$user_swf =uc_avatar($uid);/**用户头象上传flash代*/
}else{
	$show or $show="choose";
	$user_swf =keke_user_avatar_class::avatar_html($uid);/**用户头象上传flash代*/
}


// $show or $show="choose";



/**
 * 系统头象列表默认有20个
 */
for($i=1;$i<21;$i++){
   $sys_pic[$i] =  $i;
}
if($ac=='set_pic'){
	$url = "index.php?do=$do&view=$view&op=$op&show=$show";
	
    abs(intval($pic_id)) and   $id = keke_user_avatar_class::set_user_sys_pic($uid, $pic_id);
    if($id){
    	$kekezu->_cache_obj->del ( "keke_witkey_member_ext" );
    	kekezu::show_msg ( "操作提示", $url, '1', '编辑成功！', 'alert_right' ) ;
    }else{
    	kekezu::show_msg ( "操作提示", $url, '1', '系统繁忙请稍后！', 'alert_error' ) ;
    }
}
 
require keke_tpl_class::template ( "$do/" . $do . "_" . $op );


