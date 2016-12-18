<?php
/**
 * 全部任务
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-14 11:36
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$ac_url=$_K['site_url']. $_SERVER["REQUEST_URI"];
//2012-09-21 URL重写，把“/taskbrand/m1/t1f1r1e1/s15-o2/page2.html?k=关键词”
//中的"m1/t1f1r1e1/s15-o2/page2.html?k=关键词"参数取出

if($ac=='urlrewrite'){
	$preg_search="/(m(\d+))?\/?(t([n12]))?(f(\d+))?(r(\d+))?(e(\d+))?\/?(s([135][05]))?\-?(o(\d+))?\/?(page(\d+))?(\.html)?(k\=(\w+))?/i";
	$preg_replace="$2,$4,$6,$8,$10,$12,$14,$16,$19";
	$preg_param=preg_replace($preg_search,$preg_replace,$param);

	$arr=explode(',',$preg_param);
	$m=$arr[0];
	$t=$arr[1];
	$f=$arr[2];
	$r=$arr[3];
	$e=$arr[4];
	$p_s=$arr[5];
	$o=$arr[6];
	$page=$arr[7];
	!$k and $k=$arr[8];
}
$page_title ="任务大厅-IT帮手网";

$cash_cove_arr = kekezu::get_cash_cove ();

$sql = "select task_id,model_id,task_status,task_title,task_cash,task_cash_coverage,indus_id,indus_pid,start_time,sub_time,end_time,view_num,work_num,pay_item,task_type, IF(FIND_IN_SET('top', pay_item)>0, 1, 0) sticky from " . TABLEPRE . "witkey_task  ";

$where = "where  task_type != 3  and task_status !=-1 and task_status !=0 and task_status !=1 and task_status !=10 ";
//增值服务列表
$item_list = kekezu::get_table_data ( 'item_code,big_pic,small_pic,model_code', 'witkey_payitem', ' is_open=1 and user_type="employer" ', '', '', '', 'item_code', 3600 );

//任务大类
if (isset ( $p )) {
	$where .= sprintf ( " and   indus_pid = %d", intval ( $p ) );
	
	//频道宣传
	$channel = kekezu::get_table_data ( "art_id,art_cat_id,art_title,pub_time", "witkey_article", " art_cat_id=590 and indus_id = " . intval ( $p ), " pub_time desc", "", "4", "", 86400 );

}
//任务小类
if (isset ( $c )) {
	$where .= sprintf ( " and   indus_id = %d", intval ( $c ) );
	
	//频道宣传
	$channel = kekezu::get_table_data ( "art_id,art_cat_id,art_title,pub_time", "witkey_article", " art_cat_id=590 and indus_id =" . intval ( $indus_c_arr [intval ( $c )] ['indus_pid'] ), "pub_time desc", "", "4", "", 86400 );
}
//任务模型
if (isset ( $m )) {
	switch (intval ( $m )) {
		case 1 :
			$where .= " and model_id = 1 ";
			;
			break;
		case 2 :
			$where .= " and model_id = 2 ";
			;
			break;
		case 3 :
			$where .= " and model_id = 3 ";
			;
			break;
		case 4 :
			$where .= " and model_id = 4 and task_type <2 and task_cash_coverage>0 ";
			;
			break;
		case 5 :
			$where .= " and model_id = 4 and task_type = 1 and ifnull(task_cash_coverage,0)<1 ";
			;
			break;
		case 6 :
			$where .= " and model_id = 4 and task_type = 3 ";
			;
			break;
	}
}
isset($t) or $t = 0;
//金额托管
if (isset ( $t )&&$t!=0) {
	$where .= sprintf ( " and   cash_status = %d", intval ( $t ) );
}
//金额额度
if (isset ( $f )) {
	switch (intval ( $f )) {
		case 1 :
			$where .= " and task_cash<=500 or task_cash_coverage=1 ";
			;
			break;
		case 2 :
			$where .= " and task_cash between 500 and 1000";
			;
			break;
		case 3 :
			$where .= " and task_cash between 1000 and 2000";
			;
			break;
		case 4 :
			$where .= " and task_cash between 2000 and 5000";
			;
			break;
		case 5 :
			$where .= " and task_cash between 5000 and 10000";
			;
			break;
		case 6 :
			$where .= " and task_cash between 10000 and 20000";
			;
			break;
		case 6 :
			$where .= " and task_cash >=20000";
			;
			break;
	}
}

//发布时间
if (isset ( $r )) {
	switch (intval ( $r )) {
		case 1 :
			$today = strtotime ( date ( 'Y-m-d 0:0:0', time () ) );
			$where .= " and start_time >" . $today;
			break;
		case 2 :
			//$yesterday = strtotime ( date ( 'Y-m-d 0:0:0', time () ) ) - 24 * 3600;
			$where .= " and DATE(FROM_UNIXTIME(start_time))='" .date('Y-m-d',time()-24*3600)."'";
			break;
		case 3 :
			$nearly_three_days = time () - 24 * 3 * 3600;
			$where .= " and start_time >" . $nearly_three_days;
			break;
		case 4 :
			$where .= " and yearweek(from_unixtime(start_time),1)=yearweek('".date('Y-m-d',time())."',1)";
			break;
		case 5 :
			$where .= " and year(from_unixtime(start_time))=year('".date('Y-m-d',time())."') "
					." and month(from_unixtime(start_time))=month('".date('Y-m-d',time())."')";
			break;
		case 6 :
			$nearly_three_month = time () - 24 * 90 * 3600;
			$where .= " and start_time >" . $nearly_three_month;
			;
			break;
	}
}

if (isset ( $e )) {
	switch (intval ( $e )) {
		case 1 :
			$within_a_week = time () + 7 * 24 * 3600;
			$where .= " and task_status = 2 and sub_time >=" . time () . " and  sub_time<=" . $within_a_week . " ";
			;
			break;
		case 2 :
			$within_three_days = time () + 3 * 24 * 3600;
			$where .= " and task_status = 2  and sub_time >=" . time () . " and  sub_time<=" . $within_three_days . " ";
			;
			break;
		case 3 :
			$within_two_days = time () + 2 * 24 * 3600;
			$where .= " and task_status = 2  and sub_time >=" . time () . " and  sub_time<=" . $within_two_days . " ";
			;
			break;
		case 4 :
			$within_one_days = time () + 24 * 3600;
			$where .= " and task_status = 2 and sub_time >=" . time () . " and  sub_time<=" . $within_one_days . " ";
			;
			break;
		case 5 :
			$where .= " and sub_time <=" . time ();
			;
			break;
	}
}

if ( $k ) {
	if (intval ( $k ) == 0 || intval ( $k ) >= 10) { //1-9的数字输入会报错
		$k = kekezu::escape ( htmlspecialchars ( $k ) );
		//	$where.= " and   task_title like '%{$k}%'";
		

		$k = kekezu::escape ( htmlspecialchars ( $k ) );
		$k_arr = explode ( ' ', $k );
		if (is_array ( $k_arr )) {
			$k_arr = array_unique ( $k_arr );
			$k_arr = array_filter ( $k_arr );
		} else {
			$k_arr = array ($k );
		}
		
		$where .= " and  ( ";
		foreach ( $k_arr as $kk => $vv ) {
			$kk and $where .= " or ";
			$where .= "task_title like '%{$vv}%' ";
			
			$search_ids = kekezu::search_word_format ( $vv );
			
			$search_ids and $where .= " or   uid  in (select DISTINCT(uid) from " . TABLEPRE . "witkey_member_skill where skill_id in ($search_ids))";
		}
		$where .= ")";
	}

}
$order_by = " order by CASE WHEN sticky=1 THEN sticky END desc,CASE WHEN sticky=1 THEN start_time END desc ";
if($o){
	$where.=" and task_status=2";
}
	switch (intval ( $o )) {
		case 1 :
			$order_by .= ",sub_time desc";
			break;
		case 2 :
			$order_by .= ",sub_time asc";
			break;
		case 3 :
			$order_by .= ",work_num desc";
			break;
		case 4 :
			$order_by .= ",work_num asc";
			break;
		case 5 :
			$order_by .= ",task_cash asc,task_cash_coverage asc";
			break;
		case 6 :
			$order_by .= ",task_cash desc,task_cash_coverage desc";
			break;
		default :
			$order_by .= ",task_id desc";
			break;
	}


//url sort
$url_param_arr = array ('indus_id', 'm', 't', 'f', 'r', 'e', 'k', 'o', 'p_s', 'page' );
$ac_url_list = array ();

foreach( $url_param_arr as $field ) {
	$fs = array();

	( $field != 'indus_id' && $indus_id ) && $fs[] = "indus_id=$indus_id";
	( $field != 'm' && $m ) && $fs[] = "m=$m";
	
	( $field != 't' && $t ) && $fs[] = "t=$t";
	( $field != 'f' && $f ) && $fs[] = "f=$f";
	( $field != 'r' && $r ) && $fs[] = "r=$r";
	( $field != 'e' && $e ) && $fs[] = "e=$e";
	
	( $field != 'p_s' && $p_s ) && $fs[] = "p_s=$p_s";
	( $field != 'o' && $o ) && $fs[] = "o=$o";

        ( $field != 'k'  && $k ) && $fs[] = "k=$k";

        if ( $indus_id ) {
            $ac_url_list[$field] = $_K ['siteurl'] . '/index.php?do=indus';
        } else {
            $ac_url_list[$field] = $_K ['siteurl'] . '/index.php?do=task_list';
        }
	if ( $fs )
		$ac_url_list[$field] .= '&' . implode('&', $fs);
}
/*
foreach ( $url_param_arr as $urlp ) {
	if ($urlp != 'indus_id') {
		$temp_arr = array ();
		if ($indus_id) {
			$temp_arr [] = "do=indus";
			$temp_arr [] = "indus_id=$indus_id";
		} else {
			$temp_arr [] = "do=task_list"; //没有indus_id时不走目录解析
		}
		$urlp != 'm' and $m and $temp_arr [] = "m=$m";
		$urlp != 't' and $t and $temp_arr [] = "t=$t";
		$urlp != 'f' and $f and $temp_arr [] = "f=$f";
		$urlp != 'e' and $e and $temp_arr [] = "e=$e";
		$urlp != 'r' and $r and $temp_arr [] = "r=$r";
		$urlp != 'k' and $k and $temp_arr [] = "k=$k";
		$urlp != 'o' and $o and $temp_arr [] = "o=$o";
		$urlp != 'p_s' and $p_s and $temp_arr [] = "p_s=$p_s";
		$urlp != 'page' and $page and $temp_arr [] = "page=$page";
		
		$url_str = $_K ['siteurl'] . "/index.php?";
		$url_str .= implode ( '&', $temp_arr );
		
		$ac_url_list [$urlp] = $url_str;
	} else {
		$url_str = '';
		$m and $url_str .= "&m=$m";
		$t and $url_str .= "&t=$t";
		$f and $url_str .= "&f=$f";
		$e and $url_str .= "&e=$e";
		$r and $url_str .= "&r=$r";
		$k and $url_str .= "&k=$k";
		$o and $url_str .= "&o=$o";
		$p_s and $url_str .= "&p_s=$p_s";
		$page and $url_str .= "&page=$page";
		
		$ac_url_list [$urlp] = $url_str;
	}
}
 */

$p_s = isset ( $p_s )&&$p_s ? intval ( $p_s ) : 15;
$count_sql = "select count(task_id) from " . TABLEPRE . "witkey_task ";
$count = db_factory::get_count ( $count_sql . $where );
$where .= $order_by;
$page = isset ( $page )&&$page ? $page : 1;
$pages = $page_obj->getPages ( $count, $p_s, $page, $ac_url_list ['page'] );
$task_arr = db_factory::query ( $sql . $where . $pages ['where'] );

//推荐任务
$task_recomm = db_factory::query ( sprintf ( " select task_id,uid,username,task_title,task_cash,model_id,view_num,work_num,task_cash_coverage from %switkey_task where is_top='1' and task_status='2' and task_cash>=500 order by start_time desc limit 0,24", TABLEPRE ), 1, 86400 );

//友情链接
if((($p || $c) && $page < 2) || !$p){
	$link_tag = keke_core_class::link_make_tag ( array (11 => 1 ) );
	$link_where_value = get_task_list_link($p, $c); 
	$link_where_value and $link_where = " and obj_id = '$link_where_value'" or $link_where = " and obj_id = ''"; 
	$link_arr = db_factory::query ( sprintf ( "select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) $link_where order by listorder limit 0,30", TABLEPRE ), 1, 0 );
}
 //feed 今日
$feed_list = db_factory::query ( 'select title,feed_time,feedtype from ' . TABLEPRE . 'witkey_feed where date(from_unixtime(feed_time))=date(from_unixtime('.time().')) and (feedtype="pub_task" or feedtype="task_bid") order by feed_time desc limit 0,4', 1, 3600 );

//SEO
if ($indus_p_arr [$indus_id]) {
	$page_title = $indus_p_arr [$indus_id] ['seo_title'] . '任务列表_IT帮手网';
	$page_keyword = $indus_p_arr [$indus_id] ['seo_keywords'];
	$page_description = $indus_p_arr [$indus_id] ['seo_desc'];
} elseif ($indus_c_arr [$indus_id]) {
	$page_title = $indus_c_arr [$indus_id] ['seo_title'];
	$page_keyword = $indus_c_arr [$indus_id] ['seo_keywords'];
	$page_description = $indus_c_arr [$indus_id] ['seo_desc'];
} else {
	$page_title = '威客任务 一品任务平台汇聚赏金任务 简单任务 隐藏任务 创意任务这里有无尽的任务_IT帮手网';
	$page_keyword = '威客任务，任务平台，赏金任务，简单任务，隐藏任务，创意任务，无尽的任务';
	$page_description = 'IT帮手网创意服务，征集创意服务的理想平台，雇主发布威客任务、创意任务，寻求解决方案的理想地；威客网上兼职工作的优秀平台。IT帮手网，打造威客任务中国第一平台。';
}



  $task_new4 = db_factory::query ( sprintf ( "select task_id,task_title,task_cash,view_num,work_num,task_cash_coverage from %switkey_task  where  task_status='2' and task_cash>=100 and is_recommend='1' order by start_time desc limit 0,30", TABLEPRE ), true, 86400 );
/**
*平台公告
 */
$bulletin = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=597 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 媒体报道
 */
$media = kekezu::get_table_data ( "art_id,art_title,art_cat_id,pub_time", "witkey_article", " art_cat_id=589 ", " pub_time desc", "", "4", "", 86400 );

/**
 * 中标公告
 */
$bid = db_factory::query("select a.task_id,a.uid,a.username,a.work_status,a.op_time,b.model_id from ".TABLEPRE
				."witkey_task_work a left join ".TABLEPRE."witkey_task b on a.task_id=b.task_id "
				." where a.work_status between 1 and 12 order by  a.op_time desc limit 0,4",1,86400 );


$page_title = $indus_p_arr [$indus_id] ['seo_title'] . '任务列表_IT帮手网';

$tid=$_GET['indus_id'];
if(!$tid){
$tid=1867;
}
$query1=mysql_query("select indus_pid from keke_witkey_industry where indus_id=$tid");
$row1=mysql_fetch_assoc($query1);
$tid1=$row1['indus_pid'];
$d1= array(1858,1867,1868,1869,1870);
$d2= array(1871,1872,1873,1958);
$d3= array(1874,1875);
$d4= array(1877,1878,1879,1880,1881,1882,1883,1884);
$d5= array(1885,1886,1887,1888,1889,1890,1891,1892,1893);

if(in_array($tid,$d1) or in_array($tid1,$d1)){
$ab1=' class="menuoverff"';
$d=array(1858,1867,1868,1869,1870);
}
if(in_array($tid,$d2) or in_array($tid1,$d2)){
$ab2=' class="menuoverff"';
$d=array(1871,1872,1873,1958);
}
if(in_array($tid,$d3) or in_array($tid1,$d3)){
$ab3=' class="menuoverff"';
$d=array(1874,1875);
}
if(in_array($tid,$d4) or in_array($tid1,$d4)){
$ab4=' class="menuoverff"';
$d=array(1877,1878,1879,1880,1881,1882,1883,1884);
}
if(in_array($tid,$d5) or in_array($tid1,$d5)){
$ab5=' class="menuoverff"';
$d=array(1885,1886,1887,1888,1889,1890,1891,1892,1893);
}



require $kekezu->_tpl_obj->template ( $do );

function get_task_list_link($p, $c){
	if($c){
		return $c;
	}elseif($p){
		return $p;
	}else{
		return false;
	}
}