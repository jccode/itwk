<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$shows = array("list","add","del");
in_array($show,$shows) or $show="list";
$ac_url="index.php?do=user&view=space&op=member";
switch ($show){
	case "add":
		if($sbt_action){
			if($shop_info){
				$member_obj=keke_table_class::get_instance("witkey_shop_member");//成员实例
				if($_FILES['member_pic']['name']){
					$conf['member_pic'] = keke_file_class::upload_file ( "member_pic" );
				}else{
					$conf['member_pic'] = $old_pic;
				}
				//$conf['member_pic']= $hdn_member_pic ? $hdn_member_pic : null;
				//keke_file_class::upload_file('member_pic');
				$conf['shop_id']  =$shop_info['shop_id'];
				$res=$member_obj->save($conf,$pk);
				($res || $res==0) and kekezu::show_msg($_lang['members_operation_success'],$ac_url."&show=list#userCenter",3,'','success') or kekezu::show_msg( $_lang['members_operation_fail'],$ac_url."&show=add&member_id=$member_id",3,'','warning');
			}else{
				kekezu::show_msg($_lang['you_havenot_shop'],$ac_url."&show=list#userCenter",3,'','warning');
			}
		}else{
			$member_id and $member_info=db_factory::get_one(sprintf(" select * from %switkey_shop_member where member_id='%d'",TABLEPRE,$member_id));
		}
		break;
	case "list":
		if($ac=='del'){//删除
			$res=db_factory::execute(sprintf(" delete from %switkey_shop_member where member_id='%d'",TABLEPRE,$member_id));
			$res and kekezu::show_msg($_lang['delete_success'],$ac_url."&show=list#userCenter",3,'','success') or kekezu::show_msg($_lang['delete_fail'],$ac_url."&show=list#userCenter",3,'','success');
			die();
		}else{
			$member_obj=new Keke_witkey_shop_member_class();
			$where=" shop_id='{$shop_info['shop_id']}' ";
			
			$member_obj->setWhere($where);
			$count=intval($member_obj->count_keke_witkey_shop_member());
			/**成员信息**/
			$member_list=db_factory::get_table_data('*',"witkey_shop_member",$where,'member_id desc','','','member_id',0);
		}
		break;
}
// var_dump($member_info);
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
