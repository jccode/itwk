<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 11:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$vip_level_arr = db_factory::query("select * from " . TABLEPRE . "witkey_vip_level where brand='cn'");
$all_vip_level_arr = db_factory::query("select * from " . TABLEPRE . "witkey_vip_level");
$vip_level_arr or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=vip&view=index', 3, '暂时未开通VIP商铺！', 'warning');
$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");
	
 //申请（开通/续费）
if($is_but){	
	$uid or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=login', 3, '请先登录再进行此操作！', 'warning');	
	$shop_info or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=user&view=space', 3, '请先开通普通商铺！', 'warning');
	$vip_level = list_search($all_vip_level_arr, array('level_id' => $level_id),1);
	$vip_level or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=vip&view=open', 3, '您的操作不合法请重新操作！', 'warning');
	$vip_level['price_config'] and $pay_arr = list_search(unserialize($vip_level['price_config']), array('month' => $month),1);
	$amount = $pay_arr['price'];
	$amount or kekezu::show_msg('消息提示', $_K['siteurl'].'/index.php?do=vip&view=open', 3, '您的操作不合法请重新操作！', 'warning');
	
	switch($pay_arr['month']){
		case '6': $month = 185; break;
		case '12': $month = 365; break;
		case '24': $month = 730; break;
		case '36': $month = 1095; break;
	}
	
	 //vip购买记录
	$history_obj = new Keke_witkey_vip_history_class();
	$history_obj->setUid( $uid );
	$history_obj->setUsername( $username );
	$history_obj->setH_status( 0 );
	$history_obj->setLevel_id( $level_id );
	$history_obj->setDay( $month );
	$history_obj->setCash_cost( $amount );
	$history_res = $history_obj->create_keke_witkey_vip_history();
	$history_res or kekezu::show_msg($_lang['fail'], $_K['siteurl'].'/index.php?do=vip&view=open', 3, 'VIP购买操作失败！', 'warning');
	
	 //付加金额、生成支付的订单
	$order_obj = new Keke_witkey_order_class ();
	$order_obj->setOrder_amount ( $amount );
	$order_obj->setOrder_status ( 'wait' );
	$order_obj->setOrder_name ( '开通'.$vip_level['level_name'] );
	$order_obj->setOrder_uid ( $uid );
	$order_obj->setOrder_username ( $username );
	$order_obj->setObj_type ( 'buy_vip' );//要和财务表的保持一致  扣款是属于vip购买扣款
	$order_obj->setObj_id ( $history_res );//这里存入的时间上是history_id  因为要在回调中处理
	$order_obj->setOrder_time(time());
	
	 //因为完成付款之前可能会有重复操作
	$order_exsit = db_factory::get_one ( "select order_id from " . TABLEPRE . "witkey_order where obj_type='vip_shop' and obj_id='$shop_info[shop_id]'" );
	if ($order_exsit) { 
		$order_obj->setOrder_id ( $order_exsit ['order_id'] );
		$order_obj->edit_keke_witkey_order ();
		$order_id = $order_exsit ['order_id'];
	} else {
		$order_id = $order_obj->create_keke_witkey_order ();
	}
	
	$order_id or kekezu::show_msg($_lang['fail'], $_K['siteurl'].'/index.php?do=vip&view=open', 3, 'VIP购买操作失败！', 'warning');	
	header ( "location:".$_K['siteurl']."/index.php?do=pay&order_id=$order_id&obj_type=vip_shop" );
	die ();
}	
 //已开通
if($shop_info['isvip']){  
	//根据$_SESSION['brand']来判断品牌
	$level_id = $shop_info['shop_level'];
	$vip_level = list_search($vip_level_arr, array('level_id'=>$level_id),1);
    switch($_SESSION['brand']){
    	case 'tw':
    		//台湾馆vip
    		$vip_level['rule_config_tw'] and $vip_level['rule_config_tw'] = unserialize($vip_level['rule_config_tw']);
    		$vip_level['price_config_tw'] and $vip_level['price_config_tw'] = unserialize($vip_level['price_config_tw']);
    		if(is_array($vip_level['price_config_tw'])){
    			foreach($vip_level['price_config_tw'] as $k=>$v){
    				unset($vip_level['price_config_tw'][$k]);
    				$vip_level['price_config_tw'][$v['month']] = $v;
    			}
    		}
    		$vip_level['rule_config']=$vip_level['rule_config_tw'];
    		$vip_level['price_config']=$vip_level['price_config_tw'];
    		$vip_level['price_config']=$vip_level['price_config_tw'];
    		$vip_level['level_name']=$vip_level['level_name_tw'];
    		break;
    	default:
    		$vip_level['rule_config'] and $vip_level['rule_config'] = unserialize($vip_level['rule_config']);
    		$vip_level['price_config'] and $vip_level['price_config'] = unserialize($vip_level['price_config']);
    		if(is_array($vip_level['price_config'])){
    			foreach($vip_level['price_config'] as $k=>$v){
    				unset($vip_level['price_config'][$k]);
    				$vip_level['price_config'][$v['month']] = $v;
    			}
    		}
    		break;
    }
}else{ //未开通	 
	in_array($level_id,array(2,3)) or $level_id = $vip_level_arr[0]['level_id']; //当前选择的VIP类型
	$vip_level = list_search($vip_level_arr, array('level_id'=>$level_id),1);
	$vip_level['rule_config'] and $vip_level['rule_config'] = unserialize($vip_level['rule_config']); 
	$vip_level['price_config'] and $vip_level['price_config'] = unserialize($vip_level['price_config']); 
	if(is_array($vip_level['price_config'])){
		foreach($vip_level['price_config'] as $k=>$v){
			unset($vip_level['price_config'][$k]);
			$vip_level['price_config'][$v['month']] = $v;
		}
	}	
}

$page_title = 'IT帮手网VIP商铺开通直通车与VIP管理服务_IT帮手网';
$page_keyword = 'VIP商铺，VIP直通车，VIP管理服务，开通VIP，管理VIP';
$page_description ='IT帮手网VIP商铺让威客拥有更多VIP特权，更快的找到威客任务，兼职赚钱更轻松。IT帮手网VIP商铺开通管理功能，对IT帮手网VIP商铺进行开通VIP和管理VIP的日常操作。';

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );

function list_search($list,$condition,$row='') {
    if(is_string($condition))
        parse_str($condition,$condition);

    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find){
        	if($row == 1){
        		$resultSet  =  &$list[$key];
        		break;
        	}else{
        		$resultSet[]  =  &$list[$key];
        	}
        	
        }
            
    }
    return $resultSet;
}