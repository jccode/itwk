// JavaScript Document


$(document).ready(function(){
 $('.banner2 dl dd ul:first').addClass('current');
 $('.banner2 dl dt ul:first').css('display','block');
 autoroll();
 hookThumb();
});

var i=-1; 
var offset = 4000; //轮换时间                  


function autoroll(){
 n = $('.banner2 dl dd ul').length-1;           
 i++;
 if(i > n){
 i = 0;
 }
 slide(i);
    timer = window.setTimeout(autoroll, offset);
 }
 
 
function slide(i){
 $('.banner2 dl dd ul').eq(i).addClass('current').siblings().removeClass('current');
 $('.banner2 dl dt ul').eq(i).fadeIn("slow").siblings('.banner2 dl dt ul').hide();
 }
 
 
 
function hookThumb(){    
 $('.banner2 dl dd ul').hover(
  function () {
    if (timer) {
                clearTimeout(timer);
    i = $(this).prevAll().length;
             slide(i); 
            }
  },
  function () {
            timer = window.setTimeout(autoroll, offset);  
            this.banner2lur();            
            return false;
  }
); 
}
// JavaScript Document