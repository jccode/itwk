var ZY_ADNum=1;
var ZY_ADTime;
var ZY_ADTimes=4000;
var ZY_ADAllNum=$(".trendli li").length;
var move=parseInt($("#trendimg").attr("move")); 
var ZY_OLDNum;
var ZY_Left;
ShowAdPic(0);
$("#trendNum span").click(function(){	
		 ZY_OLDNum=ZY_ADNum;						   
		 ZY_ADNum=$(this).attr("AID");
		 clearInterval(ZY_ADTime); 
		 ShowAdPic(1);
      }).mouseout(function(){
		  clearInterval(ZY_ADTime); 
		 ZY_ADTime = setInterval("ShowAdPic(0)",ZY_ADTimes);
      })

$("#trendimg").mouseover(function(){
		 clearInterval(ZY_ADTime); 
      }).mouseout(function(){
		 ZY_ADTime = setInterval("ShowAdPic(0)",ZY_ADTimes);
      })

function ShowAdPic(type){
	   $("#trendNum span").each(function(){
				if($(this).attr("AID")==ZY_ADNum){
					ZY_Left=-(ZY_ADNum-1)*move
					$("#trendimg").animate({left:ZY_Left},"normal");  
				   $(this).removeClass('trend_num').addClass('trend_num_selected'); 
				}else{
					$(this).removeClass('trend_num_selected').addClass('trend_num'); 
				  }})
			if(type==0){
				 clearInterval(ZY_ADTime); 
				 ZY_OLDNum=ZY_ADNum;
				 ZY_ADNum++;
				 if(ZY_ADNum>ZY_ADAllNum){ZY_ADNum=1}
				 ZY_ADTime = setInterval("ShowAdPic(0)",ZY_ADTimes);
			   }
      }