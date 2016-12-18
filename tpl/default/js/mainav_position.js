	
	
	var site_url = window.location.href.toLowerCase();
	switch (true) {
		
		case site_url.indexOf("/hire/success") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(1).attr("class","on");
		$("#navlisbox li:eq(1) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(1) em a").attr("class","selected");
		$("#navlisbox li:eq(1) .navlit_box").show();
		$("#navlisbox li:eq(0) .navlit_box").empty();
		break;
		
		case site_url.indexOf("/hire/wait") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(1).attr("class","on");
		$("#navlisbox li:eq(1) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(1) em a").attr("class","selected");
		$("#navlisbox li:eq(1) .navlit_box").show();
		
		break;
		
		case site_url.indexOf("/hire") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(1).attr("class","on");
		$("#navlisbox li:eq(1) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(1) em a").attr("class","selected");
		$("#navlisbox li:eq(1) .navlit_box").show();
		break;
		
		
		
		case site_url.indexOf("/task/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(2).attr("class","on");
		$("#navlisbox li:eq(2) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(2) em a").attr("class","selected");
		$("#navlisbox li:eq(2) .navlit_box").show();
		
		break;
		
		case site_url.indexOf("/indus.html") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(2).attr("class","on");
		$("#navlisbox li:eq(2) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(2) em a").attr("class","selected");
		$("#navlisbox li:eq(2) .navlit_box").show();
		break;
		
		case site_url.indexOf("/special-view-special_list") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/task_map") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(2).attr("class","on");
		$("#navlisbox li:eq(2) .navlit_box a:eq(3)").attr("class","on");
		$("#navlisbox li:eq(2) em a").attr("class","selected");
		$("#navlisbox li:eq(2) .navlit_box").show();
		break;
		
		case site_url.indexOf("/talent") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(3).attr("class","on");
		$("#navlisbox li:eq(3) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(3) em a").attr("class","selected");
		$("#navlisbox li:eq(3) .navlit_box").show();
		break;
		
		
		case site_url.indexOf("/vip/desc") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/vip/story") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(2)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/vip/help") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(3)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/vip-view-open-level") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(4)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/vip/open") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(4)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/vip/") > 0:
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		$("#navlisbox li:eq(4) .navlit_box").show();
		break;
		
		case site_url.indexOf("/anli/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/case/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/zt/video") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(4)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/zt/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/guzhu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(2)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/weike/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(3)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/zt/video/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) .navlit_box a:eq(4)").attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		$("#navlisbox li:eq(5) .navlit_box").show();
		break;
		
		case site_url.indexOf("/help/bang") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) .navlit_box a:eq(1)").attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
		$("#navlisbox li:eq(6) .navlit_box").show();
		break;
		
		case site_url.indexOf("/help/selffuwu") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) .navlit_box a:eq(2)").attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
		$("#navlisbox li:eq(6) .navlit_box").show();
		break;
		
		case site_url.indexOf("/help/novice") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) .navlit_box a:eq(3)").attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
		$("#navlisbox li:eq(6) .navlit_box").show();
		break;
		
		case site_url.indexOf("/help/callfuwu") > 0  :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) .navlit_box a:eq(4)").attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
		$("#navlisbox li:eq(6) .navlit_box").show();
		break;
		
		case site_url.indexOf("/help/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
		$("#navlisbox li:eq(6) .navlit_box").show();
		break;

		
		default :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(0).attr("class","on");
		$("#navlisbox li:eq(0) .navlit_box a:eq(0)").attr("class","on");
		$("#navlisbox li:eq(0) em a").attr("class","selected");
		$("#navlisbox li:eq(0) .navlit_box").show();
	}

	$('#navlisbox li').bind("mouseover",function(){
			$(this).addClass("on").siblings().removeClass("on"); 
			$(".navlit_box").hide();
			$(this).children(".navlit_box").show();
		})
		
	 if(navigator.userAgent.indexOf("MSIE")>0) {  
		$('#navlisbox').bind("mouseout",function(){
				if(!this.contains(event.toElement))  {
					$(".navlit_box").hide();
					$('#navlisbox li .selected').parents("li").addClass("on").siblings().removeClass("on");  
					$('#navlisbox li .selected').parents("li").children(".navlit_box").show();
				 }
			})
	 } else {
		$('#navlisbox').bind("mouseout",function(){
				$(".navlit_box").hide();
				$('#navlisbox li .selected').parents("li").addClass("on").siblings().removeClass("on");  
				$('#navlisbox li .selected').parents("li").children(".navlit_box").show();
			})
		 }


	