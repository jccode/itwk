<?php
/**
 * 客客推广联盟，初始化配置类
 * @var unknown_type
 */
global $config, $_K;

$config ['application'] = 1; //联盟应用是否启用
$config ['auto_commit'] = 1; //是否使用自动提交，若使用则在任务发布时任务会自动提交至联盟平台
$config ['log'] = 'ep'; //唯一前缀标识、请自行更改
$config ['keke_app_id'] = '238244'; //
//用户申请的联盟应用secret
$config ['keke_app_secret'] = '0dc4fe5097bf58a5060075f2137a67b1'; //


//默认签名方式
$config ['sign_type'] = 'MD5';
//默认字符编码
$config ['_input_charset'] = strtoupper ( CHARSET );
//同步回调地址
$config ['return_url'] = $_K ['siteurl'] . '/keke_client/keke/return.php';
//异步回调地址
$config ['notify_url'] = $_K ['siteurl'] . '/keke_client/keke/notify.php';