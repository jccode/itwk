<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-13 10:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if( $ac == 'urlrewrite' ) {
        // c1r1e1/s15-o2/page2.html?k=关键词
	$pattern = '/(c(\d+))?(r(\d+))?(e(\d+))?\/?(s(\d+))?-?(o(\d+))?\/?(page(\d+))?(\.html)?/i';
	preg_match($pattern, $param, $matchs);
        //var_dump( $matchs );
        
	$matchs[2] && $c = $matchs[2];
	$matchs[4] && $r = $matchs[4];
	$matchs[6] && $e = $matchs[6];
	$matchs[8] && $p_s = $matchs[8];
	$matchs[10] && $o = $matchs[10];
	$matchs[12] && $page = $matchs[12];
}

$query_fields = array('c', 'r', 'e', 'p_s', 'o', 'page', 'k');

foreach( $query_fields as $field ) {
	$f = array();

	( $field != 'c' && $c ) && $f[] = "c=$c";
	( $field != 'r' && $r ) && $f[] = "r=$r";
	( $field != 'e' && $e ) && $f[] = "e=$e";
        
	( $field != 'p_s' && $p_s ) && $f[] = "p_s=$p_s";
	( $field != 'o' && $o ) && $f[] = "o=$o";
    
	( $field != 'k' && $k ) && $f[] = "k=$k";
	
	$urls[$field] = $_K ['siteurl'] . '/index.php?do=hire_list&view=' . $view;

	if ( $f )
            $urls[$field] .= '&' . implode('&', $f);
        
}
//var_dump($urls);

$cash_cove_arr = kekezu::get_cash_cove();
$views = array('wait','success');
in_array($view,$views) or $view='wait';
$ac_url = $_K['siteurl']."/index.php?do=hire_list&view=$view";

$sql = "select task_id,model_id,task_status,task_title,task_cash,task_cash_coverage,indus_id,indus_pid,start_time,sub_time,end_time,view_num,work_num,uid,username,w_uid,w_username,w_bid_time  from " . TABLEPRE . "witkey_task  ";

if($view=='wait'){
$where = "where 1= 1 and model_id=4 and task_type=1 and task_cash_coverage<1 and task_status=2 ";

//金额额度
if(isset($c)){
	$ac_url.="&c=".intval($c);
	switch (intval($c)) {
		case 1:
			$where.=" and (task_cash<=500 or task_cash_coverage=1 )";
			;
			break;
		case 2:
			$where.=" and ((task_cash between 500 and 1000)   or task_cash_coverage=2 )";
			;
			break;
		case 3:
			$where.=" and ((task_cash between 1000 and 2000)   or task_cash_coverage=3 )";
			;
			break;
		case 4:
			$where.=" and ((task_cash between 2000 and 5000)   or task_cash_coverage=4 )";
			;
			break;
		case 5:
			$where.=" and ((task_cash between 5000 and 10000)   or task_cash_coverage=5 )";
			;
			break;
		case 6:
			$where.=" and ((task_cash between 10000 and 20000)   or task_cash_coverage=6 )";
			;
			break;
		case 6:
			$where.=" and (task_cash >=20000   or task_cash_coverage=7 )";
			;
			break;
	}
}

//发布时间
if(isset($r)){
	$ac_url.="&r=".intval($r);
	switch (intval($r)) {
		case 1:
			$today = strtotime(date('Y-m-d 0:0:0',time()));
			$where.=" and start_time >".$today;
			;
			break;
		case 2:
			$yesterday = strtotime(date('Y-m-d 0:0:0',time()))-24*3600;
			$where.=" and start_time >".$yesterday;
			;
			break;
		case 3:
			$nearly_three_days = time()-24*3*3600;
			$where.=" and start_time >".$nearly_three_days;
			;
			break;
		case 4:
			$week_firstday=date('d')-date('w');
			$this_week = strtotime(date("Y-m-{$week_firstday} 0:0:0",time()));
			$where.=" and start_time >".$this_week;
			;
			break;
		case 5:
			$this_month = strtotime(date('Y-m-1 0:0:0',time()));
			$where.=" and start_time >".$this_month;
			;
			break;
		case 6:
			$nearly_three_month = time()-24*90*3600;
			$where.=" and start_time >".$nearly_three_month;
			;
			break;
	}
}

//结束时间
if(isset($e)){
	$ac_url.="&e=".intval($r);
	switch (intval($e)) {
		case 1:
			$within_a_week =time()+7*24*3600;
			$where.=" and (end_time >=".time()." and  end_time<=".$within_a_week." )";
			;
			break;
		case 2:
			$within_three_days =time()+3*24*3600;
			$where.=" and (end_time >=".time()." and  end_time<=".$within_three_days." )";
			;
			break;
		case 3:
			$within_two_days =time()+2*24*3600;
			$where.=" and (end_time >=".time()." and  end_time<=".$within_two_days." )";
			;
			break;
		case 4:
			$within_one_days =time()+24*3600;
			$where.=" and (end_time >=".time()." and  end_time<=".$within_one_days." )";
			;
			break;
		case 5:
			$where.=" and end_time <=".time();
			;
			break;
	}
}


if(isset($k)){
	$k = kekezu::escape ( htmlspecialchars ( $k ));
	$ac_url.="&k=".$k;
	$where.= " and   task_title like '%{$k}%'";
}

switch (intval($o)) {
	case 1 :
		$order_by = " order by end_time desc";
		break;
	case 2 :
		$order_by = " order by end_time asc";
		break;
	case 3 :
		$order_by = " order by work_num desc";
		break;
	case 4 :
		$order_by = " order by work_num asc";
		break;
	case 5 :
		$order_by = " order by task_cash desc,task_cash_coverage desc";
		break;
	case 6 :
		$order_by = " order by task_cash asc,task_cash_coverage asc";
		break;
	default:
		$order_by = " order by start_time desc";
		
}
$where.=$order_by;
}
else{
	$where = "where 1= 1 and model_id=4 and model_id=4 and task_type!=2 and task_status in (4,5,7) ";
	$where .= " order by task_id desc";
}

$p_s = isset( $p_s ) ? intval ( $p_s ) : 15;

if($view=='success') {
    $p_s=40;
}
$count_sql = "select count(task_id) from ".TABLEPRE."witkey_task ";
$count = db_factory::get_count($count_sql . $where );
$page = isset($page) ? $page : 1;
$pages = $page_obj->getPages ( $count, $p_s, $page, $urls['page'] );

$task_arr = db_factory::query ( $sql . $where . $pages ['where'] );
//右侧推荐滚动任务，与首页滚动条件相同(战报)
$recommend_info = kekezu::get_table_data('task_id,model_id,task_status,task_title,uid,username,task_cash,task_type,w_uid,w_username,w_bid_time','witkey_task','model_id=4 and task_type!=2 and task_status in(4,5,7) and task_cash>2000','task_id desc','','0,10','task_id',3600);
//优秀VIP推荐
$rec_top_shop = kekezu::get_table_data('shop_id,uid,username,shop_level,shop_name','witkey_shop','isvip=1 ',' istop desc ,shop_level asc,listorder asc','','0,24','shop_id',3600);
$rec_shop = kekezu::get_table_data('shop_id,uid,username,shop_name','witkey_shop','isvip=1 and istop=1','listorder asc','','0,8','shop_id',3600);
//最新VIP商铺
$new_shop = kekezu::get_table_data('uid,username,shop_id,shop_name','witkey_space','isvip=1','vip_start_time desc','','0,8','uid',3600);


//雇佣交易数：9997 个
$hire_count = db_factory::get_one(" select count(task_id) as task_count ,sum(task_cash) as task_cash from ".TABLEPRE."witkey_task where model_id = 4 and task_cash_coverage<1   ",1,3600);

//疑难解答
$help_answer = kekezu::get_table_data ( "art_id,art_title,pub_time", "witkey_article", " art_type = 'help'  and is_recommend =1 ", " pub_time desc", "", "5", "", 86400 );

//提现公告
$with_draw = kekezu::get_table_data( "uid,username,withdraw_cash,applic_time", "witkey_withdraw", "withdraw_status =2 ", " applic_time desc", "", "5", "", 86400 );

//周雇佣冠军
$week_kemp  = db_factory::query("
		select sum(fina_cash) as fina_cash,fina_type,fina_action,fina_time ,a.uid ,b.username,w_level from
		 keke_witkey_finance a left join keke_witkey_space b on a.uid=b.uid 
		  where  yearweek(from_unixtime(fina_time),1)=yearweek(from_unixtime(".time()."),1)
		and fina_type ='in' and fina_action = 'task_bid'
		group by a.uid
order by fina_cash desc limit 0,3;",1,3600);

//SEO
$page_title = '直接雇佣 威客任务平台 1对1服务，满意付款，赏金任务悬赏金100%_IT帮手网';
$page_keyword = '直接雇佣，威客任务平台，威客平台，威客任务';
$page_description ='IT帮手网直接雇佣模式，威客任务平台首创独有模式。威客雇主1对1服务，第三方托管赏金，满意付款，赏金100%归威客所有，真正让利于雇主与威客，诚心为雇主威客们考虑的专业的威客服务平台。';

require keke_tpl_class::template ( $do );