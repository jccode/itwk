var site_url = window.location.href.toLowerCase();
switch (true) {
	case site_url.indexOf("/task/") > 0 :
	$("#navlisbox li").attr("class","");
	$("#navlisbox li").eq(2).attr("class","on");
	$("#navlisbox li:eq(2) .navlit_box a:eq(1)").attr("class","on");
	$("#navlisbox li:eq(2) em a").attr("class","selected");
	$("#navlisbox li:eq(2) .navlit_box").show();
	
	$("#navlisbox li").eq(0).attr("class","");
	$("#navlisbox li:eq(2) .navlit_box a:eq(0)").attr("class","");
	break;
	
	case site_url.indexOf("do=task&task_id=") > 0 :
	$("#navlisbox li").attr("class","");
	$("#navlisbox li").eq(2).attr("class","on");
	$("#navlisbox li:eq(2) .navlit_box a:eq(1)").attr("class","on");
	$("#navlisbox li:eq(2) em a").attr("class","selected");
	$("#navlisbox li:eq(0) em a").attr("class","");
	$("#navlisbox li:eq(2) .navlit_box").show();
	$("#navlisbox li:eq(0) .navlit_box").hide();
	
	$("#navlisbox li").eq(0).attr("class","");
	$("#navlisbox li:eq(2) .navlit_box a:eq(0)").attr("class","");
	
	break;
		}