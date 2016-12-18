
//jQuery

    $(function(){
    	
    	
    	
    	
    	//搜索栏聚焦
    	$(".togg").focus(function(){
    		$(this).removeClass("c999");
    		if(this.value=='输入任务/商品'){
    			this.value='';
			}
    		;
    	}).blur(function(){
    		$(this).addClass("c999");
    			this.value==''?this.value=$(this).attr(this.title?'title':'original-title'):'';
    	})
			$('.operate a,a.prev,a.next,a.small_nav,.border_n a ').not('.nav .operate a').hover(function(){
				$(this).children('.icon16').not('.deep_style .icon16,.deep_style .icon32').addClass("reverse");
				}, function(){
				$(this).children('.icon16').not('.deep_style .icon16,.deep_style .icon32').removeClass("reverse");
			});
			//评论鼠标移动事件显示工具栏
			$(".top1,.comment_item").hover(function(){
				$(this).children('.operate').removeClass('hidden');
				
			},function(){
				$(this).children('.operate').addClass('hidden');
			});
		
        //为表格内容区域添加奇偶行不同色
        $("table tbody tr:odd").not('table.jqTransformTextarea tr').addClass("odd");
        //为列表添加奇偶行不同色
        $(".list dd:odd").not('dd.tags').addClass("odd");
        //为列表隐藏工具栏
        $(".list dd").children('.operate').addClass('hidden');
        
        //为表格内容区添加鼠标事件
        $('table tbody tr,.list dd,.category_list .item,.case_con').not('.list dd.tags,table.jqTransformTextarea tr').hover(function(){
            $(this).addClass("hover").children('.operate').removeClass('hidden');
			$(this).find(".tasktipx").addClass("hover"); 
        }, function(){
            $(this).removeClass("hover").children('.operate').addClass('hidden');
			$(this).find(".tasktipx").removeClass("hover"); 
        });
        
        
    	$(".tar_comment").live("click",function(event){
    		tarClick($(this),event);event.stopPropagation();
    	})
    	$(".tar_comment").live("blur",function(event){
    		tarBlur($(this),event);event.stopPropagation();
    	})
        var tarClick = function(obj,event){
        	if($(obj).val()=='我要说几句...'){
        		$(obj).val('').css({height:"50px"}).next().show();
        	}
        	event.stopPropagation();
        }
        var tarBlur = function(obj,event){
        	$("html,body").click(function(event){
        		if(!$(obj).val()){
        			$(obj).val('我要说几句...').css({height:"23px"}).next().hide().find(".answer_word").text("你还能输入100个字!");
        		}
        	})
        }
		$(".pic_middle,.pic_small,.pic_larger").live("mouseover",function(){
			if($(this).parents().is('.choose_tx')==false && $(this).parents().is('.talent_list_box')==false ){
				var u = $(this).attr('uid');
	            $(this).css('z-index','2')
	            if($('#pos_'+u).length){
					if ($('#pos_'+u).children('.pos_detail').length)
						$('#pos_'+u).removeClass('hidden');
	            }else{
	            	$("body").ajaxStart(function(){
	            		$("#ajaxwaitid").hide();
	            	})
	    			$('<div class="pos_box" id="pos_'+u+'"></div>').css({visibility:"hidden"}).appendTo($('#bubble')).load('/index.php?do=ajax&view=file&ajax=bubble&u_id='+u,function(){
						if ($('#pos_'+u).children('.pos_detail').length)
							$('#pos_'+u).css({visibility: 'visible'});//$('#pos_'+u).addClass('hidden');
					});
	            }
			}
        }).live("mousemove",function(e){        	
			var u = $(this).attr('uid');
            var range = $(window).width()-40;
            var ben = $('#pos_'+u).width();
            if(ben + e.pageX < range){
                $('#pos_'+u).css({'left': e.pageX + 10, "top": e.pageY +10})
            }
            else{
                $('#pos_'+u).css({'left': e.pageX - ben - 10, "top": e.pageY +10})
            }
        }).live("mouseout",function(){
			var u = $(this).attr('uid');
            $(this).css('z-index','1')
            $('#pos_'+u).addClass('hidden');
        });
        /* 用户中心菜单 */
     /*   $('.minor_nav li').children('a').next('ul').addClass('hidden');
          if($('.minor_nav li:first>a').next('ul').length>0){
            $('.minor_nav li:first>a').next('ul').removeClass('hidden');
            $('.minor_nav li:first>a>.icon16').removeClass('more_list').addClass('less_list');
        }
        else{
            $('.minor_nav li').eq(1).children('ul').removeClass('hidden');
            $('.minor_nav li').eq(1).children('a').children('.icon16').removeClass('more_list').addClass('less_list');
        }*/


        $('.minor_nav li a').not("$(this).next('ul>li>a')").click(function(){
            var second_list = $(this).next('ul');
            var icons       = $(this).children('.icon16');
            var parents_list = $(this).parent('li').siblings('li');
            if(second_list.length>0 && second_list.is(':visible')){
                 second_list.addClass('hidden');
                 icons.removeClass('less_list').addClass('more_list');
                
            }
            else{
            	if(second_list.length>0){
            		icons.removeClass('more_list').addClass('less_list');
            		second_list.removeClass('hidden');
            		parents_list.children('ul').addClass('hidden');
            	}
                parents_list.children('a').children('.icon16').removeClass('less_list').addClass('more_list');
            }

        });

      


        
	
        var s = $('.messages');
        //msgshow(s);

        // 消息
        $('.messages .close').click(function() {
        	var s = $(this).parent('.messages');
        	msghide(s);
        });

        // 显示消息
        /*function msgshow(ele) {
        	var t = setTimeout(function() {
        		ele.slideDown(200);
        		clearTimeout(t);
        	}, 400);
        };*/
        // 关闭消息
        function msghide(ele) {
        	ele.animate({
        		opacity : .01
        	}, 200, function() {
        		ele.slideUp(200, function() {
        			ele.remove();
        		});
        	});
        };
	
        // input框
        $("input[type=text],input[type=password]").addClass("txt_input");

        $("#ie6 input[type=text],#ie6 input[type=password]").focus(function(){
            $(this).addClass("focus");
        }).blur(function(){
            $(this).removeClass("focus");
        });
        /*,$("this").removeClass("search_input")*/

        
        $("input[type=submit],button[type=submit]").addClass("submit").hover(function(){
        	$(this).addClass('hover');
        },function(){
        	$(this).removeClass('hover');
        });
        
        
       
     // icon图标
        $('.deep_style .icon16,.deep_style .icon32').addClass('reverse');
   //返回顶部
        $('.top').addClass('hidden');
        $.waypoints.settings.scrollThrottle = 30;
        $('#wrapper').waypoint(function(event, direction){
            $('.top').toggleClass('hidden', direction === "up");
        }, {
            offset: '-1%'
        });
        
      
        $(".box.model .shop .box_detail .small_list li.item,.case_con,.example .text_ov,.goods_words,.goods").hover(
        		function(){$(this).css('z-index','2');},
        		function(){$(this).css('z-index','1');}
    	);

    });
    
  //菜单固定浮动
    /*if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style && location.href.indexOf('do=browser') < 0) {
	}
	else {
    
        if ( $(".second_menu").length > 0 ) { 
        	
        	$('.section').waypoint(function(event, direction) {
    			$(this).children('.second_menu').toggleClass('fixed-top', direction === "down");
    			event.stopPropagation();
    		});
        } 
	}*/
    
var checkall = function(){
    if ($('#checkbox').attr('value') == 0) {
    	$("#checkbox").attr("value",1);
    	$('input[type=checkbox]').attr('checked', true);
    }  else {
    	$("#checkbox").attr("value",0);
        $('input[type=checkbox]').attr('checked', false);
    }

}
     //解决select 宣染
	/*function jq_select(){
	$("#reload_indus div.jqTransformSelectWrapper ul li a").click(function(){
			 $("#indus_id").removeClass("jqTransformHidden").css('display:none');
			 $("select").jqTransSelect().addClass("jqTransformHidden");
		});
	}*/
	
	/**
	 * 获取任务行业
	 * @param indus_pid
	 */
	function showIndus(indus_pid){
		if(indus_pid){
			$.post("/index.php?do=ajax&view=indus",{indus_pid: indus_pid}, function(html){
				var str_data = html;
				if (trim(str_data) == '') {
					$("#indus_id").html('<option value="-1"> 请选择子行业 </option>');
				}
				else {
					$("#indus_id").html(str_data);
					$("#reload_indus div.jqTransformSelectWrapper ul li a").triggerHandler("click");
				}
			},'text');
		}
	}


/**
 * 需求字数检查
 * 
 * @param obj
 *            需求对象
 * @param 最大长度
 */
function checkInner(obj,maxLength,e){
	var  val   = $.trim(obj.value);
	var  len   = val.length-1;
		e.keyCode==8?len-=1:len+=1;
		len<0?len=0:'';
	
	var Remain = Math.abs(maxLength-len);
	if(maxLength>=len){
       
        $("#length_show").text("已输入长度:"+len+",还可以输入:"+Remain+"字");
	}else{
		$("#length_show").text("可输入:"+maxLength+"字,已超出长度:"+Remain+"字");
	}
}

/*$('#carousel').jcarousel({
    wrap: 'circular',
	 animation: 200
}).jcarouselAutoscroll({
   interval:  10000
});*/

/**
 * 
 * @param string  form 表单ID或者操作链接
 * @param int     type 操作类型，为链接时默认为1；为表单时为2；
 * @param boolean check 是否验证表单。默认为false，需验证请设置为true 
 */
function siteSub(form,type,check){
	var t      = type==2?2:1;//操作类型 1为链接型，二为表单型
	var c      = check==true?true:false;//是否需验证表单 true为验证,默认为false
	var pass   = true;//默认为通过 ,当表单验证不过时为false;
	switch(t){
		case 1://链接
			var url = form;
			break;
		case 2://表单
			if(c==true){
				pass = checkForm(document.getElementById(form));
			}
			break;
	}
	if(pass==true){
		if(t==1){
			showWindow('sitesub',url,'get','0');return false;
		}else{
			showWindow('sitesub',form,'post','0');return false;
		}
	}
}	

