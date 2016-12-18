<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

//$page_title ="人才大厅" . '-' . $_K ['html_title'];

$query_fields = array('p', 'page');

foreach( $query_fields as $field ) {
	$f = array();

	( $field != 'p' && $p ) && $f[] = "p=$p";	
	$urls[$field] = $_K ['siteurl'] . '/index.php?do=liangpu';

	if ( $f )
		$urls[$field] .= '&' . implode('&', $f);
}

$ac_url=$_K['site_url']. $_SERVER["REQUEST_URI"];

$sql = "select uid,username,user_type,reg_time,w_level,shop_level,isvip,qq,residency,skill_ids,auth_realname,auth_email,auth_mobile,auth_bank,integrity,take_num,accepted_num,shop_id,shop_name,w_good_rate  from " . TABLEPRE . "witkey_space  ";
$where = " where  1= 1 and group_id<1  and (chief_designer =1 or isvip=1)";

if(isset($p)){
	//$ac_url.="&p=".intval($p);
	switch ($p){
		case 'others':
			$where.=sprintf ( " and  ( uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id in (select indus_id from keke_witkey_industry where indus_pid not in ('63','71','60','66','64'))))" );
			break;
		default:
		    $where.=sprintf ( " and  ( uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id in (select indus_id from keke_witkey_industry where indus_pid=%d)))", intval($p) );
	        break;
	}
		
}else{
	$p=63;
	$where.=sprintf ( " and  ( uid  in (select DISTINCT(uid) from ".TABLEPRE."witkey_member_skill where skill_id in (select indus_id from keke_witkey_industry where indus_pid=%d)))", intval($p) );
}
$order_by = " order by isvip desc,integrity desc ,w_level desc,accepted_num desc ,uid asc";
$where.=$order_by;
$p_s = isset($p_s ) ? intval ( $p_s ) : 15;
$count_sql = "select count(uid) from ".TABLEPRE."witkey_space ";
$count = db_factory::get_count($count_sql . $where, 0, null, 3600 );
$page = isset($page) ? $page : 1;
$pages = $page_obj->getPages ( $count, $p_s, $page, $urls[page] );
$talent_arr = db_factory::query ( $sql . $where . $pages ['where'], 1, 3600 );

/**
 * 服务信息and地区
 */

foreach ($talent_arr as  $k=>$v){
//echo $talent_arr[$k]['username'];
	//服务信息
	$service_info = db_factory::query(sprintf("select service_id,price,service_type,shop_id,pic,title from %switkey_service where uid=%d order by service_id desc limit 4",TABLEPRE,$v['uid']));    
	$talent_arr[$k]['service_info']=$service_info;
	//地区
	$regin_info[$k]=explode(',',$talent_arr[$k]['residency']);
    $talent_arr[$k]['residency']=$regin_info[$k][0]."-".$regin_info[$k][1];
}


//SEO
$indus_c_arr[$c][indus_name] and $seo_title[1] = $indus_c_arr[$c][indus_name].' ';
$indus_p_arr[$p][indus_name] and $seo_title[2] = $indus_p_arr[$p][indus_name].' ';
$page_title = $seo_title[1].$seo_title[2].'人才宝库 最全威客人才、创意人才 一品拥有专业公司和高校人才是人才新干线_IT帮手网';
$page_keyword = '人才宝库，威客人才，创意人才，高校人才，人才新干线';
$page_description ='IT帮手网人才宝库，汇聚威客人才、创意人才、高校人才，最全的威客人才宝库。找威客人才、创意人才就到IT帮手网人才宝库，一品拥有专业公司和高校人才打造人才新干线。';

require keke_tpl_class::template ( $do );