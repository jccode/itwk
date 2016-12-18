/*------------------------------------------

* File Name:			global_so.js
* Author:				der33,sutroon,编程浪子
* Version:				1.0.0 (2010-06-18)
* Website:				
* Description:			This document is used for itbangshou.com website.
* 						It required sopocket and jquery.

---------------------------------------------*/
var soep = {};
soep.SwitchNoticeTags = function(arg_obj_link, arg_int_id) {
    //Note: 首页中标公告和提现公告俩标签切换
    //Deom: soep.SwitchNoticeTags(this,1)
    var obj_item = $(arg_obj_link);
    var obj_parent = obj_item.parent();
    obj_parent.children().removeClass();
    obj_item.addClass("current");
    if (arg_int_id == 1) {
        obj_parent.removeClass("wrap_header_ex2");
        obj_parent.next().children("ul").first().show();
        obj_parent.next().children("ul").last().hide();
    }
    if (arg_int_id == 2) {
        obj_parent.addClass("wrap_header_ex2");
        obj_parent.next().children("ul").first().hide();
        obj_parent.next().children("ul").last().show();
    }
}

soep.SwitchTaskRoomTags = function(arg_obj_link, arg_int_id) {
    //Note: 首页任务大厅四标签切换
    //Demo: soep.SwitchTaskRoomTags(this,1)
    var obj_item = $(arg_obj_link);
    var obj_parent = obj_item.parent();
    obj_parent.parent().children("li").removeClass("current");
    obj_parent.addClass("current");
    obj_parent.parent().parent().siblings(".wrap_bodier").children().hide();
    obj_parent.parent().parent().siblings(".wrap_bodier").children().eq(arg_int_id - 1).show();
}

soep.SwitchBlock = function(arg_obj_link, arg_int_id,arg_str_idprefix) {
    //Note: 支持9个div切换
    //Demo: soep.SwitchTaskRoomTags(this,1,"mydiv")
    var obj_item = $(arg_obj_link);
    var obj_parent = obj_item.parent();
    obj_parent.parent().children("li").removeClass("current");
    obj_parent.addClass("current");
    for(var i=0;i<10;i++){
        var objDiv=document.getElementById(arg_str_idprefix+i);
        if(objDiv!=null){
            objDiv.style.display="none";
        }
    }
    document.getElementById(arg_str_idprefix+arg_int_id).style.display="";    
}

soep.Tip=function(arg_obj_link,arg_str_id){
	//Note:首页顶部快捷菜单弹出式下拉菜单
	//Demo:soep.Tip(this,'.mydemo');
	var blnShow=false;
	var intTimeout=200;
	var objCurrent=$(arg_obj_link);
	var objContent=$(arg_str_id);
	var offset=objCurrent.offset();
	var timer=null;
	objContent.offset({top:offset.top+24,left:offset.left});
	var showMenu=function(){	
		if(blnShow){return false;}
		objContent.show();
		blnShow=true;
		objContent.mouseover(function(){clearTimeout(timer);});
		objContent.mouseout(function(){hideMenu();});
	}
	
	var hideMenu=function(){	
		clearTimeout(timer);
		timer=setTimeout(function(){objContent.hide();blnShow=false;},intTimeout);
	}
	
	objCurrent.hover(
		function(){
			clearTimeout();
			timer=setTimeout(function(){showMenu();},intTimeout);
		},
		function(){
			clearTimeout(timer);
			if(blnShow){timer=setTimeout(function(){hideMenu();},intTimeout);};
		}
	)	
}


soep.Validator = function(arg_str_formname) {
    //Demo: onsubmit="return soep.Validator('login_form');"
    switch (arg_str_formname) {
        case "login_form":
            return checkloginform();
            break;

    }
    function checkloginform() {
        //登录表单检测
        var strSplitSymbol = "\n\n";
        var errmsg = "";
        var errcounter = 0;
        var objform = document.forms[arg_str_formname];
        if (objform.elements["form_username"].value == "") {
            errmsg += "用户名未填写" + strSplitSymbol;
            errcounter++;
        }
        if (objform.elements["form_password"].value == "") {
            errmsg += "密码未填写" + strSplitSymbol;
            errcounter++;
        }
        if (errcounter > 0) {
            alert("登录失败，原因是：\n\n" + errmsg);
            return false;
        }
        objform.submit();
    }
}
soep.RefreshValCode = function(arg_str_panelid) {
    //刷新验证码
    var objimg = document.getElementById(arg_str_panelid);
    objimg.src = "/Isu/Core/Lib/securityImage_code.asp?r=" + Math.round(Math.random() * 100);
}

soep.ToggleTree=function(arg_obj_item){
	//树形菜单的展开和收缩
	var objA=$(arg_obj_item);
	objA.parent().parent().parent().children("ul").children("li").children("ul").css("display","none");
	objA.next().css("display","block");
}


function AddAttachmentNew() {
    //新建的时候调用
    var objTable = $("#id_attachmentpanel");
    var intCount = $("#id_attachmentpanel tr").children().size() / 2 + 1;
    if (intCount > 5) { alert("附件不能超过5个"); return; }
    objTable.append("<tr><td>" + intCount + ". <input type='file' name='file" + intCount + "' onchange='FileExtChecking(this,1)' /></td><td><a href='javascript:void(0)' onclick='AddAttachmentNew()'>[增加]</a><a href='javascript:void(0);' onclick='DisposeTr(this)'>[取消]</a></td></tr>");
    $("#id_attachmentpanel a").hide();
    $("#id_attachmentpanel a").last().show();
    if (intCount < 5) { $("#id_attachmentpanel a").last().prev().show(); }
}
function AddAttachmentModifyP() {
    //编辑的时候调用
    var intAttach = parseInt('<%=intAttach %>');
    var objTable = $("#id_attachmentpanel");
    var intCount = $("#id_attachmentpanel tr").children().size() / 2 + 1;
    if (intCount > (5 - intAttach)) { alert("附件不能超过5个"); return; }
    objTable.append("<tr><td>" + intCount + ". <input type='file' name='file" + intCount + "' onchange='FileExtChecking(this,1);' /></td><td><a href='javascript:void(0)' onclick='AddAttachmentModify()'>[增加]</a><a href='javascript:void(0);' onclick='DisposeTr(this)'>[取消]</a></td></tr>");
    $("#id_attachmentpanel a").hide();
    $("#id_attachmentpanel a").last().show();
    if (intCount < (5 - intAttach)) { $("#id_attachmentpanel a").last().prev().show(); }
}
function DisposeTr(arg_obj_item) {
    var objTr = $(arg_obj_item).parent().parent();
    objTr.remove();
    $("#id_attachmentpanel a").last().show();
    $("#id_attachmentpanel a").last().prev().show();
}
function FileExtChecking(arg_obj_item, arg_int_mode) {
    if (arg_int_mode == 1) {
        if (FileExtCheck(arg_obj_item.value, 1) == false) {
            alert("文件类型不在允许上传的文件类型之内.");
        }
    }
    if (arg_int_mode == 2) {
        if (FileExtCheck(arg_obj_item.value, 2) == false) {
            alert("文件类型在系统禁止上传的黑名单之内。");
        }

    }
}
function FileExtCheck(arg_str_filename, arg_int_mode) {

    if (arg_str_filename == "") { return; }
    var enumAllowExt = "jpg,png,gif,jpeg,rar,zip,7z,doc,docx,ppt,pptx,xls,xlsx,txt,rtf";
    var enumDisallowExt = "asp;php;htm;html;exe;bat";
    var strExt = arg_str_filename.substr(arg_str_filename.lastIndexOf(".") + 1).toLowerCase();
    var enumExt;
    if (arg_int_mode == 1) {
        //白名单模式
        var arrExt = enumAllowExt.split(",");
        var intErr = 1;
        for (var i = 0; i < arrExt.length; i++) {
            if (strExt == arrExt[i]) { intErr = 0; }
        }
        if (intErr == 1) {
            return false;
        }
    }
    if (arg_int_mode == 2) {
        //黑名单模式
        var arrExt = enumDisallowExt.split(",");
        var intErr = 0;
        for (var i = 0; i < arrExt.length; i++) {
            if (strExt == arrExt[i]) { intErr = 1; }
        }
        if (intErr == 1) {
            return false;
        }
    }

}

function GetFileExt(arg_str_path) {
    var strExt = arg_str_filename.substr(arg_str_filename.lastIndexOf(".") + 1).toLowerCase();
    return strExt;
}




function menuFix() {
    var sfEls = document.getElementById("nav").getElementsByTagName("li");
    for (var i=0; i<sfEls.length; i++) {
        sfEls[i].onmouseover=function() {
        this.className+=(this.className.length>0? " ": "") + "sfhover";
        }
        sfEls[i].onMouseDown=function() {
        this.className+=(this.className.length>0? " ": "") + "sfhover";
        }
        sfEls[i].onMouseUp=function() {
        this.className+=(this.className.length>0? " ": "") + "sfhover";
        }
        sfEls[i].onmouseout=function() {
        this.className=this.className.replace(new RegExp("( ?|^)sfhover\\b"), 
"");
        }
    }
}
window.onload = menuFix;

