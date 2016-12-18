
function killerror() {
    return true;
}

//window.onerror = killerror();

var obj = $("body");
if (obj instanceof jQuery) {
    $(document).ready(function() {
        document.onerror = killerror();
    });
} else {
    window.onerror = killerror();
} 
 

function DoJSLoaded(js, callback) { //JS载入完毕执行函数
    var script = document.createElement("script")
    script.type = "text/javascript";
    script.language = 'javascript';
    script.defer = true;
    script.src = '' + js + '';
    //var head = document.getElementsByTagName('head').item(0);
    var head = document.getElementsByTagName('head')[0];
    head.appendChild(script);
    if (script.readyState) {  //IE
        script.onreadystatechange = function() {
            if (script.readyState == "loaded" || script.readyState == "complete") {
                if (callback != null && callback != '') {
                    try {
                        callback();
                    }
                    catch (e) { }
                }
            }
        };
    }
    else {  //Others
        script.onload = function() {
            if (callback != null && callback != '') {
                try {
                    callback();
                }
                catch (e) { }
            }
        };
    }
}

//设置cookie 
function setCookie(NameOfCookie, value, expiredays) {
    var ExpireDate = new Date();
    ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
    document.cookie = NameOfCookie + "=" + escape(value) + ((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString());
}

//获取cookie值 
function getCookie(NameOfCookie) {
    if (document.cookie.length > 0) {
        var begin = document.cookie.indexOf(NameOfCookie + "=");
        if (begin != -1) {
            begin += NameOfCookie.length + 1; //cookie值的初始位置 
            end = document.cookie.indexOf(";", begin); //结束位置 
            if (end == -1) end = document.cookie.length; //没有;则end为字符串结束位置 
            return unescape(document.cookie.substring(begin, end));
        }
    }
    return null;
}

//删除cookie 
function delCookie(NameOfCookie) {
    if (getCookie(NameOfCookie)) {
        document.cookie = NameOfCookie + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}

function trim(str) {  //删除左右两端的空格   
    return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str) {  //删除左边的空格   
    return str.replace(/(^\s*)/g, "");
}
function rtrim(str) {  //删除右边的空格   
    return str.replace(/(\s*$)/g, "");
}

/*------------------------------------------------------------------------------------
* Block Name       :       AjaxLogin() Ajax跨域登录JS模块
* Version          :       1.0.0 (2011-6-8)
* Author           :       Lajox
* Description      :       解决Ajax跨域登录问题，一站式登录、单点登录方式 
* Homepage         :       http://www.19www.com
* Email            :       lajox@19www.com
---------------------------------------------------------------------------------------*/
var loginState = 0;
var loginMsg = "";
var AjaxLogin = function(URL, userid, pwd, vcode, keepon, callback_s, callback_f) { // 当vcode(验证码)设置为vcode=0表示跳过验证码验证这步; 当password=0时表示忽略密码验证(出于安全此项暂无效)
    var remoteURL = URL + '?module=global&action=ajaxlogin&userid=' + userid + '&pwd=' + pwd + '&vcode=' + vcode + '&keepon=' + keepon + '&t=' + Math.random() + '&callback=?';
    /*
    //remoteURL页面返回信息如下:
    {
    "msg":"\u64cd\u4f5c\u6210\u529f\uff01",
    "userid": "lajox",
    "pwd": "",
    "status":1
    }
    */

    $(document).ready(function() {
        $.getJSON(remoteURL, function(data) {
            if (data.status == 1) {
                loginState = 1;
                //alert("login success");
                //alert(data.msg);

                /*___*/
                var items = [];
                $.each(data, function(key, val) {
                    items.push('key:' + key + ',value:' + val + ';');
                });
                loginMsg = items.join(''); //远程页面返回信息,可进一步加工处理

                callback_s(); //登录成功回调函数

            } else {
                loginState = 0;
                //alert("login failed");
                //alert(data.msg);

                /*___*/
                callback_f(); //登录失败回调函数
            }
        });
    });

};

/*------------------------------------------------------------------------------------
* Block Name       :       AjaxLogout() Ajax跨域退出JS模块
* Version          :       1.0.0 (2011-6-8)
* Author           :       Lajox
* Description      :       解决Ajax跨域用户注销问题，一站式注销、单点退出方式 
* Homepage         :       http://www.19www.com
* Email            :       lajox@19www.com
---------------------------------------------------------------------------------------*/
var logoutState = 0;
var logoutMsg = "";
var AjaxLogout = function(URL, userid, pwd, vcode, callback_s, callback_f) { // 当password=0时表示忽略密码验证; 当vcode(验证码)设置为vcode=0表示跳过验证码验证这步
    var remoteURL = URL + '?module=global&action=ajaxlogout&userid=' + userid + '&pwd=' + pwd + '&vcode=' + vcode + '&t=' + Math.random() + '&callback=?';
    /*
    //remoteURL页面返回信息如下:
    {
    "msg":"\u64cd\u4f5c\u6210\u529f\uff01",
    "userid": "lajox",
    "password": "",
    "status":1
    }
    */
    //window.open(remoteURL,'_blank');

    $(document).ready(function() {
        $.getJSON(remoteURL, function(data) {
            if (data.status == 1) {
                logoutState = 1;
                //alert("logout success");
                //alert(data.msg);

                /*___*/
                var items = [];
                $.each(data, function(key, val) {
                    items.push('key:' + key + ',value:' + val + ';');
                });
                logoutMsg = items.join(''); //远程页面返回信息,可进一步加工处理

                callback_s(); //登录成功回调函数

            } else {
                logoutState = 0;
                //alert("login failed");
                //alert(data.msg);

                /*___*/
                callback_f(); //登录失败回调函数
            }
        });
    });
};


    function redirect(strURL, T, strTip) {
        if (strTip == null || strTip == '') { strTip = "操作成功" }
        if (T == null || T == '' || T <= 0) { T = 0 }
        document.write('<span style=\'font-size:12px;\'>' + strTip + '，正在跳转...</span>.');
        document.write('<span style=\'font-size:12px;color:#888;\'>&nbsp;若' + T + '秒后没有跳转，请点击<a href=\'' + strURL + '\' style=\'color:#0000ff;text-decoration:underline;\' target=\'_top\'>这里</a></span>');
        setTimeout(function() { window.top.location.href = '' + strURL + ''; }, T * 1000);
    }



    /***_______________________________________________________________________________***/
    
    /*
        //ajax登录
        function ajax_login() {
            var username_ = document.getElementById("username").value;
            var password_ = document.getElementById("password").value;
		    var vcode_ = 0;
		    var output_ = "json"; //output="json"、output="code"
		    var rnd_ = Math.floor(Math.random()*100000+1);
		    var callback_ = "jsoncallback";
    		
            //$.get("/apiLogin/login.do.asp?action=ajaxlogin&userid=" + username_ + "&pwd=" + password_ + "&vcode=" + vcode_ + "&output=" + output_ + "" + "&t=" + rnd_ + "&callback=" + callback_ + "", function(data) { alert(data); });
		    $.get(
			    "/apiLogin/login.do.asp",
			    {
				    action: "ajaxlogin",
				    userid: ""+username_,
				    pwd: ""+password_,
				    vcode: ""+vcode_,
				    output: ""+output_,
				    t: ""+rnd_,
				    callback: callback_
			    }, 
			    function(data){
				    alert(data);
			    }
		    );
        }

        //ajax退出
        function ajax_logout() {
            var username_ = document.getElementById("username").value;
            var password_ = document.getElementById("password").value;
		    var vcode_ = 0;
		    var output_ = "json"; //output="json"、output="code"
		    var rnd_ = Math.floor(Math.random()*100000+1);
		    var callback_ = "jsonp" + rnd_;

		    $.post(
			    "/apiLogin/login.do.asp", 
			    {
				    action: "ajaxlogout",
				    userid: ""+username_,
				    pwd: ""+password_,
				    vcode: ""+vcode_,
				    output: ""+output_,
				    t: ""+rnd_,
				    callback: callback_
			    }, 
			    function(data){
				    if(output_ == "json"){ //output=="json"
					    alert(data);
				    }
				    else { //output=="code"
					    alert(data);
				    }
			    }
			    ,"json"
		    );
        }

	    $(function(){
		    $.post('/', {v2: 2}, function(data){
			    $('<pre/>').append(data).appendTo('body');
		    },'json');
	    });
    */
    
    
    

    var loginState = 0;
    var loginMsg = "";

    var ajaxlogin = function(URL, userid, pwd, vcode, keepon, callback_s, callback_f) {
        var serverurl = (typeof URL == 'undefined' || URL == null || URL == '') ? "/apiLogin/login.do.asp" : URL;
        var username_ = userid;
        var password_ = pwd;
        var vcode_ = vcode;
        var keepon_ = keepon;
        var output_ = "json"; //output="json"、output="code"
        var rnd_ = Math.floor(Math.random() * 100000 + 1);
        var callback_ = "?";
        var pageurl = "?action=ajaxlogin&userid=" + username_ + "&pwd=" + password_ + "&vcode=" + vcode_ + "&output=" + output_ + "" + "&t=" + rnd_ + "&callback=" + callback_ + "";
        var remoteurl = "" + serverurl + pageurl;
        $(document).ready(function() {
            $.getJSON(
				remoteurl,
				function(data) {
                    var stout = setTimeout(function() { //如果8秒还没响应，则强制下一步
                        if (callback_s != null) callback_s();
                    }, 8000);
				    if (output_ == "json") {
				        if (data.status == 1) {
				            loginState = 1;
				            var items = [];
				            $.each(data, function(key, val) {
				                items.push('key:' + key + ',value:' + val + ';');
				            });
				            loginMsg = items.join(''); //远程页面返回信息,可进一步加工处理
				            if (callback_s != null) callback_s(); //登录成功回调函数
				        } else {
				            loginState = 0;
				            if (callback_f != null) callback_f(); //登录失败回调函数
				        }
				    } else {
				        if (data == 1) {
				            loginState = 1;
				            if (callback_s != null) callback_s();
				        } else {
				            loginState = 0;
				            if (callback_f != null) callback_f();
				        }
				    }
				}
			);
        });

    };

    var logoutState = 0;
    var logoutMsg = "";

    var ajaxlogout = function(URL, userid, pwd, vcode, keepon, callback_s, callback_f) {
        var serverurl = (typeof URL == 'undefined' || URL == null || URL == '') ? "/apiLogin/login.do.asp" : URL;
        var username_ = userid;
        var password_ = pwd;
        var vcode_ = vcode;
        var keepon_ = keepon;
        var output_ = "json"; //output="json"、output="code"
        var rnd_ = Math.floor(Math.random() * 100000 + 1);
        var callback_ = "?";
        var pageurl = "?action=ajaxlogout&userid=" + username_ + "&pwd=" + password_ + "&vcode=" + vcode_ + "&output=" + output_ + "" + "&t=" + rnd_ + "&callback=" + callback_ + "";
        var remoteurl = "" + serverurl + pageurl;
        $(document).ready(function() {
            $.getJSON(
				remoteurl,
				function(data) {
				    if (output_ == "json") {
				        if (data.status == 1) {
				            logoutState = 1;
				            var items = [];
				            $.each(data, function(key, val) {
				                items.push('key:' + key + ',value:' + val + ';');
				            });
				            logoutMsg = items.join(''); //远程页面返回信息,可进一步加工处理
				            if (callback_s != null) callback_s(); //退出成功回调函数
				        } else {
				            logoutState = 0;
				            if (callback_f != null) callback_f(); //退出失败回调函数
				        }
				    } else {
				        if (data == 1) {
				            logoutState = 1;
				            if (callback_s != null) callback_s();
				        } else {
				            logoutState = 0;
				            if (callback_f != null) callback_f();
				        }
				    }
				}
			);
        });

    };

    /***************************************************************************************************/


    function check_islogin() { //JS验证登录状态
        if ($.cookie('epcki') == null || $.cookie('epcki') == '') {
            return false;
        } else {
            var epcki = $.cookie('epcki');
            var userid = 0;
            var tempid = '';
            var ta = epcki.split('{isu;;}');
            for(var i=0;i<=ta.length-1;i++){
                if (ta[i].indexOf('userid{isu::}') >= 0) {
                    tempid = ta[i].split('{isu::}')[1];
                    if(tempid!='') tempid = parseInt(tempid);
                    userid = tempid;
                    break;
                }
            }
            if(userid>0){
                return true;
            }else{
                return false;
            }
        }
    }

    function ajaxlogin_checkform(url, callback_s, callback_f) {
        ljxloginform_callbackOK = callback_s;
        ljxloginform_callbackNO = callback_f;
        if (check_islogin() == true) {
            return true;
        } else {
            if (url != null && url != "") $.cookie('returnurl', url);
            Comment('/ajaxlogin/login_form.asp' + '?r=' + Math.floor(Math.random() * 100000 + 1), 246, 230);
            return false;
        }
    }

    function logincheck_gourl(url) {
        if (check_islogin() == true) {
            return true;
        } else {
        alert('登录失败！');
        return ajaxlogin_checkform(window.top.location.href, function() { window.top.location.href = url; }, function() { alert('登录失败！'); });
        }
    }

    function logincheck_do(callback_s) {
        if (check_islogin() == true) {
            if (callback_s != null) callback_s();
            return true;
        } else {
            return ajaxlogin_checkform(location.href, function() { if (callback_s != null) callback_s(); return true; }, function() { alert('登录失败！'); return false; });
        }
    }

    function ajax_dologin(e_url, e_username, e_password, e_vcode, e_keepon, e_returnurl) {
        var server_url = (e_url == null || e_url == "") ? "/apiLogin/login.do.asp" : e_url;
        var username = (e_username == null) ? "" : escape(e_username);
        var password = (e_password == null) ? "" : escape(e_password);
        var keepon = (e_keepon != "on") ? "on" : e_keepon;
        var vcode = (e_vcode == null) ? 0 : e_vcode;
        /*_____*/
        var oldurl = window.top.location.href;
        var returnurl = '/index.asp';
        var hasckurl = false;
        if (typeof jQuery != 'undefined' && $.cookie) {
            $(document).ready(function() {
                if (e_returnurl != null && e_returnurl != '') { $.cookie('returnurl', e_returnurl); }
                if ($.cookie('returnurl') != null && $.cookie('returnurl') != '') { hasckurl = true; returnurl = $.cookie('returnurl') + ''; }
                $.cookie('returnurl', '');
            });
        }
        ajaxlogin(server_url, username, password, vcode, keepon, function() {
            var stout = setTimeout(function() { //如果8秒还没响应，则强制下一步
                if (hasckurl == true) {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl);
                    }
                }
                else {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl);
                    }
                }
            }, 8000);
            var server_url = "apiLogin/login.do.asp";
            ajaxlogin(server_url, username, password, vcode, keepon, function() {
                if (hasckurl == true) {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl);
                    }
                }
                else {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl);
                    }
                }
            }, function() {
                /*
                if (parent) {
                if (parent.ljxloginform_callbackNO == null || parent.ljxloginform_callbackNO == '') { alert('登录失败！'); }
                else { parent.ljxloginform_callbackNO(); }
                }
                else {
                alert('登录失败！');
                }
                */
                if (hasckurl == true) {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl);
                    }
                }
                else {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl);
                    }
                }
            });
        },
		function() {
		    if (parent) {
		        if (parent.ljxloginform_callbackNO == null || parent.ljxloginform_callbackNO == '') { alert('登录失败！'); }
		        else { parent.ljxloginform_callbackNO(); }
		    }
		    else {
		        alert('登录失败！');
		    }
		});
    }

    function ajax_dologout(s_userid, backurl) {
        var oldurl = window.top.location.href;
        var returnurl = '/index.asp';
        var hasckurl = false;
        if (typeof jQuery != 'undefined' && $.cookie) {
            $(document).ready(function() {
                if (backurl != null && backurl != "") $.cookie('returnurl', backurl);
                if ($.cookie('returnurl') != null && $.cookie('returnurl') != '') { hasckurl = true; returnurl = $.cookie('returnurl') + ''; }
                $.cookie('returnurl', '');
            });
        }
        var server_url = "/apiLogin/login.do.asp";
        var username = escape(s_userid);
        var password = 0;
        var keepon = "on";
        var vcode = 0;
        ajaxlogout(server_url, username, password, vcode, keepon, function() {
            var server_url = "apiLogin/login.do.asp";
            ajaxlogout(server_url, username, password, vcode, keepon, function() {
                if (hasckurl == true) {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl); }
                        else { parent.ljxloginform_callbackNO(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl);
                    }
                }
                else {
                    if (parent) {
                        if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl); }
                        else { parent.ljxloginform_callbackOK(); }
                    }
                    else {
                        location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl);
                    }
                }
            }, function() {
                    /*
                    if (parent) {
                        if (parent.ljxloginform_callbackNO == null || parent.ljxloginform_callbackNO == '') { alert('注销失败！'); }
                        else { parent.ljxloginform_callbackNO(); }
                    }
                    else {
                        alert('注销失败！');
                    }
                    */
                    if (hasckurl == true) {
                        if (parent) {
                            if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl); }
                            else { parent.ljxloginform_callbackNO(); }
                        }
                        else {
                            location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(returnurl);
                        }
                    }
                    else {
                        if (parent) {
                            if (parent.ljxloginform_callbackOK == null || parent.ljxloginform_callbackOK == '') { location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl); }
                            else { parent.ljxloginform_callbackOK(); }
                        }
                        else {
                            location = '/ajaxlogin/gourl.asp?entype=escape&url=' + escape(oldurl);
                        }
                    }
            });
        },
		function() {
		    if (parent) {
		        if (parent.ljxloginform_callbackNO == null || parent.ljxloginform_callbackNO == '') { /*alert('注销失败！');*/ }
		        else { parent.ljxloginform_callbackNO(); }
		    }
		    else {
		        /*alert('注销失败！');*/
		    }
		});
        return false;
    }

    /***************************************************************************************************/

