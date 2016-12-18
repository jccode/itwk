/**
 * 任务事件处理
 * 
 */

$(function() {
	var loading = parseInt($(".process li.selected").index()) + 1;
	$(".progress_bar").css("width", loading * 20 + "%");
	if (task_status == 9) {
		$(".progress_bar").css({
			width : "100%",
			background : "grey"
		});
	}

})

var LOCK=0;
/** 任务发起投票 */
function taskVote() {
	if (check_user_login()) {
		if(LOCK==0){
			LOCK=1;
			var url = basic_url + '&op=start_vote';
			$.getJSON(basic_url + '&op=start_vote', function(json) {
				LOCK=0;
				if (json.status == 1) {
					showDialog(json.data, 'notice', json.msg, "location.href='"
							+ basic_url + "&view=work'");
					return false;
				} else {
					showDialog(json.data, 'alert', json.msg);
					return false;
				}
			})
		}
	}
}

/** 稿件提交 */
function workHand(url) {
	if (check_user_login()) {
		if (uid == guid) {
			showDialog('操作无效，用户对自己发布的任务交稿!', 'alert', '操作失败提示', '', 0);
			return false;
		} else {
			$.getJSON(url + '&output=json&op_check=1', function(json) {
				if (json.status == 0) {
					showDialog('您已经投过标了!', 'alert', '操作提示', '', 0);
					return false;
				} else {
					showWindow("work_hand", url, "get", '0');
					return false;
				}
			})

		}
	}
}

/** 修改报价*/
function workModify(url) {
	if (check_user_login()) {
		if (uid == guid) {
			showDialog('操作无效，雇主无法修改他人报价!', 'alert', '操作失败提示', '', 0);
			return false;
		} else {
			$.getJSON(url + '&output=json&op_check=1', function(json) {
				if (json.status == 0) {
					showDialog('你无权修改此报价', 'alert', '操作提示', '', 0);
					return false;
				} else {
					showWindow("work_modify", url, "get", '0');
					return false;
				}
			})

		}
	}
}
/**
 * 稿件进行投票
 * 
 * @param int
 *            work_id 稿件编号
 * @param int
 *            vote_uid 被投票人
 */
function workVote(work_id, vote_uid) {
	if (check_user_login()) {
		if (vote_uid == uid) {
			showDialog("无法对自己进行投票", 'alert', '操作提示');
			return false;
		}
		if(LOCK==0){
			LOCK=1;
			var url = basic_url + '&op=work_vote';
			$.post(url, {
				work_id : work_id
			}, function(json) {
				LOCK=0;
				if (json.status == 1) {
					$("#work_vote_" + work_id).remove();
					var vote_num = $("#vote_num_" + work_id).html();
					num = parseInt(vote_num) + 1;
					$("#vote_num_" + work_id).html(num);
					showDialog(json.data, 'notice', json.msg);
					return false;
				} else
					showDialog(json.data, 'alert', json.msg);
			return false;
		}, 'json')
		}
	}
}

// 完工
function work_over(op) {
	var task_status = task_status;
	var op = op;
	var url = basic_url + '&op=' + op;
	if (check_user_login()) {
		showWindow("work_hand", url, "get", '0');
		return false;
	}

}

// 取消任务
function task_cancer(task_id) {
	showDialog("放弃招标一般在您不再需要竞标或者中标者不满意时操作，确定要放弃招标么？", 'confirm', "放弃招标",
			function() {
				location.href = '/index.php?do=task&task_id=' + task_id
						+ '&op=task_cancer';
			}, 0);
}
// 取消任务
function work_cancer(task_id,work_id) {
	showDialog("您被选为中标但是雇主还没有托管赏金，您确定要放弃竞标么？", 'confirm', "放弃竞标", function() {
		location.href = '/index.php?do=task&task_id=' + task_id+'&work_id='+work_id
				+ '&op=work_cancer';
	}, 0);
}


// 完成任务
function complate_task(task_id) {
	if (check_user_login()) {
		showDialog("完成任务后需要等待雇主验收，确定完成任务吗？", 'confirm', "任务完成", function() {
			location.href = '/index.php?do=task&task_id=' + task_id
					+ '&op=work_complate';
		}, 0);
	}
}
// 完成任务
function confirm_pay(task_id,t) {
	if (check_user_login()) {
		var d = t==1?'确定要验收吗?':'确定要付款吗?';
		showDialog(d, 'confirm', "任务完成", function() {
			if(LOCK==0){
				LOCK=1;
				location.href = '/index.php?do=task&task_id=' + task_id+ '&op=confirm_pay';
			}
		}, 0);
	}
}

function part_pay(task_id) {
	if (check_user_login()) {
		showWindow("part_pay", '/index.php?do=task&task_id='+task_id+'&op=part_pay', "get", '0');
	}
}

/**
 * 直接雇佣接受确认
 */
function hire_accept(task_id,t) {
	if (check_user_login()) {
		if (t == 1 || t == 2) {
			var d = t == 1 ? '确认接受雇佣吗?' : '确认放弃此次雇佣吗?';
			var op = t == 1 ? 'agree_task' : 'refuse_task';
			showDialog(d, 'confirm','操作提示', function() {
				location.href = '/index.php?do=task&task_id=' + task_id
						+ '&op='+op;
			}, 0);return false;
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
	if (guid != uid) {
		showDialog("只有雇主才能操作稿件", "alert", "操作提示");
		return false;
	} else {
		var str_content;
		str_content='是否确认选择此稿件，选择之后无法修改';
		if(to_status==11){
			showDialog(str_content,'confirm','操作提示',function(){
				if(LOCK==0){
					LOCK=1;
					var url = basic_url + '&op=work_choose&work_id=' + work_id;
					$.post(url, {
						to_status : to_status
					}, function(json) {
						LOCK=0;
						if (json.status == 1) {
							$("#work_11_" + work_id).remove();
							// $("#work_14_"+work_id).remove();
							$("#show_status_" + work_id).attr(
									"class",
									"work_status_big work_" + to_status
											+ "_big qualified_big1 po_ab");
							showDialog(json.data, "right", json.msg, "location.href='"
									+ basic_url + "'");
							return false;
						} else {
							showDialog(json.data, "alert", json.msg);
							return false;
						}
					}, 'json')
				}
			});
		}else{
			if(LOCK==0){
				LOCK=1;
				var url = basic_url + '&op=work_choose&work_id=' + work_id;
				$.post(url, {
					to_status : to_status
				}, function(json) {
					LOCK=0;
					if (json.status == 1) {
						$("#work_11_" + work_id).remove();
						// $("#work_14_"+work_id).remove();
						$("#show_status_" + work_id).attr(
								"class",
								"work_status_big work_" + to_status
										+ "_big qualified_big1 po_ab");
						showDialog(json.data, "right", json.msg, "location.href='"
								+ basic_url + "'");
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
