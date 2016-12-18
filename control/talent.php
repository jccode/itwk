<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//$page_title ="人才大厅" . '-' . $_K ['html_title'];

if($ajax){
	$service_info = db_factory::query(sprintf("select service_id,shop_id,pic,title from %switkey_service where uid=%d",TABLEPRE,$s_uid));
	require keke_tpl_class::template ( "ajax/ajax_talent" );
	die();
}

if( $ac == 'urlrewrite' ) {
	$pattern = '/(t(\d+))?\/?(a(\d+))?(s(\d+))?\/?(ps(\d+))?-?(o(\d+))?\/?(page(\d+))?(\.html)?/i';
	preg_match($pattern, $param, $matchs);

	$matchs[2] && $t = $matchs[2];
	$matchs[4] && $a = $matchs[4];
	$matchs[6] && $s = $matchs[6];
	$matchs[8] && $p_s = $matchs[8];
	$matchs[10] && $o = $matchs[10];
	$matchs[12] && $page = $matchs[12];
}

$query_fields = array('p', 'c', 't', 'a', 's', 'p_s', 'o', 'page', 'k', 'z');

foreach( $query_fields as $field ) {
	$f = array();

	( $field != 'p' && $p ) && $f[] = "p=$p";
	( $field != 'c' && $c ) && $f[] = "c=$c";

	( $field != 't' && $t ) && $f[] = "t=$t";
	( $field != 'a' && $a ) && $f[] = "a=$a";
	( $field != 's' && $s ) && $f[] = "s=$s";

        /*
	( $field != 'p_s' && $field != 'p' && $field != 'c' && $field != 't' && $field != 'a' && $field != 's' && $p_s ) && $f[] = "p_s=$p_s";
	( $field != 'o' && $field != 'p' && $field != 'c' && $field != 't' && $field != 'a' && $field != 's' && $o ) && $f[] = "o=$o";
	( $field != 'page' && $field != 'p' && $field != 'c' && $field != 't' && $field != 'a' && $field != 's' && $page ) && $f[] = "page=$page";
	( $field != 'k' && $field != 'p' && $field != 'c' && $k ) && $f[] = "k=$k";
        */
        
	( $field != 'p_s' && $p_s ) && $f[] = "p_s=$p_s";
	( $field != 'o' && $o ) && $f[] = "o=$o";
	( $field != 'k' && $k ) && $f[] = "k=$k";

	( $field != 'z' && $field != 'p' && $field != 'c' && $z ) && $f[] = "z=$z";
	
	$urls[$field] = $_K ['siteurl'] . '/index.php?do=talent';

	if ( $f )
		$urls[$field] .= '&' . implode('&', $f);
}

//$ac_url = $_K['siteurl']."/index.php?do=talent";
$ac_url=$_K['site_url']. $_SERVER["REQUEST_URI"];


// indus_id
if(intval($indus_id)){
    $indus_info = $kekezu->findIndusById($indus_id);
    
	//格式化数据
	if(!$indus_info['children']){
		$p = $indus_info['indus_pid'];
		$c = $indus_id;
	}
	else{
		$p = $indus_id;
        	$pids = $kekezu->get_indus_ids_below_except_leaf($indus_info);
	}
}


$sql = "select c.uid,c.username,c.user_type,c.reg_time,c.w_level,c.shop_level,c.isvip,c.qq,c.residency,c.skill_ids,c.auth_realname,c.auth_email,c.auth_mobile,c.auth_bank,c.integrity,c.take_num,c.accepted_num,c.shop_id,c.shop_name,c.w_good_rate  from " . TABLEPRE . "witkey_space c ";


//人才大类
if(isset($p)||isset($c)){
	
	/*
	if(isset($p)){
		//$ac_url.="&p=".intval($p);
		//$where.=sprintf ( " and  ( uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id in (select indus_id from keke_witkey_industry where indus_pid=%d)))", intval($p) );
		
		$where.=sprintf ( " inner join (select uid from keke_witkey_member_skill as a left join keke_witkey_industry as b on a.skill_id = b.indus_id where b.indus_pid=%d GROUP BY a.uid) as e on c.uid = e.uid ", intval($p) );
	}elseif(isset($c)){
		//$ac_url.="&c=".intval($c);
		//$where.=sprintf ( " and  ( uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id =%d)))", intval($c) );
		$where.=sprintf ( " inner join (select uid from keke_witkey_member_skill as a left join keke_witkey_industry as b on a.skill_id = b.indus_id where b.indus_id=%d GROUP BY a.uid) as e on c.uid = e.uid ", intval($c) );
	}
	*/
	
	if(isset($c)) {
		$where.=sprintf ( " inner join (select uid from keke_witkey_member_skill as a left join keke_witkey_industry as b on a.skill_id = b.indus_id where b.indus_id=%d GROUP BY a.uid) as e on c.uid = e.uid ", intval($c) );
	}
	elseif (isset($p)) {
		$pids_in = join(", ", $pids);
		$where.=sprintf ( " inner join (select uid from keke_witkey_member_skill as a left join keke_witkey_industry as b on a.skill_id = b.indus_id where b.indus_pid in (%s) or indus_id in (%s) GROUP BY a.uid) as e on c.uid = e.uid ", $pids_in, $pids_in );
	}

	$where.= " where  1= 1 and c.group_id<1  ";
}else{
	$where = " where  1= 1 and c.group_id<1  ";
}
//人才类型
if(isset($t)){
	//$ac_url.="&t=".intval($t);
	$where.=sprintf ( " and   c.user_type = %d", intval($t) );
}
//人才能力
if(isset($a)){
	//$ac_url.="&a=".intval($a);
	$where.=sprintf ( " and   c.w_level = %d", intval($a) );
}
//人才状态
if(isset($s)){
	//$ac_url.="&s=".intval($s);
	switch (intval($s)) {
		case 1:
			$where.=sprintf ( " and   c.auth_realname = %d", 1 );
		;
		break;
		case 2:
			$where.=sprintf ( " and   c.auth_email = %d", 1 );
			;
		break;
		case 3:
			$where.=sprintf ( " and   c.auth_mobile = %d", 1 );
		;
		break;
		case 4:
			$where.=sprintf ( " and   c.auth_bank = %d", 1 );
		;
		break;
		case 5:
			$where.=sprintf ( " and   c.isvip = %d", 1 );
			;
		case 6:
				$where .= " and c.integrity = 1";
		break;
	}
}
//所在地区
if(isset($z)){
	$z = kekezu::escape ( htmlspecialchars ( $z ));
	//$ac_url.="&z=".$z;
	$where.= " and   c.residency like '%{$z}%'";
}

//关键字
if(isset($k)&&trim($k)){
	$k = kekezu::escape ( htmlspecialchars ( $k ));
	$k_arr =  explode(' ', $k);
	if(is_array($k_arr)){
		$k_arr = array_unique($k_arr);
		$k_arr = array_filter($k_arr);
	}else{
		$k_arr = array($k);
	}

	$where.= " and  ( ";
	foreach($k_arr as $kk=>$vv){
		$kk and $where .= " or ";
		$where .= "c.shop_name like '%{$vv}%' or c.residency like '%{$vv}%' or c.username like '%{$vv}%'";

		$search_ids = kekezu::search_word_format($vv);

		$search_ids and $where .= " or c.uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id in ($search_ids))";
	}
	$where .= ")";

	//$indus_id  = db_factory::query($sql);



	//$ac_url.="&k=".$k;
}

switch ($o) {
	case 1 :
		$order_by = " order by c.reg_time asc";
		break;
	case 2 :
		$order_by = " order by c.reg_time desc";
		break;
	case 3 :
		$order_by = " order by c.accepted_num desc";
		break;
	case 4 :
		$order_by = " order by c.accepted_num asc";
		break;
	case 5 :
		$order_by = " order by c.w_level desc";
	break;
	case 6 :
		$order_by = " order by c.w_level asc";
	break;
	default:
		$order_by = " order by c.shop_level desc,c.isvip desc,c.w_level desc,c.listorder desc";
		break;
}




$where.=$order_by;
$p_s = isset($p_s ) ? intval ( $p_s ) : 15;
$count_sql = "select count(*) from ".TABLEPRE."witkey_space c";
$count = db_factory::get_count($count_sql . $where, 0, null, 3600 );
$page = isset($page) ? $page : 1;
$pages = $page_obj->getPages ( $count, $p_s, $page, $urls[page] );
$talent_arr = db_factory::query ( $sql . $where . $pages ['where'], 1, 3600 );

/**
 * 推荐店铺
 */
$cat_ids = 0;
$cat_ids .= ( $p ) ? ',' . $p : '';
$cat_ids .= ( $c ) ? ',' . $c : '';
$recomm_sql = "select b.shop_id, b.uid, b.shop_name, b.isvip, b.residency from " . TABLEPRE . "witkey_talent_link a, " . TABLEPRE . "witkey_space b where a.uid = b.uid and catid in ( $cat_ids ) order by a.catid DESC, a.level desc, a.tid desc limit 0, 20";
$shop_recomm = db_factory::query ( $recomm_sql, 1, 86400 );

if ( ! $shop_recomm /*|| sizeof($shop_recomm) < 20*/ ) {
	$shop_recomm_more = db_factory::query ( sprintf ( "select shop_id,uid,shop_name,isvip,residency from %switkey_space where   istop = 1   order by istop desc,isvip desc,shop_id asc limit 0,20", TABLEPRE ), 1, 86400 );
	$shop_recomm = $shop_recomm + $shop_recomm_more;
}

//友情链接
$link_tag = keke_core_class::link_make_tag( array(6=>1) );
$link_arr = db_factory::query(sprintf("select link_id,link_name,link_url,link_status from %switkey_link where link_status=1 and (location & $link_tag=$link_tag) order by listorder limit 0,30", TABLEPRE ), 1, 86400 );

//SEO
$indus_c_arr[$c][indus_name] and $seo_title[1] = $indus_c_arr[$c][indus_name].' ';
$indus_p_arr[$p][indus_name] and $seo_title[2] = $indus_p_arr[$p][indus_name].' ';
$page_title = $seo_title[1].$seo_title[2].'人才宝库 最全威客人才、创意人才 一品拥有专业公司和高校人才是人才新干线_IT帮手网';
$page_keyword = '人才宝库，威客人才，创意人才，高校人才，人才新干线';
$page_description ='IT帮手网人才宝库，汇聚威客人才、创意人才、高校人才，最全的威客人才宝库。找威客人才、创意人才就到IT帮手网人才宝库，一品拥有专业公司和高校人才打造人才新干线。';
//推荐商铺
$shop_recomm = db_factory::query ( sprintf ( "select shop_id,uid,shop_name,shop_level,isvip,logo from %switkey_shop where   is_close!=1 and isvip = 1   order by istop desc,shop_level desc,listorder asc limit 0,21", TABLEPRE ), 1, 86400 );
//最新商铺
$shop_recomm1 = db_factory::query ( sprintf ( "select shop_id,uid,shop_name,shop_level,isvip,logo from %switkey_shop where   is_close!=1 order by shop_id desc,shop_level desc limit 0,21", TABLEPRE ), 1, 86400 );

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

$art_new = kekezu::get_table_data("art_id,art_cat_id,art_title","witkey_article","art_type='help' and is_show=1","art_id desc","","0,9",art_id,3600);


/*
$tid=$_GET['p'];
$tida=$_GET['c'];
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
*/

$tid=$_GET['indus_id'];
if(!$tid){
	$indus_vals = array_values($indus_map);
    $nav = array($indus_vals[0]);
} else {
    $nav = $kekezu->findIndusWithParentById($tid);
    $nav = array_reverse($nav);
}
$l1tid = $nav[0]["indus_id"];








require keke_tpl_class::template ( $do );