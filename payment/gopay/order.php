<?php

require 'gopay_class.php';
/**
 * 用户充值生成付款url 
 * @param string $charge_type  充值类型（order_charge订单充值。user_charge余额充值）
 * @param float $pay_amount 金额 (必填)
 * @param array $payment_config 商家号配置信息组数 (必填)
 * @param string $subject 订单标题 (必填)
 * @param int $order_id 订单ID（必填)
 * @param int $model_id 模型ID（无值表示余额充值，无付款动作)
 * @param int $obj_id   对象ID（ 可空)
 * @param $bank_code 银行代码
 * @return string url 
 */
function get_pay_url($charge_type, $pay_amount, $payment_config, $subject, $order_id, $model_id = null, $obj_id = null,$bank_code='CCB') {
	global $_K, $uid, $username;
	$body = "订单充值(from:" . $username . ")";
	$body = $t . "(from:" . $username . ")";
	$params = array (
					 "goodsName" => $subject,
					 "goodsDetail" => $body,
					 "tranDateTime"=>date('YmdHis',time()),
					 "merOrderNum" => "{$uid}-{$obj_id}-{$order_id}-{$model_id}",
					 "tranAmt" => $pay_amount
	);
	$gopay = new gopay_class ( $payment_config);
	$gopay->setGateway(false);//选择网关
	$gopay->set_params($params);//初始化参数
	$gopay->anti_phishing(false);//防钓鱼开关
	$gopay->setDirectConnect(false,$bank_code);//开启银行直连
	$gopay->build_sign();//生成签名
	return $gopay->create_url ();
}