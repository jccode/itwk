/**
 * 威客商城js
 */
$(function() {

	var grade = $(".progress_bar").attr("grade");
	$(".progress_bar").animate({
		width : grade + "%"
	}, 3000);
	$("#leave").click(function() {
		$("html,body").animate({
			scrollTop : $(".lyk").offset().top
		});
	})
	
	$(".arrow-bottom-left,.arrow-top-right").click(function(){
		$("#left_nav").toggleClass("hidden");
		$("#top_nav").toggleClass("hidden");
		setcookie('nav-arrow-'+sid,$(this).attr("id"),3600);
	})
	var nav_arrow = getcookie('nav-arrow-'+sid);
	if(nav_arrow){
		if(nav_arrow=='arrow-bottom-left'){
			$("#top_nav").addClass("hidden");
			$("#left_nav").removeClass("hidden");
		}else if(nav_arrow=='arrow-top-right'){
				$("#left_nav").addClass("hidden");
				$("#top_nav").removeClass("hidden");
			}	
	}
})

/**
 * 内容检测
 * @param obj
 * @param event
 */
function checkCommentInner(obj,e){
	var  num   = obj.value.length;
		e.keyCode==8?num-=1:num+=1;
		num<0?num=0:'';
	var Remain = Math.abs(100-num);
		if(num<=100){
			$(obj).next().find(".answer_word").text("你还能输入"+Remain+"个字!");
		}else{
			var nt = $(obj).val().toString().substr(0,100);
			$(obj).val(nt);	
		}
}
/**
 * 商品下单
 * @param type
 *            汉字 购买类型
 * @param sid
 *            商品ID
 * @param s_uid
 *            威客uid
 */
function sub_order(type,sid,s_uid){
	if(check_user_login()){
		if(uid==s_uid){
			showDialog("您无法购买自己的"+type,"alert","操作提示");return false;
		}else{
			showDialog("您确认下单购买吗","confirm","操作提示","location.href='/index.php?do=shop_order&op=confirm&sid="+sid+"'");return false;
		}
	}
}