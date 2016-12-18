<?php
/**
 * 用户图象输出
 * this not free,powered by keke-tech
 * @author jiujiang
 * @charset:GBK  last-modify 2011-10-15-下午09:04:22
 * @version V2.0
 * 
 */
if ($a) {
	$method = $a;
	$uid = $input;
	$class = new keke_user_avatar_class();
	echo $data=$class->$method($uid);	
	exit ();
}else{
    exit('parame is error');
}
