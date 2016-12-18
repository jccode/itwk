// JavaScript Document


$(document).ready(function(){
 $('.banner dl dd ul:first').addClass('current');
 $('.banner dl dt ul:first').css('display','block');
 autoroll();
 hookThumb();
 
 //首页鼠标移动到分类变色效果
 $(".fenlei").hover(function(){
	 $(this).find("ul").css("backgroundColor","#FCF7E3");
	 $(this).find("span:first").removeClass("aside_ico").addClass("aside_icob");
	 }
	 ,function(){
		 $(this).find("ul").css("backgroundColor","#fff");
		  $(this).find("span:first").removeClass("aside_icob").addClass("aside_ico");
		 }
	 );
 
 
});

var i=-1; 
var offset = 4000; //轮换时间                  


function autoroll(){
 n = $('.banner dl dd ul').length-1;           
 i++;
 if(i > n){
 i = 0;
 }
 slide(i);
    timer = window.setTimeout(autoroll, offset);
 }
 
 
function slide(i){
 $('.banner dl dd ul').eq(i).addClass('current').siblings().removeClass('current');
 $('.banner dl dt ul').eq(i).fadeIn("slow").siblings('.banner dl dt ul').hide();
 }
 
 
 
function hookThumb(){    
 $('.banner dl dd ul').hover(
  function () {
    if (timer) {
                clearTimeout(timer);
    i = $(this).prevAll().length;
             slide(i); 
            }
  },
  function () {
            timer = window.setTimeout(autoroll, offset);  
            this.bannerlur();            
            return false;
  }
); 
}
