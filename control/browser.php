<?php

/**
 * @copyright keke-tech
 * @author Monkey
 * @version v 2.0
 * 2010-8-11上午08:05:04
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');
$page_title=$_lang['brower_up'].' - '.$_K['html_title'];
 
require  $kekezu->_tpl_obj->template($do);
 
