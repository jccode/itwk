<?php
/**
 * 客户跟踪管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 158 );

$yewuyuan = get_sale_member();
//var_dump($yewuyuan);

//初始化 
//$track_type = keke_glob_class::get_track_type();
$track_type = array(
	'8' => '未接通',  //'8' => '未接通的客户',
	'1' => '新联系，有待跟进', //'1' => '新联系，有待跟进', 
	'2' => '意向不明确',  //'2' => '意向不是很明确',
	'3' => '高意向',  //'3' => '为高意向客户',   '4' => '有明确合作需求，发了合同', 
	'5' => '已确认合作细节', //'5' => '最终确认合作细节', 
	'6' => '毁单', //'6' => '悔单客户', 
	'10' => '已成交',
	'7' => '无意向', //'7' => '没意向客户', 
	'9' => '空号停机', //'9' => '空号停机' '8' => '未接通的客户', 
	'0' => '暂无'
);

if($ac == 'tiqu' && $t_uid){
	$tiqu_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_space where uid='$t_uid'");
	if($tiqu_info['track_uid'] && $tiqu_info['track_uid'] != $admin_info['track_uid']){
		kekezu::admin_show_msg ("该用户已被他人提取","index.php?do={$do}&view=track",3,'','warning' );
	}
	
	$res = db_factory::execute (" update ".TABLEPRE."witkey_space set track_uid = '$admin_info[uid]', last_track_time = ".time()." where uid='$t_uid'");	
	$res and kekezu::admin_show_msg ("用户提取成功","index.php?do={$do}&view=track&op={$op}",3,'','success' );

}elseif (isset ( $sbt_action )) { //批量转移
	//if(in_array($admin_info['uid'],array(1,41645))){
		$to_track_uid or kekezu::admin_show_msg ( '请选择业务员', $to_url,3,'','warning' );	
		sizeof ( $ckb ) or kekezu::admin_show_msg ( $_lang['choose_operate_item'], $to_url,3,'','warning' );
		is_array ( $ckb ) and $ids = implode ( ',', array_filter ( $ckb ) );
		$space_obj = new Keke_witkey_space_class();
		$space_obj->setWhere("uid in($ids)");
		$space_obj->setTrack_uid( $to_track_uid );
		$space_obj->setLast_track_time( time() );		
		$space_obj->setTrack_username( $yewuyuan[$to_track_uid]['real_name'] ? $yewuyuan[$to_track_uid]['real_name'] : $yewuyuan[$to_track_uid]['username'] );
		$res = $space_obj->edit_keke_witkey_space();
		$res and kekezu::admin_show_msg ("转移成功", $to_url,3,'','success') or kekezu::admin_show_msg ("转移失败", $to_url,3,'','warning');
	//}else{
	//	kekezu::admin_show_msg ("你没有权限进行此操作", $to_url,3,'','warning');
	//}
}

//查询
$table_class = new keke_table_class ( 'witkey_space' );
$url = "index.php?do=$do&view=$view&op={$op}&w[track_type]=$w[track_type]
&w[username]=$w[username]&w[uid]=$w[uid]&w[qq]=$w[qq]&w[mobile]=$w[mobile]&slt_page_size=$slt_page_size
&w[track_uid]=$w[track_uid]&w[ord][]=".$w['ord']['0']."&w[ord][]=".$w['ord']['1'];

$op = $op ? $op : 'my';
$where = " where 1=1 ";

//每页显示多少条，默认10
$page and $page = intval($page) or $page = 1;
$slt_page_size = intval ( $slt_page_size ) ? intval ( $slt_page_size ) : 15;

switch ($op) {
	case 'my': //我的跟踪用户
		$where .= "and track_uid = '$admin_info[uid]'";
		if(!empty($w['ord']['0']) && !empty($w['ord']['1'])){
			$ord_where = ' order by '.$w['ord']['0'].' '.$w['ord']['1'];
		}else{
			$ord_where .= ' order by last_track_time desc';
		}
		
		$stat_arr = stat_track($admin_info['uid']);
	break;
	case 'reserve': //我的预约用户
		$where .= "and track_uid = '$admin_info[uid]' and track_reserve > 0 ";
		if(!empty($w['ord']['0']) && !empty($w['ord']['1'])){
			$ord_where = ' order by '.$w['ord']['0'].' '.$w['ord']['1'];
		}else{
			$ord_where .= ' order by last_track_time desc';
		}
	break;
	case 'new': //最新注册用户
		$where .= "and user_role = 2 and track_uid = 0 and ((reg_time> ".(time()-2*86400).") or (reg_time> ".(time()-7*86400)." and mobile <> ''))";
		$ord_where .=" order by reg_time desc";
	break;
	case 'rand': //随机跟踪用户
		$where .= "and user_role = 2 and track_uid =0";  
		$ord_where .=" order by rand() desc";
	break;
	case 'migrate': //数据搬迁		
		kekezu::admin_check_role ( 203 );
		
		$w[track_uid] and $where .= "and track_uid ='$w[track_uid]'" or $where .= "and uid ='0'";		
		$ord_where .= ' order by last_track_time desc';
		
		if($w[track_uid]){
			$stat_arr = stat_track($w[track_uid]);
		}
	break;
}

$w['uid'] and $where .= " and uid='{$w[uid]}' ";
$w['username'] and $where .= " and username like '%{$w['username']}%' ";
$w['qq'] and $where .= " and qq='{$w[qq]}' ";
$w['mobile'] and $where .= " and mobile='{$w[mobile]}' ";
$w['truename'] and $where .= " and truename='{$w[truename]}' ";

if($w['track_type'] == '0'){
	$where .= " and (track_type < 1 or track_type = '')";
}else{
	$w['track_type'] and $where .= " and track_type = '$w[track_type]' ";
}

 //统计
$sql_count = "select count(*) from ".TABLEPRE."witkey_space";
$sql_count .= $where;   
$count = db_factory::get_count($sql_count);
if($count > 200 && $op == 'rand'){
	$count = 200;
}

$sql = "select * from ".TABLEPRE."witkey_space";
$pages = $kekezu->_page_obj->getPages ( $count, $slt_page_size, $page, $url );
$where .= $ord_where; 
$sql .= $where.$pages['where']; //echo $sql;
$data_arr = db_factory::query($sql);	

//echo '<font color="red">维护中，请稍等几分钟...</font>'; 
if($op == 'migrate'){
	require $template_obj->template ( 'control/admin/tpl/admin_user_track_migrate' );
}else{
	require $template_obj->template ( 'control/admin/tpl/admin_'.$do.'_'.$view );
}

 //数据统计
function stat_track($uid){
	$time = $stat = array();
	$time['this_month_start'] = mktime(0, 0 , 0,date("m"),1,date("Y")); 
	$time['today_start'] = mktime(0, 0 , 0,date("m"),date("d"),date("Y")); 
	//$time['this_month_end'] = mktime(23,59,59,date("m"),date("t"),date("Y")); 
	
	 //本月成交/总		
	$stat['this_month_cj'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid' and track_type = 10 and last_track_time > $time[this_month_start]");
	$stat['total_month_cj'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid' and track_type = 10");
	
	 //今日跟踪/总	
	$stat['today_gz'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid' and last_track_time > $time[today_start]");
	$stat['total_gz'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid'");
	
	//今日高意向客户/总	 
	$stat['today_gyx'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid' and track_type = 3 and last_track_time > $time[today_start]");
	$stat['total_gyx'] = db_factory::get_count(" select count(uid) from " . TABLEPRE . "witkey_space where track_uid ='$uid' and track_type = 3");

	return $stat;
}

function get_sale_member(){
	$admin_name = array(
		'41645' => '李建川', '44505' => '朱丹梅', '36357' => '黄志达', '88075' => '杨浩',    
		'69682' => '蔡清希', '78082' => '林宜敏', '70144' => '苏美波', '70308' => '张羽影',  
		'44753' => '杨祖林', '40577' => '傅胜豪', '39718' => '叶海新', '36965' => '杨文杰 ',  
		'558418' => '柳芳妹', '563582' => '吴雯雯',
	);
	
	$arr_1 = $arr_2 = array();
	$arr_1 = db_factory::query(" select * from " . TABLEPRE . "witkey_space where group_id in(5,7)");
	foreach($arr_1 as $key => $val){
		$val['real_name'] = $admin_name[$val['uid']];
		$arr_2[$val['uid']] = $val;
	}
	
	return $arr_2;
}