<?php
/**
 * @copyright keke-tech
 * @author shangk
 * @version v 2.0
 * 2012-6-5 下午   17:32:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if($user_info['skill_ids']){
	$skill_ids = explode(',',$user_info['skill_ids']);
	if(is_array($skill_ids)){
		foreach ($skill_ids as  $k=> $v) {
			if($k==0){
				$user_skill.=$indus_c_arr[$v]['indus_name'];
			}else{
				$user_skill.=",".$indus_c_arr[$v]['indus_name'];
			}
		}
	}
}

$indus_map_json = json_encode($indus_map);

/**
 * 技能*
 */
switch ($ac) {
	case "get_skill" :
		$get_skill = db_factory::query( sprintf ( " select indus_id,indus_name from %switkey_industry where indus_pid='%d'", TABLEPRE, intval($indus_id)));
		if (isset($get_skill)&&$get_skill) {
			kekezu::echojson ( '1', '1', $get_skill );
		} else {
			kekezu::echojson ( '1', '0' );
		}
		die ();
		break;
	case "save_skill" :
		if(isset($skill_str)){
			$skill_str = kekezu::unescape ( $skill_str );
			
			$skill_str and $skill_arr = explode(',',$skill_str);
			//去重复  清除空值
			if($skill_arr){
				$temp = array();
				foreach($skill_arr as $k=>$v){
					$v and $temp[] = $v;
				}
				$skill_arr = $temp; 
				$skill_arr and $skill_str = implode(',',$skill_arr);
			}
			
			$res = db_factory::execute ( sprintf ( "update %switkey_space set skill_ids = '%s' where uid = '%d'", TABLEPRE,$skill_str, $uid ));
			
			
			if(is_array($skill_arr)){
				db_factory::execute( sprintf ( "delete from  %switkey_member_skill where uid = '%d'", TABLEPRE, $uid ));
				foreach ($skill_arr as  $v) {
					db_factory::inserttable(TABLEPRE."witkey_member_skill", array('uid'=>$uid,'skill_id'=>$v,'on_time'=>time()));
				}
			}
			$res and kekezu::echojson ( '1' ) or kekezu::echojson ( '0' );
		}
	
		break;
}


require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );