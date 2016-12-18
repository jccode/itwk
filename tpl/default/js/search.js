
/*删除cookie*/
function clear_cookie(){
		var url = window.location.href+"&hid_del_cookie=1";
		$.post(url,function (json){
			if(json.status==1){
				$("#history_collect").html("");
			} 
		},'json'); 
}

 /*存储cookie*/
function save_cookie(){
    var url = window.location.href + "&hid_save_cookie=1";
    $.post(url, function(json){
        if (json.status == 1) {
  
            $("#success").fadeIn(1000);
            $("#success").fadeOut(500);
			var li_length = $("#history_collect").children('li').length;
			if(li_length>=5){
				   $("#history_collect li:first").remove();
			} 
            var html = "<li><a href=" + window.location.href + ">"+json.data+"</a></li>";
            $("#history_collect").append(html);
        } 
		if(json.status==2){
			$("#success").html(json.msg);	  
			$("#success").fadeIn(1000);
            $("#success").fadeOut(500); 
		} 
    }, 'json');
} 


/* 地区搜索 */ 
function search_area(){  
	var province = $("#province").val();
	var city = $("#city").val(); 
	var area = $("#area").val()+'slt_city';
	var new_slt_city =  '&province='+province+'&city='+city+'&area='+area; 
	var url = window.location.href;
	if(url.indexOf('&province') ==-1){ 
		url = url+new_slt_city;
	}else{
		url = url.replace(/&province(.*)slt_city/,new_slt_city); 
	} 
	location.href = url;  
}







//赏金重置
function task_cash_reset(){
	setcookie('search_cash', '',-999); 
            $("#cool_search").hide();
            $("#general_search").show();

}


//赏金搜索自定义
function custom_search_cash(){
	setcookie('search_cash',1); 

    $("#general_search").hide();
    $("#cool_search").show();
  
}

