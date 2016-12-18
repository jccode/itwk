<?php
/**
 * @author       Administrator
 */
keke_lang_class::load_lang_class('keke_auth_fac_class');
class keke_auth_fac_class {
	/**
	 * 获取用户认证记录
	 * @param $uid
	 */
	public static function user_auth_info($uid) {
		$auth_list = kekezu::get_table_data ( "*", "witkey_auth_record", "uid='$uid'", '', '', '', 'auth_code' );
		return $auth_list;
	}
	/**
	 * 获取某用户提交各项认证的记录信息
	 * @param int $uid
	 * @param int $auth_status 认证状态
	 */
	public static function get_submit_auth_record($uid,$auth_status='1'){
		$auth_item = keke_auth_base_class::get_auth_item();
		$auth_code = array_keys($auth_item);
		return self::auth_check($auth_code, $uid,$auth_status);
	}
	/**
	 * 获取认证图片
	 * @param $auth_code
	 * @param $uid user id 
	 * @return   $img_list
	 */
	public static function get_auth_imghtm($auth_code, $uid) {
		global $_lang;
		$auth_list = self::user_auth_info ( $uid );
		$config_list = self::get_auth_item_list();
		$img_list = '';
		foreach ( $config_list as $c ) {
			if (! $c ['auth_open'])
				continue;
			$str = '';
			$str .= '<img src="';
			$str .= 'data/uploads/' . 'ico/';
			$str .= $auth_list [$c ['auth_code']] ['auth_status'] ? $c ['auth_small_ico'] : $c ['auth_small_n_ico'];
			$str .= '" align="absmiddle" title="' . $c ['auth_title'];
			$str .= $auth_list [$c ['auth_code']] ['auth_status'] ? $_lang['has_pass'] : $_lang['not_pass'];
			$str .= '" width="15">&nbsp;';
			
			$img_list .= $str;
		
		}
		return $img_list;
	
	}
	/**
	 * 添加认证项目。 
	 * @param $auth_dir auth dir
	 * @return   void
	 */
	public static function install_auth($auth_dir) {
		global $_lang;
		$tab_obj       = keke_table_class::get_instance("witkey_auth_item");
		if ($auth_dir) {
			$file_path = S_ROOT . "./auth/" . $auth_dir . "/control/admin/auth_config_inc.php";
			if(file_exists ( $file_path )){
				require $file_path;
			$exists    = db_factory::get_count(" select auth_code from ".TABLEPRE."witkey_auth_item where auth_dir = '$auth_dir'");
			$exists and kekezu::admin_show_msg($_lang['auth_item_exist_add_fail'],$_SERVER['HTTP_REFERER'],'3','','error');
			
			$res=$tab_obj->save($auth_config);//安装
			
			if (file_exists ( S_ROOT . "./auth/" .$auth_dir. "/control/admin/install_sql.php" )) {
				require S_ROOT . "./auth/" . $auth_dir. "/control/admin/install_sql.php";
			}
			$res and kekezu::admin_system_log ( $_lang['add_auth_item'] . "$res" );
			$res and kekezu::admin_show_msg($_lang['auth_item_add_success'],$_SERVER['HTTP_REFERER'],'3');
			}else{
				
				kekezu::admin_show_msg($_lang['unknow_error_add_fail'],$_SERVER['HTTP_REFERER'],'3','','error');
			}
		} else {
			kekezu::admin_show_msg($_lang['unknow_error_add_fail'],$_SERVER['HTTP_REFERER'],'3','','error');
		}
	}
	/**
	 * 删除认证项目
	 * @param $auth_code auth item name  str/arr
	 * @return   void
	 */
	public static function del_auth($auth_code, $cash_name) {
		global $kekezu;
		global $_lang;
		$auth_item_obj = new Keke_witkey_auth_item_class();
		if (isset ( $auth_code )) {
			switch (is_array ( $auth_code )) {
				case "0" :
					$auth_item     = keke_auth_base_class::get_auth_item($auth_code);
					$auth_item['auth_small_ico']   and keke_file_class::del_file($auth_item['auth_small_ico']);
					$auth_item['auth_small_n_ico'] and keke_file_class::del_file($auth_item['auth_small_n_ico']); 
					$auth_item['auth_big_ico']     and keke_file_class::del_file($auth_item['auth_big_ico']); 
					$auth_item_obj->setWhere ("auth_code='$auth_code'" );
					$res = $auth_item_obj->del_keke_witkey_auth_item ();
					$res and $kekezu->_cache_obj->del ( $cash_name );
					$res and kekezu::admin_system_log ( $_lang['delete_auth_item'] . $auth_item['auth_title'] );
					
					if (file_exists ( S_ROOT . "./auth/" . $auth_item['auth_dir'] . "/control/admin/uninstall_sql.php" )) {
						require S_ROOT . "./auth/" . $auth_item['auth_dir'] . "/control/admin/uninstall_sql.php";
					}
					$res and kekezu::admin_show_msg($_lang['auth_item_delete_success_notice'],$_SERVER['HTTP_REFERER'],'3')	or kekezu::admin_show_msg($_lang['auth_item_delete_fail'],$_SERVER['HTTP_REFERER'],'3','','error');
					break;
				case "1" :
					$auth_code_str=implode(",",$auth_code);
					if (sizeof ( $auth_code_str )) {
						$auth_item_obj->setWhere ( " FIND_IN_SET(auth_code,'$auth_code_str')>0" );
						$res = $auth_item_obj->del_keke_witkey_auth_item ();
						$res and kekezu::admin_system_log ( $_lang['delete_auth_item']."$auth_code_str" );
						$res and kekezu::admin_show_msg($_lang['auth_item_mulit_delete_success'],$_SERVER['HTTP_REFERER'],'3') or $res and kekezu::admin_show_msg($_lang['auth_item_mulit_delete_fail'],$_SERVER['HTTP_REFERER'],'3','','error');
					}
					break;
			}
		}
	}
	/**
	 * 保存/编辑认证项
	 * @param $auth_code 			认证编码
	 * @param  $data  				外部传入认证项
	 * @param  $pk                  编辑项目
	 * @param  $big_ico_name		认证大图
	 * @param  $small_ico_name	 	认证小图
	 * @param  $small_n_ico_name	认证默认小图
	 * @param $conf                 扩展配置
	 */
	public static function edit_item($auth_code,$data,$pk=null,$big_ico_name=null,$small_ico_name=null,$small_n_ico_name=null,$conf=array()){
		global $kekezu;
		global $_lang;
		$auth_item     = keke_auth_base_class::get_auth_item($auth_code);
		$auth_item or kekezu::admin_show_msg($_lang['auth_item_edit_fail_notice'],"index.php?do=auth&view=item_list",'3','','error');
		$tab_obj       = keke_table_class::get_instance("witkey_auth_item");
		//echo $big_ico_name;
		$big_ico_name and $data['auth_big_ico'] = $big_ico_name=='delete' ? '' : $big_ico_name;//keke_file_class::upload_file($big_ico_name);//认证大图片上传
		$small_ico_name and $data['auth_small_ico'] = $small_ico_name=='delete' ? '' : $small_ico_name; //keke_file_class::upload_file($small_ico_name);//认证后图片上传
		$small_n_ico_name and $data['auth_small_n_ico'] = $small_n_ico_name=='delete' ? '' : $small_n_ico_name; //keke_file_class::upload_file($small_n_ico_name);//认证前图片上传
		
		$data['update_time'] = time();
// 		var_dump($data);
		$res=$tab_obj->save($data,$pk);//保存.编辑
		if($res){
			$kekezu->_cache_obj->del('auth_item_cache_list');
			kekezu::admin_system_log($_lang['edit_auth_item'].$auth_item['auth_title']);
			kekezu::admin_show_msg($_lang['auth_item_edit_success'],$_SERVER['HTTP_REFERER'],3,'','success');
		}else{
			kekezu::admin_show_msg($_lang['auth_item_edit_fail'],$_SERVER['HTTP_REFERER'],3,'','warning');
		}
	}
	/**
	 * 用户认证记录验证
	 * @param  $auth_code sting or array()认证类型 获取单一或多类
	 * @param  $uid 用户名
	 * @param  $auth_status 认证状态
	 * @return $auth_info
	 */
	public static function auth_check($auth_code,$uid,$auth_status='1'){
		if(!is_array($auth_code)){
			$auth_table=TABLEPRE."witkey_auth_".$auth_code;
			$data = db_factory::get_one(" select * from ".$auth_table."  where uid ='$uid' and auth_status='$auth_status'");
			return $data;
		}else{
			$t = implode(",",$auth_code);
			return db_factory::query(" select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico from ".TABLEPRE."witkey_auth_record a left join ".TABLEPRE."witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='$uid' and FIND_IN_SET(a.auth_code,'$t') and a.auth_status='$auth_status'",1,3600);
		}
	}
}