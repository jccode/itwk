<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2010-11-23 10:41:01
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

(!$user_info['uid']) and kekezu::show_msg('非法操作', $_K['siteurl'].'/index.php?do=login', 3, '请先登录再进行此操作！', 'warning');

switch($ac){
	case 'logo': //LOGO编辑
		switch($opp){
			case 'save':
				if($logo_pic){				
					$shop_obj = new Keke_witkey_shop_class();
					$shop_obj->setWhere("uid = $uid");
					$shop_obj->setLogo($logo_pic);
					$res = $shop_obj->edit_keke_witkey_shop();
					$res and kekezu::echojson ( '保存成功', "1" ) or kekezu::echojson ( '保存失败', "0" );					
				}else{
					kekezu::echojson ( '保存失败', "0" );
				}
				die();
			break;
			case 'del':			
				$shop_obj = new Keke_witkey_shop_class();
				$shop_obj->setWhere("uid = $uid");
				$shop_obj->setLogo('');
				$res = $shop_obj->edit_keke_witkey_shop();
				$res and kekezu::echojson ( '删除成功', "1" ) or kekezu::echojson ( '删除失败', "0" );
				die();
			break;
		}
		
		$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");
		$title = '编辑 - 上传logo图片';
	break;
	case 'banner': //其它页面中上部图片编辑
		$type=intval($type);
		//var_dump($type);die();
		$banner_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop_topimg where uid='$uid'");
		$banner_img = $banner_info['ban_'.$type];
		$title = '编辑 - 上传banner图片';
		switch($opp){
			case 'save':
				if($banner_pic){
					  $topimg_obj = new Keke_witkey_shop_topimg_class();
					  //var_dump($type);die();
					  switch($type){
					  	case "1":
					  		$topimg_obj->setBan_1($banner_pic);
					  		break;
					  	case "2":
					  		$topimg_obj->setBan_2($banner_pic);
					  		break;
					  	case "3":
					  		$topimg_obj->setBan_3($banner_pic);
					  		break;
					  	case "4":
					  		$topimg_obj->setBan_4($banner_pic);
					  		break;
					  	case "5":
					  		$topimg_obj->setBan_5($banner_pic);
					  		break;	
					  	case "6":
					  		$topimg_obj->setBan_6($banner_pic);
					  		break;				  
					  }
					  
					if($banner_info){            //有则为编辑更新即可		
					  $topimg_obj->setWhere("uid = $uid");
					 // $topimg_obj->setBan_1($banner_pic);			 
					  $res = $topimg_obj->edit_keke_witkey_shop_topimg();
					}else{                        //无信息则新建一条数据
					  $topimg_obj->setUid($uid);
					  $topimg_obj->setUsername($username);
					  $topimg_obj->setShop_id($user_info['shop_id']);
					  $topimg_obj->setOn_time(time());
					 // $topimg_obj->setBan_1($banner_pic);                     
					  $res = $topimg_obj->create_keke_witkey_shop_topimg();
					}									
					$res and kekezu::echojson ( '保存成功', "1" ) or kekezu::echojson ( '保存失败', "0" );					
				}else{
					kekezu::echojson ( '保存失败', "0" );
				}
				die();
			break;
			case 'del':			
				$topimg_obj = new Keke_witkey_shop_topimg_class();
				$topimg_obj->setWhere("uid = $uid");
		        switch($type){
					  	case "1":
					  		$topimg_obj->setBan_1('');
					  		break;
					  	case "2":
					  		$topimg_obj->setBan_2('');
					  		break;
					  	case "3":
					  		$topimg_obj->setBan_3('');
					  		break;
					  	case "4":
					  		$topimg_obj->setBan_4('');
					  		break;
					  	case "5":
					  		$topimg_obj->setBan_5('');
					  		break;	
					  	case "6":
					  		$topimg_obj->setBan_6('');
					  		break;				  
					  }
				$res = $topimg_obj->edit_keke_witkey_shop_topimg();
				$res and kekezu::echojson ( '删除成功', "1" ) or kekezu::echojson ( '删除失败', "0" );
				die();
			break;
		}
		
		
	break;
	case 'skin': //导航颜色 
		$skin_arr = array('orange', 'purple', 'green', 'blue');
		if(!$shop_style || !in_array($shop_style,$skin_arr)){
			$shop_style = 'orange';
		}
		
		$shop_skin_yuan = db_factory::get_count(" select shop_skin from " . TABLEPRE . "witkey_shop where uid='$uid' and shop_skin = '$shop_style'");
		if($shop_skin_yuan){ 
			kekezu::echojson ( '颜色定义成功', "1" );
			die();
		} 
		
		$shop_obj = new Keke_witkey_shop_class();
		$shop_obj->setShop_skin($shop_style);
		$shop_obj->setWhere(" uid = $uid");
		$res = $shop_obj->edit_keke_witkey_shop();
		$res and kekezu::echojson ( '颜色定义成功', "1" ) or kekezu::echojson ( '颜色定义失败', "0" );
		die();
	break;
	case 'slide': //幻灯片
		switch($opp){
			case 'save': //保存设置
				if($slide){
					foreach($slide as $key=> $val){ 
						if( !$val['s_pic'] ) continue;
						$shop_slide_obj = new Keke_witkey_shop_slide_class();
						$shop_slide_obj->setUid($user_info['uid']);
						$shop_slide_obj->setUsername($user_info['username']);
						$shop_slide_obj->setShop_id($user_info['shop_id']);
						$shop_slide_obj->setShop_name($user_info['shop_name']);									
						$shop_slide_obj->setListorder($val['listorder']);
						$shop_slide_obj->setS_pic($val['s_pic']);
						$shop_slide_obj->setS_url($val['s_url']);					
						$shop_slide_obj->setOn_time(time());
						
						if($val['s_id']){
							$shop_slide_obj->setWhere(" s_id = '$val[s_id]'");
							$shop_slide_obj->edit_keke_witkey_shop_slide();
						}else{
							$shop_slide_obj->create_keke_witkey_shop_slide();
						}
							
					}
				}
				
				kekezu::echojson ( '保存成功', "1" );
				die();
			break;
			case 'del': //删除图片				
				$shop_slide_obj = new Keke_witkey_shop_slide_class();
				$shop_slide_obj->setWhere(" uid = '$uid' AND s_id = '$s_id'");
				$res = $shop_slide_obj->del_keke_witkey_shop_slide();
				$res and kekezu::echojson ( '删除成功', "1" ) or kekezu::echojson ( '删除失败', "0" );
				die();
			break;
		}
		
		$slide_arr = db_factory::query(" select * from " . TABLEPRE . "witkey_shop_slide where shop_id=".$user_info['shop_id']." ORDER BY listorder");
		$title = '编辑 - 头部通栏950幻灯';
	break;
	case 'self_diy': //完全自定义区	
		if($is_btn){
			$shop_obj = new Keke_witkey_shop_class();
			$shop_obj->setShop_active($shop_active);
			$shop_obj->setWhere(" uid = $uid");
			$res = $shop_obj->edit_keke_witkey_shop();			
			//$res and kekezu::echojson ( '编辑成功', "1" ) or kekezu::echojson ( '编辑失败', "0" );
			if($res){
				echo 1;
			}else{
				echo 0;
			}
			die();
		}
		
		$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");
		
		$title = '编辑 - 左侧750完全自定义区';
	break;
	case 'qq': //联系客服
		if($is_btn){ //print_r($qq);exit;
			$shop_obj = new Keke_witkey_shop_class();
			if($qq){
				$shop_obj->setService_qq( serialize($qq) );	
			}else{
				$shop_obj->setService_qq( '' );	
			}
			
			$shop_obj->setWhere(" uid = $uid");
			$res = $shop_obj->edit_keke_witkey_shop();
			$res and kekezu::echojson ( '编辑成功', "1" ) or kekezu::echojson ( '编辑失败', "0" );
			die();
		}
		
		$shop_info = db_factory::get_one(" select * from " . TABLEPRE . "witkey_shop where uid='$uid'");
		$shop_info['service_qq'] and $qq = unserialize($shop_info['service_qq']);
		$title = '编辑 - 客服在线QQ';
	break;
}

require $template_obj->template ( 'ajax/ajax_shop_diy' );
		
if($opp == 'ajax_add'){
	$link_obj = keke_table_class::get_instance("witkey_link");
	$fds['uid'] = $user_info['uid'];
	$fds['username'] = $user_info['username'];
	$fds['on_time'] = time();
	$fds['link_status'] = 0;
	$fds['listorder'] = 0;
	$fds['link_name'] = $link_name;
	$fds['link_url'] = $link_url;
	$fds['location'] = link_make_tag( array(1=>1) );

	/*if($_FILES['link_pic']['name']){
		$fds['link_pic'] = keke_file_class::upload_file('link_pic');
	}*/
	
	$res = $link_obj->save($fds);	
	$res and kekezu::echojson ( '申请友情链接成功，请等待管理员审核', "1" ) or kekezu::echojson ( '操作失败', "0" );
	
	exit;
}

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}