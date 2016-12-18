//页面加载，判断搜索条件是现实还是隐藏
    $(function(){
        var show_cookie = getcookie('show_cookie',1);
        if (show_cookie == 1) {
            $("#condition_list").show();
            $("#tool_hide").show();
            $("#tool_show").hide();
        }
    });
    //搜索条件现实 /隐藏
    function show_hide(){
        $("#condition_list").toggle(0, function(){
            if ($("#tool_show").is(":hidden")) {
                setcookie('show_cookie', '');
                $("#tool_show").show();
                $("#tool_hide").hide();
            }
            else {
                setcookie('show_cookie', 1,3600);
                $("#tool_hide").show();
                $("#tool_show").hide();
            }
        });
    }

    var uid = parseInt('') + 0;
    $('.re_on_off').click(function(){
        if ($(this).children('.fl_l').text() == "展示相关服务") {
            $(this).children('.fl_l').text('收起相关服务');
        }
        else {
            $(this).children('.fl_l').text('展示相关服务');
        }
        $(this).find('.re_ser').toggleClass('re_ser_off');
        $(this).parents(".talent_info_box").children('.re_ser_box').toggleClass('hidden');
        $(this).parents('dd').find('.re_ser_box').find('.jcarousel').attr('id', 'jcarousel1');
        $(this).parents('dd').siblings().find('.re_ser_box').addClass('hidden').end().find('.jcarousel').attr('id', '').end().find('.re_on_off .fl_l').text('展示相关服务').end().find('.re_ser').removeClass('re_ser_off').end();
        ajaxTab('jcarousel1', '', "/index.php?do=talent&ajax=1&s_uid=" + $(this).attr('uid'));
        
var t = setTimeout(function() {
        		 $('#jcarousel1').jcarousel();
        		clearTimeout(t);
        }, 400);
       

    });
    $('#jcarousel2').jcarousel({
        wrap: 'circular',
        animation: 200
    }).jcarouselAutoscroll({
        interval: 3000
    });