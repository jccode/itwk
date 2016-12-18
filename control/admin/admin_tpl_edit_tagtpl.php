<?php
/**
 * 编辑标签模板
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-5-26上午11:58:41
 */


defined ( 'ADMIN_KEKE' ) or 	exit ( 'Access Denied' );

kekezu::admin_check_role (29);
$filename = S_ROOT.'./control/admin/tpl/template_tag_'.$tplname.'.htm';
$code_content = "";

if (file_exists($filename)) {
	$fp=fopen($filename,"r"); 
	while (!feof($fp)) {   
		$code_content  .= fgets($fp);   
	}

	fclose($fp);
}

require_once $template_obj->template ( 'control/admin/tpl/admin_tpl_'.$view );