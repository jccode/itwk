<?php

/**
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' ) && defined ( 'ISWAP' ) && ISWAP or kekezu::echojson ( $wap_msg, 0 );

//语言包
keke_lang_class::package_init ( "task" );
keke_lang_class::loadlang ( $do );
$pub_mode = 'professional';
$indus_p_arr = $kekezu->_indus_p_arr;
$indus_c_arr = $kekezu->_indus_c_arr;
$indus_arr = $kekezu->_indus_arr;

if ($ac == 'pub_task') {
	$task_title = kekezu::gbktoutf ( $task_title );
	$task_desc = kekezu::gbktoutf ( $task_desc );
	
	if (! $indus_pid) {
		kekezu::echojson ( array ('r' => '请选择父级分类' ), 0 );
	}
	if (! $indus_id) {
		kekezu::echojson ( array ('r' => '请选择子级分类' ), 0 );
	}
	
	if (! $task_title) {
		kekezu::echojson ( array ('r' => '请填写任务标题' ), 0 );
	} elseif (kekezu::utf8_strlen ( $task_title ) < 10 || kekezu::utf8_strlen ( $task_title ) > 20) {
		kekezu::echojson ( array ('r' => '任务标题长度10-20字' ), 0 );
	}
	
	if (! $task_desc) {
		kekezu::echojson ( array ('r' => '请填写任务描述' ), 0 );
	} elseif (kekezu::utf8_strlen ( $task_desc ) < 20 || kekezu::utf8_strlen ( $task_desc ) > 1500) {
		kekezu::echojson ( array ('r' => '任务描述长度20-1500字' ), 0 );
	}
	
	if (! $phone) {
		kekezu::echojson ( array ('r' => '请填写联系电话' ), 0 );
	} elseif (! kekezu::is_mobile ( $phone )) {
		kekezu::echojson ( array ('r' => '联系电话格式15812345678' ), 0 );
	}
	
	if($qq){
		if (strlen ( intval ( $qq ) ) < 4 || strlen ( intval ( $qq ) ) > 12) {
			kekezu::echojson ( array ('r' => '联系QQ格式123456' ), 0 );
		}
	}
	
	
	$task_obj = new Keke_witkey_task_class ();
	$task_obj->setIndus_id ( $indus_id );
	$task_obj->setIndus_pid ( $indus_pid );
	$task_obj->setTask_title ( $task_title );
	$task_obj->setTask_desc ( $task_desc );
	$task_obj->setTask_status ( - 1 );
	$task_obj->setUid ( $uid );
	$task_obj->setUsername ( $username );
	$task_obj->setStart_time ( time () );
	$task_obj->setCash_status ( 0 );
	$default_listviewtype = $indus_arr [$indus_id] ['worklist_viewtype'];
	$default_listviewtype or $default_listviewtype = $indus_p_arr [$indus_pid] ['worklist_viewtype'];
	$task_obj->setWorklist_viewtype ( $default_listviewtype );
	
	//联系方式保存
	$contact_arr = array ();
	$contact_arr ['mobile'] = $phone;
	$contact_arr ['qq'] = $qq;
	$task_obj->setContact ( serialize ( $contact_arr ) );
	
	//创建记录
	$task_id = $task_obj->create_keke_witkey_task ();
	
	if ($task_id) {
		kekezu::echojson ( array ('r' => '发布成功' ), 1, intval ( $task_id ) );
	} else {
		kekezu::echojson ( array ('r' => '服务器繁忙，请稍后...' ), 0 );
	}

}
