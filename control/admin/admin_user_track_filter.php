<?php
/**
 * 客户跟踪管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 158 );

 //业务组成员
$yewuyuan = get_sale_member();

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
		kekezu::echojson ( "该用户已被他人提取", "0" );
	}else{
		$res = db_factory::execute (" update ".TABLEPRE."witkey_space set track_uid = '$admin_info[uid]', last_track_time = ".time()." where uid='$t_uid'");	
		$res and kekezu::echojson ( "用户提取成功", "1" );
	}
	
	exit();
}
//echo $_SERVER["HTTP_REFERER"];
//查询
$table_class = new keke_table_class ( 'witkey_space' );
$url = "index.php?do=$do&view=$view&op={$op}&w[auth_realname]=$w[auth_realname]
&w[auth_bank]=$w[auth_bank]&w[auth_mobile]=$w[auth_mobile]&w[auth_email]=$w[auth_email]
&w[balance]=$w[balance]&w[take_num]=$w[take_num]&w[track_uid]=$w[track_uid]&w[last_login_day]=$w[last_login_day]
&w[ord][]=".$w['ord']['0']."&w[ord][]=".$w['ord']['1'];

//每页显示多少条，默认10
$page and $page = intval($page) or $page = 1;
$slt_page_size = intval ( $slt_page_size ) ? intval ( $slt_page_size ) : 15;

$where = " where user_role = 2 and track_uid =0 ";
$w[auth_realname] and $where .= " and auth_realname = $w[auth_realname]";
$w[auth_bank] and $where .= " and auth_bank = $w[auth_bank]";
$w[auth_mobile] and $where .= " and auth_mobile = $w[auth_mobile]";
$w[auth_email] and $where .= " and auth_email = $w[auth_email]";
$w[balance] and $where .= " and balance >= $w[balance]";
$w[take_num] and $where .= " and take_num >= $w[take_num]";
$w[last_login_day] and $where .= " and (reg_time > ".(time()-($w[last_login_day]*86400))." or last_login_time > ".(time()-($w[last_login_day]*86400)).")";
$w['ord'] and $ord_where = ' order by '.$w['ord']['0'].' '.$w['ord']['1'] or $ord_where = ' order by uid desc';
 
$count = db_factory::get_count("select count(*) from ".TABLEPRE."witkey_space".$where);
$sql = "select * from ".TABLEPRE."witkey_space";
$pages = $kekezu->_page_obj->getPages ( $count, $slt_page_size, $page, $url );
$where .= $ord_where; 
$sql .= $where.$pages['where'];//echo $sql;
$data_arr = db_factory::query($sql);	

require $template_obj->template ( 'control/admin/tpl/admin_'.$do.'_'.$view );

function get_sale_member(){
	$admin_name = array(
		'41645' => '李建川', '44505' => '朱丹梅', '36357' => '黄志达', '88075' => '杨浩',    
		'69682' => '蔡清希', '78082' => '林宜敏', '70144' => '苏美波', '70308' => '张羽影',  
		'44753' => '杨祖林', '40577' => '傅胜豪', '39718' => '叶海新', '36965' => '杨文杰 ',  
		'558418' => '柳芳妹',
	);
	
	$arr_1 = $arr_2 = array();
	$arr_1 = db_factory::query(" select * from " . TABLEPRE . "witkey_space where group_id in(5,7)");
	foreach($arr_1 as $key => $val){
		$val['real_name'] = $admin_name[$val['uid']];
		$arr_2[$val['uid']] = $val;
	}
	
	return $arr_2;
}