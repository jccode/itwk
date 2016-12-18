/*
 -----------------------------------------------
| FileName:			sobox.js					|
| Desctiption:		JAVASCRIPT通用脚本集		|
| version:			1.0 (2010-05-14	)			|
 -----------------------------------------------
*/
var sobox={};
sobox.SOHPanel=function(arg_str_panelid){
	/*
	控制容器的显示或隐藏
	author:sutroon; version:1.0 (2010-05-14)
	==DEMO======================================
	sobox.SOHPanel("#testid")
	*/
	var obj_panel=$(arg_str_panelid);
	if(obj_panel.css("display")=="block"){
		obj_panel.css({display:"none"});
	}else{
		obj_panel.css({display:"block"});
	}
}

sobox.URLVar=function(strVarName){
	/* 
	获取URL变量
	author:编程浪子; version:1.0 (2010-01-08)
	==DEMO===================================
	pageid=sobox.URLVar("page")
	*/
	var strUrl=document.location.href;
	var intPos1=strUrl.indexOf("?");
	if(intPos1==-1){
		return;
	}
	var strVars=strUrl.substr(intPos1+1);
	var arrVars=strVars.split("&");
	var strName,strValue,strItem;
	for(var i=0;i<arrVars.length;i++){
		strItem=arrVars[i];
		strName=strItem.substr(0,strItem.indexOf("="));
		strValue=strItem.substr(strItem.indexOf("=")+1);
		if(strName==strVarName){
			return strValue;
			break;
		}
	}	
}

sobox.FormValidator = function(arg_obj_form, arg_enum_keyvalue) {
    /*-----------------------------------------------------------
    * sobox.FormValidator(arg_obj_form, arg_enum_keyvalue):Boolean
    * 表单验证
    * author:编程浪子; version:1.0.0 (2010-07-12)
    ==DEMO=======================================================
    return sobox.FormValidator(this,"form_title:require:标题未填写")
    -------------------------------------------------------------*/
    if (arg_obj_form == null) { return; }
    if (arg_obj_form.tagName.toLowerCase() !== "form") { return; }
    var strErrMsg = "";
    var intErrCount = 0;
    var strErrSplit = "\n\n";

    var arrKeyValue = arg_enum_keyvalue.split(";");
    for (var i = 0; i < arrKeyValue.length; i++) {
        var enumKeyValue = arrKeyValue[i];

        if (enumKeyValue.indexOf(":") > 0) {
            arrItem = enumKeyValue.split(":");
            strField = arrItem[0];
            strErrTip = ". " + arrItem[2] + strErrSplit;
            switch (arrItem[1]) {
                case "require":
                    //必填字段
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    if (domFiled.value == "") { intErrCount++; strErrMsg += intErrCount + strErrTip; }
                    break;
                case "number":
                    //数字类型
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    if (domFiled.value !== "") {
                        if (isNaN(domFiled.value)) {
                            intErrCount++;
                            strErrMsg += intErrCount + strErrTip;
                        }
                    }
                    break;
                case "date":
                    //日期类型
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    if (domFiled.value !== "") {
                        if ((domFiled.value.indexOf("-")==-1 && domFiled.value.indexOf("/")==-1) || isNaN(domFiled.value.replace(/-/g, '').replace(/:/g, '').replace(' ', ''))) {
                            intErrCount++;
                            strErrMsg += intErrCount + strErrTip;
                        }
                    }
                    break;
                case "email":
                    //邮件类型
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    if (domFiled.value !== "") {
                        if (domFiled.value.indexOf('@') < 0 || domFiled.value.indexOf(".") < 0) {
                            intErrCount++;
                            strErrMsg += intErrCount + strErrTip;
                        }
                    }
                    break;
                case "password":
                    //密码类型
                    break;
                case "radio":
                    //单选类型
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    var groupRadio = domFiled;
                    var intChecked = 0;
                    for (var i1 = 0; i1 < groupRadio.length; i1++) {
                        if (groupRadio[i1].checked == true) { intChecked++; break; }
                    }
                    if (intChecked == 0) { strErrMsg += intErrCount + strErrTip; }
                    break;
                case "checkbox":
                    //复选类型
                    var domFiled = arg_obj_form.elements[strField];
                    if (domFiled == null) { intErrCount++; strErrMsg += intErrCount + " ." + strField + " no found" + strErrSplit; break; }
                    break;
                case "logic":
                    //复杂逻辑类型                    
                    break;
                default:
                    //无效的类型限制
                    intErrCount++;
                    strErrMsg += intErrCount + ". " + strField + " 无效的类型声明" + strErrSplit;
                    break;
            }
        }
    }
    if (intErrCount > 0) {
        alert("表单提交失败，原因是：\n\n" + strErrMsg);
        return false;
       
    }
}
sobox.Form = null;
sobox.FormVar = function(strFieldName__) {
    /*-------------------------------------
    * 获取表单文本域的值:String
    * author:编程浪子; version:1.0.0 (2010-10-11)
    * 错误则返回undefined
    */
    var objField = sobox.Form.elements[strFieldName__];
    if (objField == undefined) {
        return objField;
    }
    return objField.value;
}

sobox.Isnumeric = function(strValue__) {
    /*
    * 判断是否是数字:Boolean
    * author:铸剑师; version:1.0.0 (2010-10-11)
    */
    if (sobox.Trim(strValue__) == "") { return false; }
    if (isNaN(strValue__)) { return false; }
    return true;
}

sobox.Trim = function(strText__) {
    /*
    * 过滤字符串中的空格:String，已弃用
    * author:来自网络; version:1.0.0 (2010-10-11)
    */
    return strText__.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

sobox.FileExtValidator = function(arg_str_path, arg_enum_fileext, arg_bln_validtype) {
/*---------------------------------------------------------------------------------
* sobox.FileExtValidator(arg_str_path, arg_enum_fileext, arg_bln_validtype):Boolean
* 文件类型检测
* author:sutroon; version:1.0.0 (2010-07-08)
==DEMO=============================================================================
if(!sobox.FileExtValidator("C:\myfile.asp","jpg,png,gif",true)){alert('文件不是图片格式');}
----------------------------------------------------------------------------------*/

    if (arg_str_path == "") { return; }
    var strExt = arg_str_path.substr(arg_str_path.lastIndexOf(".") + 1).toLowerCase();
    var arrExt = arg_enum_fileext.split(",");
    var intErr = 1;
    if (arg_bln_validtype) {
        //包含模式
        for (var i = 0; i < arrExt.length; i++) {
            if (strExt == arrExt[i]) { intErr = 0; }
        }
    }

    if (!arg_bln_validtype) {
        //反包含模式         
        intErr = 0;
        for (var i = 0; i < arrExt.length; i++) {
            if (strExt == arrExt[i]) { intErr = 1; }
        }
    }
    if (intErr > 0) { return false; } else { return true; }
}

/// [Void] 加入收藏
/// Author:来源自网络; Version:1.0.0 (2009-11-9)
/// ==DEMO==
/// sobox.AddFavorite("http://www.net-window.com","网窗科技-官方首页")
sobox.AddFavorite=function(strURL,strTitle){
	if(strURL=="" || strURL==null){
		strURL=window.location.href;
	}
	try{
		window.external.addFavorite(strURL,strTitle);
	}catch(e){
		try{
			window.sidebar.addPanel(strTitle,strURL, "");
		}catch(e){
			alert("加入收藏失败，请使用Ctrl+D进行添加");
		}
	}
}
/// [Void] 设为首页
/// Author:来源自网络; Version:1.0.0 (2009-11-9)
/// ==DEMO==
/// sobox.SetHome(this,"http://www.net-window.com")
sobox.SetHome=function (obj,strURL){
	try{
		obj.style.behavior='url(#default#homepage)';obj.setHomePage(strURL);
	}catch(e){
		if(window.netscape){
			try {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			}catch (e) {
				alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage',strURL);
		}
	}
}

sobox.GetCookie=function(strKey){
	/*
	获取Cookie值
	author:sutroon; version:1.0 (2009-11-9)
	==DEMO=======================================
	username=sobox.GetCookie("username")
	*/
	var strCookie=document.cookie;
	var aryCookie=strCookie.match(new RegExp("(^|)"+strKey+"=([^;]*)(;|$)"));
	if(aryCookie==null){
		return null;
	}else{
		return aryCookie[2];
	}
}

sobox.SetClock=function(arg_str_panelid,arg_str_template){
	/*
	时钟功能，已弃用
	author:sutroon; version:1.1 (2009-11-9)
	==UPDATE=============================================
	2010-02-15 by 编程浪子:修改构造结构，支持自定义模板，取消strMsgId参数
	==DEMO===============================================
	<span id="Clock">Time Zone Reseting...</span>
	setInterval("sobox.SetClock('#Clock','y年m月d日 h时i分 w');",1000);
	*/
	var spanClock=$(arg_str_panelid);
	if(spanClock==null){return;}
	var datNow=new Date();
	var datYear=datNow.getFullYear();
	var datMonth=datNow.getMonth()+1;
	var datDay=datNow.getDate();
	//时间
	var datHour=datNow.getHours();
	var datMinute=datNow.getMinutes();
	var datSecond=datNow.getSeconds();
	//星期
	var datWeek=datNow.getDay();
	//更换
	var aryWeek=new Array("Sun.","Mon.","Tues.","Wed.","Thur.","Fri.","Sat.");
	var aryWeek1=new Array("天","一","二","三","四","五","六");
	//格式化	
	var strWord="";
	if(datHour>0 && datHour<6){
		strWord="凌晨了，还没休息吗？注意保暖哦！";
	}else if(datHour>=6 && datHour<12){
		strWord="上午好！祝您今天好心情！";
	}else if(datHour>=12 && datHour<13){
		strWord="中午了，要休息一下哦。";
	}else if(datHour>=13 && datHour<18){
		strWord="下午好，努力战斗吧~";
	}else if(datHour>=18 && datHour<22){
		strWord="晚上好！";
	}else if(datHour>=22 && datHour<24){
		strWord="夜深了，注意休息哦。";
	}else{
		strWord="Hi,欢迎回来。";
	}	
	if(arg_str_template==undefined){
		arg_str_template="y-m-d h-i-s w";
	}
	arg_str_template=arg_str_template.replace("y",datYear);
	arg_str_template=arg_str_template.replace("m",sobox.PaddingInt(datMonth,2));
	arg_str_template=arg_str_template.replace("d",sobox.PaddingInt(datDay,2));
	arg_str_template=arg_str_template.replace("h",sobox.PaddingInt(datHour,2));
	arg_str_template=arg_str_template.replace("i",sobox.PaddingInt(datMinute,2));
	arg_str_template=arg_str_template.replace("s",sobox.PaddingInt(datSecond,2));
	arg_str_template=arg_str_template.replace("w",aryWeek[datWeek]);
	arg_str_template=arg_str_template.replace("W","星期"+aryWeek1[datWeek]);
	arg_str_template=arg_str_template.replace("text",strWord);
	spanClock.html(arg_str_template);
}

sobox.PaddingInt=function(intNumber,intLength){
	/*
	补齐数位(用于Clock函数)，已弃用？
	author:sutroon; version:1.0 (2009-11-9)
	==Desc=======================================
	说明
		1. intNumber:原始数值
		2. intLength:位数
		3. 自动以0补齐
	*/
	var strNumber=intNumber.toString();
	var strPadding="";
	for(var i=0;i<(intLength-strNumber.length);i++){
		strPadding+="0";
	}
	return strPadding+strNumber;
}

/// [Void] 选中表单中所有名称为strName的checkbox
/// Author:sutroon; Version:1.1.0
/// ==LOG==
/// 1.0.0 [2009.11.26] BY sutroon
/// 1.1.0 [2010.9.10] BY 编程浪子 新增可选参数 arg_str_formname
/// ==DEMO==
/// sobox.checboxChecking("n","myform")
sobox.CheckboxChecking = function(arg_str_name, arg_str_formname) {
    if (arg_str_formname == undefined) {
        var objCollect = document.all[arg_str_name];
    } else {
        var objCollect = document.forms[arg_str_formname].elements[arg_str_name];
    }
    objCollect[0].checked;
    for (var i = 0; i < objCollect.length; i++) {
        objCollect[i].checked = !objCollect[i].checked;
    }
}
/// [Void] 复制内容到系统剪贴簿
/// Author:sutroon; Version:1.0 (2009-11-26)
/// ==DEMO==
/// sobox.CopyTxt("hello")
sobox.CopyTxt=function (arg_str_text) { 
	if(arg_str_text==""){
		return;
	}
	//obj_input.select();
  if(window.clipboardData) {        
		window.clipboardData.clearData();        
		window.clipboardData.setData("Text", arg_str_text);
		alert("内容已经复制到剪贴板！")              
	} else if(navigator.userAgent.indexOf("Opera") != -1) {        
		window.location = arg_str_text;        
	} else if (window.netscape) {        
		try {        
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");        
		} catch (e) {        
				alert("无法将内容复制到剪贴板，请手动复制内容。\n解决方法：\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");        
		}        
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);        
		if (!clip)  return;        
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);        
		if (!trans)  return;        
		trans.addDataFlavor('text/unicode');        
		var str = new Object();        
		var len = new Object();        
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);        
		var copytext = arg_str_text;        
		str.data = copytext;        
		trans.setTransferData("text/unicode",str,copytext.length*2);        
		var clipid = Components.interfaces.nsIClipboard;        
		if (!clip)  return false;        
		clip.setData(trans,null,clipid.kGlobalClipboard);        
		alert("内容已经复制到剪贴板！")        
	}        
}

/// [Void] 设置页面所有TEXT类型的INPUT获取焦点的时候自动选择文本
/// Author:编程浪子; Version:1.0 (2009-12-05)
/// ==DESC==
/// 用于表单，在每个有表单的页面加入sobox.TextFocus()即可
sobox.TextBoxFocus=function(){
	for(var i=0;i<$(":text").length;i++){
		$(":text").bind("focus",function(){$(this).select()});
	}
}

sobox.URLAction=function(arg_str_actionurl,arg_enum_keys){
	/*
	模拟表单提交数据(Get提交)
	author:sutroon; version:1.0.0 (2010-07-07)
	==DEMO=========================================================
	sobox.URLAction("action.asp","kw:form_keyword:d,cat:form_catalog:d,q:search:c;id:intID:v")
	==DESC=========================================================
	arg_enum_keys:(URL键名称:取值元素:元素类型)
	d:控件节点(dom),函数将根据其id读取其值
	v:变量(var),函数将引用其本身，不加以运算
	c:常量(const)
	*/
	fnc_arr_keys=arg_enum_keys.split(",");
	for(var i=0;i<fnc_arr_keys.length;i++){
		fnc_arr_key=fnc_arr_keys[i].split(":");
		fnc_str_keyname=fnc_arr_key[0];
		fnc_str_data=fnc_arr_key[1];
		fnc_str_datatype=fnc_arr_key[2].toLowerCase();
		fnc_str_output+=fnc_str_keyname+"="
		switch(fnc_str_datatype){
			case "d":
				fnc_str_output+=escape($(fnc_str_data).val());	
				break;
			case "v":
				fnc_str_output+=escape(eval(fnc_str_data));
				break;
			case "c":
				fnc_str_output+=escape(fnc_str_data);
				break;
		}		
	}
	location.href=arg_str_actionurl+fnc_str_output;
}

sobox.Link=function(arg_str_uri){
	/*
	* 跳转链接，已弃用
	* author:sutroon; version:1.0.0 (2010-07-13)
	==DEMO================================
	sobox.Link("login.asp?q=logoff");
	*/
	location.href=arg_str_uri;
}
/// [Void] 提交表单，顺便修改formquery值
/// Author:编程浪子; Version:1.0.0 (2010-09-01)
/// ==DESC==
/// 如果此功能无效，则必定是未放入隐藏文本域
sobox.SubmitForm = function(strFormName__, strFormQuery__) {
    var objForm = document.forms[strFormName__];
    objForm.elements["query_sohide"].value = strFormQuery__;
    objForm.submit();
}
sobox.Browser = function() {
    /*
    * 判断浏览器类型，似乎已经弃用？
    * author:sutroon; version:1.0.0 (2010-11-22)
    */
    var enumUserAgent = window.navigator.userAgent;
    if (enumUserAgent.indexOf("MSIE 8.0") > 0) {
        return "IE8";
    }
}
/// [Void] 符合SOTAGMENU标准的标签菜单切换
/// Author:铸剑师; Version:1.5.0
/// ==LOG==
/// 1.0.0 [2010.11.26] BY 铸剑师 创建方法
/// 1.2.0 [2010.12.9] BY Boolean.CN 新增自定义内容对象oContent__,支持自定义内容匹配
/// 1.5.0 [2011.1.26] BY sutroon 新增布局判断，支持Td布局和Li布局
sobox.SwitchTagsMenu = function(oLink__, intIndex__, oContent__) {
    var oLink = $(oLink__);
    var strLayoutTag = oLink.parent().attr("tagName");
    oLink.parent().parent().children(strLayoutTag).removeClass("current");
    oLink.parent().addClass("current");
    var oDB = null;
    if (oContent__ == undefined) {
        oDB = oLink.parent().parent().parent().parent().next();
    } else {
        oDB = oContent__;
    }
    oDB.children().hide();
    oDB.children().eq(intIndex__ - 1).show();

}
/// [String] 编码符合GOOGLE MAP标准的编码
/// Author:sutroon; Version:1.0.0 (2010-12-6)
sobox.GooMapEncode = function(strText__) {
    var strText = escape(strText__);
    strText = strText.replace(/\%/g, '\\');
    return strText;
}

/// [Array] 获取表单文本域的值，返回对应的数组
/// Author:铸剑师; Version:1.0.0 (2011.1.11)
/// ==DEMO==
/// GetFormValues("#regUsername,#regPassword,#regConfpwd,#regUsertype,#regProvince,#regCity,#regMobilphone,#regEmail,#regQq")
sobox.GetFormValues = function(enumFields__) {
    var arrFields = enumFields__.toString().split(',');
    var arrValues = new Array();
    for (var i = 0; i < arrFields.length; i++) {
        arrValues[i] = $(arrFields[i]).val();
    }
    return arrValues;
}