/**
 *品牌馆 
 */

	$(function(){
		$('.br_info a').hover(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			$(this).parent().siblings().hide();
			var sid = $(this).attr('sid').toString();
			if($('#'+sid).length==0){
				$('<div class="bl_info clearfix fl_l" id="'+sid+'">')
						.insertBefore($(this).parent())
						.load(SITEURL+"/index.php?do=brand&ajax=service&sid="+sid);
			}
			$('#'+sid).show();
		});
	})	
  $(function(){
        var show_cookie = getcookie('show_cookie',1);
        if (show_cookie == 1) {
            $("#condition_list").show();
            $("#tool_hide").show();
            $("#tool_show").hide();
        }

        $('#change_list').find('li').not('.group0').addClass('hidden');
		var i = 0;
        $('.task_list_foot #change_op').click(function(){
            $('#change_list').find('li').addClass('hidden'); 
            ++i;
            if(i<=2){
                $('#change_list').find('li.group'+i).removeClass('hidden');
                //alert('li.group'+i); 
            }
            else{
                i=0;
                 $('#change_list').find('li.group'+i).removeClass('hidden')
                //alert('li.group'+i);
            }
        })

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
	$('.re_on_off').click(function(){
        if ($(this).children('.fl_l').text() == "展示相关服务") {
            $(this).children('.fl_l').text('收起相关服务');
        }
        else {
            $(this).children('.fl_l').text('展示相关服务');
        }
        $(this).find('.re_ser').toggleClass('re_ser_off');
        $(this).parents(".talent_info_box").children('.re_ser_box').toggleClass('hidden');
        $(this).parents('dd').find('.re_ser_box').find('.jcarousel').attr('id', 'jcarousel2');
        $(this).parents('dd').siblings().find('.re_ser_box').addClass('hidden').end().find('.jcarousel').attr('id', '').end().find('.re_on_off .fl_l').text('展示相关服务').end().find('.re_ser').removeClass('re_ser_off').end();
        ajaxTab('jcarousel2', '', SITEURL+"/index.php?do=talent&ajax=1&s_uid=" + $(this).attr('uid'));
        
		var t = setTimeout(function() {
        		 $('#jcarousel2').jcarousel();
        		clearTimeout(t);
        }, 400);
       

    });
		$('#carousel,#jcarousel1,#jcarousel2').jcarousel({
		    wrap: 'circular',
			 animation: 200
		}).jcarouselAutoscroll({
		   interval:  10000
		});
	$('.jcarousel-pagination').jcarouselPagination({
	   perpage: 1
	});

function brand(brand){
	if(check_user_login()){
		if(shop_id){
			var url = SITEURL+'/index.php?do=brand&ajax=apply&brand='+brand;
			showWindow('brand',url,'get','0');
			return false;
		}else{
			showDialog('您还未开通店铺,无法申请加入品牌馆,点击确认去开通','confirm','操作提示',function(){
				location.href=SITEURL+'/index.php?do=user&&view=space';
			});
		}
	}
}