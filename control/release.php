<?php
/**
 * 任务的发布入口页
 */
defined ( 'IN_KEKE' ) or exit('Access Denied');

 //语言包
keke_lang_class::package_init("task");
keke_lang_class::loadlang($do);

//标题
$page_title= "发布任务". '--' . $_K ['html_title'];

//发布类型
$pub_mode or $pub_mode='professional';

//类似需求   但没传id
$pub_mode=='onkey' and	($t_id or kekezu::show_msg($_lang['warning'],$_SERVER['HTTP_REFERER'],3,$_lang['onekey_pub_notice'],"warning"));

//默认步骤
$r_step or $r_step='step1';

//模型列表
$model_list = kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'task' and model_status='1'", 'model_id asc ', '', '', 'model_id', 3600 );
if($r_step!='step1'){//未登录允许进入第一步
	kekezu::check_login();
}
if($ac=='check_login'){//检测登录
	$_SESSION['uid'] and kekezu::echojson('',1) or kekezu::echojson('',0);die();
}

//任务模型默认第1个
if(!$model_id&&$r_step!='step1'){
	$model_ids = array_keys($model_list);
	$model_id = $model_ids['0'];
}

//获得模型信息
$model_id and $model_info = $model_list[$model_id] or $model_id = 0;

/*阶段导航**/
$stage_nav=array("1"=>array("step1",$_lang['stage_nav_step1_a'],$_lang['stage_nav_step1_b']),
				"2"=>array("step2",$_lang['stage_nav_step2_a'],$_lang['stage_nav_step2_b']),
				"3"=>array("step3",$_lang['stage_nav_step3_a'],$_lang['stage_nav_step3_b']),
				"4"=>array("step4",$_lang['stage_nav_step4_a'],$_lang['stage_nav_step4_b']));

//基础链接
$basic_url = $_K['siteurl']."/index.php?do=release&pub_mode=$pub_mode&t_id=$t_id&task_id=$task_id&model_id=".$model_id."&r_step=".$r_step;

//检查发布权限
if($model_id){
	$r = keke_privission_class::check_condit_priv($uid,'pub',$model_list[$model_id]['model_name'],'允许发布');
	
	$ajax and $r_type='json' or $r_type='normal';
	$r['pass'] or kekezu::keke_show_msg ( $url, $this->_priv ['notice'] . "没有发布权限。", "error", $r_type );
	if($ajax=='check_priv'){
		$r['pass'] and kekezu::keke_show_msg ( $url, '允许发布', '', $r_type );die();
	}
	
}
 
//这段代码用于"继续发布" 或者 "返回上一步"时 读取信息用
$release_info = array();
if ($task_id){
	$release_info =  db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id'");
	//以及对应的附件
	$file_list = kekezu::get_table_data("file_id,obj_type,obj_id,task_id,work_id,task_title,file_name,save_name,uid,username,on_time","witkey_file","obj_type='task' and task_id='$task_id'");
}
	
switch($pub_mode){
	case "professional"://默认发布	
		break;
	case "guide":
		break;
	case "onekey"://这是发布类似需求
		//读被类似的任务信息
		if ($t_id){
			$t_info = db_factory::get_one("select 
				task_id,
				model_id,
				work_count,
				single_cash,
				task_title,
				task_desc,
				indus_id,
				indus_pid,
				start_time,
				end_time,
				sub_time,
				task_cash,
				task_cash_coverage,
				must_choosework,
				notice_count,
				city
			from ".TABLEPRE."witkey_task where task_id = '$t_id'");
			$release_info = array_merge($release_info,$t_info); //合并需要初始化的数据
		}
		break;
}

//任务金额必须为整数
$release_info['task_cash'] = intVal($release_info['task_cash']);

//已存在的任务信息
$task_id  and  $task_info = db_factory::get_one("select * from ".TABLEPRE."witkey_task where task_id = '$task_id'");

//非第1 2步   转到任务模型下执行
if($model_id&&!in_array($r_step,array('step1','step2'))){
	require "task/".$model_info['model_dir']."/control/release.php";
}elseif ($ac == 'save') {//2--->1
	echo json_encode ( array ('msg' => 1 ) );
}else{

	switch($r_step){
		case 'step1':
//		if(kekezu::submitcheck($formhash)){
		if($formhash){
			// 发布任务时间间隔判断, 默认60s
			/* if ( $_SESSION['latest_post_time'] && ( time() - $_SESSION['latest_post_time'] < $basic_config['post_interval'] ) ) {
				kekezu::keke_show_msg ( '', '任务发布太过频繁(' . $basic_config['post_interval'] . 's), 请稍候再试.', 'error' );
			} */
			//服务器端验证验证码
			$code_res = kekezu::check_secode($txt_code);
			if($code_res != true){
				kekezu::keke_show_msg('', '验证码错误，请正确提交表单！', 'error');
			}
			
			// set indus_id, indus_pid. 取最后一个非空的id为indus_id, 倒数第二个非空为indus_pid
			$i = count($indus_ids)-1;
			while ($i >= 0 && $indus_ids[$i] == "") {
				$i--;
			}
			$indus_id = $i >= 1 ? $indus_ids[$i] : 0;
			$indus_pid = $i >= 1 ? $indus_ids[$i-1] : 0;
			
			//其他字段 验证
			$check_res = true;
// 			isset($indus_pid) or $indus_pid=0;
// 			isset($indus_id) or $indus_id=0;
		/*	if($indus_pid==0||$indus_id==0){
				$check_res = false;
			}  */
			$titlelen = kekezu::strlen_utf8($txt_title);
			if($titlelen<10||$titlelen>20){
				$check_res = false;
			}
			$conlen = kekezu::strlen_utf8($tar_content);
			if($conlen<10){
				$check_res = false;
			}
			if(!isset($point)){
				$check_res = false;
			}
			if(!isset($cont[mobile])){
				$check_res = false;
			}
		/*	if(!$check_res){
				kekezu::keke_show_msg('', '表单提交错误！', 'error');
			}   */
			

			// 敏感词检测
			kekezu::k_match ( array ($kekezu->_sys_config ['ban_content'] ), $txt_title ) and kekezu::keke_show_msg ( '', $_lang ['sensitive_word'], 'error' );
			kekezu::k_match ( array ($kekezu->_sys_config ['ban_content'] ), $tar_content ) and kekezu::keke_show_msg ( '', $_lang ['sensitive_word'], 'error' );

				$task_obj = new Keke_witkey_task_class();
				$task_obj->setIndus_id($indus_id);
				$task_obj->setIndus_pid($indus_pid);
				$task_obj->setTask_title($txt_title);
				$task_obj->setCity($province . "," . $city . "," . $area);//地区
				$task_obj->setAddress($txt_address);
				$task_obj->setPoint($point);
				$task_obj->setTask_desc($tar_content);
				$task_obj->setTask_status(-1);
				$task_obj->setUid($uid);
				$task_obj->setUsername($username);
				$task_obj->setStart_time(time());
				$task_obj->setCash_status(0);
				$default_listviewtype = $indus_arr[$indus_id]['worklist_viewtype'];
				$default_listviewtype or $default_listviewtype = $indus_p_arr[$indus_pid]['worklist_viewtype'];
				$task_obj->setWorklist_viewtype($default_listviewtype);
				
				//联系方式保存
				$contact_arr = array();
				$contact['mobile'] and $contact_arr['mobile'] = $cont['mobile'];
				$contact['email'] and $contact_arr['email'] = $cont['email'];
				$contact['qq'] and $contact_arr['qq'] = $cont['qq'];
				$contact['msn'] and $contact_arr['msn'] = $cont['msn'];
				$task_obj->setContact(serialize($contact_arr));
				
				
				//创建记录
				if ($task_id){
					$task_obj->setTask_id($task_id);
					$task_obj->edit_keke_witkey_task();
				}
				else{
					$task_id = $task_obj->create_keke_witkey_task();
				}
				//附件赋值
				$file_ids and db_factory::execute("update ".TABLEPRE."witkey_file set task_id = '$task_id',obj_id='$task_id' where file_id in ($file_ids)");
				
				// 保存发布时间, 防止频繁发布
				$_SESSION['latest_post_time'] = time();

				header ( "location:".$_K['siteurl']."/index.php?do=release&pub_mode=$pub_mode&t_id=$t_id&task_id=$task_id&r_step=step2" );
				die();
					
			}
			
			//允许的附件格式
			$ext_types   = kekezu::get_ext_type ();
			
			
			
			//联系方式的解出
			if ($release_info['contact']){
				$release_info['cont'] = unserialize($release_info['contact']);
			}
			
			$release_info or $release_info = array('cont'=>array(
				'mobile'=>$user_info['mobile'],
				'qq'=>$user_info['qq'],
				'email'=>$user_info['email'],
				'msn'=>$user_info['msn']
			));
			
			$release_info['cont']['mobile'] or $release_info['cont']['mobile'] = $user_info['mobile'];
			$release_info['cont']['email'] or $release_info['cont']['email'] = $user_info['email'];
			$release_info['cont']['qq'] or $release_info['cont']['qq'] = $user_info['qq'];
			$release_info['cont']['msn'] or $release_info['cont']['msn'] = $user_info['msn'];
			
			if($release_info['city']){
				$loca=explode(',',$release_info['city']);
			}
			
			require  keke_tpl_class::template ($do);
			break;
		case 'step2':
			$r = keke_privission_class::check_priv($task_id, $user_info, 'pub','单人悬赏任务','允许发布');
			if ($ajax =='check_priv'){
				$r['pass'] and kekezu::keke_show_msg ( $url, $_lang ['can_pub'], '', 'json' ) or kekezu::keke_show_msg ( $url, $this->_priv ['notice'] . $_lang ['not_rights_pub_task'], "error", 'json' );
				die();
			}
			$task_info=$release_info;
			
			if (kekezu::submitcheck($formhash)) {
				require "task/".$model_info['model_dir']."/control/release.php";
			}
			
			//已被设为多人任务时   读取价格区间
			if($release_info['model_id']==2){
				$prize_list = db_factory::get_table_data('prize,prize_cash',"witkey_task_prize","task_id='$task_id'","prize","","","prize");
			}
			
			//计算时间差值得到上次输入的时间
			$release_info['task_day'] = $task_info['sub_time'] ? 
			     round(($task_info['sub_time']-$task_info['start_time'])/24/3600) : "";
			//招标任务配置
			$tender_config = kekezu::get_task_config(4);
			$sreward_config = kekezu::get_task_config(1);
			$mreward_config = kekezu::get_task_config(2);
			$preward_config = kekezu::get_task_config(3);
			
			//招标价格区间
			$cash_cove = kekezu::get_cash_cove('tender');
			require  keke_tpl_class::template ($do);
			break;
	}
}



$d1= array(1858,1867,1868,1869,1870);
$d2= array(1871,1872,1873,1958);
$d3= array(1874,1875);
$d4= array(1877,1878,1879,1880,1881,1882,1883,1884);
$d5= array(1885,1886,1887,1888,1889,1890,1891,1892,1893);