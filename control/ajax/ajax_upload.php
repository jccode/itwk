<?php
/**
 * @copyright keke-tech
 * @author jie
 * @version v 2.0
 * 2010-6-5下午04:40:43
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');
$upload_obj=keke_ajax_upload_class::get_instance($_SERVER['QUERY_STRING']);
switch ($upload_obj->_file_type){
	case 'sys'://系统附件上传
	case 'editor'://编辑器
	case 'att'://上传附件
		
		if($maxsize){
			
			$upload_obj->upload_file($maxsize);
		}
		else{
			$upload_obj->upload_file();
		}
		break;
	case 'big'://上传大文件
		$upload_obj->upload_big_file();
		break;
	case 'service'://上传图片，会自动剪切成大中小
		$upload_obj -> upload_and_resize_pic();
		break;
}