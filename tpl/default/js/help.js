/**
 * 帮助中心
 */
$(function(){
	$(".nav_list .small_nav").click(function(event){
		var fpid = parseInt($(this).attr("fpid"))+0;
		var spid = parseInt($(this).attr("spid"))+0;
		var url  = "/index.php?do=help&fpid="+fpid+"&spid="+spid+"#pageTop";
		location.href=url;
	})
	$(".nav_list dl dd ul li").click(function(){
		var art_id = $(this).attr("id");
		if($("#art_"+art_id).length>0){
		$("html,body").animate({scrollTop:$("#art_"+art_id).offset().top},300);
		}
	})
	$('.minor_nav li').hover(function(){
		$(this).siblings().removeClass('selectedLava');
	},function(){
		$('.minor_nav li.now').addClass('selectedLava');
	})
	$(".question").click(function(event){
		var parent = $(this).parents(".all_content");
		if (parent.children(".article").is(':visible')) {
				parent.children(".article").addClass('hidden');
				event.stopPropagation();
			} else {
				parent.children(".article").removeClass('hidden');
				parent.siblings().children(".article").addClass('hidden');
				event.stopPropagation();
			}
		
	})
	
    $(".togg1").focus(function(){
    		$(this).removeClass("c999");
    		this.value='';
    	}).blur(function(){
    		$(this).addClass("c999");
    		 this.value==''?this.value=$(this).attr("t"):'';
    	})
})
document.getElementById("keyword").onkeydown = function(event){
    if ($.browser.msie) {
        if (window.event.keyCode == 13) {
        	return false;
        }
    }
    else {
        if (event.keyCode == 13) {
        	return false;
        }
    }
}
function searchKeyword(){
	var keyword = $.trim($("#keyword").val());
	if(keyword&&keyword!="想了解什么?"){
		var hasLoaded=$(".loadcontent[keyword='"+keyword+"']").length;
		if(hasLoaded==0){
			var url = "/index.php?do=ajax&view=file&ajax=help_search&keyword="+keyword;
			$(".loadcontent").slideUp();
			$(".newcontent").load(url)
				.attr("keyword",keyword).slideDown(1000)
				.removeClass("newcontent").before('<div class="loadcontent newcontent"></div>');
		}else{
			$(".loadcontent").slideUp();
			$(".loadcontent[keyword='"+keyword+"']").slideDown(600);
		}
	}
}
