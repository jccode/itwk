
$(document).ready(function () {
	$('#wz_begin').click(function(){
		$('#wz_box').show();
		$('#body_id').show();
		$("#wz_box").load("/tpl/default/wz/wz_yipin.htm #wz_step_b");
		return false;
	})
	$('.wz_close').live('click',function(){
		$('#wz_box').empty();
		$('#body_id').hide(); 
	})
	$('#wz_steb').live('click',function(){
		$('#wz_box').empty();
		$("#wz_box").load("/tpl/default/wz/wz_yipin.htm #wz_step_b");
	})
	$('#wz_stea').live('click',function(){
		$('#wz_box').empty();
		$("#wz_box").load("/tpl/default/wz/wz_yipin.htm #wz_step_a");
	})
})