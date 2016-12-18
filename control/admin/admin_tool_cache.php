<?php
/**
 * 后台缓存清除
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19下午09:25:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
//kekezu::admin_check_role(20);
$file_obj = new keke_file_class();
$backup_patch = S_ROOT.'./data/tpl_c/';
if(isset($sbt_edit)){

	if($ckb_obj_cache){
	
		$res = $file_obj->delete_files(S_ROOT."./data/data_cache/");
		$msg = $_lang['object_cache_empty'];
	}
	if($ckb_tpl_cache){
		$res = $file_obj->delete_files($backup_patch);
		$msg.= $_lang['template_cache_empty'];
	}
	if(CACHE_TYPE!='file' && IS_CACHE ==1){
		$kekezu->_cache_obj->flush();
	}
	//kekezu::admin_system_log($_lang['cache_empty']);
	if($ajax&&$ajax==1){
		 
		kekezu::echojson('clear success',1);  
	}else{
		kekezu::admin_show_msg($msg,'index.php?do='.$do.'&view='.$view,2,'','success');
		
	}
	
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );