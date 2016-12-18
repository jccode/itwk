<?php

define("IN_KEKE",TRUE);
require 'app_comm.php';
$ops = array('time','tag');
in_array($op,$ops) or $op="time";

switch ($op){
	case "time":
		if ($exec_time_traver){
			set_time_limit(240);
			$kekezu->_cache_obj->set('time_traveler_last_exec_cache',time()+300);
			$time_factory = new time_fac_class();
			$time_factory->run();
		}
		break;
	case "tag":
		
		$html_str = $kekezu->_cache_obj->get('tag_html_data_'.$tag_id);
		$html_str = $html_str?$html_str:keke_loaddata_class::gettagHTML($tag_id);
	
		$html_str = str_replace("'","\'",trim($html_str));
		$html_str = str_replace("\r\n","",$html_str);
		$html_str = str_replace("\n","",$html_str); 
 
	 
		echo("document.write('".$html_str."');");
		
		break;
}
die();


