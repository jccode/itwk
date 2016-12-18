<?php
/**
 * @copyright keke-tech
 * @author Ch
 * @version v 2.0
 * 2012-06-20 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$title = "申请开票";

if($taskid){
	$invoice_info=db_factory::get_one(sprintf("select * from %switkey_invoice where task_id=%d",TABLEPRE,$taskid));
	$local = explode(",",$invoice_info['iv_city']);
}else{
	kekezu::echojson("开票过程中出现错误，请刷新页面再试！",2,$return_info);
	die();
}

if($op){
	if($op=="add"){
		if($taskid){
			$invoice_obj =  new Keke_witkey_invoice_class();
			$task_info = db_factory::get_one(sprintf("select task_title,model_id,task_status,uid,username,task_cash,att_cash,task_type,single_cash,task_id from %switkey_task where task_id=%d",TABLEPRE,$taskid));
			$invoice_obj->setTask_id($taskid);
			$invoice_obj->setTask_title($task_info['task_title']);
			$invoice_obj->setModel_id($task_info['model_id']);
			$invoice_obj->setUid($task_info['uid']);
			$invoice_obj->setUsername($task_info['username']);
			$invoice_obj->setIv_client($iv_client);
			$invoice_obj->setIv_reason('设计费');
			$invoice_obj->setIv_contact($iv_contact);
			$invoice_obj->setIv_city($province.",".$city.",".$area);
			$invoice_obj->setIv_add($iv_add);
			$invoice_obj->setIv_zipcode($iv_zipcode);
			$invoice_obj->setIv_phone($iv_phone);
			
			$model_id = $task_info['model_id'];
			if($model_id==3){//计件任务在实际发放金上扣
				$count = db_factory::get_count('select count(work_id) from '.TABLEPRE.'witkey_task_work where task_id='.$task_info['task_id'].' and work_status=12');
				$iv_price = $count*$task_info['single_cash'];
			}elseif($model_id==1||$model_id==2){
				$iv_price = $task_info['task_cash'];
			}else{
				if($task_info['task_type']==3){//直接雇佣
					$iv_price = $task_info['task_cash'];	
				}else{
					$iv_price = db_factory::get_count(' select quote from '.TABLEPRE.'witkey_task_work where task_id='.$task_info['task_id'].' and work_status = 11 ');
				}
			}
			if(floatval($task_info['att_cash'])){//有增值费的。将增值费算上
				$iv_price+=$task_info['att_cash'];
				$invoice_obj->setIv_item_cash(floatval($task_info['att_cash']));//记录增值服务费
			}
			$iv_taxes = $iv_price*0.055;//税金
			$invoice_obj->setIv_price($iv_price);
			$invoice_obj->setIv_taxes($iv_taxes);
			
			$invoice_obj->setIv_taxes_status(0);
			$invoice_obj->setIv_fee(0);
			$invoice_obj->setIv_status(0);
			$invoice_obj->setIv_datetime(time());
			$invoice_obj->setTransport_type($transport_type);
			//根据不同任务设置开票时任务状态，各类任务可以分别判断
			$iv_tm_status=2;
			if(($task_info['model_id']==1 || $task_info['model_id']==2||$task_info['model_id']==4) and $task_info['task_status']<8){
				$iv_tm_status=1;
			}
			$invoice_obj->setIv_tm_status($iv_tm_status);
			/**
			 * 单人，多人在结束前申请开票，直接更改real_cash。
			 * 先从威客身上将税金扣除。如果在结束前在后台客服没有通过
			 * 这条申请，再将real_cash还原，不扣威客税金;
			 * 	悬赏任务在结束前的开票。只要客服没有直接不通过，
			 * 	那么就要按照通过默认来处理
			 */
			if(($task_info['model_id']==1 || $task_info['model_id']==2) and $task_info['task_status']<=5){
				$real_cash = $task_info['task_cash']*0.945;//任务部分的
				db_factory::execute(' update '.TABLEPRE.'witkey_task set real_cash='.$real_cash.' where task_id='.$task_info['task_id']);
			}
			//数据保存，如果已存在开票记录，则修改数据
			if($invoice_info){
				$invoice_obj->setIv_applicount($invoice_info['iv_applicount']+1);
				$invoice_obj->setWhere("iv_id=".$invoice_info['iv_id']);
				$res=$invoice_obj->edit_keke_witkey_invoice();
				if($res==0 || $res==1){
					$return_info=array("iv_id"=>$invoice_info['iv_id'],"task_id"=>$taskid);
					kekezu::echojson("重新申请开票成功！",1,$return_info);
				}else{
					$return_info["task_id"]=$taskid;
					kekezu::echojson("重新申请开票失败，请联系客服人员。",0,$return_info);
				}
			}else{
				$invoice_obj->setIv_applicount(1);
				//保存数据
				$res=$invoice_obj->create_keke_witkey_invoice();
				if($res){
					//修改任务表开票状态
					$task_obj = new Keke_witkey_task_class();
					$task_obj->setWhere("task_id=".$taskid);
					$task_obj->setInvoice_status(1);
					$task_obj->edit_keke_witkey_task();
					//以json返回信息
					$return_info=array("iv_id"=>$res,"task_id"=>$taskid);
					kekezu::echojson("开票申请成功！",1,$return_info);
				}else{
					$return_info["task_id"]=$taskid;
					kekezu::echojson("开票申请失败，请联系客服人员。",0,$return_info);
				}
				die();
			}
		}
	}
}
//if(isset($ivid)){
//	$invoice_info=db_factory::get_one(sprintf("select * from %switkey_invoice where iv_id=%d",TABLEPRE,$ivid));
//}

require $kekezu->_tpl_obj->template ( 'ajax/ajax_invoice' );