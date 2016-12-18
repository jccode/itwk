<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-21 16:40
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$task_id or kekezu::show_msg("参数未传递",$_K['siteurl']."/index.php?do=lottery");



$task_info = db_factory::get_one("select task_id,model_id,task_title,task_cash,real_cash,start_time,end_time,exec_time,task_status,lottery_config from ".TABLEPRE."witkey_task where task_id = '$task_id'");

//$task_info['task_status']==4 or $task_id or kekezu::show_msg("错误","index.php?do=lottery",3,"");

$task_info['lottery_config'] or $task_id or kekezu::show_msg("错误",$_K['siteurl']."/index.php?do=lottery",3,"摇奖配置信息错误，请联系客服");

$task_config = kekezu::get_task_config($task_info['model_id']);

$lottery_config = unserialize($task_info['lottery_config']);
/*if($uid == '1'){
	var_dump($lottery_config);
}*/
//我的摇奖信息
$my_lottery_info = db_factory::get_one("select l_number from ".TABLEPRE."witkey_task_lottery where task_id='$task_id' and uid = '$uid'");

switch ($op){
	case 'join':
		
		$msg = '';
		$status = 0;
		
		//判断是否允许他交稿
		in_array($uid,explode(',',$lottery_config['join_member'])) or $msg = '您不是有效的投稿人，无法参与摇奖';
		//判断是否已投递过
		$my_lottery_info and $msg = '您已参与过摇奖';
		
		if(!$msg){
			//执行摇奖
			$rand_key = rand(1,9).'.'.str_pad(rand(1,99999999),8,'0').str_pad(rand(1,9999999),7,'0');
			$lottery_obj = new Keke_witkey_task_lottery_class();
			$lottery_obj->setJoin_time(time());
			$lottery_obj->setL_number($rand_key);
			$lottery_obj->setTask_id($task_id);
			$lottery_obj->setUid($uid);
			$lottery_obj->setUsername($username);
			$res = $lottery_obj->create_keke_witkey_task_lottery();
			$res and $status = 1;
		}
		//数量判断代码
		
		kekezu::echojson($msg,$status,array('l_number'=>$rand_key));
		
		break;
}

//过期检测
if($task_info['exec_time']<time()&&$task_info['task_status']==4){
	$task_info['model_id']==1 and $time_obj = new sreward_time_class() or $time_obj = new mreward_time_class();
	$time_obj->exec_task($task_id);
	$task_info = header("Location:".$_K['siteurl']."/index.php?do=lottery&view=info&task_id=$task_id");
}

//参与者列表
$join_list = db_factory::get_table_data("lottery_id,uid,username,l_number,join_time,get_ratio,get_cash","witkey_task_lottery","task_id='$task_id'","l_number desc");



require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );
