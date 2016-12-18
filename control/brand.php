<?php
/**
 * 品牌馆@copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-7-31 10:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$views = array ('index', 'hongkong', 'taiwan' );
! in_array ( $view, $views ) && $view = 'index';
$unit_price = keke_glob_class::get_price_unit();

switch ($ajax) {
	case 'talent' :
		$service_info = db_factory::query ( sprintf ( "select service_id,shop_id,pic,title from %switkey_service where uid=%d", TABLEPRE, $s_uid ) );
		require keke_tpl_class::template ( "ajax/ajax_talent" );
		die ();
		break;
	case 'tw' :
	case 'abroad' :
	case 'hk' :
		$recomm_list = db_factory::query ( ' select * from ' . TABLEPRE . 'witkey_brand where  brand="' . $ajax . '" and is_recommend = 1 and  app_status = 1 order by is_recommend desc limit 0,27', 1, 3600 );
		require keke_tpl_class::template ( 'ajax/ajax_brand' );
		die ();
		break;
	case 'brand_tw_service':
	case 'service':
		$ser_info = db_factory::get_one(' select * from '.TABLEPRE.'witkey_service where shop_id='.$sid.' order by views desc,price desc limit 0,1');
		require keke_tpl_class::template('ajax/ajax_brand');
		die();
		break;
	case 'apply' :
		$title = '申请加入';
		$app_status = db_factory::get_count(' select app_status from '.TABLEPRE.'witkey_brand where uid='.$uid);
		$brand_type = keke_glob_class::get_brand_type();
		$brand_type[$brand] and $title.=$brand_type[$brand] or $title.='品牌馆';
		$brand or $brand = 'hk';
		$user_info['group_id'] and kekezu::show_msg('操作警告','',3,'客服人员不得加入品牌馆','alert_error');
		if(!$app_status){
			if($is_sbt){
				$id=db_factory::inserttable(TABLEPRE.'witkey_brand',array(
						'uid'=>$uid,'username'=>$username,'shop_id'=>$user_info['shop_id'],
						'brand'=>$brand,'app_desc'=>$tar_content,'app_files'=>$file_ids,
						'on_time'=>time()
				));
				$id and kekezu::echojson('',1) or kekezu::echojson('',0);
			}else{
				require keke_tpl_class::template('ajax/ajax_brand');
			}
		}else{
			$app_status==-1 and $d='您已申请加入品牌馆,请等待客服处理' or ($app_status==2 and $d='您的品牌馆申请已被客服驳回,请联系客服' or $d='您已加入品牌馆,无须再次申请');
			kekezu::show_msg('友情提示','',3,$d,'alert_error');
		}
		break;
}
/** 精彩案例**/
$view=='index' and $li=6 or $li=8;
$view=='hongkong' and $brand1='hk';
$view=='taiwan' and $brand1='tw';
if($brand1){
	$brand_where = 'and  a.brand="'.$brand1.'"';
}
$case_list = db_factory::query(' select b.case_id,b.case_name,b.case_url,b.shop_id,b.case_pic from '.TABLEPRE.'witkey_brand a '
			.' inner join '.TABLEPRE.'witkey_shop_case b on a.shop_id = b.shop_id,'.TABLEPRE.'witkey_space s where s.shop_id=b.shop_id and s.isvip=1 and a.app_status=1 '.$brand_where.' group by a.uid order by b.on_time desc limit 0,'.$li,1,3600);



if (in_array ( $view, array ('hongkong', 'taiwan' ) )) {
	$ac_url = $_K ['siteurl'] . "/index.php?do=brand&view=".$view."&op=ppk";




	$sql = "select uid,username,user_type,reg_time,w_level,shop_level,isvip,qq,residency,skill_ids,auth_realname,auth_email,auth_mobile,auth_bank,take_num,accepted_num,shop_id,shop_name,w_good_rate  from " . TABLEPRE . "witkey_space  ";
	$view=='hongkong' and $brand='hk' or ($view=='taiwan' and $brand='tw' or $brand='aborad');
	$where = " where group_id<1  and brand='$brand'";




	//人才大类
	if (isset ( $p )) {
		$ac_url .= "&p=" . intval ( $p );
		$where .= sprintf ( " and  ( uid  in (select DISTINCT(uid) from " . TABLEPRE . "witkey_member_skill where skill_id in (select indus_id from keke_witkey_industry where indus_pid=%d)))", intval ( $p ) );
	} elseif (isset ( $c )) {
		$ac_url .= "&c=" . intval ( $c );
		$where .= sprintf ( " and  ( uid  in (select DISTINCT(uid) from " . TABLEPRE . "witkey_member_skill where skill_id =%d)))", intval ( $c ) );

	}
	//人才类型
	if ( $t ) {
		$ac_url .= "&t=" . intval ( $t );
		$where .= sprintf ( " and   user_type = %d", intval ( $t ) );
	}
	//人才能力
	if (isset ( $a )) {
		$ac_url .= "&a=" . intval ( $a );
		$where .= sprintf ( " and   w_level = %d", intval ( $a ) );
	}
	//人才状态
	if (isset ( $s )) {
		$ac_url .= "&s=" . intval ( $s );
		switch (intval ( $s )) {
			case 1 :
				$where .= sprintf ( " and   auth_realname = %d", 1 );
				break;
			case 2 :
				$where .= sprintf ( " and   auth_email = %d", 1 );
				break;
			case 3 :
				$where .= sprintf ( " and   auth_mobile = %d", 1 );
				break;
			case 4 :
				$where .= sprintf ( " and   auth_bank = %d", 1 );
				break;
			case 5 :
				$where .= sprintf ( " and   isvip = %d", 1 );
				break;
			case 6 :
				$where .= sprintf ( " and   integrity = %d", 1 );
				break;
			default:
				break;
		}
	}
	//所在地区
	if (isset ( $z )) {
		$z = kekezu::escape ( htmlspecialchars ( $z ) );
		$ac_url .= "&z=" . $z;
		$where .= " and   residency like '%{$z}%'";
	}

	//关键字
	if (isset ( $k ) && trim ( $k )) {
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
			$where .= "shop_name like '%{$vv}%' or residency like '%{$vv}%' or username like '%{$vv}%'";

			$search_ids = kekezu::search_word_format ( $vv );

			$search_ids and $where .= " or   uid  in (select DISTINCT(uid) from " . TABLEPRE . "witkey_member_skill where skill_id in ($search_ids))";
		}
		$where .= ")";

		//$indus_id  = db_factory::query($sql);


		$ac_url .= "&k=" . $k;
	}


	switch (intval ( $o )) {
		case 1 :
			$order_by = " order by reg_time desc";
			break;
		case 2 :
			$order_by = " order by reg_time asc";
			break;
		case 3 :
			$order_by = " order by accepted_num desc";
			break;
		case 4 :
			$order_by = " order by accepted_num asc";
			break;
		case 5 :
			$order_by = " order by w_level desc";
			break;
		case 6 :
			$order_by = " order by w_level asc";
			break;
		default :
			$order_by = " order by shop_level desc,isvip asc,listorder asc";
			break;
	}


	$where .= $order_by;

	$p_s = isset ( $p_s ) ? intval ( $p_s ) : 15;
	$count_sql = "select count(uid) from " . TABLEPRE . "witkey_space ";
	$count = db_factory::get_count ( $count_sql . $where );
	$page = isset ( $page ) ? $page : 1;
	$pages = $page_obj->getPages ( $count, $p_s, $page, $ac_url );
	$talent_arr = db_factory::query ( $sql . $where . $pages ['where'] );
}
$view='taiwan';
$op and $view=$view .'_'.$op;
require $do . '/' . $do . '_' . $view . '.php';
