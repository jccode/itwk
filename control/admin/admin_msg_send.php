<?php
/**
 * @copyright keke-tech
 * @author Chen
 * @version v 1.4
 * 2011-9-19上午10:15:13
 */
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(60);
require '../../keke_client/sms/postmsg.php';

$account_info=$kekezu->_sys_config;//手机账号信息
$mobile_u=$account_info['mobile_username'];
$mobile_p=$account_info['mobile_password']; 
switch ($ac){
	case "ser":
		//$type=='uid' and $where=" uid IN ($u) " or $where=" INSTR(username,'$u')>0 "; 
		//$user_info=db_factory::get_one(" select uid,username,phone,mobile,email from ".TABLEPRE."witkey_space where $where ");
		$where = send_user_arr($u, $type);
		if( !$where){
			kekezu::echojson($_lang['he_came_from_mars'], '3');
			die();
		}

		$space_obj = new Keke_witkey_space_class();  			
		$space_obj->setWhere ( $where );
		$user_info = $space_obj->query_keke_witkey_space();  
		$mobile = $email = array();
		foreach($user_info as $val){
			$val['email'] and $email[] = $val['email'];
			$val['mobile'] and $mobile[] = $val['mobile'];
		} 
		
		if(!$user_info){
			kekezu::echojson($_lang['he_came_from_mars'], '3');die();
		}else{
			if(!$mobile){
				kekezu::echojson($_lang['no_record_of_his_cellphone'], '2');die();
			}else{ 
				kekezu::echojson($_lang['success'], '1',array('mobile'=>implode(',', $mobile),'email'=>implode(',', $email))); die();
			}
		}
	break;
	case "send":      
		//$tar_content=strip_tags($tar_content);
		$sms_content = strip_tags($sms_content);
		$email_content = strip_tags($email_content);
		$mobile_content = strip_tags($mobile_content);
	
		switch ($slt_type=='specify'){
			case "1": //制定用户
				$msg_config = db_factory::get_one(" select * from ".TABLEPRE."witkey_msg_config where k = '{$msg_config}' ");
				$msg_obj = new keke_msg_class();

				if( $txt_u ) { 
					$space_obj = new Keke_witkey_space_class();  
					$where = send_user_arr($txt_u, $u_type);
					$space_obj->setWhere ( $where ); 
					$space_arr = $space_obj->query_keke_witkey_space ();  
				 	
					 //发送邮箱
					if($send_type[email]==1 && $email_content){
						$txt_email_arr = explode(',', $txt_email);
						foreach($txt_email_arr as $v_email){
							if(!$v_email) continue;							
							$email_user_info = get_email_user_info($v_email, $space_arr); 
							if($email_user_info){
								$msg_obj->setUid($email_user_info['uid']);		
								$msg_obj->setUsername($email_user_info['username']);
								$msg_obj->setEmail_content($email_content); 
								$msg_obj->send_message ( $email_user_info['uid'], $email_user_info['username'], $msg_config['k'], $send_title, array (), $v_email ); 
							}else{								
								kekezu::send_mail ($v_email, $send_title, $email_content);
							}
						}
					}

					 //发送站内信息
					if($send_type[sms]==1 && $sms_content){
						foreach($space_arr as $val)
							kekezu::notify_user ( $send_title, $sms_content , $val['uid'], $val['username']); 
					}
				}
				
				/*foreach($space_arr as $val){		
						$msg_obj->setUid($val['uid']);		
						$msg_obj->setUsername($val['username']);
						$msg_obj->setEmail_content($email_content);
						//$sms_content = $msg_obj->tpl_format($sms_content); 
						//$email_content = $msg_obj->tpl_format($email_content);								
						//($send_type[email]==1) and $res = kekezu::send_mail ($val['email'], $send_title, $email_content);  //电子邮件  
					 	($send_type[sms]==1 && $sms_content) and $res = kekezu::notify_user ( $send_title, $sms_content , $val['uid'], $val['username']); //站内信息 
						($send_type[email]==1 && $email_content) and $res = $msg_obj->send_message ( $val['uid'], $val['username'], $msg_config['k'], $send_title, array (), $val['email'] ); //电子邮件
						//($send_type[sms]==1) and $msg_obj->send_private_message ( $send_title, $sms_content, $val['uid'], $val['username'] ); //站内信息 
					}*/
				
				 //发送手机短信
				if($send_type[mobile] == 1 && $mobile_content){
					$mobile_content = $msg_obj->tpl_format($mobile_content);
					strpos($txt_tel, ",")>0 and $tel=explode(",",$txt_tel) or $tel=$txt_tel;
					is_array($tel) and $res=Msg_PostBlockNumber($mobile_u, $mobile_p, $tel, $mobile_content,'') or $res=Msg_PostSingle($mobile_u, $mobile_p, $tel, $mobile_content,'');
				}

				kekezu::admin_show_msg(Desc_ReturnInfo(0),"index.php?do=$do&view=$view",3,'','success');
			break;
			case "0": //全站用户
				$msg_config = db_factory::get_one(" select * from ".TABLEPRE."witkey_msg_config where k = '{$msg_config}' ");
				$msg_obj = new keke_msg_class();
				
				$slt_type=='vip' and $where="isvip='1'" or $where=" 1=1";		
				$tel_arr=db_factory::query(" select uid,username,email,mobile from ".TABLEPRE."witkey_space where $where"); //and mobile is not null 
				$tel_group=array(); 
				foreach ($tel_arr as $val){
					$msg_obj->setUid($val['uid']);		
					$msg_obj->setUsername($val['username']);
					$msg_obj->setEmail_content($email_content);
						
					($send_type[sms]==1 && $sms_content) and $res = kekezu::notify_user ( $send_title, $sms_content , $val['uid'], $val['username']); //站内信息 
					($send_type[email]==1 && $email_content && $val['email']) and $res = $msg_obj->send_message ( $val['uid'], $val['username'], $msg_config['k'], $send_title, array (), $val['email'] ); //电子邮件
					(!empty($val['mobile'])) and $tel_group[] = $val['mobile'];
				}				

				kekezu::admin_system_log($_lang['parameters']);
				 //手机短信
				if($mobile_content){
					$res=Msg_PostBlockNumber($mobile_u, $mobile_p, $tel_group, $mobile_content,'');
					kekezu::admin_show_msg(Desc_ReturnInfo($res),"index.php?do=$do&view=$view",3,'','success');
				}
			break;
		}
		break;
}

$msg_config_obj = new Keke_witkey_msg_config_class();  
$msg_config_obj->setWhere ( '  1 = 1' );
$msg_config_arr = $msg_config_obj->query_keke_witkey_msg_config ();  
$msg_config_arr = msg_config_arr_set($msg_config_arr); 

$msg_tpl_obj = new Keke_witkey_msg_tpl_class();
$msg_tpl_obj->setWhere ( '  1 = 1' );
$msg_tpl_arr = $msg_tpl_obj->query_keke_witkey_msg_tpl ();  

require $template_obj->template('control/admin/tpl/admin_'.$do.'_'.$view);

function msg_config_arr_set($arr){
	$new_arr = array();
	foreach($arr as $val){
		$new_arr[$val['k']] = $val;
	}
	
	return $new_arr;
}

function get_email_user_info($email, $space_arr){
	$res_arr = list_search($space_arr, array('email'=>$email)); 
	if($res_arr){
		$res_arr = $res_arr[0];
	}else{
		$space_obj = new Keke_witkey_space_class();  		
		$space_obj->setWhere ( " email = '{$email}')");
		$space_arr = $space_obj->query_keke_witkey_space ();
		$res_arr = $space_arr[0];
	}
	
	return $res_arr;
}

function list_search($list,$condition) { 
    if(is_string($condition))
        parse_str($condition,$condition);
     //返回的结果集合
    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]  =  &$list[$key];
    }
    
    return $resultSet;
}

 //过滤用户UID、username列表
function send_user_arr($list_str,$type){
	strpos($list_str, ",")>0 and $list=explode(",", $list_str) or $list=$list_str;
	switch($type){
		case 'uid': 
			if(is_array($list)){
				foreach($list as $k=>$v)
					$v and $list[$k] = intval($v);
					
				$list and $where = " uid IN(".implode(',',$list).")";
			}else{
				$list and $where = " uid =".intval($list);
			}
		break;
		case 'username': 
			if(is_array($list)){
				foreach($list as $k=>$v)
					$v and $list[$k] = "'".$v."'";
					
				$list and $where = " username IN(".implode(',',$list).")";
			}else{
				$list and $where = " username = '{$list}'";
			}
		break;
	}
	
	return $where;
}



