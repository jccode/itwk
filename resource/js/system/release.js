/*
 *任务发布公有js 
*/

$(function(){
	$("#qq").click(function(){
		$("#ct_qq").toggle();
	})
	$("#msn").click(function(){
		$("#ct_msn").toggle();
	})
	$("#email").click(function(){
		$("#ct_email").toggle();
	})
	$("#mobile").click(function(){
		$("#ct_mobile").toggle();
	})
//	contact();
//	$(":radio[name='contact_type']").click(function(){$(this).attr("checked","checked");contact()});
	
	$("#tar_content").blur(function(){
		contentCheck('tar_content',"任务需求",10,1000);
	})
	
	$(".lit_form input:[type='text']").each(function(){
		$(this).val()==''&&$(this).val($(this).attr("ext"));
	})
})

uploadBlur = function() {
	if (ifOut("upfile", "5") && $("#upload").val()) {
		upload("upload", "att", "front", "", "", "task")
	} else {
		return false
	}
};

/*
 * 选择任务模型
 * */
function choose_model(model_type){
	switch(model_type){
		case 'reward':
			$('#model_box_reward').show();
			$('#model_box_tender').hide();
			$('#model_box_pre').hide();
			$('#hdn_model_id').val(1);
			$('#li_1').attr('class','selected');
			$('#li_3').attr('class','three_box');
			$('#li_4').attr('class','');
			break;
		case 'tender':
			$('#model_box_reward').hide();
			$('#model_box_tender').show();
			$('#model_box_pre').hide();
			$('#hdn_model_id').val(4);
			$('#li_1').attr('class','');
			$('#li_3').attr('class','three_box');
			$('#li_4').attr('class','selected');
			break;
		case 'pre':
			$('#model_box_reward').hide();
			$('#model_box_tender').hide();
			$('#model_box_pre').show();
			$('#hdn_model_id').val(3);
			$('#li_1').attr('class','');
			$('#li_3').attr('class','selected three_box');
			$('#li_4').attr('class','');
			break;
	}
}

/**
 * 联系方式清空
 */
function contact(){
//	var contact_type = parseInt($(":radio[name='contact_type']:checked").val())+0;
//		if(contact_type=='1'){
//			$(".lit_form input:[type='text']").removeAttr("ignore").removeAttr("disabled").val('');
//		}else{
//			$(".lit_form input:[type='text']").each(function(){
//				$(this).attr("disabled","disabled").attr("ignore","true").val($(this).attr("ext"));
//			})
//		}
}

/**
 * 奖项设置  多人模式专用 (当键入enter 键时促发)
 */
function task_prizeset_enter(pcount){
 if ( event.keyCode=='13' ) {
	if(pcount==''||pcount<2){
		pcount = 2;
		$('#txt_prize_count').val(2);
	}
	if(pcount>5){
		pcount = 5;
		$('#txt_prize_count').val(5);
	}
	
	pcount>=2&&$('#prize_1_li').show()||$('#prize_1_li').hide();
	pcount>=2&&$('#prize_2_li').show()||$('#prize_2_li').hide();
	pcount>=3&&$('#prize_3_li').show()||$('#prize_3_li').hide();
	pcount>=4&&$('#prize_4_li').show()||$('#prize_4_li').hide();
	pcount>=5&&$('#prize_5_li').show()||$('#prize_5_li').hide();
	
	prize_valid();
	}
}

/**
 * 奖项设置  多人模式专用
 */
function task_prizeset(pcount){
	
	if(pcount==''||pcount<2){
		pcount = 2;
		$('#txt_prize_count').val(2);
	}
	if(pcount>5){
		pcount = 5;
		$('#txt_prize_count').val(5);
	}
	
	pcount>=2&&$('#prize_1_li').show()||$('#prize_1_li').hide();
	pcount>=2&&$('#prize_2_li').show()||$('#prize_2_li').hide();
	pcount>=3&&$('#prize_3_li').show()||$('#prize_3_li').hide();
	pcount>=4&&$('#prize_4_li').show()||$('#prize_4_li').hide();
	pcount>=5&&$('#prize_5_li').show()||$('#prize_5_li').hide();
	
	prize_valid();
}

/*
 * 计件价格验证
 * */
function valid_single_price(){
	var totle_cash = $('#txt_task_cash_3').val();
	var work_count = $('#txt_work_count').val();
	var single_price = $('#txt_single_price').val();
	
	if(totle_cash==0||totle_cash==''){
		return false;
	}
	if(parseInt(work_count)<2){
		work_count = 2;
		$('#txt_work_count').val(2);
		return false;
	}
	if(parseFloat(single_price)<1){
		single_price = 1;
		$('#txt_single_price').val(1);
		$('#msg_single_price').html('计件任务每个稿件的平均单价不能低于1元人民币');
		return false
	}
	
	if(parseInt(work_count)*parseFloat(single_price)!=parseFloat(totle_cash)){
		$('#msg_single_price').html('稿件单价*稿件数量不等于任务总金额');
		return false;
	}else{
		$('#msg_single_price').html('');
		return true;
	}
}

/*
 * 奖项验证
 * */
function prize_valid(){
	
	var pcount = $('#txt_prize_count').val();
	var t_cash = $('#txt_task_cash_1').val();
	
	if(t_cash==''||pcount==''){
		return false;
	}
	$('#msg_prize_valid').hide();
	var mess='';
	var pz_c_cash = 0;//当前填写的总金额
	for(var i=1;i<=pcount;i++){
		if($('#task_prize_'+i).val()==''||$('#task_prize_'+i).val()<=0){
			$('#msg_prize_valid').html(i+'等奖的中标金额未填写');
			$('#msg_prize_valid').show();
			return false;
		}
		
		if(i>=2&&parseInt($('#task_prize_'+i).val())>=parseInt($('#task_prize_'+(i-1)).val())){
			
			
			$('#msg_prize_valid').html((i-1)+'等奖的中标金额必须大于'+i+'等奖');
			$('#msg_prize_valid').show();
			return false;
		}
		pz_c_cash += parseInt($('#task_prize_'+i).val());
	}
	
	if(pz_c_cash != t_cash){
		$('#msg_prize_valid').html('中标金额不等于任务金额');
		$('#msg_prize_valid').show();
		return false;
	}
	
	return true;
}

/*
 * 最大天数的默认值判断  赋值
 * */
function valid_task_day(model_id){
	var maxday = $('#task_maxday_'+model_id).val();
	var minday = $('#task_minday_'+model_id).val();
	
	var m_id = model_id;
	if(m_id==2){m_id = 1;} //因为模型1和2同表单
	
	if(!minday||!maxday){
		//未读时重新加载时间规则
		var m_cash = $('#txt_task_cash_'+m_id).val();
		getMaxDday(m_cash,m_id);
		return false;
	}
	
	var nowv = $('#txt_task_period_'+m_id).val();
	if(nowv==''){
		$('#txt_task_period_'+m_id).val(maxday);
		return false
	}
	nowv = parseInt(nowv);
	if(nowv<minday){
		$('#txt_task_period_'+m_id).val(minday);
	}
	else if(nowv>maxday){
		$('#txt_task_period_'+m_id).val(maxday);
	}
	$('#task_tips_'+m_id).html("您的赏金金额任务最长周期为<span class='red'>"+maxday+"</span>天");
	
}


/**
 * 获取相应预算范围内的最大天数
 * @param task_cash
 */
function getMaxDday(task_cash,model_id){
	if(task_cash){
		
		
		$.get('/index.php?do=ajax&view=task&ajax=getmaxday&task_cash='+task_cash+'&model_id='+model_id,function(json){
			//$(".lit_form .pad10 span:last-child").removeClass().text('');
			if(json.status==1){ 	
				//上下限默认值赋予
				var maxday = json.data.maxday;
				var minday = json.data.minday;
				var mincash = json.data.mincash;
				$('#task_maxday_'+model_id).val(maxday);
				$('#task_minday_'+model_id).val(minday);
				switch(model_id){
					case '1':
					case '2':
						
						if(parseFloat($('#txt_task_cash_1').val())<parseFloat(mincash)){
							$('#txt_task_cash_1').val(mincash);
						}
						break;
					case '3':
					case '4':
						if(parseFloat($('#txt_task_cash_'+model_id).val())<parseFloat(mincash)){
							$('#txt_task_cash_'+model_id).val(mincash);
						}
						break;
				}
				
				//赋值
				valid_task_day(model_id);
				
			}else
				return false;
			},'json')
		
		
		
		
		
		
//		$.getJSON(basic_url,{ajax:'getmaxday',task_cash:task_cash},function(json){
//			$(".lit_form .pad10 span:last-child").removeClass().text('');
//			if(json.status==1){ 	
//				 $("#txt_task_day").attr("limit","required:true;type:date;than:min;less:"+json.msg).val(json.msg);
//				 $("#max").val(json.msg); 
//				 var min_day = $("#txt_task_day").attr("min_day");
//				 title=" 预计的任务持续天数,当前预算允许最小天数为:"+min_day+"天,最大截止时间："+json.data;
//				 $("#txt_task_day").attr("title",title); 
//				 $("#txt_task_day").attr("max",json.msg); 
//				 $("#txt_task_day").attr("msg",title);
//			}else
//				return false;
//			})
	}
}



//显示隐藏使用天数的输入框
function show_payitem_num(obj,item_code){
	
	var item_code = item_code;
	var checked = $(obj).attr("checked");  
	if(checked ==true){ 
		if(item_code=='map'){
			$("#set_map").show(); 
			add_payitem($("#item_map"),'add',1);  
		}else{
			$("#span_"+item_code).show();  
		}
	}else{ 	
		if(item_code=='map'){
			add_payitem($("#item_map"),'del',1);  
			$("#set_map").hide(); 
		}else{
			del_payitem(item_code);//删除增值服务
			$("#span_"+item_code).hide(); 
			$("#payitem_"+item_code).val(""); 
		} 
	} 
}


//编辑增值服务
function edit_payitem(item_code){

	var item_code = item_code;
	var payitem_num = parseInt($("#payitem_"+item_code).val());
	var item_cash = parseInt($("#checkbox_"+item_code).attr("item_cash"));
	var total_cash = parseInt( $("#ago_total").val()); 
//	$("#total").html(total_cash+(item_cash*payitem_num)); 
	add_payitem($("#checkbox_"+item_code),'add',payitem_num); 
}

//删除增值服务
function del_payitem(item_code){
	var item_code = item_code;
	var payitem_num = parseInt($("#payitem_"+item_code).val()); 
	add_payitem($("#checkbox_"+item_code),'del',payitem_num);  
}

/**
 * 检查任务周期
 * @returns {Boolean}
 */
function checkDay(){
	var max_day = parseInt($("#txt_task_day").attr("max"))+0;
	var day     = parseInt($("#txt_task_day").val())      +0;
	
	if(day>max_day){
		$("#span_task_day").html("<span>当前任务金额允许最大周期为:"+max_day+"天</span>");
		return false;
	}else
		return true;
}
/**
 * 检测是否同意协议
 */
function checkAgreement(){
	if($("#agreement").attr("checked")==false){
		showDialog("请先同意任务发布协议","alert","操作提示");return false;
	}else return true;
}


//上一步
function stepsave(model_id,step,task_id){
	
	var fromname  = 'frm_step'+step;
	
	if(model_id==''){
		var model_id = $('#hdn_model_id').val();
//		if(model_id==1){
//			model_id = $('#reward_model_id').val();
//		}
	}
	if(step=='2'){
		fromname += '_'+model_id;
	}
	var queryString = $("#"+fromname).formSerialize();
    $.post('/index.php?do=release&r_step=step'+(parseInt(step))+'&model_id='+model_id+'&task_id='+task_id+'&ac=save', queryString, function(json){
        if (json.msg == 1) {
            location.href = '/index.php?do=release&r_step=step'+(parseInt(step)-1)+'&model_id='+model_id+'&task_id='+task_id;
        }
        else {
            showDialog('系统繁忙', 'alert', '错误提示');
        }
    }, 'json');
}
$(function(){
	if(m&&r_step=='step2'){
		toggLimit(m);
	}
})
function toggLimit(m){
	/*var limit= 'required:true;type:float;between:';
	var otil = '任务预算,不支持小数,最小金额为';
	var msg  = '任务预算不得为空,最小金额为';
	var min  = 0;
		m==1?min=smin:min=mmin;
		limit+=min;
		otil+=min+'元';
		msg+=min+'元';
	//$('#txt_task_cash_1').attr({'original-title':otil,'limit':limit,'msg':msg});
	//getMaxDday(min,m);
	m==2?prize_valid():'';
	//choose_model('reward');*/
}


function stepCheck(){
	if(r_step=='step2'){
		var i 	 = checkForm(document.getElementById('frm_'+r_step+'_'+$('#hdn_model_id').val()));
	}
	else{
		var i 	 = checkForm(document.getElementById('frm_'+r_step));
	}
	
	var pass = false;
	switch(r_step){
		case "step1": 
			if(i){ 
				if(contentCheck('tar_content',"任务需求",10,1500,0,'',editor)&&checkAgreement()){
					pass = true;
				}
				else{
					
					if(!checkAgreement()){
						showDialog('您必须同意协议','alert','协议确认');
					}
					else{
						showDialog('需求字数限定10-1500','alert','需求不完整');
					}
				}
				if($('#mobile').attr('checked')){
					if(!$('#contact_mobile').val()){pass=false;showDialog("联系电话未填写！","alert","操作提示")}
				}
				if($('#email').attr('checked')){
					if(!$('#contact_email').val()){pass=false;showDialog("email未填写！","alert","操作提示")}
				}
				if($('#qq').attr('checked')){
					if(!$('#contact_qq').val()){pass=false;showDialog("qq未填写！","alert","操作提示")}
				}
				if($('#msn').attr('checked')){
					if(!$('#contact_msn').val()){pass=false;showDialog("msn未填写！","alert","操作提示")}
				}
				
				if(!$('#mobile').attr('checked')&&!$('#email').attr('checked')&&!$('#qq').attr('checked')&&!$('#msn').attr('checked')){
					pass=false;
					showDialog("至少填写一种联系方式！","alert","操作提示");
					$('#mobile').attr("checked","checked");
					$('#ct_mobile').show()
				}
				
				// 检查附件的个数
				if($(".uploadify-queue-item").length > 5) {
					pass=false;
					showDialog("附件最多只能上传5个.请删除一些后重试.","alert","操作提示");
				}
				
			}
			return checkLogin(pass);
			break;
		case "step2":
			if(checkDay()){
				if(i){
					pass=true;
				}
			}
			
			switch($('#hdn_model_id').val()){
				case '1':
					if($('#reward_model_id').val()==2&&!prize_valid())
						pass = false;//奖项规则验证
					break;
				case '3':
					if(!valid_single_price())
						pass=false;
					break;
			}
			
			if(pass==true){
				
				check_pub_priv();
			}
			break;
		case "step3":
			if($("#item_map").attr("checked")==true&&$.trim($("#point").val())==''){
				set_map();return false;
			}else{
				if(i){
					$("#frm_"+r_step).submit();
					$(":input[name='is_submit']").unbind("click").attr("type","button");
				}				
			}
			if(pass==true){
				
				check_pub_priv();
			}
			break;
		case "step4":
			if(pass==true){
				
				check_pub_priv();
			}
			break;
	}
	
}
/**
 * 检查登录
 */
function checkLogin(pass){
	
	if(pass==false){
		return false;
	}
	$.getJSON('/index.php?do=release&ac=check_login',function(json){
		if(json.status){
			$("#frm_step1").submit();
		}else{
			showWindow('login', '/index.php?do=ajax&view=login&visitmode=release');
//			showDialog('请登录后再发布','confirm','操作提示',function(){
//				window.open('index.php?do=login','_blank');$('#fwin_dialog,#fwin_dialog_cover').remove();
//			});
			return false;
		}
	})
}
/**
 * 发布权限检测
 * @returns {Boolean}
 */
function check_pub_priv(){
	if('r_step'=='step2'){
		basic_url+='&model_id='+$('#hdn_model_id').val();
	}
	
	$.getJSON(basic_url,{ajax:"check_priv"},function(json){
		if(json.status=='1'){
			if(r_step=='step2'){
				
				$("#frm_"+r_step+'_'+$('#hdn_model_id').val()).submit();
			}
			else{
				$("#frm_"+r_step).submit();
			}
		}else{
			
			showDialog(json.data,"alert",json.msg);return false;
		}
	})
}
/**
 * 增值项添加
 * @param obj 当前对象
 * @param action当前动作  add增加/del删除
 */
function add_payitem(obj,action,item_num){
	
	var item_id = parseInt($(obj).attr('item_id'))+0;
	var item_cash = parseFloat($(obj).attr('item_cash')*item_num);
	var item_name = $.trim($(obj).val());
	var item_code = $.trim($(obj).attr("item_code"));
	var total_cash = parseFloat($("#total").text().toString());//总金

	switch(action){
		case "add":
			$.post(basic_url,{ajax:"save_payitem",item_id:item_id,item_name:item_name,item_cash:item_cash,item_code:item_code,item_num:item_num},function(json){
				$("#total").text(json.msg);
			},'json')
			break;
		case "del":
			$.post(basic_url,{ajax:"rm_payitem",item_id:item_id},function(json){
					$("#total").text(json.msg);
			},'json')
			break;
	}
}
/**
 * 上传完成后的页面响应
 * @param json json数据
 */
function uploadResponse(json){
	if($("#"+json.fid).length<1){//判断是否已有同样的li、
		var file_ids = $("#file_ids").val();
		if(file_ids){
			$("#file_ids").val(file_ids+','+json.fid)
		}else{	
			$("#file_ids").val(json.fid);
		}
	}
   
}