$(document).ready(function () {
				//切换
				$('.help_navlist:eq(1)').hide();
				$('.help_navlist li ul:gt(0)').hide();
				$(".nav_corqh span").bind("click",function(){ 
					var i= $(".nav_corqh span").index(this); 
					$(this).addClass("on").siblings().removeClass("on"); 
					$(".help_navlist").eq(i).show().siblings(".help_navlist").hide(); 
				})
				//展开收缩菜单
				$('.big_tilt').bind('click',function(){
						if ( $(this).siblings('ul').is(':hidden')){
							$('.help_navlist li ul').hide();
							$('.big_tilt').attr('class','big_tilt off');
							$(this).attr('class','big_tilt on')
							$(this).next('ul').show();
						}else if ( $(this).siblings('ul').is(':visible')) {
							$(this).attr('class','big_tilt off')
							$(this).next('ul').hide();
							}
					})
				//外部载入相应的图片
				$(".help_navlist li li").bind("click",function(){
						var n=$(this).attr("name");
						var n=n.replace("lips_",""); 
						$(".help_navlist li li").removeClass("selec"); 
						$(this).addClass("selec");
						$(".play_self_img").load("/tpl/default/wz/"+n+".htm"); 
					})
				//上一步
				$("#co_cs_1").live("click",function(){
						var obName_1=$(".selec").attr("name");
						var objNum=obName_1.replace("lips_",""); 
						var objNum=objNum-1;
						var objNum_c=objNum-2;
						$(".play_self_img").load("/tpl/default/wz/"+objNum+".htm"); 
						var objCrent=$(".selec");
						$(objCrent).removeClass("selec"); 
						$(objCrent).prev().addClass("selec"); 
					})
				//下一步
				$("#co_cs_2").live("click",function(){
						var obName=$(".selec").attr("name");
						var objNum=obName.replace("lips_",""); 
						var objNum=parseFloat(objNum)+1;
						$(".play_self_img").load("/tpl/default/wz/"+objNum+".htm"); 
						var objCrent=$(".selec");
						$(objCrent).removeClass("selec"); 
						$(objCrent).next().addClass("selec"); 
					})
			})