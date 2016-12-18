<?php
 /**
 * @copyright keke-tech
 * @author Liyingqing
 * @version v 2.0
 * 2010-7-15 10:00:34
 */


defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );
$art_cat_obj = new Keke_witkey_article_category_class();

$need_index_art = kekezu::get_table_data('*',"witkey_article_category"," art_index='' or art_index is null","",'','','art_cat_id');

if ($op == "repair_index")
{
	$need_index_art = kekezu::get_table_data('*',"witkey_article_category",""," ",'','art_cat_id',null);
}
if ($need_index_art)
{
	$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category",""," listorder asc","",'',"art_cat_id",null);
	$record_id = '';//+
	foreach ($need_index_art as $n_art)
	{
		$flag = $n_art;
		$art_index = "";
		while ($flag['art_cat_pid'])
		{
			$art_index = "{{$flag['art_cat_pid']}}".$art_index;
			$flag = $art_cat_arr[$flag['art_cat_pid']];
		}
		$art_index = $art_index."{{$n_art['art_cat_id']}}";
		$art_cat_obj->setWhere("art_cat_id = {$n_art['art_cat_id']}");
		$art_cat_obj->setArt_index($art_index);
		$art_cat_obj->edit_keke_witkey_article_category();
		$record_id .= $n_art['art_cat_id'] . ',';//+
	}
 
    kekezu::admin_system_log($_lang['mulit_edit_cate'] . $record_id); //日志记录
}


$where = ' 1 = 1 ';

$types =  array ('help', 'art','case');

$type = (! empty ( $type ) && in_array ( $type, $types )) ? $type : 'art';

switch ( $type )
{
	case 'art':
		$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{1}%'"," art_cat_id desc",'','','art_cat_id',null);
		$where.=" and art_index like ('%{1}%') ";
		kekezu::admin_check_role(22);
		break;
		;
	case 'help':
		$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{100}%'"," art_cat_id desc",'','','art_cat_id',null);
		$where.=" and art_index like ('%{100}%') ";
		kekezu::admin_check_role(44);
		break;
		;
	case 'case';
		$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","art_index like '%{500}%'"," art_cat_id desc",'','','art_cat_id',null);
		$where.=" and art_index like ('%{500}%') ";
		kekezu::admin_check_role(14);
	break;
	;
}



if(isset($sbt_search)){
	intval($slt_cat_id) and  $where.=" and art_cat_id != $slt_cat_id and art_cat_id like '%{$slt_cat_id}%'";
	intval($txt_id) 	and  $where.=' and art_cat_id = '.$txt_id;
	$txt_title 			and  $where.=' and cat_name like '.'"%'.$txt_title.'%" ';
	$ordertype			and  $where.=' order by '.$ordertype.' '.$orderby.' ';
}



//查询结果数组
$where.=$pages['where'];

$art_cat_obj->setWhere($where);

$art_cat_show_arr = $art_cat_obj->query_keke_witkey_article_category();



$temp_arr = array();
kekezu::get_tree($art_cat_show_arr,$temp_arr,'list',NULL,'art_cat_id','art_cat_pid','cat_name');

$cat_show_arr = $temp_arr;
unset($temp_arr);
/*
┏
┣
   ┣
   	┗
   ┗
┣
┗
*/
//删除分类
if($ac=='del')
{
	if($art_cat_id)
	{
		if (in_array($art_cat_id,array(1,100,200,500))){kekezu::admin_show_msg($_lang['first_classification_can_not_be_deleted'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','warning');}
		
		$art_cat_obj->setWhere('art_cat_id='.$art_cat_id);
		$res = $art_cat_obj->del_keke_witkey_article_category();
		$kekezu->_cache_obj->del('keke_witkey_article_category');
		kekezu::admin_system_log($_lang['delete_cate'] . $art_cat_id); //日志记录
		kekezu::admin_show_msg($_lang['cate_delete_successfully'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','success');
	}
	else
	{
		kekezu::admin_show_msg($_lang['cate_does_not_exist_delete_fail'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','success');
	}
}

//批量操作
if (isset ( $sbt_action )) 
{
	$o_p = $rdo;
	$keyids = $ckb;
	//var_dump($keyids);die();
	 if(is_array($keyids))
	 {
	 	$ids = implode(',',$keyids);
	 }
	if ( ! count ( $keyids )) kekezu::admin_show_msg($_lang['choose_operate_item'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','warning');
	
	$art_cat_obj->setWhere(' art_cat_id in ('.$ids.') ');
	switch ($sbt_action) 
	{
 		case $_lang['mulit_delete'] : //批量删除
			$art_cat_obj->setWhere(' art_cat_id in ('.$ids.') and art_cat_id not in (1,100,200,500) ');
			$res = $art_cat_obj->del_keke_witkey_article_category();
			break;
			;
		default : 
			break;
			;
	}
	$kekezu->_cache_obj->del('keke_witkey_article_category');
	kekezu::admin_system_log($_lang['mulit_delete_cate'] . $ids ); //日志记录
	if($res)
	{
		kekezu::admin_show_msg($_lang['mulit_operate_success'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','success');//批量操作成功
	}
	else
	{
		kekezu::admin_show_msg($_lang['mulit_operate_fail'],'index.php?do='.$do.'&view='.$view.'&type='.$type,3,'','warning');
	}

}
if ($op == 'listorder') {
	$art_obj = new Keke_witkey_article_category_class();
	$art_obj->setWhere ( "art_cat_id='$art_cat_id'" );
	$art_obj->setListorder (intval($value));
	$art_obj->edit_keke_witkey_article_category(); 
	kekezu::admin_system_log($_lang['edit_cate_order'].$art_cat_id);
	die ();
} 

 
//递归分类列表
$temp_arr = array();
kekezu::get_tree($art_cat_arr,$temp_arr,'option',NULL,'art_cat_id','art_cat_pid','cat_name');
$cat_arr = $temp_arr;
unset($temp_arr);
 

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);