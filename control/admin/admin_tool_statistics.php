<?php
/**
 * 附件管理
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-5-19早上0:54:00
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 195 );

//时间起范围
$start_time = date ( 'Y-m-d 00:00:00', time () ) ; //取今天0点0分
$end_time =date ( 'Y-m-d 00:00:00', time ()+24*3600 ) ;

$data_list = keke_admin_class::daily_statistics ();

$submit = unserialize($data_list['submit']); //提交统计
$success = unserialize($data_list['success']); //成功统计
$conversion = unserialize($data_list['conversion']); //转换率统计
$complete = unserialize($data_list['complete']); //完成统计
$additional =unserialize($data_list['additional']); //新增统计

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );
