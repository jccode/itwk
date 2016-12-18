<?php

defined('ADMIN_KEKE') or exit('Access Denied');
kekezu::admin_check_role ( 171 );
//语言包初始化
keke_lang_class::package_init ( "task_{$model_info ['model_dir']}" );
keke_lang_class::loadlang("task_process");

$views = array('list','config','edit','track','task','op','cove','process','lottery','work');
in_array($view,$views) or $view = "list";


require "task_$view.php";
