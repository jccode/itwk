<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-09-29 15:31:34
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$service_info = db_factory::get_one(sprintf("select * from %switkey_service where service_id='%d'",TABLEPRE,$service_id));
$price_unit  = keke_glob_class::get_price_unit();//价格单位
//保存编辑
if($sbt_edit){
	kekezu::admin_system_log($_lang['to_witkey_service_name_is'].$service_info[title].$_lang['in_edit_operate']);

	$service_obj = keke_table_class::get_instance('witkey_service');	
    $service=kekezu::escape($service); 
	$res = $service_obj->save($service,array("service_id"=>$service_id));
	$res and kekezu::admin_show_msg($_lang['service_edit_success'],"index.php?do=$do&view=service_list",2,$_lang['service_edit_success'],'success') or kekezu::admin_show_msg($_lang['service_edit_fail'],"index.php?do=$do&view=service_edit&service_id=".$service_id,2,$_lang['service_edit_fail'],'warning');
}

//行业
$industry_obj = new Keke_witkey_industry_class();
$industry_obj->setWhere ( " 1=1" ); 
$industry_arr = $industry_obj->query_keke_witkey_industry();
$industry_arr = list_to_tree($industry_arr);

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . "_" . $view );

function get_fid($path){//删除图片时获取图片对应的fid,图片的存放形式是e.g ...img.jpg?fid=1000
	if(!path){
		return false;
	}
	$querystring = substr(strstr($path, '?'), 1);
	parse_str($querystring, $query);
	return $query['fid'];
}

function list_to_tree($list, $pk='indus_id',$pid = 'indus_pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    
    return $tree;
}