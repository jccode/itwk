/**
 * 首页js
 */

$(function(){
	//网站统计
	$("#slider").easySlider({
		auto: true,
		continuous: true,
		vertical: true,
		speed: 200,
		nextId: "down",
		prevId: "up"
	});
	 
	//首页幻灯
	$('#slides').slides({
		  preload: true,
		  preloadImage: SITEURL+'/resource/img/system/loading.gif',
		  play: 5000,
		  pause: 2500,
		  hoverPause: true,
		  animationStart: function(){
				$('.caption').fadeOut();
			},
			animationComplete: function(current){
				$('.caption').fadeIn();
				/*if (window.console && console.log) {
					// example return of current slide number
					console.log(current);
				};*/
			}
	});
	$("#indus li a").click(function(){
		$(this).addClass("selected").parent().siblings().find("a").removeClass("selected");
	})

	$(".indus_list>li").each(function(){
		$(this).hover(function(){
			$(this).css("z-index",2);
			$(this).children().eq(0).addClass('selected');
			$(this).find('ul').removeClass('hidden');
		},function(){
			$(this).css("z-index",1);
			$(this).children().eq(0).removeClass('selected');
			$(this).find('ul').addClass('hidden');
		})
	})


	 //友情链接
		var uid = parseInt('{$uid}')+0;
		function link_windows(){
			if(check_user_login()){
				showWindow('linkbox', 'index.php?do=ajax&view=link&ac=add');
			}
		}
	
	


	$(".box.model .task .box_detail .small_list li").hover(
	    function(){$(this).addClass('hover')},
		function(){$(this).removeCss('hover')}
		);
	if($('#jcarousel,#jcarousel2').length>0){
		$('#jcarousel,#jcarousel2').jcarousel({
		    wrap: 'circular',
			 animation: 200
		}).jcarouselAutoscroll({
			interval:  3000,
			create: $('#jcarousel,#jcarousel2').hover(function() {
                $(this).jcarouselAutoscroll('stop');
            },
            function() {
                $(this).jcarouselAutoscroll('start');
            })
		});
		
	}
	if($('.jcarousel-pagination').length>0){
		$('.jcarousel-pagination').jcarouselPagination({
			   perpage: 1
			});	
	}

	
/*
	$(".box.model .shop .box_detail .small_list li").hover(function(){
		$(this).css('z-index','2')
		$('.pos_box').removeClass('hidden');
	},function(){
		$(this).css('z-index','1')
		$('.pos_box').addClass('hidden');
	}).mousemove(function(e){ 
            $('.pos_box').css({'left': e.pageX + 10, "top" :e.pageY +10})
    });*/
	//商品
});
function indusLinkInit(list_type){
	
	$(".indus_list>li").each(function(){
			href= $(this).find('a').attr('href');
			if(list_type=='task_list'){ 
				var url = href.replace('shop_list',list_type);
				 $(this).find('a').attr("class","");
			}else{
				var url = href.replace('task_list',list_type);
				 $(this).find('a').attr("class","purple");
			} 
			$(this).find('a').attr('href',url);
	})
	
}
if($('#carousel').length>0){
	$('#carousel').jcarousel({
	    wrap: 'circular',
		 animation: 200
	}).jcarouselAutoscroll({
	   interval:  10000
	});
	
}