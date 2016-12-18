<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$shop_info or kekezu::show_msg('请先开通商铺', "index.php?do=user&view=space&op=basic", 3, '', 'warning');

$shows = array("list", "add", "edit");
in_array($show,$shows) or $show="list";
($ajax == 'add') and $show = 'ajax_add';
($ajax == 'list') and $show = 'ajax_list';

switch ($show){
	case "add": //添加分类	
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			//$user_info['shop_id'] or kekezu::show_msg('您还未成为VIP商铺,无法添加案例分类',$ac_url."&show=list",3,'','warning');
			$cate_obj=keke_table_class::get_instance("witkey_shop_cate"); //案例分类实例
			$cate_arr['shop_id'] = $user_info['shop_id'];
			$cate_arr['cate_name']=$conf[cate_name]; //var_dump($cate_arr);exit;
			$cate_arr['uid'] = $uid;
			$cate_arr['username'] = $username;
			$res = $cate_obj->save($cate_arr);
			kekezu::show_msg ( "操作提示",$ac_url."&show=list", '1', '编辑成功！', 'alert_right' ) ;
		}
	break;	
	case "ajax_add": //异步添加分类
		$shop_info['shop_id'] or kekezu::echojson ( '需先开通商铺，才能添加分类', "0" );
		if($shop_info['shop_id'] && $cate_name){			
			$cate_obj=keke_table_class::get_instance("witkey_shop_cate"); 
			$cate_arr['shop_id'] = $user_info['shop_id'];
			$cate_arr['cate_name']=$cate_name;
			$cate_arr['uid'] = $uid;
			$cate_arr['username'] = $username;
			$res = $cate_obj->save($cate_arr);
			//$res and kekezu::echojson ( $_lang['add_success'], $res ) or kekezu::echojson ( $_lang['add_fail'], 0 );
			echo $res ? $res : 0;
		}
		die();
	break;	
	case 'edit': //修改分类
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			$cate_obj=keke_table_class::get_instance("witkey_shop_cate"); //案例分类实例
			$pk = array('cate_id'=> $cate_id);
			$cate_arr['cate_name']=$conf[cate_name];
			$cate_arr['uid'] = $uid;
			$cate_arr['username'] = $username;
			$res = $cate_obj->save($cate_arr ,$pk);
			kekezu::show_msg ( "操作提示",$ac_url."&show=list", '1', '编辑成功！', 'alert_right' ) ;
		
		} else {
			$cate_obj=new Keke_witkey_shop_cate_class(); //
		
			$cate_obj->setWhere(" cate_id='{$cate_id}'");
			$cate_info = $cate_obj->query_keke_witkey_shop_cate(); 
			$cate_info = $cate_info[0];
		}
	break;
	case "ajax_list": //异步加载分类
		$cate_obj = new Keke_witkey_shop_cate_class();
		$where=" shop_id='{$user_info['shop_id']}' order by cate_id desc ";
		$cate_obj->setWhere($where);
		$cate_list = $cate_obj->query_keke_witkey_shop_cate(); 

		foreach($cate_list as $k=>$v){
			if($seleched_cate_id == $v['cate_id']){
				$html .= '<option value="'.$v[cate_id].'" selected>'.$v[cate_name].'</option>';
			}else{
				$html .= '<option value="'.$v[cate_id].'">'.$v[cate_name].'</option>';
			}
		}
		echo $html;
		die();
	break;	
	case "list":
		if($ac=='del'){//删除			
			//$res=db_factory::execute(" delete from ".TABLEPRE."witkey_shop_cate where cate_id=".$cate_id);
			$cate_obj=new Keke_witkey_shop_cate_class(); //		
			$cate_obj->setWhere(" cate_id='{$cate_id}'");
			$res = $cate_obj->del_keke_witkey_shop_cate();
			kekezu::show_msg ( "操作提示",$ac_url."&show=list", '1', '删除成功！', 'alert_right' ) ;
		}else{
			$cate_obj=new Keke_witkey_shop_cate_class();
			$page_obj=$kekezu->_page_obj;
			$where=" shop_id='{$user_info['shop_id']}' order by cate_id desc ";
			intval($page) or $page='1';
			intval($page_size) or $page_size='10';
			$url=$ac_url."&show=list&page_size=$page_size&page=$page";
			/**分页**/
			$cate_obj->setWhere($where);
			$count=intval($cate_obj->count_keke_witkey_shop_cate());
			$pages=$page_obj->getPages($count, $page_size, $page, $url,'#userCenter');
			/**案例信息**/
			$cate_obj->setWhere($where.$pages['where']);
			$cate_list=$cate_obj->query_keke_witkey_shop_cate(); 
		}
		break;
}

require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
