<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-26 14:11
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$page_size and $page_size=intval($page_size) or $page_size=12;
$page and $page=intval($page) or $page=1;

$service_son_indus_arr = get_service_indus_arr($p);  //子分类 
$price_unit  = keke_glob_class::get_price_unit(); //价格单位
kekezu::cutstr($string, $length);

if($user_info['residency']){
	$loca = explode(',', $user_info['residency']);
}else{
	$loca = array('福建省','厦门市'); 
}

 //地区数据
$province and $province=urldecode($province) or $province=$loca['0'];
$city and $city=urldecode($city) or $city=$loca['1'];
isset($area) and $area=urldecode($area);
 //获取地区第三级信息
$area_arr = explode(',',kekezu::get_Area($province, $city));
 //地区数据修正
if(!in_array($area,$area_arr)){
	$area="";
}

//url sort
$url_param_arr = array('indus_id','o','order_type','page_size','province','city','area','page');
$ac_url_list = array();
foreach($url_param_arr as $urlp){
	if($urlp!='indus_id'){
		$temp_arr = array();
		if($indus_id){
			 $temp_arr[]="do=indus";
			 $temp_arr[] = "indus_id=$indus_id";
		}
		else{
			$temp_arr[]="do=service&view=n_list";//没有indus_id时不走目录解析
		}
		$urlp!='o' and $o and $temp_arr[] = "o=$o";
		$urlp!='order_type' and $temp_arr[] = "order_type=$order_type";
		$urlp!='page_size' and $page_size and $temp_arr[] = "page_size=$page_size";
		$urlp!='province' and $province and $temp_arr[] = "province=$province";
		$urlp!='city' and $city and $temp_arr[] = "city=$city";
		$urlp!='area' and $area and $temp_arr[] = "area=$area";
		$urlp!='page' and $page and $temp_arr[] = "page=$page";
		
		$url_str = $_K['siteurl']."/index.php?";
		$url_str .= implode('&',$temp_arr);
		
		$ac_url_list[$urlp] = $url_str;
	}
	else{
		$url_str = '';
		$o and $url_str .= "&o=$o";
		$order_type and $url_str .= "&order_type=$order_type";
		$page_size and $url_str .= "&page_size=$page_size";
		$province and $url_str .= "&province=$province";
		$city and $url_str .= "&city=$city";
		$area and $url_str .= "&area=$area";
		$page and $url_str .= "&page=$page"; 
		
		$ac_url_list[$urlp] = $url_str;
	}
}

 //地区地址
$city_box_url = $_K['siteurl']."/index.php?do=indus&indus_id=".$indus_id;

//$url = $ac_url."&c=$c&page_size=$page_size&order_type=$order_type&area=".urlencode($area);
//$url="index.php?do=service&view=n_list&p=$p&c=$c&o=$o&page_size=$page_size&$province=".urlencode($province)."&city=".urlencode($city)."&area=".urlencode($area);
//$ac_url = "index.php?do=indus&indus_id=$p&province=".urlencode($province)."&city=".urlencode($city);
//$url = $ac_url."&c=$c&page_size=$page_size&order_type=$order_type&area=".urlencode($area);

$service_obj = new Keke_witkey_task_class();
$page_obj = $kekezu->_page_obj;

$where = " model_id=4 and task_type=2";
if($p == '10yuan'){ 
	$where .= " and indus_pid in (575,586,593,602,609,616) and task_cash = 10";
}elseif(intval($p)){
	$where .= " and indus_pid=".$p;
}
if(intval($c)){ 
	$where .= " and indus_id=".$c;
}

$area_str = $province.",".$city;
$area && $area!="all" and $area_str .= ",".$area;
$area_str and $where .= " and city like '%".$area_str."%'";

 //排序
if($o){
	switch($o){
		case "1":
			$ord = " order by start_time desc";
			break;
		case "2":
			$ord = " order by start_time asc";
			break;
		case "3":
			$ord = " order by task_cash desc";
			break;
		case "4":
			$ord = " order by task_cash asc";
			break;
	}
}

$service_obj->setWhere($where);
$count = intval($service_obj->count_keke_witkey_task());

$pages = $page_obj->getPages($count, $page_size, $page, $url);

$service_obj->setWhere($where.$ord.$pages['where']);
$service_arr = $service_obj->query_keke_witkey_task();
//推荐需求列表
$service_rec_arr = db_factory::query(sprintf("select task_id,indus_pid,indus_id,task_title,task_desc,task_cash,unite_price from %switkey_task where model_id=4 and task_type=2 and indus_pid in (575,586,593,602,609,616) and is_top=1 order by task_cash desc limit 5",TABLEPRE),1,86400);

//SEO
switch($p){
	case '124': //生活服务
		$page_title = '生活服务 提供最新最全的生活服务类信息包括生活小常识、生活小技巧等信息_IT帮手网';
		$page_keyword = '生活服务，生活服务类，生活小常识，生活小技巧';
		$page_description ='提供生活服务类悬赏征集服务，无论是家庭理财规划、感情婚姻心理咨询，还是网上代购，甚至是生活小常识、生活小技巧等信息的征集都可以在这里实现。生活服务频道，提供最新最全的生活服务类信息';
	break;
	case '125': //商务服务
		$page_title = '商务服务 商务服务业，电子商务服务，商务服务公司_IT帮手网';
		$page_keyword = '商务服务，商务服务业，电子商务服务，商务服务公司';
		$page_description ='无论是征集商务服务、电子商务服务，还是您本身就是商务服务公司，提供商务服务业，您都可以在IT帮手网的商务服务频道发布需求任务或发布商务服务信息，IT帮手网一定能让您满意而回。';
	break;
	case '126': //招聘找人
		$page_title = '招聘找人  网上兼职招聘 发布招聘信息_IT帮手网';
		$page_keyword = '招聘找人，网上兼职招聘，发布招聘信息';
		$page_description ='想要找人、找对象、找供应商，或是发布招聘信息，招聘找人，寻找网上兼职招聘，只要是属于招聘找人的范畴，您都可以到IT帮手网招聘找人频道发布任务需求或服务信息。IT帮手网竭诚为您服务。';
	break;
	case '127': //网络钟点工
		$page_title = '网络钟点工  网络钟点工招聘 网络钟点工兼职_IT帮手网';
		$page_keyword = '网络钟点工，网络钟点工招聘，网络钟点工兼职';
		$page_description ='请全职不划算，想要网络钟点工招聘？或是想当网络钟点工，找网络钟点工兼职，进入威客行业做威客兼职？IT帮手网网络钟点工频道，让雇主找到合适的网络钟点工，让威客找到满意的网络钟点工兼职。';
	break;
	case '128': //旅游服务
		$page_title = '旅游服务 个性化旅游服务等旅游服务图片_IT帮手网';
		$page_keyword = '旅游服务，个性化旅游服务，旅游服务图片';
		$page_description ='旅游服务想玩个性化，公司旅游路线规划、旅游地产买什么好，或者是汽车自驾游、摩托车自驾游、征集或提供自驾旅游攻略等个性化旅游服务，只要与旅游服务相关，您都可以到IT帮手网旅游服务频道发布任务需求或服务信息。';
	break;
	case '129': //其他服务
		$page_title = '其他服务 创意设计服务包括图片处理、文案策划等信息_IT帮手网';
		$page_keyword = '创意设计，创意设计服务，图片处理，文案策划';
		$page_description ='想要网上找人进行创意设计服务、图片处理、文案策划，亲友生日想手机点歌送祝福；又或是你有提供家乡特产出售、甚至是提供宠物饲养服务，只要你能想得到的，需要的创意设计服务，都可以在IT帮手网上发布任务需求或服务信息。';
	break;
}
$c and $page_title = $service_indus_c_arr[$c]['indus_name'].' '.$page_title;

require keke_tpl_class::template ( SKIN_PATH . "/{$do}/{$do}_{$view}" );

function get_service_indus_arr($indus_pid){ 
	global $service_indus_c_arr;
	
	return keke_core_class::list_search($service_indus_c_arr, array('indus_pid' => $indus_pid));
}