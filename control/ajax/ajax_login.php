<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$uid and kekezu::show_msg('非法操作', '', 3, '您已是登录状态！', 'warning');

$title = 'IT帮手网 - 登录';
require $template_obj->template ( 'ajax/ajax_login' );