$(function(){
	$("#qq").click(function(){
		$("#ct_qq").toggle();
	})
	$("#msn").click(function(){
		$("#ct_msn").toggle();
	})
	$("#email").click(function(){
		$("#ct_email").toggle();
	})
	$("#mobile").click(function(){
		$("#ct_mobile").toggle();
	})
	
	$("#tar_content").blur(function(){
		contentCheck('tar_content',"服务描述",10,1000);
	})
	
	$(".lit_form input:[type='text']").each(function(){
		$(this).val()==''&&$(this).val($(this).attr("ext"));
	})
})

uploadBlur = function() {
	if (ifOut("upfile", "5") && $("#upload").val()) {
		upload("upload", "att", "front", "", "", "task")
	} else {
		return false
	}
};
/**
 * 获取商品行业
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
			}
		},'text');
	}
}

/*
 * 选择任务模型
 * */
function choose_model(service_type){
	if(service_type=='1'){
			$('#hdn_service_type').val("2");
			$('#li_1').addClass('selected');
			$('#li_2').removeClass('selected');
	}else{
			$('#hdn_service_type').val("1");
			$('#li_1').removeClass('selected');
			$('#li_2').addClass('selected');
	}
}
function checkAgreement(){
	if($("#agreement").attr("checked")==false){
		showDialog("请先同意服务发布协议","alert","操作提示");return false;
	}else return true;
}

function stepCheck(){
	var pass = false;
		if(contentCheck('tar_content',"服务描述",5,1000,0,'',editor)&&checkAgreement()){
				pass=true;
		}
		
		if($('#mobile').attr('checked')){
			if(!$('#contact_mobile').val()){pass=false;showDialog("联系电话未填写！","alert","操作提示")}
		}
		if($('#email').attr('checked')){
			if(!$('#contact_email').val()){pass=false;showDialog("email未填写！","alert","操作提示")}
		}
		if($('#qq').attr('checked')){
			if(!$('#contact_qq').val()){pass=false;showDialog("qq未填写！","alert","操作提示")}
		}
		if($('#msn').attr('checked')){
			if(!$('#contact_msn').val()){pass=false;showDialog("msn未填写！","alert","操作提示")}
		}
		
		if(!$('#mobile').attr('checked')&&!$('#email').attr('checked')&&!$('#qq').attr('checked')&&!$('#msn').attr('checked')){
			pass=false;
			showDialog("至少填写一种联系方式！","alert","操作提示");
			$('#mobile').attr("checked","checked");
			$('#ct_mobile').show()
		}
		
		if(pass==true){
			siteSub('frm_step2',2,true);
		}
}

/**
 * 上传完成后的页面响应
 * @param json json数据
 */
function uploadResponse(json){
	if($("#"+json.fid).length<1){//判断是否已有同样的li、
		var file_ids = $("#file_ids").val();
		if(file_ids){
			$("#file_ids").val(file_ids+','+json.fid);
		}else{	
			$("#file_ids").val(json.fid);
		}
		$("#pic").val(json.msg.url);
	}
}