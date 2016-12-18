<?php
/**
 * 客户跟踪管理
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 158 );
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
	'9' => '空号停机' //'9' => '空号停机' '8' => '未接通的客户', 
);

if($t_username){
	$space_info = kekezu::get_user_info ( $t_username, 1 );
}elseif($t_uid){
	$space_info = kekezu::get_user_info ( intval ( $t_uid ) );
}

if ( $space_info['track_uid'] ) {
    $track_user_info = kekezu::get_user_info ( $space_info['track_uid'] );
    $space_info['track_username'] = $track_user_info['truename'] ? $track_user_info['truename'] : $track_user_info['username'];    
}
$yewuyuan = array(
	'41645' => '李建川',  //龙帅 41645  李建川
	'44505' => '朱丹梅',  //晴天小猪 44505  朱丹梅
	'36357' => '黄志达',  //da87431439 36357 黄志达
	'88075' => '杨浩',    //杨君浩 88075 杨浩
	'69682' => '蔡清希',  //caiqingxi 69682  蔡清希
	'78082' => '林宜敏',  //麦兜  78082 林宜敏
	'70144' => '苏美波',  //小惜哈  70144 苏美波
	'70308' => '张羽影',  //smilezyy01 70308 张羽影
	'44753' => '杨祖林',  //yangzu2008 44753  杨祖林
	'40577' => '傅胜豪',  //峻险牛犊  40577 傅胜豪
	'39718' => '叶海新',  //daven 39718 叶海新
	'36965' => '杨文杰 ',  //wejir 36965  杨文杰
        '563582' => '吴雯雯'    // vava 563582 吴雯雯
);

if ($sbt_edit) {
	$fds[uid] = intval($fds[uid]);
	if(!$fds[uid]){
		kekezu::admin_show_msg ("请选择要跟踪的用户","index.php?do={$do}&view=track",5,'','warning' );
	}
	
	 //判断权限
	if($space_info['track_uid'] && $space_info['track_uid'] != $admin_info['track_uid']){// var_dump($space_info['track_uid'],$admin_info['track_uid']);exit;
		kekezu::admin_show_msg ("此用户已被  ".$space_info['track_username']."  跟踪，您无法添加跟踪记录。","index.php?do={$do}&view=track",5,'','warning' );
	}
	
	 //加添跟踪记录
	$track_obj = new Keke_witkey_member_track_class();
	$track_obj->setC_type(intval($slt_track_type));
	$track_obj->setDateline(time());
	$track_obj->setT_content($txt_t_content);
	$track_obj->setT_reserve($txt_t_reserve);
	$track_obj->setUid($fds[uid]);
	$track_obj->setUsername($fds[username]);
	$track_obj->setReserve_time(strtotime($txt_reserve_time));
	$track_obj->setT_uid($admin_info['uid']);
	$track_obj->setT_username($admin_info['username']);
	$res = $track_obj->create_keke_witkey_member_track();
	
	 //修改跟踪的业务员
	//$res2 = db_factory::execute("update ".TABLEPRE."witkey_space set track_type =".intval($slt_track_type).", track_reserve = '".$txt_t_reserve."' , track_uid ='$admin_info[uid]', track_username='$admin_info[username]' ,last_track_time = ".time()." where uid = ".intval($fds[uid]));
	$space_obj = new Keke_witkey_space_class();
	$space_obj->setWhere("uid = '$fds[uid]'");  
	$space_obj->setTrack_type(intval($slt_track_type));
	$space_obj->setTrack_uid($admin_info[uid]);
	$space_obj->setTrack_username($admin_info[username]);
	$space_obj->setLast_track_time(time());

	 //是否要预约
	if($slt_track_type == 1){
		$space_obj->setTrack_reserve($slt_track_type); 
	}else{
		$space_obj->setTrack_reserve(0); 
	}
	$res2 = $space_obj->edit_keke_witkey_space();
	
	if($res){
		kekezu::admin_system_log ( "添加客户跟踪记录".':' .$res ); //日志记录
		res and kekezu::admin_show_msg ("客户跟踪记录添加成功","index.php?do={$do}&op=my&view=track&page={$page}",3,'','success' ) or kekezu::admin_show_msg ("客户跟踪记录添加失败","index.php?do={$do}&view=track_add",3,'','warning' );
	}
}

if($ac=='get_user_info'){
	kekezu::echojson(1,1,kekezu::get_user_info($guid));
	die();
}

if($t_username){
	$track_arr = db_factory::query ( "select * from ".TABLEPRE."witkey_member_track where username = '$t_username' order by t_id desc" );
}elseif($t_uid){ 
	$track_arr = db_factory::query ( sprintf ( "select * from %switkey_member_track where uid = (%s) order by t_id desc", TABLEPRE, intval($t_uid) ) );
}
require $template_obj->template ( 'control/admin/tpl/admin_'.$do.'_'.$view );