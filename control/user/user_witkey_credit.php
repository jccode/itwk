<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

in_array($opp, array('grow','mark')) or $opp= 'grow';
$ac_url=$origin_url."&op=credit";

$mark_date_arr = array(
		'three_day'=>array('最近三天','DATE_SUB(CURDATE(), INTERVAL 3 DAY) <='),
		'one_week'=>array('最近一周','DATE_SUB(CURDATE(), INTERVAL 7 DAY) <='),
		'one_month'=>array('最近一个月','DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <='),
		'three_month'=>array('最近三个月','DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <='),
		'half_year'=>array('最近半年','DATE_SUB(CURDATE(), INTERVAL 6 MONTH) <='),
		'one_year'=>array('最近一年','DATE_SUB(CURDATE(), INTERVAL 1 YEAR) <='),
		'three_year'=>array('最近三年','DATE_SUB(CURDATE(), INTERVAL 3 YEAR) <='),
		'three_year_ago'=>array('三年以前','DATE_SUB(CURDATE(), INTERVAL 3 DAY) >')
);

$model_name_arr = 	kekezu::get_table_data ( 'model_code,model_name', 'witkey_model', '', 'model_id asc ', '', '', 'model_code');

/**
 * 星级数组
 */
$star_arr=keke_glob_class::get_mark_star();
switch ($opp) {
	case "grow" :
		/**能力**/
		$able_level =  unserialize($user_info['seller_level']);
		/*发布、中标、购买服务、销售服务款项统计*/
		$found_count = kekezu::get_table_data ( " sum(fina_cash) cash,sum(fina_credit) credit,count(fina_id) count,fina_action ", "witkey_finance", " uid='$uid' and fina_action in ('pub_task','task_bid','task_mark','buy_service','sale_service') ", "", " fina_action ", "", "fina_action" );
		
		intval($page) or $page="1";
		intval($page_size) or $page_size="10";
		$url=$ac_url."&opp=$opp&page_size=$page_size&page=$page";
		/***分页统计*/
		$count = intval(db_factory::get_count(' select count(mark_id) m from '.TABLEPRE.'witkey_mark where uid='.$uid.' and mark_type=2 ',0,'m',600));
		$pages=$kekezu->_page_obj->getPages($count, $page_size, $page, $url,"#userCenter");
		/**互评信息**/
		$sql = ' select a.*,b.task_title from '.TABLEPRE.'witkey_mark a left join '.
				TABLEPRE.'witkey_task b on a.origin_id=b.task_id where a.uid='.$uid.' and mark_type=2 order by mark_time desc '.$pages['where'];
		$mark_list = db_factory::query($sql,1,600);
		
		break;
	case "mark" :
		
		/**雇主辅助评价**/
		$buyer_aid=keke_user_mark_class::get_user_aid($uid,'2',null,'1');
		
		$wq = $wc = '';
		intval($page) or $page="1";
		intval($page_size) or $page_size="5";
		!isset($mark_status) and $mark_status='n';//某人评价状态
		//$mark_type      or         $mark_type='1';//默认评价类型为威客
		$role_type      or         $role_type="1";//默认评论发起者角色类型1=>他人  2=>自己
		$url=$ac_url."&opp=$opp&mark_status=$mark_status&mark_type=$mark_type&role_type=$role_type&page_size=$page_size&page=$page";
		/**筛选条件**/
		
		if($role_type=='1'){
			$wc.=" uid='$uid' and mark_type=2 ";
			$wq.=" a.uid='$uid' and mark_type=2 ";
		}else{
			$wc.=" by_uid='$uid' and mark_type=2 ";
			$wq.=" a.by_uid='$uid' and mark_type=2 ";//角色类型为1=>我是被评价者uid  2=>我是评价者by_uid
		}
		//$mark_type      and $where.=" and mark_type  ='$mark_type' ";//默认评价时类型为威客
	
		/**统计**/
		$mark_count=kekezu::get_table_data(" count(mark_id) count,mark_status","witkey_mark",$wc,"","mark_status ","","mark_status");

		$mark_status!='n'&&isset($mark_status) and $wq.=" and a.mark_status='$mark_status' ";
	
		$count = intval(db_factory::get_count('select count(a.mark_id) c from '.TABLEPRE.'witkey_mark a where '.$wq,0,'c',600));
		
		$pages=$kekezu->_page_obj->getPages($count, $page_size, $page, $url,"#userCenter");
		
		/**互评信息**/
		$mark_list=db_factory::query('select a.*,b.task_title from '.TABLEPRE.'witkey_mark a '.
					'left join '.TABLEPRE.'witkey_task b on a.origin_id=b.task_id where '.$wq.$pages['where'],1,600);
	
		break;
}

function gen_star($num,$name){
	$j = round($num*2);
    $str = "";  
	for($i=1;$i<11;$i++){
     $str .= "<input name=\"star_$name\" type=\"radio\" class=\"star {split:2}\" value=\"$i\" 
     disabled=\"disabled\"";
     if($j==$i) $str .= " checked />";
    }
       
    return $str;
}

require keke_tpl_class::template ( "user/" . $do . "_" .$view."_". $op.'_'.$opp );