<?php
 /**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * @说明   纯粹的模板操作过渡端. 目的只是饶开过滤
 * 2010-5-25上午09:39:42
 */

 
$tpl_mode = 1;
 
define('ADMIN_KEKE',TRUE);
require '../../app_comm.php';

define('ADMIN_ROOT',S_ROOT.'./control/admin/');//后台根目录
 
$_K['admin_tpl_path']= S_ROOT.'./control/admin/tpl/';//后台模板目录

if ($do == 'previewtag')
{

	$tagid = intval($tagid);
	if (!$tagid){
		die();
	}
	$taglist = kekezu::get_tag(1);
	$tag_info = $taglist[$tagid];
	//var_dump($taglist);die();
	//预览feed
	if($tag_info['tag_type']==8){
		keke_loaddata_class::preview_feed($tag_info);
	}else if($tag_info['tag_type']==9){
		keke_loaddata_class::preview_addgroup($tag_info['tagname']);
	}//预览广告
    else{
	keke_loaddata_class::previewtag($tag_info);
	}//预览其他
}

