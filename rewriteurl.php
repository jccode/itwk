<?php

//本文件给伪静态链接解析用  主要针对2级目录

if($_GET['url_param']){
	$param_list = explode('-',$_GET['url_param']);
	
	$param_exec_count = 0;
	
	$param_count = count($param_list);
	$param_list[0] and $do = $_GET['do'] = $param_list[0] and $param_exec_count++;
	
	while ($param_exec_count+2 <= $param_count){
		
		$param_p = $param_list[$param_exec_count];
		$param_v = $param_list[$param_exec_count+1];
		
		$$param_p = $_GET[$param_p] = $param_v;
		
		$param_exec_count += 2;
	}
	
	require 'index.php';
	
} 



