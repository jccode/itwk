<?php
/**
 * 短信发送记录
 * @author Aaron
 * @version v 1.3
 * 2012-06-13 11:53:12
 */

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 182 ); //权限
$msg_obj = new Keke_witkey_msg_class ();

//分页 
$w ['page_size'] and $page_size = intval ( $w ['page_size'] ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = '1';
$url = "index.php?do=$do&view=$view&w[messageid]=$w[messageid]&w[title]=$w[title]&w[targetno]=$w[targetno]&w[sendflag]=$w[sendflag]
&w[start_intime]=$w[start_intime]&w[end_intime]=$w[end_intime]&w[start_outtime]=$w[start_outtime]&w[end_outtime]=$w[end_outtime]";

//条件
$where = " where messagetype = 'email'";
$w ['messageid'] and $where .= " and messageid = '{$w[messageid]}'";
$w ['title'] and $where .= " and title like '%{$w[title]}%'";
$w ['targetno'] and $where .= " and targetno ='{$w[targetno]}'";
$w ['sendflag'] and $where .= " and sendflag ='{$w[sendflag]}'";
$w ['start_intime'] and $where .= " and intime >= ".strtotime($w[start_intime]);
$w ['end_intime'] and $where .= " and intime <= ".strtotime($w[end_intime]);
$w ['start_outtime'] and $where .= " and outtime >= ".strtotime($w[start_outtime]);
$w ['end_outtime'] and $where .= " and outtime <= ".strtotime($w[end_outtime]);
is_array($w['ord']) and $where .= ' order by '.$w['ord'][0].' '.$w['ord'][1] or $where .= "order by messageid desc "; //排序 

$sql_count = "select count(*) from ".TABLEPRE."messqueue ";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);

$sql = "select * from ".TABLEPRE."messqueue ";
$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
$sql .= $where.$pages['where'];
$messqueue_arr = db_factory::query($sql);	

require $template_obj->template('control/admin/tpl/admin_'.$do.'_'.$view);