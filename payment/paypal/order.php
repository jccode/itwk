<?php
 /**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-9-8下午04:28:19
 */

defined('IN_KEKE') or exit('Access Denied');

/**
 * 生成付款url
 * @param string $charge_type  充值类型（order_charge订单充值。user_charge余额充值）
 * @param float $pay_amount 金额 (必填)
 * @param array $payment_config 商家号配置信息组数 (必填)
 * @param string $subject 订单标题 (必填)
 * @param int $order_id 订单ID（必填)
 * @param int $model_id 模型ID（无值表示余额充值，无付款动作)
 * @param int $obj_id   对象ID（ 可空)
 * @param string $service  付款类型(可空）
 * @param string $sign_type 签名类型(可空）
 * @return string url
 */
function get_pay_url($charge_type,$pay_amount,$payment_config,$subject,$order_id,$model_id=null,$obj_id=null, $service ="_xclick", $sign_type = 'MD5', $method = ''){
	global $_K,$uid;
	//CHARSET=='gbk' and $subject=kekezu::gbktoutf($subject);
	$subject = 'paypal online pay(UID='.$uid.')';
	$seller_account = $payment_config['account'];
	$return_url = $_K ['siteurl'] . '/payment/paypal/return.php';
	$notify_url = $_K ['siteurl'] . '/payment/paypal/notify.php';
	$show_url = $_K ['siteurl'] . "/index.php?do=user&view=finance&op=order";
	$out_trade_no = "charge-{$charge_type}-{$uid}-{$obj_id}-{$order_id}-{$model_id}";
	$total_money =$pay_amount ;
    $p = array("business"=>$seller_account,
    			"cmd"=>'_xclick',
    			"custom"=>$out_trade_no,
    			"amount"=>$pay_amount,
    			"v_moneytype"=>"CNY",
    			"notify_url"=>$notify_url,
    			"return"=>$return_url,
    			"cancel_return"=>$show_url,
    		 	"currency_code"=>"HKD",
    		 	"item_name"=>$subject,);
	if($method == 'form'){
		return  build_postform($p);
	}
	else{
		return create_url($p);
	}
}

function create_url($p){
	ksort($p);
    reset($p);
    $arg  = "";
    while (list ($key, $val) = each ($p)) {
		if ($key != "service" && $key != "_input_charset")
			$arg.=$key."=".urlencode($val)."&";
		else $arg.=$key."=".$val."&";
    }
    $arg = substr($arg,0,count($arg)-2);
    return "https://www.paypal.com/cgi-bin/webscr?$arg";
}

function build_postform($p) {
	//测试地址：https://sandbox.paypal.com/cgi-bin/webscr
	//正式地址:https://www.paypal.com/cgi-bin/webscr 
	$sHtml = "<form id='frm_paypal' name='frm_paypal' target='_blank' action='https://www.paypal.com/cgi-bin/webscr' enctype=\"application/x-www-form-urlencoded\" method='post'>";
	while (list ($key, $val) = each ($p)) {
		$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
	}
	$sHtml = $sHtml."<button type='button' name='v_action' value='Paypal确认付款' onClick='document.forms[\"frm_paypal\"].submit();'>Paypal确认付款</button>";
	return $sHtml;
}



