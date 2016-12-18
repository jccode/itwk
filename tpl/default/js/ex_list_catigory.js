//搜索地名选择
$(".area_box").hide();
$(".area_topname").bind("click",function(){
  $(".area_box").css("display","block"); 
})
$(".area_topname").bind("mouseout",function(){
  $(".area_box").css("display","none"); 
})
$(".area_box").bind("mouseover",function(){
  $(".area_box").css("display","block"); 
})
$(".area_box").bind("mouseout",function(){
  $(".area_box").css("display","none"); 
})
$(".area_tini").hover(
  function(){
	  $(this).css("background","#f60"); 
	  $(this).css("color","#fff"); 
  },function(){
	  $(this).css("background","#f3f7fa"); 
	  $(this).css("color","#000"); 
  }
)
var areatext=$(".area_topname").text();
$(".area_tini").bind("click",function(){
  var areatinitext=$(this).text();
  if(areatinitext=='台湾地区'){
	  myintext='台湾';
  }else  if(areatinitext=='大陆地区'){
	  myintext='';
  }else{
	  myintext=areatinitext;
  }
  $(".area_topname").text(areatinitext);
  $("#z").val(myintext);
  $(".area_box").css("display","none"); 
})
//验证
$(".serchbox .t_x").bind("focus", function(){
	 if(this.value==this.defaultValue){
			this.value='';
			this.className='t_x t_x_v';
		}; 
 })
$('.serchbox .t_x').bind("blur",function(){
	if(this.value==''){
		this.value='请输入人才关键词或者任务关键词';
		this.className='t_x';
	}
})
$(function(){
		$('.pic_zslibox a').hover(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			$(this).parent().siblings().hide();
			var sid = $(this).attr('sid').toString();
			$(".tj_piclb").remove();
			if($('#'+sid).length==0){
				$('<div class="tj_piclb"><div class="bl_info clearfix fl_l" id="'+sid+'">')
						.insertBefore($(this).parent())
						.load("/index.php?do=brand&ajax=brand_tw_service&sid="+sid);
			}
			$('#'+sid).show();
		});
	})