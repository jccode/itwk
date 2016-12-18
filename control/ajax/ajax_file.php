<?php
 /**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 任务相关的ajax调用
 * 2011-10-08下午02:57:33
 */

defined ( 'IN_KEKE' ) or exit('Access Denied');

	switch ($ajax){
		case "load":
			if($work_id){
				$file_ids = db_factory::get_count(sprintf(" select work_file from %switkey_task_work where work_id='%d'",TABLEPRE,$work_id));
				$file_list = keke_task_class::get_work_file($file_ids);
			}
			break;
		case "download":
			keke_file_class::file_down($file_name, $file_path);
			break;
		case "delete":
			$res = keke_file_class::del_att_file($file_id, $filepath);
			$res and kekezu::echojson ( '', '1' ) or kekezu::echojson ( '', '0' );
			die ();
			break;
		case "del"://通过路径删除
			//三个条件任何一个不成就die掉
			if(strtolower($_SERVER['REQUEST_METHOD'])!='post' || !isset($fid) || !isset($filepath)){	
				kekezu::echojson ( '', '0' );die();
			}
			$fid = intval($fid);//file_id
			$size = kekezu::escape($size);//图片的不同尺寸
			$res = keke_file_class::del_att_file($fid,$filepath,$size);
			$res and kekezu::echojson ( '', 1 ) or kekezu::echojson ( '', '0' );
			die ();
		case "goods_filedown"://店铺文件下载
			$service_info = db_factory::get_one(sprintf(" select file_path,submit_method,uid from %switkey_service where service_id='%d'",TABLEPRE,$_GET['sid']));
			//检测是否购买过
			$has_buy = keke_shop_class::check_has_buy($_GET['sid'], $user_info['uid']);
			if($has_buy['order_status']=='confirm'||$service_info['uid']==$user_info['uid']){
				$file_list = explode(",",$service_info['file_path']);
			}
			break;
		case "help_search":
			$keyword and $search_list = db_factory::query(sprintf(" select a.art_title,a.content from %switkey_article a left join %switkey_article_category b on a.art_cat_id=b.art_cat_id where b.cat_type='help' and INSTR(a.art_title,'%s')",TABLEPRE,TABLEPRE,$keyword));
		break;
		case 'bubble'://信息气泡
			$info = kekezu::get_user_info($u_id);
			$indus_c_arr = $kekezu->_indus_c_arr;
			$ids  = explode(',',$info['skill_ids']);
			break;
	}
	
require keke_tpl_class::template("ajax/ajax_".$view);



