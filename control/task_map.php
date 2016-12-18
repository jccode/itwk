<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-19 18:21
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

/*初始化信息  */ 
/**
 * 推荐任务
 */
$task_recomm = db_factory::query ( sprintf ( " select task_id,start_time,uid,username,task_title,point,task_cash,model_id,view_num,work_num,task_cash_coverage from %switkey_task where is_top='1' and task_status='2' and task_cash>=500 and point!='' order by start_time desc limit 0,50", TABLEPRE ), 1, 86400 );
$cash_cove_arr = kekezu::get_cash_cove();

foreach ($task_recomm as $k=>$v) {
	$v['user_pic'] = keke_user_class::get_user_pic($v['uid']);
	$v['start_time'] = kekezu::time2Units(time()-$v['start_time']) ;
	$arr_point .= 'new google.maps.LatLng(' . $v ['point'] . '),';
	$arr_marker .= '  new google.maps.Marker({ position: point[' . $k . '], map: map}),';
	//取任务金额
	if($v['task_cash_coverage']>0){
		$str_cash = $cash_cove_arr[$v['task_cash_coverage']]['cove_desc'];
	}else{
		$str_cash = intval($v['task_cash']).'元';
	}
	$arr_infoWindow .= ' new google.maps.InfoWindow({content:"<div class=\'basic_style map_info\'><div class=\'fl_l mr_10\'><a target=_blank  href='.$_K['siteurl'].'/index.php?do=shop&u_id='.$v['uid'].'>'.$v['user_pic'].'</a></div><div class=\'fl_l mr_10 font12\' ><strong><span>'.$str_cash.'</span> <a target=_blank href='.$_K['siteurl'].'/index.php?do=task&task_id='.$v['task_id'].'   target=_blank>'.$v['task_title'].'</a></strong></br><a href='.$_K['siteurl'].'/index.php?do=shop&u_id='.$v['uid'].' target=_blank  class=\'font12\'>'.$v['username'].'</a></b>&nbsp;&nbsp;'.$v['start_time']. $_lang['front_release'] .'</div></div>"}),';
	$addEventListener.='google.maps.event.addListener(marker['.$k.'],"mouseover", function() { clearOverlays();infoWindow['.$k.'].open(map,marker['.$k.']);});';
}

$map_script.=<<<END
	<script type="text/javascript">
	var myOptions = {center: new google.maps.LatLng(30.937862, 113.937226),	zoom: 6,mapTypeId: google.maps.MapTypeId.ROADMAP};
	var map = new google.maps.Map(document.getElementById('container'),myOptions); 
	var point = [$arr_point];
	var marker = [$arr_marker];
	var infoWindow = [$arr_infoWindow];
	
	$addEventListener
	
	function clearOverlays() {
	  if (infoWindow) {
	    for (var i=0;i<infoWindow.length;i++) {
	    	if(typeof(infoWindow[i]) != "undefined") {
	      	infoWindow[i].close();
	      }
	    }
	  }
	}
	
	
var ind = 0;
var timer = null;
show();

	function openMyWin(id){  
		clearOverlays();
		infoWindow[id].open(map,marker[id]);
	}
	
	function show(){  
	timer = setInterval(
		 function(){	
		 	 if(ind == infoWindow.length){     
				 ind = 0;     
			}    
			openMyWin(ind);
			 ind++;  
		},
	5000);

}
	</script>


END;
	
$page_title = '任务地图 实时更新任务24小时跟踪_IT帮手网';
$page_keyword = '任务地图，更新任务，24小时跟踪';
$page_description ='IT帮手网任务跟踪大厅 24小时跟踪任务为你提供简洁快捷服务，更快的找到威客任务，兼职赚钱更轻松。一品在手，赚钱不愁。';
	
require  keke_tpl_class::template ($do);  
