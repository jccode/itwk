<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

switch($ac){
	case 'title':
		$title = '如何写好任务标题？';
	break;
	case 'desc':
		$title = '看看别人怎么写的';
	break;
	case 'file':
		$title = '上传文件说明';
	case'fl':
		$title = '请选择分类';
	break;
}

require $template_obj->template ( 'ajax/ajax_release' );