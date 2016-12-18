/**
 * 页头js
 */

$(function(){
	//头部下拉菜单
	$('#nav_pull1,#nav_pull2,#nav_pull3,#nav_pull4').hover(
		function(){
			$(this).find('div').removeClass('hidden').addClass('block');
			
		},
		function(){
			$(this).find('div').removeClass('block').addClass('hidden');
		}
	);
	

	//头部搜索条件切换
	$("#topsearch_task").click(function(){
		$(this).addClass("selected");
		$("#topsearch_talent").removeClass("selected");
		$("#topsearch_type").val("task_list");
	});
	
	$("#topsearch_talent").click(function(){
		$(this).addClass("selected");
		$("#topsearch_task").removeClass("selected");
		$("#topsearch_type").val("talent");
	});
	
	
	//头部搜索
	$("#topsearch_btn").click(function (){
		topSearch();
	});
});
if($("#search_key").length){
window.document.getElementById('search_key').onkeydown = function(e){
	var e = e ? e : window.event; 
	var code = e.which ? e.which : e.keyCode;     //获取按键值
	if(code==13){
		topSearch();
	}
}
}
function topSearch(){
	var searchKey = $.trim($("#search_key").val());
		if(searchKey&&searchKey!='输入关键词搜索'){
			var type    = $("#topsearch_type").val();
			var link    = encodeURI("/index.php?do="+type+"&k="+searchKey);
				$('#frm_topsearch').attr('action',link);
			window.document.location.replace(link);
	}
}


