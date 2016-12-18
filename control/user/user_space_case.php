<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$shows = array("list","add");
in_array($show,$shows) or $show="list";
//echo 'sasa';exit;
$shop_info or kekezu::show_msg('需先开通商铺才能进行此操作', 'index.php?do=user&view=space&op=basic', 3, '', 'warning');

switch ($show){
	case "add":
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$case_obj=keke_table_class::get_instance("witkey_shop_case");//案例实例		
			$conf['shop_id']  = $shop_info['shop_id'];
			//图片
			$conf['case_pic'] = $case_pic;
			$conf['on_time'] = time();
			$res=$case_obj->save($conf,$pk);
			kekezu::show_msg ( "操作提示",$ac_url."&show=list#userCenter", '1',$_lang['case_operate_success'], 'alert_right' ) ;
			
		}else{
			$case_id and $case_info=db_factory::get_one(sprintf(" select * from %switkey_shop_case where case_id='%s'",TABLEPRE,$case_id));
		  //echo sprintf(" select * from %switkey_shop_case where case_id='%s'",TABLEPRE,$case_id) ;
		  	 //自定义分类列表
			$cate_list = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_cate where shop_id='{$user_info[shop_id]}' order by cate_id desc ");
		}
		switch ($ac){
			case "show_indus":
				$str_html="<option value='0'>".$_lang['please_select']."</option>";
				if ($indus_pid && $indus_arr [$indus_pid])
					foreach ($indus_arr [$indus_pid] as $v) {
						isset($indus_id)&&$indus_id==$v['indus_id'] and $selected=" selected " or $selected="";
						$str_html .="<option value='".$v['indus_id']."' ".$selected.">".$v['indus_name']."</option>";
					}
				
				echo $str_html;
				die ();
			break;
			case "show_service":
				$service_list=db_factory::query(sprintf(" select * from %switkey_service where shop_id='%d'",TABLEPRE,$shop_info['shop_id']));
				$str_html="<option value=''>".$_lang['please_select']."</option>";
				foreach ($service_list as $v) {
					$case_info['service_id']==$v['service_id'] and $selected=" selected " or $selected="";
					$str_html .="<option value='".$v['service_id']."' ".$selected.">".strip_tags($v['title'])."</option>";
				}
			
				echo $str_html;
				die ();
				break;
		}
		break;	
	case "list":
		if($ac=='del'){//删除
			$res=db_factory::execute(sprintf(" delete from %switkey_shop_case where case_id=%s",TABLEPRE,$case_id));
			kekezu::show_msg ( "操作提示","index.php?do=user&view=space&op=case&show=list#userCenter", '1','删除成功！', 'alert_right' ) ;
		}else{ 
			$page and $page = intval($page) or $page = 1;
			$url = $_K['siteurl']."/index.php?do=user&view=space&op=case&service_title=$service_title&start_time=$start_time&end_time=$end_time";			
			$where = " where shop_id='{$shop_info[shop_id]}'";
			$service_title and $where .= " and case_name like '%$service_title%'";
			$cate_id and $where .= " and cate_id = ".$cate_id;
			
			$start_time and $where .= " and on_time >= '".strtotime($start_time)."'";
			$end_time and $where .= " and on_time <= '".strtotime($end_time)."'";			
			$sql_count = "select count(*) from ".TABLEPRE."witkey_shop_case";
			$sql_count .= $where;   
			$count = db_factory::get_count($sql_count);					
			$sql = "select * from ".TABLEPRE."witkey_shop_case";
			$pages = $kekezu->_page_obj->getPages ( $count, 9, $page, $url ); 	
			$order=" order by listorder";		
			$sql .= $where.$order.$pages['where']; 
			$case_list = db_factory::query($sql);	
			 //自定义分类列表
			$cate_list = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_cate where shop_id='{$user_info[shop_id]}' order by cate_id desc ");
			$cate_list = func_case_cate_list($cate_list); 

		}
		break;
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );

 //格式化案例分类数组
function func_case_cate_list($list){
	$new_list = array();
	foreach($list as $v){
		$new_list[$v['cate_id']] = $v['cate_name'];
	}
	
	return $new_list;
}
