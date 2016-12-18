
var LOCK=0;

$(function(){
			var loading = parseInt($(".process li.selected").index()) + 1;
			$(".progress_bar").css("width", loading * 33.3 + "%");
			if(task_status==9){
				$(".progress_bar").css({width:"100%",background:"grey"}); 
			}
			
		})
		

/** 稿件提交 */
function workHand() {
	if (check_user_login()) {
		if(!uid){
			showDialog('您必须登录之后才能交稿!', 'alert', '操作失败提示', '', 0);
			return false;
		}
		else if(if_can_hand==0){
			showDialog('操作无效，所交稿件已达到任务所需的最大量，不能再交稿了','alert','操作失败提示','',0);
			return false;
		}else{
			if (uid == guid) {
				showDialog('操作无效，用户对自己发布的任务交稿!', 'alert', '操作失败提示', '', 0);
				return false;
			} else {
				showWindow("work_hand",basic_url+'&op=work_hand',"get",'0');return false;
			}
		}
		
	}
}



/**
 * 计件悬赏选择稿件
 * @param work_id 稿件编号
 * @param to_status 变更状态
 * @returns {Boolean}
 */
function workBid(work_id,to_status){
	if(guid!=uid){
		showDialog('只有雇主才能操作稿件',"alert","操作提示");return false;
	}else{
		var op = 'work_choose';
		switch(to_status){
			case '13':
				op='work_notice';
				break;
			case '14':
				op='work_remark';
				break;
			case '15':
				op='work_out';
				break;
		}
		var str_content;
		str_content='是否确认选择此稿件，选择之后无法修改';
		if(to_status==12){
			showDialog(str_content,'confirm','操作提示',function(){
				if(LOCK==0){
					LOCK=1;
					var url = basic_url+"&op="+op+"&work_id="+work_id;
					$.post(url,{to_status:to_status},function(json){
						LOCK=0;
						if(json.status==1){ 
							$("#work_12_"+work_id).remove();
							$("#work_14_"+work_id).remove();
							$("#work_15_"+work_id).remove();
							var divstatus = $("<div class='work_status_big work_"+to_status+"_big qualified_big1 po_ab'></div>");
							divstatus.appendTo($("#"+work_id));
							showDialog(json.data,'right',json.msg);return false;
						}else{
							showDialog(json.data,'alert',json.msg);return false;
						}
					},'json')
				}
			});
		}else{
			if(LOCK==0){
				LOCK=1;
				var url = basic_url+"&op="+op+"&work_id="+work_id;
				$.post(url,{to_status:to_status},function(json){
					LOCK=0;
					if(json.status==1){ 
						$("#work_12_"+work_id).remove();
						$("#work_14_"+work_id).remove();
						$("#work_15_"+work_id).remove();
						var divstatus = $("<div class='work_status_big work_"+to_status+"_big qualified_big1 po_ab'></div>");
						divstatus.appendTo($("#"+work_id));
						showDialog(json.data,'right',json.msg);return false;
					}else{
						showDialog(json.data,'alert',json.msg);return false;
					}
				},'json')
			}
		}
		
	}
}

/**
 *取消稿件中标
 */

function workCancel(work_id){
	var url = basic_url+"op=work_cancel&work_id="+work_id;
	if(LOCK==0){
		LOCK=1;
		$.post(url,'',function(json){
			LOCK=0;
			if(json.status==1){
				$("#work_cancel_"+work_id).remove();
				showDialog(json.data,'right',json.msg);return false;
			}else{
				showDialog(json.data,'alert',json.msg);return false;
			}
		},'json')
	}
}