<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');

/**店铺信息**/
$shop_info=db_factory::get_one(sprintf(" select * from %switkey_shop where uid='%d' ",TABLEPRE,intval($uid)));
$shop_info['shop_info'] and $shop_info['shop_info'] = unserialize($shop_info['shop_info']); 

$ops = array('basic','domain','skill','job','job_manage','service','about','article','article_manage','case','design','product','cate','member','link');

in_array($op,$ops) or $op ="basic";

$ac_url = "index.php?do=$do&view=$view&op=$op";

require 'user_'.$view.'_'.$op.'.php';