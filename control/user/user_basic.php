<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$opps = array ('basic', 'contact', 'skill', 'exp', 'cert' );
in_array ( $opp, $opps ) or $opp = 'basic';

$user_type = max ( 1, ( int ) $user_info ['user_type'] );

$ac_url = $origin_url . "&op=$op&opp=".$opp;
$ext_obj = new Keke_witkey_member_ext_class ();
switch ($opp) {
	case "basic" :
		$loca= explode ( ',', $user_info ['residency'] );
		$sect_info = kekezu::get_table_data ( "*", "witkey_member_ext", " type='sect' and uid='$uid' ", "", "", "", "k" );
		if (isset($formhash)&&kekezu::submitcheck($formhash)) {
			
			if ($sect) {
				foreach ( $sect as $k => $v ) {
					if ($sect_info [$k])
						db_factory::execute ( sprintf ( " update %switkey_member_ext set v1='%s' where k='%s' and uid='%d'", TABLEPRE, $v, $k, $uid ) );
					else {
						$ext_obj = new Keke_witkey_member_ext_class ();
						$ext_obj->_ext_id = null;
						$ext_obj->setK ( $k );
						$ext_obj->setV1 ( kekezu::escape ( $v ) );
						$ext_obj->setUid ( $uid );
						$ext_obj->setType ( 'sect' );
						$ext_obj->create_keke_witkey_member_ext ();
					}
				}
			}
			
			$space_obj = keke_table_class::get_instance ( 'witkey_space' );
			$province && $city and $conf ['residency'] = $province . ',' . $city.','.$area;
			db_factory::updatetable(TABLEPRE."witkey_member", array('email'=>$conf['email'],'mobile'=>$conf['mobile']), array('uid'=>$pk));
			$space_obj->save ( $conf, $pk );
			kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
		}
		break;

	case "cert" :
		/**
		 * 证书*
		 */
		$cert_info = db_factory::query ( sprintf ( " select * from %switkey_member_ext where uid = '%d' and type='cert'", TABLEPRE, $uid ) );
		if ($ac == 'del') {
			$cert_id = intval ( $cert_id );
			if ($cert_id) {
				$res = db_factory::execute ( sprintf ( " delete from %switkey_member_ext where ext_id= '%d' ", TABLEPRE, $cert_id ) );
				db_factory::execute ( sprintf ( " delete from %switkey_file where file_id='%d'", TABLEPRE, $f_id ) );
				if ($res) {
					kekezu::del_att_file ( $f_id );
					kekezu::echojson ( $_lang['delete_success'], "1" );
					die ();
				} else {
					kekezu::echojson ( $_lang['unknow_error_delete_fail'], "0" );
					die ();
				}
			} else {
				kekezu::echojson ( $_lang['delete_fail_select_null'], '0' );
				die ();
			}
		} elseif ($ac == "upload") {
			$ext_obj->_ext_id = null;
			$ext_obj->setUid ( $uid );
			CHARSET == 'gbk' and $v1 = kekezu::utftogbk ( $v1 );
			$ext_obj->setV1 ( kekezu::escape ( $v1 ) );
			$ext_obj->setV2 ( $v2 );
			$ext_obj->setV3 ( $v3 );
			$ext_obj->setType ( 'cert' );
			$ext_id = $ext_obj->create_keke_witkey_member_ext ();
			if ($ext_id) {
				kekezu::echojson ( $_lang['congratulations_save_succeed'], $ext_id );
				die ();
			} else {
				kekezu::echojson ( $_lang['error_save_fail'], "0" );
				die ();
			}
		}
		break;
	case "exp" :
		$exp_info = kekezu::get_table_data ( "*", "witkey_member_ext", " type='exp' and uid='$uid' " );
		$ext_obj = keke_table_class::get_instance ( "witkey_member_ext" );
		$today = date ( "Y-m-d", time () );
		switch ($ac) {
			case "del" :
				$res = $ext_obj->del ( 'ext_id', $ext_id );
				if ($res) {
					kekezu::echojson ( $_lang['delete_success'], "1" );
					die ();
				} else {
					kekezu::echojson ( $_lang['delete_fail'], "0" );
					die ();
				}
				$res and kekezu::show_msg ( $_lang['personal_exp_delete_success'], $ac_url . "&opp=$opp", '3', '', 'success' ) or kekezu::show_msg ( $_lang['personal_exp_delete_fail'], $ac_url . "&opp=$opp", '3', '', 'warning' );
				break;
			case "edit" :
				if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($ext_id) {
						$exp ['v4'] = time ();
						$pk ['ext_id'] = $ext_id;
						$exp = kekezu::escape ( $exp );
						$ext_obj->save ( $exp, $pk );
						kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					} else {
						kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '系统繁忙请稍后！', 'alert_error' ) ;
					}
				}
				break;
			case "add" :
				if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($exp) {
						$exp ['uid'] = $uid;
						$exp ['type'] = 'exp';
						$exp ['v4'] = time ();
						$exp = kekezu::escape ( $exp );
						$res = $ext_obj->save ( $exp );
						kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
		}
		break;
}

require keke_tpl_class::template ( "user/" . $do . "_" . $op . "_" . $opp );