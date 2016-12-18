<?php

/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );

//语言包
keke_lang_class::package_init ( "task" );
keke_lang_class::loadlang ( $do );
if ($ac == 'pub_task') {
	if (! intval ( $task_id )) {
		kekezu::echojson ( array ('r' => '不存在的任务编号' ), 0 );
	}
	if (! intval ( $model_id )) {
		kekezu::echojson ( array ('r' => '请选择任务模式' ), 0 );
	}
	
	if ($task_cash) {
		if ($task_cash < 50) {
			kekezu::echojson ( array ('r' => '任务金额不得小于50元' ), 0 );
		}
	}
	
	
	if ($task_type == 1) {
		$coverage = max ( $task_coverage, 1 );
		$tender = true;
	}
	$model_info = $kekezu->_model_list [$mdoel_id];
	$model_info ['config'] and $config = unserialize ( $model_info ['config'] ); //任务配置
	
	if(intval($model_id)==1){
		$maxday = kekezu::get_show_day ( $task_cash, $model_id );
		$minday = $config ['min_day'];
		
		if(intval ($task_day)>intval($maxday)){
			kekezu::echojson ( array ('r' => '任务周期不得大于'.$maxday."天" ), 0 );
		}elseif(intval ($task_day)<intval($minday)){
			kekezu::echojson ( array ('r' => '任务周期不得小于'.$minday."天" ), 0 );
		}
	}else{
		if (! intval ( $task_day )) {
			kekezu::echojson ( array ('r' => '请填写任务周期' ), 0 );
		} elseif (intval ( $task_day ) < 1 || intval ( $task_day ) > 30) {
			kekezu::echojson ( array ('r' => '任务周期范围1-30天' ), 0 );
		}
	}
	
	$task_obj = new Keke_witkey_task_class ();
	$task_obj->setModel_id ( $model_id );
	$task_obj->setTask_type ( $task_type );
	$sub_time = time () + ($task_day * 24 * 3600);
	$end_time = $sub_time + ($config ['choose_time'] * 24 * 3600);
	$task_obj->setEnd_time ( $end_time );
	$task_obj->setSub_time ( $sub_time );
	$task_obj->setNotice_count ( intval ( $rw_num ) );
	if ($tender) {
		$cash_cove = kekezu::get_cash_cove ( 'tender' );
		$cove = $cash_cove [$coverage];
		$task_cash = $cove ['end_cove'];
		$task_cash or $task_cash = $cove ['start_cove'];
		$task_obj->setTask_cash ( $task_cash );
		$task_obj->setTask_cash_coverage ( $coverage );
	} else {
		$task_obj->setTask_cash ( $task_cash );
		$task_obj->setTask_cash_coverage ( 0 );
	}
	$task_obj->setReal_cash ( $task_cash );
	if ($model_id == 4) { //招标任务.审核判断
		
		$task_obj->setTask_status ( 1 );
	} else {
		$task_obj->setTask_status ( 0 );
	}
	//修改记录
	$task_obj->setTask_id ( $task_id );
	$res = $task_obj->edit_keke_witkey_task ();
	
	if ($res){
		kekezu::echojson ( array ('r' => '发布成功' ), 1, intval ( $task_id ));
	} else {
		kekezu::echojson ( array ('r' => '服务器繁忙，请稍后...' ), 0 );
	}

}
