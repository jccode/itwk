/**
 * 单人悬赏交付js
 */
$(function(){
	
})

/**
 * 签署协议
 * @param user_type 用户角色
 */
function taskAgree(user_type){
	$.getJSON(basic_url,{op:'sign',user_type:user_type},function(json){
		if(json.status=='1'){
			showDialog(json.data,'right',json.msg,"document.location.reload()");return false;
		}else{
			showDialog(json.data,'alert',json.msg);return false;
		}
	})
}
/**
 * 确认提交附件
 */
function confirmUpload(){
	var fileNum = $("#upload_file li").length;
	if(fileNum){
		showDialog("你上传了"+fileNum+'个源文件,确认交付吗?',"confirm","操作提示","confirm()");return false;
	}else{
		showDialog("你没有上传源文件,确认交付吗?","confirm","操作提示","confirm()");return false;
	}
}
/**
 * 确认接收附件
 */
function confirmFile(){
	var fileNum = $("#file li").length;
	if(fileNum){
		showDialog("对方上传了"+fileNum+'个源文件,确认接收吗？',"confirm","操作提示","Complete()");return false;
	}else{
		showDialog("对方没有上传源文件,确认接收吗?","confirm","操作提示","Complete()");return false;
	}
}
/**
 * 表单提交
 */
function confirm(){
	$("#agree_frm").submit();
}
/**
 * 协议完成
 */
function Complete(){
	$.getJSON(basic_url,{op:'accept'},function(json){
		if(json.status==1){
			switch(trust_mode){
				case "0":
					showDialog(json.data,"right",json.msg,"document.location.reload()");return false;
					break;
				case "1":
					location.href=basic_url+'&step=step3';
					break;
			}
		}else{
			showDialog(json.data,"alert",json.msg);return false;
		}
	})
}

/** 
 * 仲裁
 *@param string type 维权类型 1=>维权,2=>举报,3=>投诉
 *@param string obj 维权对象 task/work/product/order
 *@param string obj_id 对象编号 
 *@param int to_uid 被举报人
 *@param string to_username 被举报人名称
 */
function report( obj, type,obj_id,to_uid,to_username) {
	
	if(to_uid==uid){
		showDialog("无法对自己发起仲裁","alert","操作提示");return false;
	}else{
			showWindow("report",basic_url+'&op=report&type='+type+'&obj='+obj+'&obj_id='+obj_id+'&to_uid='+to_uid+'&to_username='+to_username,'get','0');return false;
		}
}

function checkInner(obj,event){

	var num = parseInt($(obj).val().length)+0;
		if(num<=100)
			$(obj).next().find(".answer_word").text("你还能输入"+(100-num)+"个字!");
		else{
			var nt = $(obj).val().toString().substr(0,100);
			$(obj).val(nt);	
		}
}