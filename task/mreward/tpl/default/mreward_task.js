/**
 * 任务事件处理
 * 
 */

$(function() {
	var loading = parseInt($(".process li.selected").index()) + 1;
	$(".progress_bar").css("width", loading * 25 + "%");
	if(task_status==9){
		$(".progress_bar").css({width:"100%",background:"grey"}); 
	}
})
var LOCK=0;

/** 稿件提交 */
function workHand() {
	if (check_user_login()) {
		if(!uid){
			showDialog('您必须登录之后才能交稿!', 'alert', '操作失败提示', '', 0);
			return false;
		}
		else if (uid == guid) {
			showDialog('操作无效，用户对自己发布的任务交稿!', 'alert', '操作失败提示', '', 0);
			return false;
		} else {
			showWindow("work_hand", basic_url + '&op=work_hand', "get", '0');
			return false;
		}
	}
}

/**
 * 选择稿件
 * 
 * @param work_id
 *            稿件编号
 * @param to_status
 *            变更状态
 * @returns {Boolean}
 */
function workBid(work_id, to_status) {
	if (check_user_login()) {
		if (guid != uid) {
			showDialog("只有雇主才能操作稿件", "alert", "操作提示");
			return false;
		} else {
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
			if(task_status==2){
				str_content='是否确认选择此稿件，选择之后无法修改';
			}else{
				str_content='是否确认选择此稿件，选择之后无法修改';
			}
			if(to_status>0 && to_status<11){
				showDialog(str_content,'confirm','操作提示',function(){
					if(LOCK==0){
						LOCK=1;
						var url = basic_url + '&op='+op+'&work_id=' + work_id+'&to_status='+to_status;
						$.post(url, {
							to_status : to_status
						}, function(json) {
							LOCK=0;
							if (json.status == 1) {
								
								showDialog(json.data, "right", json.msg,"location.href='" + basic_url+"'");
								return false;
							} else {
								showDialog(json.data, "alert", json.msg);
								return false;
							}
						}, 'json');
					}
				});
			}else{
				if(LOCK==0){
					LOCK=1;
					var url = basic_url + '&op='+op+'&work_id=' + work_id+'&to_status='+to_status;
					$.post(url, {
						to_status : to_status
					}, function(json) {
						LOCK=0;
						if (json.status == 1) {
							
							showDialog(json.data, "right", json.msg,"location.href='" + basic_url+"'");
							return false;
						} else {
							showDialog(json.data, "alert", json.msg);
							return false;
						}
					}, 'json')
				}
			}
		}
	}
}

function work_confirm_pay(work_id){
	if (check_user_login()) {
		if (guid != uid) {
			showDialog("只有雇主才能操作稿件", "alert", "操作提示");
			return false;
		} else {
			showDialog('确定现在付款吗？','confirm','确认付款',function(){
				if(LOCK==0){
					LOCK=1;
					var url = basic_url + '&op=work_confirm&work_id=' + work_id;
					$.post(url, function(json){
						LOCK=0;
						if (json.status == 1) {
							showDialog(json.data, "right", json.msg,"location.href='" + basic_url + "'");
						}
						else{
							showDialog(json.data, "alert", json.msg);
						}
					}, 'json')
				}
			});
		}
	}
}

function set_choosework(task_id){
	showDialog("保证选稿能提高威客的积极性，是否确定？", 'confirm', "保证选稿", function(){
		$.get('/index.php?do=task&&task_id='+task_id+'&op=set_mustchoose');
		$('#btn_set_must_choose').remove();
	}, 0);
}

