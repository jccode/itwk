<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index', 'link', 'pic_list','pic_info');
in_array ( $view, $views ) and $view or $view = 'index';
$nav_active_index = "prom";
$kekezu->init_prom(); //初始化推广
$kekezu->_prom_obj->create_prom_cookie($epi,$_SERVER['QUERY_STRING']);//产生推广cookie记录
 //推荐热图推广
$prom_style_obj = new Keke_witkey_prom_style_class();  
$prom_style_obj->setWhere ( ' s_recommend = 1 AND s_type = "image" AND s_status = 1 limit 0,4 ' ); 
$prom_style_recommend_arr = $prom_style_obj->query_keke_witkey_prom_style();
$income = $kekezu->_prom_obj->prom_income($uid);
//feed
$feed_list= db_factory::query('select title,feed_time,feedtype from '.TABLEPRE.'witkey_feed where feedtype="prom_task_bid" order by feed_time desc limit 0,4',1,3600);

require $do.'/'.$do.'_' . $view . '.php';