$(function() {
	$("#qq").click(function() {
		$("#ct_qq").toggle()
	});
	$("#msn").click(function() {
		$("#ct_msn").toggle()
	});
	contact();
	$(":radio[name='contact_type']").click(function() {
		$(this).attr("checked", "checked");
		contact()
	});
	$("#tar_content").blur(function() {
		contentCheck("tar_content", "��������", 50, 1000)
	})
});
function contact() {
	var a = parseInt($(":radio[name='contact_type']:checked").val()) + 0;
	if (a == "1") {
		$(".lit_form input:[type='text']").removeAttr("ignore").removeAttr(
				"disabled").val("")
	} else {
		$(".lit_form input:[type='text']").each(
				function() {
					$(this).attr("disabled", "disabled").attr("ignore", "true")
							.val($(this).attr("ext"))
				})
	}
}
function getMaxDday(a) {
	if (a) {
		$.getJSON(basic_url, {
			ajax : "getmaxday",
			task_cash : a
		}, function(b) {
			$(".lit_form .pad10 span:last-child").removeClass().text("");
			if (b.status == 1) {
				$("#txt_task_day").attr("limit",
						"required:true;type:date;than:min;less:" + b.msg).val(
						b.msg);
				$("#max").val(b.msg);
				var c = $("#txt_task_day").attr("min_day");
				title = " Ԥ�Ƶ������������,��ǰԤ��������С����Ϊ:" + c
						+ "��,����ֹʱ�䣺" + b.data;
				$("#txt_task_day").attr("original-title", title);
				$("#txt_task_day").attr("max", b.msg)
			} else {
				return false
			}
		})
	}
}
function show_payitem_num(c, a) {
	var a = a;
	var b = $(c).attr("checked");
	if (b == true) {
		if (a == "map") {
			$("#set_map").show();
			add_payitem($("#item_map"), "add", 1)
		} else {
			$("#span_" + a).show()
		}
	} else {
		if (a == "map") {
			add_payitem($("#item_map"), "del", 1);
			$("#set_map").hide()
		} else {
			del_payitem(a);
			$("#span_" + a).hide();
			$("#payitem_" + a).val("")
		}
	}
}
function edit_payitem(a) {
	var a = a;
	var b = parseInt($("#payitem_" + a).val());
	var c = parseInt($("#checkbox_" + a).attr("item_cash"));
	var d = parseInt($("#ago_total").val());
	add_payitem($("#checkbox_" + a), "add", b)
}
function del_payitem(a) {
	var a = a;
	var b = parseInt($("#payitem_" + a).val());
	add_payitem($("#checkbox_" + a), "del", b)
}
function checkDay() {
	var a = parseInt($("#txt_task_day").attr("max")) + 0;
	var b = parseInt($("#txt_task_day").val()) + 0;
	if (b > a) {
		$("#span_task_day").html(
				"<span>��ǰ�����������������Ϊ:" + a + "��</span>");
		return false
	} else {
		return true
	}
}
function checkAgreement() {
	if ($("#agreement").attr("checked") == false) {
		showDialog("����ͬ�����񷢲�Э��", "alert", "������ʾ");
		return false
	} else {
		return true
	}
}
function stepCheck(c) {
	var a = checkForm(document.getElementById("frm_" + r_step));
	var b = false;
	switch (r_step) {
	case "step1":
		if (checkDay()) {
			if (a) {
				b = true
			}
		}
		break;
	case "step2":
		if (a) {
			if (contentCheck("tar_content", "��������", 50, 1000)
					&& checkAgreement()) {
				b = true
			}
			if (c == 8 && checkAgreement()) {
				b = true
			}
		}
		break;
	case "step3":
		if ($("#item_map").attr("checked") == true
				&& $.trim($("#point").val()) == "") {
			set_map();
			return false
		} else {
			if (a) {
				$("#frm_" + r_step).submit()
			}
		}
		break;
	case "step4":
		break
	}
	if (b == true) {
		check_pub_priv()
	}
}
function check_pub_priv() {
	$.getJSON(basic_url, {
		ajax : "check_priv"
	}, function(a) {
		if (a.status == "1") {
			$("#frm_" + r_step).submit()
		} else {
			showDialog(a.data, "alert", a.msg);
			return false
		}
	})
}
uploadBlur = function() {
	if (ifOut("upfile", "5") && $("#upload").val()) {
		upload("upload", "att", "front", "", "", "task")
	} else {
		return false
	}
};
function add_payitem(f, d, a) {
	var c = parseInt($(f).attr("item_id")) + 0;
	var e = parseFloat($(f).attr("item_cash") * a);
	var h = $.trim($(f).val());
	var b = $.trim($(f).attr("item_code"));
	var g = parseFloat($("#total").text().toString());
	switch (d) {
	case "add":
		$.post(basic_url, {
			ajax : "save_payitem",
			item_id : c,
			item_name : h,
			item_cash : e,
			item_code : b,
			item_num : a
		}, function(i) {
			$("#total").text(i.msg)
		}, "json");
		break;
	case "del":
		$.post(basic_url, {
			ajax : "rm_payitem",
			item_id : c
		}, function(i) {
			$("#total").text(i.msg)
		}, "json");
		break
	}
}
function uploadResponse(b) {
	if ($("#" + b.fid).length < 1) {
		var a = $('<li class="items" id="'
				+ b.fid
				+ '" style="display:none"><span>'
				+ b.msg.localname
				+ '</span><a href="javascript:;" class="close" onclick="del_file('
				+ b.fid + ",'" + b.msg.url + "')\">&times;</a></li>");
		a.appendTo("#upfile").fadeIn(1000);
		loadingControl("#upfile li", "#loading_" + b.fid, 2000);
		var c = $("#file_ids").val();
		if (c) {
			$("#file_ids").val(c + "," + b.fid)
		} else {
			$("#file_ids").val(b.fid);
			$("#upload").val("")
		}
	}
}
function del_file(a, b) {
	$.getJSON("index.php?do=ajax&view=file&ajax=delete&file_id=" + a
			+ "&filepath=" + b, function(c) {
		if (c.status == "1") {
			var d = $("#file_ids").val().toString();
			d = d.replace(a, "");
			$("#file_ids").val(d);
			$("#" + a).remove()
		}
	})
};