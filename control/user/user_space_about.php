<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 2.0
 * 2011-10-8下午06:42:39
 */

defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$opps = array ('intro', 'honor', 'power', 'report', 'contact' );
in_array ( $opp, $opps ) or $opp = 'intro';
$ac_url = $origin_url . "&op=".$op."&opp=".$opp;
$shop_obj = new Keke_witkey_shop_class ();
$shop_id = $shop_info['shop_id'];
switch ($opp) {
			case "intro" :
				if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($shop_intro) {
						$shop_intro = kekezu::escape ( $shop_intro );
						$shop_obj->setWhere("shop_id=".$shop_id);
						$shop_obj->setShop_intro($shop_intro);
						$res = $shop_obj->edit_keke_witkey_shop();						
						$res and kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
			case "honor" :
                 if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($shop_honor) {
						$shop_honor = kekezu::escape ( $shop_honor );
						$shop_obj->setWhere("shop_id=".$shop_id);
						$shop_obj->setShop_honor($shop_honor);
						$res = $shop_obj->edit_keke_witkey_shop();						
						$res and kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
		case "power" :
                if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($shop_power) {
						$shop_power = kekezu::escape ( $shop_power );
						$shop_obj->setWhere("shop_id=".$shop_id);
						$shop_obj->setShop_power($shop_power);
						$res = $shop_obj->edit_keke_witkey_shop();						
						$res and kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
		case "report" :
               if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($shop_report) {
						$shop_report = kekezu::escape ( $shop_report );
						$shop_obj->setWhere("shop_id=".$shop_id);
						$shop_obj->setShop_report($shop_report);
						$res = $shop_obj->edit_keke_witkey_shop();						
						$res and kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
		case "contact" :
              if (isset($formhash)&&kekezu::submitcheck($formhash)) {
					if ($contact_type) {
						$contact_type = kekezu::escape ( $contact_type );
						$shop_obj->setWhere("shop_id=".$shop_id);
						$shop_obj->setContact_type($contact_type);
						$res = $shop_obj->edit_keke_witkey_shop();						
						$res and kekezu::show_msg ( "操作提示", $ac_url . "&opp=$opp", '1', '编辑成功！', 'alert_right' ) ;
					}
				}
				break;
		}
		
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op);
