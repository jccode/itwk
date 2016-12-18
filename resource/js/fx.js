

jQuery.cookie = function(G, H, L) {
    if (typeof H != "undefined") {
        L = L || {};
        if (H === null) {
            H = "";
            L.expires = -1
        }
        var F = "";
        if (L.expires && (typeof L.expires == "number" || L.expires.toUTCString)) {
            var B;
            if (typeof L.expires == "number") {
                B = new Date();
                B.setTime(B.getTime() + (L.expires * 24 * 60 * 60 * 1000))
            } else B = L.expires;
            F = "; expires=" + B.toUTCString()
        }
        var I = L.path ? "; path=" + L.path : "",
            J = L.domain ? "; domain=" + L.domain : "",
            A = L.secure ? "; secure" : "";
        document.cookie = [G, "=", encodeURIComponent(H), F, I, J, A].join("")
    } else {
        var C = null;
        if (document.cookie && document.cookie != "") {
            var K = document.cookie.split(";");
            for (var D = 0; D < K.length; D++) {
                var E = jQuery.trim(K[D]);
                if (E.substring(0, G.length + 1) == (G + "=")) {
                    C = decodeURIComponent(E.substring(G.length + 1));
                    break
                }
            }
        }
        return C
    }
};
jQuery.extend({
    createUploadIframe: function(A, C) {
        var B = "jUploadFrame" + A;
        if (window.ActiveXObject) {
            var D = document.createElement("<iframe id=\"" + B + "\" name=\"" + B + "\" />");
            if (typeof C == "boolean") D.src = "javascript:false";
            else if (typeof C == "string") D.src = C
        } else {
            D = document.createElement("iframe");
            D.id = B;
            D.name = B
        }
        D.style.position = "absolute";
        D.style.top = "-1000px";
        D.style.left = "-1000px";
        document.body.appendChild(D);
        return D
    },
    createUploadForm: function(C, G) {
        var F = "jUploadForm" + C,
            B = "jUploadFile" + C,
            E = $("<form  action=\"\" method=\"POST\" name=\"" + F + "\" id=\"" + F + "\" enctype=\"multipart/form-data\"></form>"),
            D = $("#" + G),
            A = $(D).clone();
        $(D).attr("id", B);
        $(D).before(A);
        $(D).appendTo(E);
        $(E).css("position", "absolute");
        $(E).css("top", "-1200px");
        $(E).css("left", "-1200px");
        $(E).appendTo("body");
        return E
    },
    ajaxFileUpload: function(F) {
        F = jQuery.extend({}, jQuery.ajaxSettings, F);
        var A = new Date().getTime(),
            E = jQuery.createUploadForm(A, F.fileElementId),
            J = jQuery.createUploadIframe(A, F.secureuri),
            C = "jUploadFrame" + A,
            G = "jUploadForm" + A;
        if (F.global && !jQuery.active++) jQuery.event.trigger("ajaxStart");
        var D = false,
            H = {};
        if (F.global) jQuery.event.trigger("ajaxSend", [H, F]);
        var I = function(A) {
            var J = document.getElementById(C);
            try {
                if (J.contentWindow) {
                    H.responseText = J.contentWindow.document.body ? J.contentWindow.document.body.innerHTML : null;
                    H.responseXML = J.contentWindow.document.XMLDocument ? J.contentWindow.document.XMLDocument : J.contentWindow.document
                } else if (J.contentDocument) {
                    H.responseText = J.contentDocument.document.body ? J.contentDocument.document.body.innerHTML : null;
                    H.responseXML = J.contentDocument.document.XMLDocument ? J.contentDocument.document.XMLDocument : J.contentDocument.document
                }
            } catch (B) {
                jQuery.handleError(F, H, null, B)
            }
            if (H || A == "timeout") {
                D = true;
                var G;
                try {
                    G = A != "timeout" ? "success" : "error";
                    if (G != "error") {
                        var I = jQuery.uploadHttpData(H, F.dataType);
                        if (F.success) F.success(I, G);
                        if (F.global) jQuery.event.trigger("ajaxSuccess", [H, F])
                    } else jQuery.handleError(F, H, G)
                } catch (B) {
                    G = "error";
                    jQuery.handleError(F, H, G, B)
                }
                if (F.global) jQuery.event.trigger("ajaxComplete", [H, F]);
                if (F.global && ! --jQuery.active) jQuery.event.trigger("ajaxStop");
                if (F.complete) F.complete(H, G);
                jQuery(J).unbind();
                setTimeout(function() {
                    try {
                        $(J).remove();
                        $(E).remove()
                    } catch (A) {
                        jQuery.handleError(F, H, null, A)
                    }
                }, 100);
                H = null
            }
        };
        if (F.timeout > 0) setTimeout(function() {
            if (!D) I("timeout")
        }, F.timeout);
        try {
            E = $("#" + G);
            $(E).attr("action", F.url);
            $(E).attr("method", "POST");
            $(E).attr("target", C);
            if (E.encoding) E.encoding = "multipart/form-data";
            else E.enctype = "multipart/form-data";
            $(E).submit()
        } catch (B) {
            jQuery.handleError(F, H, null, B)
        }
        if (window.attachEvent) document.getElementById(C).attachEvent("onload", I);
        else document.getElementById(C).addEventListener("load", I, false);
        return {
            abort: function() { }
        }
    },
    uploadHttpData: function(r, type) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        if (type == "script") jQuery.globalEval(data);
        if (type == "json") eval("data = " + data);
        if (type == "html") jQuery("<div>").html(data).evalScripts();
        return data
    }
});
$.setCenter = function(B) {
    if (!B instanceof jQuery) return false;
    var A = {
        left: ((jQuery(window).width() - B.outerWidth()) / 2) + jQuery(window).scrollLeft(),
        top: ((jQuery(window).height() - B.outerHeight()) / 2) + jQuery(window).scrollTop()
    };
    B.css({
        "left": A.left,
        "top": A.top
    });
    return A
};
$.getSwf = function(A) {
    if (!A) return;
    var B = null;
    if (navigator.appName.indexOf("Microsoft") != -1) B = window[A];
    else B = document[A];
    if (typeof (B) == "function" || typeof (B) == "object") return B;
    return null
};

function getRandom() {
    return String(Math.ceil(Math.random() * 100000) + String(new Date().getTime()))
}
function setCaretTo(B, C) {
    if (B.createTextRange) {
        var A = B.createTextRange();
        A.move("character", C);
        A.select()
    } else if (B.selectionStart) {
        B.focus();
        B.setSelectionRange(C, C)
    }
}
function eventSrc() {
    if (window.event) return window.event;
    src = eventSrc.caller;
    while (src) {
        var A = src.arguments[0];
        if (A) {
            if ((A.constructor == Event || A.constructor == MouseEvent) || (typeof (A) == "object" && A.preventDefault && A.stopPropagation)) return A
        } else src = src.caller
    }
    return null
}
function addToFavorite(A, C) {
    if (!A || !C) {
        alert("addToFavorite Error: \u52a0\u5165\u6536\u85cf\u5939\u5fc5\u987b\u586b\u5165\u6b63\u786e\u7684title\u4ee5\u53cahref");
        return
    }
    try {
        if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function")) window.sidebar.addPanel(A, C, "");
        else window.external.AddFavorite(C, A)
    } catch (B) {
        alert("\u52a0\u5165\u6536\u85cf\u5939: \u60a8\u7684\u6d4f\u89c8\u5668\u4e0d\u652f\u6301\u8be5\u64cd\u4f5c,\u8bf7\u624b\u52a8\u590d\u5236\u7f51\u5740\u52a0\u5165\u6536\u85cf\u5939")
    }
}
var copyTextButton = function(B, A) {
    this.fristName = "copyTextSwf_";
    this.container = $("#" + B);
    this.swfObject = null;
    this.swfConfig = {
        key: this.fristName + B,
        url: A || "/skin/1/swf/copyText.swf",
        width: 101,
        height: 16,
        parame: "key=" + B,
        bgColor: "#ffffff",
        info: "\u590d\u5236\u6210\u529f\uff0c\u73b0\u5728\u60a8\u53ef\u4ee5\u4f7f\u7528crtl+v\u7c98\u8d34\u5230\u4efb\u4f55\u4f4d\u7f6e\u3002"
    };
    this.loaded = false;
    this._delay_ = null;
    this._delay_Count = 0;
    this._catch_ = false;
    this._catch_Text = null;
    this.init()
};
copyTextButton.prototype = {
    init: function() {
        this.container.html(this.getHTML());
        this.swfObject = $.getSwf(this.swfConfig.key)
    },
    setInfo: function(A) {
        if (!A) return;
        this.swfConfig.info = A
    },
    getHTML: function() {
        var A = "<object id=\"" + this.swfConfig.key + "\" width=\"" + this.swfConfig.width + "\" height=\"" + this.swfConfig.height + "\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\">";
        A += "<param value=\"" + this.swfConfig.url + "\" name=\"movie\">";
        A += "<param value=\"always\" name=\"allowScriptAccess\">";
        A += "<param value=\"high\" name=\"quality\">";
        A += "<param value=\"noscale\" name=\"scale\">";
        A += "<param value=\"" + this.swfConfig.parame + "\" name=\"FlashVars\">";
        A += "<param value=\"" + this.swfConfig.bgColor + "\" name=\"bgcolor\">";
        A += "<param value=\"opaque\" name=\"wmode\">";
        A += "<embed name=\"" + this.swfConfig.key + "\"  height=\"" + this.swfConfig.height + "\" width=\"" + this.swfConfig.width + "\" wmode=\"opaque\" bgcolor=\"" + this.swfConfig.bgColor + "\" flashvars=\"" + this.swfConfig.parame + "\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" quality=\"high\" src=\"" + this.swfConfig.url + "\">";
        A += "</object>";
        return A
    },
    setText: function(A) {
        if (this._delay_Count >= 10000000 || !A) return false;
        var C = this;
        if (!this.loaded) {
            this._delay_Count++;
            this._delay_ = window.setTimeout(function() {
                clearTimeout(C._delay_);
                C.setText(A);
                C._delay_ = null;
                C = null
            }, 1500);
            return
        }
        try {
            this.swfObject.setCopyText(A)
        } catch (B) {
            this._catch_ = true;
            this._catch_Text = A
        }
    },
    setLoaded: function() {
        this.loaded = true
    },
    showComplete: function() {
        if (this._catch_) {
            at_infoTrace.show("\u590d\u5236\u5931\u8d25\uff0c\u60a8\u7684\u6d4f\u89c8\u5668\u5b58\u5728\u5f02\u5e38\uff08\u53ef\u80fd\u662f\u63d2\u4ef6\u5f15\u8d77\uff09\uff0c\u8bf7\u624b\u52a8\u590d\u5236\u5982\u4e0b\u5185\u5bb9\uff1a<br>" + this._catch_Text, "i", null, null, true);
            return
        }
        at_infoTrace.show(this.swfConfig.info, "o", null, null, true)
    }
};

function mouse() {
    var A = eventSrc();
    if (!A.pageX) {
        x = A.clientX + document.documentElement.scrollLeft;
        y = A.clientY + document.documentElement.scrollTop
    } else {
        x = A.pageX;
        y = A.pageY
    }
    return {
        "x": x,
        "y": y
    }
}

function at_userData() {
    var A = $.cookie("userid");
    if (A == null || !A) return null;
    else return {
        regtime: $.cookie("userregtime"),
        key: $.cookie("userkey"),
        nickname: $.cookie("nickname"),
        id: A
    }
}
var at_loading = function() {
    this.html = "<div id=\"at_loading\" class=\"at_loading\"><s></s><div class=\"at_loading_c\">\u6b63\u5728\u5904\u7406\u8bf7\u6c42\uff0c\u8bf7\u7b49\u5019...</div></div>";
    $("body").append(html);
    at_loading.loading = $("#at_loading")
};
at_loading.loading = null;
at_loading.has = false;
at_loading.show = function(A) {
    if (at_loading.loading == null) at_loading();
    $.setCenter(at_loading.loading);
    at_loading.loading.css({
        "visibility": "visible",
        "opacity": 0
    });
    at_loading.loading.animate({
        "opacity": 1
    }, 100);
    at_loading.has = true
};
at_loading.hide = function() {
    if (at_loading.loading == null) return false;
    at_loading.loading.animate({
        "opacity": 0
    }, 100, null, function() {
        at_loading.loading.css("visibility", "hidden")
    });
    at_loading.has = false
};
var at_slipTips = function() {
    this.html = "<div id=\"at_slipTips\" class=\"at_loading\"><s></s><div class=\"at_loading_b\"></div></div>";
    $("body").append(html);
    at_slipTips.o.box = $("#at_slipTips");
    at_slipTips.o.txt = at_slipTips.o.box.children("div")
};
at_slipTips.o = {
    box: null,
    txt: null
};
at_slipTips.pause = null;
at_slipTips.show = function(C, B, A) {
    if (!C || !B) return false;
    if (at_slipTips.o.box == null) at_slipTips();
    var D = $.setCenter(at_slipTips.o.box).top;
    if (at_slipTips.pause != null) window.clearTimeout(at_slipTips.pause);
    at_slipTips.o.box.stop();
    at_slipTips.o.box.css({
        visibility: "visible",
        opacity: 0,
        top: D + 100
    });
    if (A) at_slipTips.o.txt.attr("class", "at_loading_b at_l_ys");
    else at_slipTips.o.txt.attr("class", "at_loading at_l_nt");
    at_slipTips.o.txt.html(C + "&nbsp;<strong>" + B + "</strong><p class=\"subinfo\">" + A + "</p>");
    at_slipTips.o.box.animate({
        opacity: 1,
        top: D
    }, 300, function() {
        at_slipTips.pause = window.setTimeout(function() {
            window.clearTimeout(at_slipTips.pause);
            at_slipTips.pause = null;
            at_slipTips.o.box.animate({
                opacity: 0,
                top: D - 100
            }, 300, function() {
                at_slipTips.o.box.css("visibility", "hidden")
            })
        }, 2000)
    })
};
var at_fbox = function() {
    this.keyId = "at_fbox_" + getRandom();
    this.window = jQuery(window);
    this.body = jQuery("body:eq(0)");
    this.container = "<div id=\"" + this.keyId + "\" class=\"at_fbox\"><div class=\"at_fbox_t\"><span>\u6807\u9898</span><a href=\"javascript:void()\"></a></div><div class=\"at_fbox_c\"></div></div>";
    this.c = {
        bottomMargin: 3,
        rightMargin: 3,
        allPadding: 1,
        width: 260,
        height: 200,
        titleHeight: 0,
        availWidth: this.window.width(),
        availHeight: this.window.height(),
        scrollLeft: this.window.scrollLeft(),
        scrollTop: this.window.scrollTop(),
        display: "hide",
        showTop: 0,
        hideTop: 0,
        alignMode: "right"
    };
    this.title = null;
    this.close = null;
    this.content = null;
    this.body.append(this.container);
    this.eventOutTime = null;
    this.eventHasBind = false;
    var A = this;
    this.eventOutTime = window.setTimeout(function() {
        window.clearTimeout(A.eventOutTime);
        A.eventOutTime = null;
        A.init()
    }, 1000)
};
at_fbox.prototype = {
    init: function() {
        this.container = $("#" + this.keyId);
        var A = this.container.children(".at_fbox_t");
        this.title = A.find("span");
        this.close = A.find("a");
        this.content = this.container.children(".at_fbox_c");
        this.content.css("marginLeft", this.c.allPadding);
        this.c.titleHeight = this.title.parent().outerHeight();
        this.setSize(this.c.width, this.c.height);
        this.setPosition()
    },
    hasShowPig: function(C) {
        if (this.eventOutTime != null) {
            var B = this,
                A = window.setTimeout(function() {
                    window.clearTimeout(A);
                    B.hasShowPig(C)
                }, 300);
            return
        }
        if (C) this.container.prepend("<div class=\"at_fbox_p\"></div>");
        else this.container.children(".at_fbox_p").remove()
    },
    bindEvent: function(E) {
        var F = this;
        if (!this.eventHasBind) {
            this.close.bind("click", D);
            this.window.bind("resize", A);
            this.eventHasBind = true
        }
        try {
            if ($.browser.version == "6.0") if (E == "show") this.window.bind("scroll", G);
            else this.window.unbind("scroll", C)
        } catch (B) { }
        function G() {
            F.setPosition(true)
        }
        function D() {
            F.setDisplay("hide");
            return false
        }
        function A() {
            F.c.availWidth = F.window.width();
            F.c.availHeight = F.window.height();
            F.setPosition()
        }
    },
    setPosition: function(B) {
        var A = (B) ? 0 : 0,
            C = null;
        this.c.scrollLeft = ($.browser.version == "6.0") ? this.window.scrollLeft() : 0;
        this.c.scrollTop = ($.browser.version == "6.0") ? this.window.scrollTop() : 0;
        this.c.hideTop = (this.c.availHeight - this.c.height) + this.c.scrollTop + this.c.height - this.c.bottomMargin + 60;
        this.c.showTop = (this.c.availHeight - this.c.height) + this.c.scrollTop - this.c.bottomMargin;
        if (this.c.alignMode == "left") C = this.c.rightMargin + this.c.scrollLeft + ((! -[1, ] && !window.XMLHttpRequest) ? 2 : 0);
        else if (this.c.alignMode == "right") C = this.c.availWidth - this.c.width - this.c.rightMargin + this.c.scrollLeft;
        this.container.css({
            top: (this.c.display == "hide") ? this.c.hideTop : this.c.showTop,
            left: C
        })
    },
    setSize: function(C, B) {
        if (this.eventOutTime != null) {
            var D = this,
                A = window.setTimeout(function() {
                    window.clearTimeout(A);
                    D.setSize(C, B)
                }, 300);
            return
        }
        if (C) {
            this.c.width = C;
            this.container.width(C);
            this.content.width(this.c.width - (this.c.allPadding * 2))
        } else this.container.width(this.c.width);
        if (B) {
            this.c.height = B;
            this.container.height(B);
            this.content.height(this.c.height - this.c.titleHeight - this.c.allPadding)
        } else this.container.height(this.c.height);
        if (C || B) this.setPosition()
    },
    setContent: function(C, B) {
        if (this.eventOutTime != null) {
            var D = this,
                A = window.setTimeout(function() {
                    window.clearTimeout(A);
                    D.setContent(C, B)
                }, 300);
            return
        }
        if (C) this.title.html(C);
        if (B) {
            this.content.html(B);
            if (B instanceof jQuery) B.css("display", "block")
        }
    },
    setAlign: function(C) {
        if (this.eventOutTime != null) {
            var B = this,
                A = window.setTimeout(function() {
                    window.clearTimeout(A);
                    B.setAlign(C)
                }, 300);
            return
        }
        switch (C) {
            case "left":
                this.c.alignMode = C;
                break;
            case "right":
                this.c.alignMode = C;
                break;
            default:
                this.c.alignMode = "right";
                break
        }
        this.setPosition()
    },
    setDisplay: function(C, D) {
        if (C == this.c.display) return;
        var B = this;
        if (this.eventOutTime != null) {
            var A = window.setTimeout(function() {
                window.clearTimeout(A);
                B.setDisplay(C, D)
            }, 300);
            return
        }
        if (D) {
            A = window.setTimeout(function() {
                window.clearTimeout(A);
                B.setDisplay(C)
            }, D);
            return
        } else switch (C) {
            case "show":
                this.bindEvent("show");
                this.container.css("visibility", "visible");
                this.container.animate({
                    top: this.c.showTop
                }, null, function() {
                    B.c.display = "show";
                    try {
                        B.onShow()
                    } catch (A) { }
                });
                break;
            case "hide":
                this.bindEvent("hide");
                this.container.animate({
                    top: this.c.hideTop
                }, null, function() {
                    B.c.display = "hide";
                    B.container.css("visibility", "hidden");
                    try {
                        B.onHide()
                    } catch (A) { }
                });
                break
        }
    },
    getThis: function() {
        return this.content
    }
};
var at_tips = function() {
    this.body = jQuery("body:eq(0)");
    this.container = "<div id=\"at_temp_tipsCreateId\" class=\"at_tips\"><span class=\"at_tips_p\"></span><p class=\"at_tips_t\"><a href=\"javascript:void(0)\" class=\"at_tips_b\"></a><a href=\"javascript:void(0)\" class=\"at_tips_g\"></a></p><div class=\"at_tips_c\"></div><div class=\"at_tips_s\"></div></div>";
    this.close = null;
    this.edits = null;
    this.arrow = null;
    this.contact = null;
    this.shadow = null;
    this.body.append(this.container);
    this.init()
};
at_tips.prototype = {
    init: function() {
        this.container = $("#at_temp_tipsCreateId");
        this.container.removeAttr("id");
        var A = this.container.find("a");
        this.edits = A.filter(".at_tips_b");
        this.close = A.filter(".at_tips_g");
        this.arrow = this.container.find("span.at_tips_p");
        this.contact = this.container.find("div.at_tips_c");
        this.shadow = this.container.find("div.at_tips_s");
        this.resetShadow();
        this.bindEvent()
    },
    bindEvent: function() {
        var A = this;
        this.close.click(function() {
            A.setDisplay("hide");
            return false
        })
    },
    setDisplay: function(B) {
        if (B == "show") {
            this.container.css("visibility", "visible");
            try {
                this.onHide()
            } catch (A) { }
        } else if (B == "hide") {
            this.container.css("visibility", "hidden");
            try {
                this.onHide()
            } catch (A) { }
        }
    },
    setWidth: function(A) {
        if (typeof A == "number") {
            this.container.width(A);
            this.contact.width(A - (parseInt(this.contact.css("margin-left").replace(/[^0-9]/g, "")) * 2));
            this.resetShadow()
        }
    },
    setPosition: function(B, A) {
        if (typeof B == "number") this.container.css("left", B);
        if (typeof A == "number") this.container.css("top", A)
    },
    setArrowAlign: function(A) {
        var B = "15%";
        if (A == "left") B = "15%";
        if (A == "center") B = "46%";
        if (A == "right") B = "75%";
        this.arrow.css("left", B)
    },
    setContact: function(A) {
        if (typeof (A) == "string" || A == "") this.contact.html(A);
        else if (A.attr("tagName") != "undefined" || A.attr("tagName") != null || !A.attr("tagName")) this.contact.append(A);
        this.resetShadow()
    },
    setToolsDisplay: function(D, B, C) {
        var A;
        if (D == "edit") A = this.edits;
        else if (D == "close") A = this.close;
        else return;
        if (B == "show") A.css("display", "inline-block");
        else if (B == "hide") A.css("display", "none");
        if (C instanceof Function) A.bind("click", C)
    },
    append: function(A) {
        if (!A || A.length != 1) return;
        A.append(this.container)
    },
    resetShadow: function() {
        this.shadow.width(this.container.width());
        this.shadow.height(this.container.height())
    }
};
var at_tipsUse = function(C, B, A, E, D) {
    if (at_tipsUse.hasInit == false) {
        at_tipsUse.i = new at_tips();
        at_tipsUse.hasInit = true
    }
    if (C) at_tipsUse.i.append(C);
    if (B) at_tipsUse.i.setContact(B);
    if (A) at_tipsUse.i.setWidth(A);
    if (E && E instanceof Array && E.length == 2) at_tipsUse.i.setPosition(E[0], E[1]);
    if (D) at_tipsUse.i.setArrowAlign(D)
};
at_tipsUse.i = null;
at_tipsUse.hasInit = false;
at_tipsUse.show = function(C, B, A, E, D) {
    at_tipsUse(C, B, A, E, D);
    at_tipsUse.i.setDisplay("show")
};
at_tipsUse.hide = function() {
    at_tipsUse.i.setDisplay("hide")
};
var at_fboxUse = function() {
    if (at_fboxUse.i == null) at_fboxUse.i = new at_fbox()
};
at_fboxUse.i = null;
at_fboxUse.show = function(C, D, A, G, F, B, E) {
    at_fboxUse();
    at_fboxUse.i.setContent(C, D);
    at_fboxUse.i.setSize(A, G);
    at_fboxUse.i.hasShowPig(F);
    at_fboxUse.i.setDisplay("show", B);
    at_fboxUse.i.setAlign(E)
};
at_fboxUse.hide = function(A) {
    at_fboxUse.i.setDisplay("hide", A)
};
var traceList = [],
    at_trace = function() {
        traceList.push(this);
        var A = at_trace.getKey();
        this.randomId = A.key;
        this.guidIndex = A.index;
        this.body = jQuery("body:eq(0)");
        this.container = "<div id=\"at_trace_w" + this.randomId + "\" class=\"at_trace_w\"><div class=\"at_trace_c\"><div class=\"at_trace_t\"><a class=\"at_trace_s\" href=\"javascript:void(0)\"></a><h2>\u6807\u9898</h2></div><div class=\"at_trace_cont\"></div><div class=\"at_trace_b\"></div></div></div>";
        if (at_trace.mask == null) {
            at_trace.mask = "<div id=\"at_trace_m\" class=\"at_trace_m\">" + (($.browser.version == "6.0") ? "<iframe></iframe>" : "") + "</div>";
            this.trace = at_trace.mask + this.container
        } else this.trace = this.container;
        this.body.prepend(this.trace);
        this.title = null;
        this.contact = null;
        this.buttons = null;
        this.close = null;
        this.width = 200;
        this.init()
    };
at_trace.mask = null;
at_trace.maskDisplay = function(A) {
    if (A == "hide") {
        for (var B = 0; B < at_trace.guid.length; B++) if (at_trace.guid[B].display == "show") return;
        at_trace.mask.animate({
            "opacity": 0
        }, 150, null, function() {
            at_trace.mask.css("visibility", "hidden")
        })
    } else if (A == "show") {
        if (at_trace.mask.css("visibility") == "visible") return;
        at_trace.mask.css({
            "visibility": "visible",
            "opacity": 0
        });
        at_trace.mask.animate({
            "opacity": 0.5
        }, 150)
    }
};
at_trace.guid = [];
at_trace.getKey = function() {
    var B = getRandom(),
        A = at_trace.guid.length;
    while (at_trace.checkKey(B)) B = getRandom();
    at_trace.guid[A] = {
        key: B,
        display: "hide"
    };
    return {
        key: B,
        index: A
    }
};
at_trace.checkKey = function(A) {
    for (var B = 0; B < at_trace.guid.length; B++) if (at_trace.guid[B].key == A) return true;
    return false
};
at_trace.prototype = {
    init: function() {
        var D, C;
        at_trace.mask = this.body.find("#at_trace_m");
        this.trace = this.body.find("#at_trace_w" + this.randomId);
        D = this.trace.children(".at_trace_c");
        C = D.children(".at_trace_t");
        this.title = C.children("h2");
        this.close = C.children("a.at_trace_s");
        this.contact = D.children("div.at_trace_cont");
        this.buttons = D.children("div.at_trace_b");
        var B = this,
            A;
        this.close.click(function() {
            B.setDisplay("hide");
            return false
        });
        if (at_trace.guid.length == 1) this.initMaskStyle();
        $(window).bind("scroll", function() {
            B.setCenter()
        }).resize(function() {
            B.initMaskStyle();
            B.setCenter()
        })
    },
    initMaskStyle: function() {
        var C = jQuery(window),
            B = (this.body.width() < C.width()) ? C.width() : this.body.width();
        at_trace.mask.css("display", "none");
        var A = C.height();
        at_trace.mask.css("display", "block");
        at_trace.mask.width(B);
        at_trace.mask.height(A);
        at_trace.mask.css("opacity", 0.5)
    },
    setCenter: function() {
        var D = jQuery(window),
            B = ((this.body.width() - this.trace.outerWidth()) / 2) + D.scrollLeft(),
            C = ((D.height() - this.trace.outerHeight()) / 2) + D.scrollTop(),
            A = D.scrollTop();
        this.trace.css({
            "left": B,
            "top": C
        });
        at_trace.mask.css("top", A)
    },
    setDisplay: function(B) {
        var A = this;
        if (B == "hide" || B == "show") {
            at_trace.mask.stop(false, true);
            this.trace.stop(false, true);
            at_trace.guid[this.guidIndex].display = B;
            at_trace.maskDisplay(B);
            if (B == "hide") this.trace.animate({
                "opacity": 0
            }, 150, null, function() {
                $(this).css("visibility", "hidden");
                try {
                    A.onHide()
                } catch (B) { }
            });
            else if (B == "show") {
                this.setCenter();
                this.trace.css({
                    "visibility": "visible",
                    "opacity": 0
                });
                this.trace.animate({
                    opacity: 1
                }, 150, function() {
                    try {
                        A.onShow()
                    } catch (B) { }
                })
            }
        }
    },
    setContact: function(B, E, C) {
        var A = "",
            D = "";
        if (B) this.title.html(B);
        if (E) this.contact.html(E);
        if (C) {
            for (var F = 0; F < C.length; F++) A += C[F].html + (((F + 1) == C.length) ? "" : " ");
            this.buttons.html(A);
            this.bindEvent(C)
        }
        this.setCenter()
    },
    setTitle: function(A) {
        if (A) this.title.html(A)
    },
    appendContact: function(A) {
        this.contact.html("");
        this.contact.append(A);
        this.setCenter()
    },
    bindEvent: function(B) {
        var C = this.buttons.find("a"),
            A, D = this;
        C.click(function() {
            A = C.index(this);
            if (B[A].callBack == "close") D.setDisplay("hide");
            else B[A].callBack();
            return false
        })
    },
    setWidth: function(A) {
        this.trace.find("div.at_trace_c").width(A);
        this.setCenter()
    },
    getButton: function(A, B, D) {
        if (!A || !B) return null;
        if (B == "blue" || B == "yellow" || B == "gray") var B = /^.?/.exec(B),
            C = "<a href=\"javascript:void(0)\" class=\"at_but b_2_" + B + "\"><u></u>" + A + "</a>";
        else if (B == "href") C = "<a href=\"javascript:void(0)\" class=\"at_txthref\">" + A + "</a>";
        return {
            "html": C,
            "callBack": D
        }
    },
    setDefaultCloseDisplay: function(B) {
        var A = this.getThis().find("a.at_trace_s");
        if (B == "hide") A.hide();
        else A.show()
    },
    setDefaultButtonDisplay: function(A) {
        if (A == "hide") this.buttons.hide();
        else this.buttons.show();
        this.setCenter()
    },
    getThis: function() {
        return $("#at_trace_w" + this.randomId)
    }
};
var at_traces_i = at_trace.prototype,
    at_infoTrace = function() {
        at_traces_i = new at_trace();
        at_traces_i.setWidth(350);
        at_traces_i.setContact("\u7cfb\u7edf\u63d0\u793a", null, [at_traces_i.getButton("\u786e\u5b9a", "gray", "close")]);
        at_traces_i.getThis().addClass("at_trace_w_i");
        at_traces_i.onShow = function() {
            try {
                at_infoTrace.onShow()
            } catch (A) { }
        };
        at_traces_i.onHide = function() {
            try {
                at_infoTrace.onHide()
            } catch (A) { }
        }
    };
at_infoTrace._timer = null;
at_infoTrace.hasInit = false;
at_infoTrace.show = function(C, E, B, A, D) {
    if (at_infoTrace.hasInit == false) {
        at_infoTrace();
        at_infoTrace.hasInit = true
    }
    var E = E || "i";
    at_traces_i.setContact(null, "<div class=\"at_trace_if clearfix\"><u class=\"at_msg m_1_" + E + "\"></u><p class=\"at_trace_if_c " + (D ? "" : "m10") + " ml15\">" + C + "</p></div><div class=\"clearabs\"></div>", ((A) ? A : [at_traces_i.getButton("\u786e\u5b9a", "gray", "close")]));
    at_traces_i.setDisplay("show");
    if (B && /^[0-9]*$/.test(B)) at_infoTrace._timer = window.setTimeout(function() {
        window.clearTimeout(at_infoTrace._timer);
        at_infoTrace.hide()
    }, B)
};
at_infoTrace.hide = function() {
    at_traces_i.setDisplay("hide");
    window.clearTimeout(at_infoTrace._timer)
};
var at_star = function(A) {
    if (!A instanceof jQuery) return;
    this.item = A;
    this.init();
    this.bindEvent()
};
at_star.prototype = {
    init: function() {
        var A, C = this,
            B;
        this.item.each(function() {
            A = $(this);
            B = A.find("s");
            if (B.length < 5) {
                B.remove();
                for (var D = 0; D < 5; D++) A.append("<s title=\"" + D + "\u661f\"></s>")
            }
            C.setDefault(A);
            C.bindEvent(A)
        })
    },
    bindEvent: function(A) {
        if (!A) return;
        var D, B, C = this;
        D = A.children("*");
        D.mouseover(function() {
            B = D.index(this);
            C.setHover(D, B)
        }).click(function() {
            B = D.index(this) + 1;
            try {
                C.onSelected(A, B)
            } catch (E) { }
        });
        A.mouseleave(function() {
            C.setDefault(A)
        })
    },
    setHover: function(C, A) {
        var B;
        C.each(function() {
            B = C.index(this);
            if (B < A + 1) C.eq(B).addClass("at_selected");
            else C.eq(B).removeClass("at_selected")
        })
    },
    setDefault: function(A) {
        var C = A.children("*"),
            B = A.attr("starNumber");
        if (B) {
            C.removeClass("at_selected");
            C.filter("*:lt(" + B + ")").addClass("at_selected")
        } else A.attr("starNumber", 0)
    }
};
var at_select = function(A) {
    if (!A) return;
    this.Menu = $("#" + A);
    if (this.Menu.length != 1) return;
    this.Item = this.Menu.find("a");
    this.Selected = this.Item.filter(".at_selected");
    this.Item = this.Item.not(".at_selected");
    this.Value = this.Menu.find("input");
    this.timer = false;
    this.bindEvent()
};
at_select.prototype = {
    bindEvent: function() {
        var B, A = this;
        this.Menu.hover(function() {
            if (A.timer) return;
            A.timer = window.setTimeout(function() {
                A.setDisplay("show")
            }, 200)
        }, function() {
            window.clearTimeout(A.timer);
            A.timer = false;
            A.setDisplay("hide")
        });
        this.Item.click(function() {
            A.setSelected($(this));
            A.setDisplay("hide")
        })
    },
    setDisplay: function(A) {
        if (A == "show") this.Item.css("display", "block");
        if (A == "hide") this.Item.css("display", "none")
    },
    setSelected: function(B) {
        var C = B.attr("value");
        this.Selected.html(B.html());
        this.Value.val(C);
        try {
            this.onSelected(C)
        } catch (A) { }
    }
};
var at_scroll = function(A, B, C) {
    if (!A || !B || !(B == "x" || B == "y") || (C > 10000 || C < 0)) return;
    this.dir = B;
    this.con = $("#" + A);
    this.ani = this.con.children("div.at_scroll_c");
    if (this.con.length != 1) return;
    this.tem = this.ani.children("*");
    this.copyTem = null;
    this.continueState = true;
    this.config = {
        count: 0,
        showPx: 0,
        pagePx: 0,
        itemPx: 0,
        index: 0,
        nowPx: 0,
        casePx: 0
    };
    this.timer = null;
    this.mouseState = false;
    this.state = false;
    this.timeout = parseInt(C || 2000);
    this.init()
};
at_scroll.prototype = {
    init: function() {
        this.config.count = this.tem.length;
        this.config.showPx = (this.dir == "y") ? this.con.height() : this.con.width();
        this.config.itemPx = (this.dir == "y") ? this.tem.eq(0).outerHeight() : this.tem.eq(0).outerWidth();
        this.config.pagePx = this.config.itemPx * this.config.count;
        if (this.dir == "y") {
            this.con.scrollTop(0);
            this.ani.height(this.config.pagePx * 2)
        } else {
            this.con.scrollLeft(0);
            this.ani.width(this.config.pagePx * 2)
        }
        this.copyTem = this.tem.clone(true);
        this.ani.append(this.copyTem);
        this.config.casePx = this.config.pagePx - ((this.timeout > 149) ? this.config.itemPx : 1);
        if (this.config.pagePx <= this.config.showPx) return;
        this.bindEvent();
        this.auto()
    },
    bindEvent: function() {
        var A = this;
        A.con.bind("mouseenter", function() {
            window.clearTimeout(A.timer);
            A.timer = false;
            A.mouseState = true
        });
        A.con.bind("mouseleave", function() {
            A.auto();
            A.mouseState = false
        })
    },
    auto: function(A) {
        var B = this,
            C;
        if (this.state == true) return;
        C = (A) ? 0 : this.timeout;
        this.timer = window.setTimeout(function() {
            window.clearTimeout(B.timer);
            B.timer = false;
            B.move()
        }, C)
    },
    move: function() {
        var A = false;
        if (this.config.nowPx == this.config.casePx) A = true;
        this.animate(A)
    },
    animate: function(toFrist) {
        var me = this;
        this.state = true;
        if (this.timeout > 149) {
            this.config.index++;
            this.config.nowPx = this.config.itemPx * this.config.index;
            this.con.stop();
            eval("this.con.animate({\"" + ((this.dir == "y") ? "scrollTop" : "scrollLeft") + "\": this.config.nowPx}, 800, null, complete)")
        } else {
            this.config.nowPx++;
            if (me.dir == "y") me.con.scrollTop(this.config.nowPx);
            else me.con.scrollLeft(this.config.nowPx);
            complete()
        }
        function complete() {
            me.state = false;
            if (me.mouseState == false) me.auto();
            if (toFrist != true) return;
            if (me.dir == "y") me.con.scrollTop(0);
            else me.con.scrollLeft(0);
            me.config.index = 0;
            me.config.nowPx = 0
        }
    }
};
var at_tab = function(A, C, B) {
    if (!A) return;
    this.con = $("#" + A);
    this.tag = this.con.children(".at_tab_t").children(".at_tab_i");
    this.lay = this.con.children(".at_tab_c").children(".at_tab_l");
    if (this.tag.length != this.lay.length) return;
    this.oldIndex = null;
    if (C) {
        this.eventMode = "click";
        this.ajaxMode = true
    } else {
        this.eventMode = "mouseover";
        this.ajaxMode = false
    }
    this.timer = null;
    this.outime = (this.ajaxMode) ? 0 : 270;
    this.loadState = false;
    this.init()
};
at_tab.prototype = {
    init: function() {
        var C = this,
            A = true,
            B = 0;
        this.tag.each(function() {
            if ($(this).hasClass("at_current")) {
                C.oldIndex = C.tag.index(this);
                C.lay.eq(C.oldIndex).css("display", "block")
            }
        });
        if (this.ajaxMode) {
            this.tag.each(function() {
                if (!$(this).attr("ajaxsrc")) B++
            });
            if (B != this.tag.length) {
                this.tag.each(function() {
                    if (!$(this).attr("ajaxsrc")) {
                        alert("at_tab Error: \u5f53\u524d\u5df2\u5f00\u542fajax\u6a21\u5f0f,\u540d\u4e3a\"" + $(this).text() + "\"\u7684\u9009\u9879\u5361\u6807\u7b7e\u5fc5\u987b\u5e26\u6709ajaxsrc\u5c5e\u6027\u6765\u63cf\u8ff0\u8bf7\u6c42\u5730\u5740");
                        A = false
                    }
                });
                if (A == false) return
            } else this.ajaxMode = false
        }
        this.bindEvent()
    },
    bindEvent: function() {
        var B = this,
            A;
        this.tag.bind(this.eventMode, function() {
            if (B.ajaxMode) $(this).unbind("mouseout");
            B.switchTab(B.tag.index(this))
        });
        this.tag.bind("mouseout", function() {
            B.loadState = false;
            window.clearTimeout(B.timer);
            B.timer = null
        })
    },
    switchTab: function(B) {
        if (this.loadState != false) return;
        this.loadState = true;
        var D = this,
            C, A, E;
        if (B == D.oldIndex || D.timer != null) {
            this.loadState = false;
            return
        }
        this.timer = window.setTimeout(function() {
            C = D.tag.eq(B);
            A = D.lay.eq(B);
            if (D.ajaxMode == true) {
                F();
                E = C.html();
                A.html("<u class=\"at_write m10\"></u>");
                $.ajax({
                    url: C.attr("ajaxsrc"),
                    success: function(B) {
                        A.html("");
                        if (B.state == 1) {
                            A.html(B.msg);
                            C.attr("loaded", "y");
                            G()
                        } else {
                            at_infoTrace.show(B.msg, "i");
                            D.loadState = false
                        }
                    }
                })
            } else {
                F();
                G()
            }
            function F() {
                D.tag.eq(D.oldIndex).removeClass("at_current");
                D.lay.eq(D.oldIndex).css("display", "none");
                C.addClass("at_current");
                A.css("display", "block")
            }
            function G() {
                D.oldIndex = B;
                window.clearTimeout(D.timer);
                D.timer = null;
                D.loadState = false;
                try {
                    D.onLoaded(B, A)
                } catch (C) { }
            }
        }, this.outime)
    },
    getSelected: function() {
        return this.oldIndex
    }
};
var at_imgSwitch = function(A, E, B) {
    if (!A) return;
    this.container = $("#" + A);
    this.container.css("visibility", "hidden");
    this.cons = this.container.children("ul.at_imgSwitch_c");
    this.team = this.cons.find("li:eq(0)");
    this.page = null;
    this.config = {
        "totalWidth": 0,
        "itemWidth": 0,
        "count": 0,
        "index": 0
    };
    this.timer = null;
    this.mode = E ? true : false;
    this._time = (this.mode && /^[0-9]*$/.test(B)) ? B : 5000;
    var F = this,
            C = (($.browser.safari) ? 500 : 0),
            D = window.setTimeout(function() {
                window.clearTimeout(D);
                F.container.css("visibility", "visible");
                F.init()
            }, C)
};
at_imgSwitch.prototype = {
    init: function() {
        var A = this.cons.find("li");
        A.css({
            width: this.container.width(),
            height: this.container.height()
        });
        this.config.itemWidth = this.team.outerWidth();
        this.config.count = A.length;
        this.config.totalWidth = this.config.itemWidth * this.config.count;
        this.cons.width(this.config.totalWidth);
        this.page = this.createPage();
        this.cons.scrollLeft(0);
        this.setPlaces();
        this.bindEvent();
        var B = this;
        if (this.mode) this.autoStart()
    },
    createPage: function() {
        var A = "<ul class=\"at_imgSwitch_p\">";
        for (var B = this.config.count; B > 0; B--) A += "<li" + ((B - 1 == this.config.index) ? (" class=\"at_current\"") : "") + ">" + (B) + "</li>";
        A += "</ul>";
        this.cons.after(A).find("li");
        return this.container.find("ul.at_imgSwitch_p li")
    },
    setPlaces: function() {
        var A, C = 0,
            B = this;
        A = this.page.parent();
        this.page.each(function() {
            C += $(this).outerWidth()
        });
        A.css({
            width: C,
            top: this.container.outerHeight() - A.outerHeight() - 12,
            left: this.container.outerWidth() - C - 12
        })
    },
    bindEvent: function() {
        var B = this,
            A;
        this.page.click(function() {
            A = ((B.config.count - 1) - B.page.index(this));
            B.changeIndex(A)
        })
    },
    changeIndex: function(A) {
        var B = this;
        window.clearTimeout(this.timer);
        this.config.index = A;
        this.page.removeClass("at_current");
        this.page.eq(((this.config.count - 1) - this.config.index)).addClass("at_current");
        this.cons.stop();
        this.cons.animate({
            "left": -(this.config.index * this.config.itemWidth)
        }, 400, null, function() {
            try {
                B.onSwitch(A)
            } catch (C) { }
            if (B.mode) B.autoStart()
        })
    },
    autoStart: function() {
        var B = this,
            A;
        this.timer = window.setTimeout(function() {
            if (B.config.index + 1 < B.config.count) A = B.config.index + 1;
            else A = 0;
            B.changeIndex(A)
        }, this._time)
    }
};


function at_loginNeed(A, C, B, D) {
    at_infoTrace.show("\u60a8\u8fd8\u6ca1\u6709\u767b\u9646\uff0c\u4e0d\u80fd\u8fdb\u884c\u6b64\u64cd\u4f5c\u3002<br>\u662f\u5426\u7acb\u5373\u767b\u9646?", "i", null, [at_traces_i.getButton("\u767b\u9646", "yellow", function() {
        at_loginUse.open(A, C, B, D);
        at_infoTrace.hide();
        return false
    }), at_traces_i.getButton("\u53d6\u6d88", "gray", "close")], true)
}
function at_userInfo(H) {
    if (!H) return;
    var E = F(),
        B = D(),
        C = A();

    function F() {
        var A = "#userinfo-",
            B = 0,
            C = [],
            D = new Array();
        while (C.length == 1 || B == 0) {
            B++;
            C = $(A + B);
            if (C.length == 1) D.push(C);
            else break
        }
        return D
    }
    function A() {
        if (/\?/.test(H)) return H + "&jsoncallback=?";
        else return H + "?jsoncallback=?"
    }
    function D() {
        var A = "";
        for (var B = 0; B < E.length; B++) A += E[B].attr("uid") + ",";
        return A.replace(/\,$/, "")
    }
    function G(A) {
        for (var B = 0; B < E.length; B++) E[B].html(A[B])
    }
    $.getJSON(C, {
        uid: B
    }, function(A) {
        if (A.state == 1) G(A.msg)
    })
}
var at_sms = function(C) {
    if (at_userData() == null) {
        function B() {
            at_sms(C)
        }
        at_loginNeed(B, B);
        return
    }
    if (at_sms.t == null) {
        var D = "<div id=\"at_sms\" class=\"at_tol_pub\"><p class=\"sms_title\">\u6807\u3000\u9898\uff1a<input value=\"\u8f93\u5165\u7ad9\u5185\u4fe1\u6807\u9898\" maxlength=\"22\"/></p><p class=\"sms_target\">\u6536\u4fe1\u4eba\uff1a<input value=\"" + (C ? C : "\u8f93\u5165\u6536\u4fe1\u4eba\u6635\u79f0") + "\" maxlength=\"22\"/></p><p class=\"sms_content\"><textarea></textarea></p></div>";
        at_sms.t = new at_trace();
        at_sms.t.setContact("\u53d1\u9001\u7ad9\u5185\u4fe1", D, [at_sms.t.getButton("\u53d1\u9001", "yellow", function() {
            var B = "",
                    A = at_sms.c.t.val().replace(/\s/g, ""),
                    D = at_sms.c.u.val().replace(/\s/g, ""),
                    C = at_sms.c.c.val().replace(/\s/g, "");
            if (A == "\u8f93\u5165\u7ad9\u5185\u4fe1\u6807\u9898" || A.length < 2 || A.length > 20) B += "\u6807\u9898\u5fc5\u987b\u5927\u4e8e2\u5e76\u5c0f\u4e8e20\u4e2a\u5b57<br>";
            if (D == "\u8f93\u5165\u6536\u4fe1\u4eba\u7528\u6237\u540d" || D == "") B += "\u5fc5\u987b\u586b\u5199\u6536\u4ef6\u4eba\u7528\u6237\u540d<br>";
            if (C.length < 5 || C.length > 250) B += "\u5185\u5bb9\u5fc5\u987b\u5927\u4e8e5\u5e76\u5c0f\u4e8e22\u4e2a\u5b57<br>";
            if (B != "") {
                at_infoTrace.show(B, "e", null, [at_sms.t.getButton("\u8fd4\u56de\u91cd\u586b", "yellow", function() {
                    at_sms.t.setDisplay("show");
                    at_infoTrace.hide()
                })], true);
                at_sms.t.setDisplay("hide");
                return
            }
            at_loading.show();
            $.getJSON("http://t.cn/a1ImID", {
                title: at_sms.c.t.val(),
                nick: at_sms.c.u.val(),
                cont: at_sms.c.c.val()
            }, function(B) {
                at_loading.hide();
                if (B.state == 1) at_infoTrace.show("\u7ad9\u5185\u4fe1\u53d1\u9001\u6210\u529f", "o", 2000);
                else at_infoTrace.show(B.msg, "e", 2000);
                at_sms.t.setDisplay("hide");
                try {
                    at_sms.onComplete(B)
                } catch (A) { }
            })
        }), at_sms.t.getButton("\u5173\u95ed", "gray", function() {
            at_sms.t.setDisplay("hide")
        })]);
        at_sms.t.setWidth(350);
        var A = $("#at_sms");
        at_sms.c.t = A.find("input:eq(0)");
        at_sms.c.u = A.find("input:eq(1)");
        at_sms.c.c = A.find("textarea");
        at_sms.c.t.focus(function() {
            if (at_sms.c.t.val().replace(/\s/g, "") == "\u8f93\u5165\u7ad9\u5185\u4fe1\u6807\u9898") at_sms.c.t.val("")
        });
        at_sms.c.u.focus(function() {
            if (at_sms.c.u.val().replace(/\s/g, "") == "\u8f93\u5165\u6536\u4fe1\u4eba\u7528\u6237\u540d") at_sms.c.u.val("")
        });
        at_sms.c.t.blur(function() {
            if (at_sms.c.t.val().replace(/\s/g, "") == "") at_sms.c.t.val("\u8f93\u5165\u7ad9\u5185\u4fe1\u6807\u9898")
        });
        at_sms.c.u.blur(function() {
            if (at_sms.c.u.val().replace(/\s/g, "") == "") at_sms.c.u.val("\u8f93\u5165\u6536\u4fe1\u4eba\u7528\u6237\u540d")
        });
        at_sms(C)
    } else {
        at_sms.c.t.val("\u8f93\u5165\u7ad9\u5185\u4fe1\u6807\u9898");
        at_sms.c.u.val("\u8f93\u5165\u6536\u4fe1\u4eba\u6635\u79f0");
        at_sms.c.c.val("");
        if (C) at_sms.c.u.val(C);
        at_sms.t.setDisplay("show")
    }
};
at_sms.t = null;
at_sms.c = {
    t: null,
    u: null,
    c: null
};
var at_ann = function(D, A) {
    if (at_userData() == null) {
        function B() {
            at_ann(D, A)
        }
        at_loginNeed(B, B);
        return
    }
    var E = $(D),
            C = E.text();
    E.html("<u></u>\u52a0\u8f7d\u4e2d");
    $.getJSON("http://t.cn/a1IupF", {
        uid: A
    }, function(B) {
        if (B.state == 1) E.html("<u></u>\u53d6\u6d88\u5173\u6ce8");
        else if (B.state == 2) E.html("<u></u>\u5173\u6ce8");
        else {
            E.html("<u></u>" + C);
            at_infoTrace.show(B.msg, "e")
        }
        try {
            at_ann.onComplete(B)
        } catch (A) { }
    })
},
    at_sre = function(A, C, B) {
        var B = B;
        at_sre.c.mt = (!B) ? "\u5206\u4eab" : "\u8f6c\u53d1";
        at_loading.show();
        $.getJSON("http://t.cn/a1IuBa", {
            mode: "sre",
            id: A,
            type: C,
            sid: B
        }, function(D) {
            at_loading.hide();
            if (D.state == 1) {
                var F = "<div id=\"at_sre\" class=\"at_tol_pub at_sre_ceg\"><p class=\"sre_infos\">" + D.msg + "</p><p class=\"sre_content\"><textarea>\u6211\u7684" + at_sre.c.mt + "\u7406\u7531\u662f...</textarea></p></div>";
                if (at_sre.t == null) {
                    at_sre.t = new at_trace();
                    at_sre.t.setWidth(450)
                }
                at_sre.t.setContact(at_sre.c.mt + "\u5230\u732a\u5708", F, [at_sre.t.getButton("\u786e\u5b9a", "yellow", function() {
                    var D = at_sre.c.c.val().replace(/\s/g, "");
                    if (D == "" || D == "\u6211\u7684" + at_sre.c.mt + "\u7406\u7531\u662f...") {
                        at_infoTrace.show(at_sre.c.mt + "\u7406\u7531\u4e0d\u80fd\u4e3a\u7a7a", "e", null, [at_traces_i.getButton("\u8fd4\u56de", "gray", function() {
                            at_sre.t.setDisplay("show");
                            at_infoTrace.hide()
                        })]);
                        at_sre.t.setDisplay("hide");
                        return
                    } else at_sre.send(A, C, D, B)
                }), at_sre.t.getButton("\u53d6\u6d88", "gray", "close")]);
                at_sre.c.t = $("#at_sre");
                at_sre.c.i = at_sre.c.t.children("p.sre_infos");
                at_sre.c.c = at_sre.c.t.find("textarea");
                var E = at_sre.c.i.find("img"),
                    H = false;
                if (at_sre.c.i.outerHeight() > 220) {
                    at_sre.c.i.height(220);
                    H = true
                }
                for (var G = 0; G < E.length; G++) if (E.eq(G).width() > 450) if (H) E.eq(G).css("width", "389px");
                else E.eq(G).css("width", "406px");
                at_sre.c.c.focus(function() {
                    if (at_sre.c.c.val().replace(/\s/g, "") == "\u6211\u7684" + at_sre.c.mt + "\u7406\u7531\u662f...") at_sre.c.c.val("")
                });
                at_sre.c.c.blur(function() {
                    if (at_sre.c.c.val().replace(/\s/g, "") == "") at_sre.c.c.val("\u6211\u7684" + at_sre.c.mt + "\u7406\u7531\u662f...")
                });
                at_sre.t.setContact(at_sre.c.mt + "\u5230\u732a\u5708");
                at_sre.c.c.val("\u6211\u7684" + at_sre.c.mt + "\u7406\u7531\u662f...");
                at_sre.t.setDisplay("show")
            } else at_infoTrace.show(D.msg, "e")
        })
    };
at_sre.t = null;
at_sre.c = {
    t: null,
    i: null,
    c: null
};
at_sre.send = function(A, C, D, B) {
    at_loading.show();
    $.getJSON("http://t.cn/a1IuBa", {
        mode: "sre_send",
        id: A,
        type: C,
        msg: D,
        sid: B
    }, function(B) {
        at_loading.hide();
        if (B.state == 1) {
            at_infoTrace.show(at_sre.c.mt + "\u6210\u529f", "o", 2000);
            at_sre.t.setDisplay("hide")
        } else at_infoTrace.show(B.msg, "e");
        try {
            at_sre.onComplete(B)
        } catch (A) { }
    })
};


var edit_customize_box = function() {
    this.trace = null;
    this.doms = {};
    this.conf = {
        keywordMax: 5,
        typeMax: 10
    };
    this.data = {
        keyword: [],
        type: []
    };
    this.onSave = null;
    this.openState = false;
    var A = "<div id=\"edit_customize_box\">";
    A += "<div class=\"c_box mb10\">";
    A += "<div class=\"c_tit\"><span class=\"fr gray3\">\u60a8\u6700\u591a\u53ef\u4ee5\u8bbe\u5b9a 5 \u7ec4\u5173\u952e\u8bcd</span><h4>\u5173\u952e\u8bcd</h4></div>";
    A += "<div class=\"c_con\">";
    A += "<div class=\"k_word\">";
    A += "<ul class=\"keys mb10 clearfix\"></ul>";
    A += "<div class=\"add_key\">";
    A += "<span class=\"at_text t_2_d\"><input size=\"30\" maxlength=\"17\"></span><a href=\"javascript:void(0)\" class=\"at_but b_pic\"><u></u>\u6dfb\u52a0\u5173\u952e\u8bcd</a>";
    A += "</div>";
    A += "<s class=\"e15\"></s>";
    A += "</div>";
    A += "</div>";
    A += "</div>";
    A += "<div class=\"c_box mb10\">";
    A += "<div class=\"c_tit\"><span class=\"fr gray3\">\u60a8\u6700\u591a\u53ef\u5173\u6ce8 10 \u4e2a\u5206\u7c7b</span><h4>\u5173\u6ce8\u5206\u7c7b</h4></div>";
    A += "<div class=\"c_con\">";
    A += "<div class=\"k_word task_type\">";
    A += "<ul class=\"keys mb10 clearfix\"></ul>";
    A += "<div class=\"add_key\">";
    A += "<p class=\"clearfix\">";
    A += "<select><option value=\"9999\">\u9009\u62e9\u5206\u7c7b</option></select>";
    A += "<a href=\"javascript:void(0)\" class=\"at_but b_pic\"><u></u>\u6dfb\u52a0</a>";
    A += "</p>";
    A += "</div>";
    A += "<s class=\"e15\"></s>";
    A += "</div>";
    A += "</div>";
    A += "</div>";
    A += "</div>";
    this.dom = A;
    this.__init__()
};
edit_customize_box.prototype = {
    __init__: function() {
        this.trace = new at_trace();
        this.trace.setContact("\u8bbe\u7f6e\u5173\u6ce8", this.dom, this.getButton());
        this.trace.setWidth(534);
        var A = $("#edit_customize_box>div.c_box>.c_con>.k_word");
        this.doms.keywordCon = A.eq(0);
        this.doms.typeCon = A.eq(1);
        this.doms.keywordAddCon = this.doms.keywordCon.children(".add_key");
        this.doms.keywordInput = this.doms.keywordAddCon.find("input");
        this.doms.keywordBut = this.doms.keywordAddCon.find("a.at_but");
        this.doms.keywordUl = this.doms.keywordCon.children(".keys");
        this.doms.typeAddCon = this.doms.typeCon.children(".add_key");
        this.doms.typeselect = this.doms.typeAddCon.find("select");
        this.doms.typeBut = this.doms.typeAddCon.find("a.at_but");
        this.doms.typeUl = this.doms.typeCon.children(".keys");
        this.setChildType(0, this.doms.typeselect.eq(0));
        this.bindEvent()
    },
    createDom: function(A) {
        if (this.openState == true) return;
        this.openState = true;
        at_loading.show();
        var B = this;
        $.getJSON("http://t.cn/a1I1GS", function(D) {
            if (D.state == 1) {
                var E = D.keyword,
                    F = D.type;
                for (var G = 0; G < E.length; G++) B.addKeyword(E[G]);
                for (G = 0; G < F.length; G++) B.addType(F[G].id, F[G].cname, F[G].cndir)
            }
            B.initState();
            B.doms.has = true;
            at_loading.hide();
            try {
                A()
            } catch (C) { }
        })
    },
    initState: function(E) {
        var D = this.doms.keywordUl.children("li").length,
            A = this.doms.typeUl.children("li").length,
            C = null,
            B = null;
        if (E == "keyword" || !E) if (D >= 1) {
            this.doms.keywordUl.show();
            this.doms.keywordAddCon.show()
        } else if (D == this.conf.keywordMax) {
            this.doms.keywordUl.show();
            this.doms.keywordAddCon.hide()
        } else if (D == 0) {
            this.doms.keywordUl.hide();
            this.doms.keywordAddCon.show()
        }
        if (E == "type" || !E) if (A >= 1) {
            this.doms.typeUl.show();
            this.doms.typeAddCon.show()
        } else if (A == this.conf.typeMax) {
            this.doms.typeUl.show();
            this.doms.typeAddCon.hide()
        } else if (A == 0) {
            this.doms.typeUl.hide();
            this.doms.typeAddCon.show()
        }
    },
    bindEvent: function() {
        var A = this;
        this.trace.onHide = function() {
            A.openState = false
        };
        this.doms.keywordBut.unbind("click");
        this.doms.keywordBut.bind("click", function() {
            var B = A.doms.keywordInput.val().replace(/\s*/g, "");
            if (B == "") return;
            A.addKeyword(B)
        });
        this.doms.typeBut.unbind("click");
        this.doms.typeBut.bind("click", function() {
            var H = A.doms.typeAddCon.find("select"),
                D = true;
            for (var I = 0; I < H.length; I++) if (H.eq(I).val() == 9999 || H.eq(I).val() == "9999") return;
            var B = H.last(),
                F = B.val().split("|"),
                C = F[0],
                E = F[2],
                G = F[3];
            A.addType(C, E, G)
        });
        this.bindSelectEvent(this.doms.typeselect.eq(0))
    },
    bindSelectEvent: function(A) {
        var B = this;
        A.unbind("change");
        A.bind("change", function() {
            var C = $(this);
            C.nextAll("select").remove();
            var A = C.val().match(/^(.+?)\|/)[1],
                D = C.val().match(/\|(.+?)\|/)[1];
            if (D == false || D == "false") return;
            B.setChildType(A, C);
            return false
        })
    },
    bindRemoveEvent: function(D) {
        if (D == "keyword" || D == "type") {
            var B = (D == "keyword") ? this.data.keyword : ((D == "type") ? this.data.type : null);
            if (!B) return;
            var C = this,
                A = null;
            for (var E = 0; E < B.length; E++) {
                A = B[E].li.find("i:eq(0)").get(0);
                A.key = B[E].key;
                A.onclick = function() {
                    if (D == "keyword") C.removeKeyword(this.key);
                    else if (D === "type") C.removeType(this.key)
                }
            }
        } else return
    },
    setChildType: function(B, C) {
        if (B === 0 || B) {
            var A = this;
            if (B !== 0) at_loading.show();
            $.getJSON("http://t.cn/a1I1rm" + B + "&jsoncallback=?", function(D) {
                at_loading.hide();
                if (D.state == 1) {
                    var E;
                    if (B === 0) E = C;
                    else {
                        E = C.after("<select><option value=\"9999\">\u9009\u62e9\u5206\u7c7b</option></select>").next();
                        A.bindSelectEvent(E)
                    }
                    for (var F = 0; F < D.msg.length; F++) E.append("<option value=\"" + D.msg[F].id + "|" + D.msg[F].hasChild + "|" + D.msg[F].cname + "|" + D.msg[F].cndir + "\">" + D.msg[F].cname + "</option>")
                }
            })
        } else return
    },
    getButton: function() {
        var A = this;
        return [this.trace.getButton("\u4fdd\u5b58\u8bbe\u7f6e", "yellow", function() {
            A.send();
            return false
        }), this.trace.getButton("\u53d6\u6d88", "gray", function() {
            A.reset()
        })]
    },
    getIndexByKey: function(A, B) {
        if (!A.length || !B) return;
        for (var C = 0; C < A.length; C++) if (A[C].key == B) return C;
        return null
    },
    removeItem: function(B, A) {
        for (var C = 0; C < B.length; C++) if (C == A) B = B.slice(0, C).concat(B.slice(C + 1, B.length));
        return B
    },
    addKeyword: function(B) {
        if (this.data.keyword.length == this.conf.keywordMax) {
            this.error("\u6700\u591a\u53ea\u80fd\u6dfb\u52a0" + this.conf.keywordMax + "\u4e2a\u5173\u952e\u5b57");
            return
        }
        if (!/^(?:[a-zA-Z0-9]|[\u4e00-\u9fa5])*$/.test(B)) {
            this.error("\u5173\u952e\u5b57\u4e0d\u5141\u8bb8\u586b\u5199\u7279\u6b8a\u7b26\u53f7");
            return
        }
        var A = this.doms.keywordUl.append("<li><em>" + B + "</em><i>\xd7</i></li>").find(">li:last");
        this.data.keyword.push({
            "key": getRandom(),
            "value": B,
            "li": A
        });
        this.bindRemoveEvent("keyword");
        this.initState();
        this.doms.keywordInput.val("")
    },
    addType: function(B, C, D) {
        if (this.data.type.length == this.conf.typeMax) {
            this.error("\u6700\u591a\u53ea\u80fd\u6dfb\u52a0" + this.conf.typeMax + "\u4e2a\u5173\u6ce8\u5206\u7c7b\u3002");
            return
        }
        for (var E = 0; E < this.data.type.length; E++) if (this.data.type[E].name == C) {
            this.error("\u5f53\u524d\u5df2\u6dfb\u52a0\u4e86\u8fd9\u4e2a\u5206\u7c7b\uff0c\u4e0d\u80fd\u91cd\u590d\u6dfb\u52a0\u76f8\u540c\u5206\u7c7b\u3002");
            return
        }
        var A = this.doms.typeUl.append("<li><em>" + C + "</em><i>\xd7</i></li>").find(">li:last");
        this.data.type.push({
            "key": getRandom(),
            "id": B,
            "name": C,
            "cndir": D,
            "li": A
        });
        this.bindRemoveEvent("type");
        this.initState()
    },
    removeKeyword: function(B) {
        if (!B) return;
        var A = this.getIndexByKey(this.data.keyword, B);
        this.data.keyword[A].li.remove();
        this.data.keyword = this.removeItem(this.data.keyword, A);
        this.initState()
    },
    removeType: function(B) {
        if (!B) return;
        var A = this.getIndexByKey(this.data.type, B);
        this.data.type[A].li.remove();
        this.data.type = this.removeItem(this.data.type, A);
        this.initState()
    },
    open: function() {
        if (!this.doms.has) {
            var A = this;
            this.createDom(function() {
                A.show()
            })
        } else this.show()
    },
    show: function() {
        this.trace.setDisplay("show")
    },
    hide: function() {
        this.trace.setDisplay("hide");
        this.openState = false
    },
    send: function() {
        var B = this;
        if (this.data.keyword.length == 0 && this.data.type.length == 0) {
            at_infoTrace.show("\u60a8\u6ca1\u6709\u8bbe\u7f6e\u4efb\u4f55\u5173\u952e\u5b57\u6216\u5173\u6ce8\u5206\u7c7b\uff0c\u662f\u5426\u4fdd\u5b58?", "i", null, [at_traces_i.getButton("\u4fdd\u5b58", "gray", function() {
                A()
            }), at_traces_i.getButton("\u91cd\u65b0\u8bbe\u7f6e", "yellow", function() {
                B.show();
                at_infoTrace.hide()
            })], true);
            this.hide()
        } else A();

        function A() {
            at_loading.show();
            var A = (function() {
                var A = "";
                for (var C = 0; C < B.data.keyword.length; C++) A += B.data.keyword[C].value + ",";
                return A.replace(/,$/, "")
            })(),
                C = (function() {
                    var A = "";
                    for (var C = 0; C < B.data.type.length; C++) A += B.data.type[C].id + ",";
                    return A.replace(/,$/, "")
                })();
                $.getJSON("http://t.cn/a1IBxj", {
                save: "1",
                keyword: A,
                type: C
            }, function(C) {
                at_loading.hide();
                if (C.state == 1) {
                    at_infoTrace.show("\u5173\u6ce8\u8bbe\u7f6e\u6210\u529f!", "o");
                    try {
                        B.onSave()
                    } catch (A) { }
                    B.clear()
                }
            });
            return false
        }
    },
    reset: function() {
        this.hide()
    },
    clear: function() {
        this.hide();
        this.data.keyword = [];
        this.data.type = [];
        this.doms.keywordUl.children("li").remove();
        this.doms.typeUl.children("li").remove();
        this.doms.has = false
    },
    error: function(B) {
        var A = this;
        at_infoTrace.show(B, "e", null, [at_traces_i.getButton("\u91cd\u65b0\u8bbe\u7f6e", "yellow", function() {
            A.show();
            at_infoTrace.hide()
        }), at_traces_i.getButton("\u5173\u95ed", "gray", "close")], true);
        this.hide()
    }
};

var _fx = function(C, D) {
    if (!C || !D) return false;
    var B = {
        rr: " http://share.renren.com/share/buttonshare.do?link={0}&title={1}{2}",
        qq: " http://v.t.qq.com/share/share.php?title={1}&url={0}&source={2}",
        qzone: " http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={0}",
        hi: " http://apps.hi.baidu.com/share/?url={0}&title={1}&content={2}",
        kx: " http://www.kaixin001.com/repaste/share.php?rtitle={1}&rurl={0}&rcontent={2}",
        db: " http://www.douban.com/recommend/?url={0}&title={1}&comment={2}",
        bsh: " http://bai.sohu.com/appLogin.jsp?bru=/app/share/blank/add.do?link={0}&title={1}",
        //sina: " http://v.t.sina.com.cn/share/share.php?url={0}&title={1}{2}&content={2}appkey=511460290",
        sina: "http://service.weibo.com/share/share.php?title={1}{2}&url={0}&source=bookmark&appkey=2992571369&pic=&ralateUid=",
        sohu: " http://t.sohu.com/third/post.jsp?url={0}&title={1}&content=utf-8",
        wy163: " http://t.163.com/article/user/checkLogin.do?link={0}&source={1}&info={2}"
    },
    A = B[D.key];
    C.href = A.replace(/\{0}/g, D.url).replace(/\{1}/g, encodeURIComponent(D.title)).replace(/\{2}/g, encodeURIComponent(D.overview))
}