	var site_url = window.location.href.toLowerCase();
	switch (true) {
		
		case site_url.indexOf("/sellfuwu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(0).attr("class","on");
		$("#navlisbox li:eq(0) em a").attr("class","selected");
		break;
		
		case site_url.indexOf("/shenghuofuwu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(1).attr("class","on");
		$("#navlisbox li:eq(1) em a").attr("class","selected");
		break;
		
		case site_url.indexOf("/shangwufuwu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(2).attr("class","on");
		$("#navlisbox li:eq(2) em a").attr("class","selected");
		break;
		
		case site_url.indexOf("/zhaopinzhaoren/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(3).attr("class","on");
		$("#navlisbox li:eq(3) em a").attr("class","selected");
		$("#navlisbox li:eq(0) .navlit_box").empty();
		break;
		
		case site_url.indexOf("/wangluozhongdiangong/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(4).attr("class","on");
		$("#navlisbox li:eq(4) em a").attr("class","selected");
		break;
		
		case site_url.indexOf("/lvyoufuwu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(5).attr("class","on");
		$("#navlisbox li:eq(5) em a").attr("class","selected");
		break;
		
		case site_url.indexOf("/qitafuwu/") > 0 :
		$("#navlisbox li").attr("class","");
		$("#navlisbox li").eq(6).attr("class","on");
		$("#navlisbox li:eq(6) em a").attr("class","selected");
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
			setTimeout (
				$('#navlisbox li').bind("mouseout",function(){
				$('#navlisbox li').removeClass("on"); 
				$('#navlisbox li .selected').parents("li").addClass("on"); 
				$(".navlit_box").hide();
				$('#navlisbox li .selected').parents("li").children(".navlit_box").show();
			}),200
			)
						
		})