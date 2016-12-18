<?php
/**
 * 个人信息修改
 * @copyright keke-tech
 */
defined ( 'IN_KEKE' )&&defined('ISWAP')&&ISWAP or kekezu::echojson ($wap_msg, 0);
$s = new keke_witkey_space_class();
$s->setWhere('uid='.$uid);
$s->setQq($qq);
$res = $s->edit_keke_witkey_space_class();
$res and kekezu::echojson('',1) or kekezu::echojson(array('r'=>'modify Failed!'));
die();










