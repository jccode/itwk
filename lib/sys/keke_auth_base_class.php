<?php
/**
 * @author       Administrator
 */
keke_lang_class::load_lang_class('keke_auth_base_class');
abstract class keke_auth_base_class {
	public $_auth_item;
	
	public $_auth_code;
	public $_auth_name;
	public $_auth_obj;
	public $_auth_table_name;

	public $_tab_obj; //keke_table_obj对象
	public $_primary_key; //主键
	
	public $mes_title;
	public $mes_content;

	public function __construct($auth_code) {
		$this->_auth_code = $auth_code;
		$this->_auth_name = $auth_code . "_auth";
		$this->_auth_table_name = "witkey_auth_" . $auth_code;
	}
	/**
	 *获取认证配置详细
	 * @param $auth_code 认证名称 支持数组
	 * @param int $find_str 查找字段  可','号连接 必须含auth_code
	 * @param is_open 是否开启
	 * @param $w 其他条件
	 * @return 传人具体认证名时会返回一维数组 
	 */
	public static  function get_auth_item($auth_code=null,$find_str=null,$is_open=false,$w=null) {
		global $_cache_obj;
			$auth_code&&is_array($auth_code) and $auth_code=implode(",", $auth_code);
			$auth_code and ( is_array($auth_code) and  $where =" auth_code in ('$auth_code') " or $where =" auth_code = '$auth_code'" ) or $where = " 1 = 1";
			$find_str and $fds = $find_str or $fds = '*';
			$is_open and $where .= " and auth_open=1 ";
			$w      and $where.=" and ".$w;
			$auth_item=kekezu::get_table_data($fds,"witkey_auth_item", $where,'listorder asc','','','auth_code',null);
			if($auth_code&&!is_array($auth_code)){ 
				return $auth_item[$auth_code];
			}else{ 
				return $auth_item;
			}
				
				
	}
	
	/**
	 *space表验证状态更新
	 *  @author Aaron
	 *  @param $uid 要修改用户的UID
	 *  @param $type 要修改的验证类型
	 *  @param $status 最终修改验证的状态
	 *  @return 返回是否修改成功
	 */
	public static  function space_auth_status_update($obj_id, $obj_type, $type, $status){ 
		if(!$obj_id) return false;
		if(!$status) return false;
		if(!in_array($obj_type, array('uid', 'username'))) return false;
		if(!in_array($type, array('auth_realname', 'realname_auth', 'auth_email', 'email_auth', 'auth_mobile', 'mobile_auth', 'bank_auth', 'auth_bank'))) return false;
	
		$space_obj = new Keke_witkey_space_class (); 
		$space_obj->setwhere( " {$obj_type} = '{$obj_id}'" );
		$space = $space_obj->query_keke_witkey_space(); 
		if(!$space) return false;
	
		$space_obj->setwhere( " {$obj_type} = '{$obj_id}'" );
		switch($type){
			case 'auth_realname': 
				$space_obj->setAuth_realname($status);
			break;
			case 'realname_auth': 
				$space_obj->setAuth_realname($status);
			break;
			case 'auth_email':
				$space_obj->setAuth_email($status);
			break;
			case 'email_auth':
				$space_obj->setAuth_email($status);
			break;
			case 'auth_mobile':
				$space_obj->setAuth_mobile($status);
			break;
			case 'mobile_auth':
				$space_obj->setAuth_mobile($status);
			break;
			case 'auth_bank':
				$space_obj->setAuth_bank($status);
			break;
			case 'bank_auth':
				$space_obj->setAuth_bank($status);
			break;
		}
		
		return $space_obj->edit_keke_witkey_space();	
	}

	/**
	 * 更改认证记录状态
	 * @param  $auth_code 可为数组
	 * @param $uid 认证人id
	 * @param  $status
	 */
	public function set_auth_record_status($uid,$status) {
		return db_factory::execute(sprintf(" update %switkey_auth_record set auth_status = '%d' where auth_code= '%s' and uid = '%d'",TABLEPRE,$status,$this->_auth_code,$uid));
	}
	/**
	 * 更改认证详细状态
	 */
	public function set_auth_status($auth_id,$status){ 
		return db_factory::execute(sprintf(" update %s set auth_status = '$status' where %s = '%d'",TABLEPRE.$this->_auth_table_name,$this->_primary_key,$auth_id));
	}
	
	/**
	 * 添加/编辑 认证记录
	 * @param $auth_code 认证编码
	 * @param $uid 认证用户
	 * @param $username 认证用户名
	 * @param $end_time 认证失效时间  0 永久
	 * @param $data 认证详细数据
	 * @param $auth_status 默认记录状态
	 */
	public function add_auth_record($uid,$username, $auth_code,$end_time, $data = array(),$auth_status='0') {
		
		$record_obj  = new Keke_witkey_auth_record_class ();
		$record_info = db_factory::get_one(sprintf(" select * from %switkey_auth_record where uid = '%d' and auth_code = '%s'",TABLEPRE,$uid,$auth_code));
		
		if ($record_info ['ext_data'] && $data) {//追加认证data记录
			$odata  =  unserialize ( $record_info ['ext_data'] );
			$odata and $data = array_merge ( $odata, $data );
		}
		$record_obj->setAuth_code ( $auth_code );
		$record_obj->setUid($uid );
		$record_obj->setUsername($username );
		is_array($data) and $data=serialize($data);
		$data and $record_obj->setExt_data ($data);
		$record_obj->setEnd_time ($end_time);
		
		if ($record_info) {
			$record_obj->setWhere ( 'record_id = ' . $record_info ['record_id'] );
			return $record_obj->edit_keke_witkey_auth_record ();
		} else {
			$record_obj->setAuth_status ($auth_status);
			return $record_obj->create_keke_witkey_auth_record ();
		}
	}
	
	/**
	 * 删除认证记录
	 * @param $uid 认证用户
	 */
	public function del_auth_record($uid) {
		$res=db_factory::execute(sprintf(" delete from %switkey_auth_record where uid= '%d' and auth_code = '%s'",TABLEPRE,$uid,$this->_auth_code));
	}
	/**
	 * 认证申请数据处理
	 * @param $data 申请提交数据
	 */
	public function format_auth_apply($data){
		global $kekezu;
	
		$auth_info           = $this->get_auth_item($this->_auth_code,'auth_expir,auth_cash,auth_code');
		$data['uid']         = $kekezu->_userinfo['uid'];
		$data['username']    = $kekezu->_userinfo['username'];
		$data['start_time']  = time();
		$data['cash']        = $auth_info['auth_cash'];
		$data['auth_status'] = '0';
		$data['end_time']    = time()+$auth_info ['auth_expir'] * 3600 * 24;
		
		return $data;
	}
	/**
	 * 获取详细认证信息
	 * @param $auth_ids 认证项编号 可以为','连接的字符串
	 */
	public function get_auth_info($auth_ids){
		if(isset($auth_ids)){
			if(!stristr($auth_ids,',')) {
			 	return  db_factory::query(sprintf(" select * from %s where %s = '%s'",TABLEPRE.$this->_auth_table_name,$this->_primary_key,$auth_ids));
			}else{
				
				return db_factory::query(sprintf(" select * from %s where %s in (%s) ",TABLEPRE.$this->_auth_table_name,$this->_primary_key,$auth_ids));
			}
		}else{
			return array();
		}
	}
	/**
	 * 获取用户单条具体认证信息
	 * @param $uid 用户id
	 * @param $is_username 是否用户名
	 */
	public function get_user_auth_info($uid,$is_username=0,$show_id=''){
		$sql="select * from ".TABLEPRE.$this->_auth_table_name;
		if($uid){
			$is_username=='0' and $sql.=" where uid = '$uid' " or $sql.=" where username = '$uid' ";
			$show_id and $sql.=" and ".$this->_primary_key."=".$show_id;
			$sql .=" order by $this->_primary_key desc";
			$data = db_factory::query($sql);
			if(sizeof($data)==1){
				return $data[0];
			}else{
				return $data;
			}
		}else{
			return array();
		}
		
		
	}
	/**
	 *认证提示
	 */
	public function auth_lang(){
		global $_lang;
		$lang=array("realname"=>$_lang['realname_auth'],
					"bank"=>$_lang['bank_auth'],
					"email"=>$_lang['email_auth'],
					"enterprise"=>$_lang['enterprise_auth'],
					"mobile"=>$_lang['mobile_auth'],
					"weibo"=>$_lang['weibo_auth']);
		return $lang[$this->_auth_code];
	}
	
	/**
	 * 前台申请认证项目
	 * @param $data 外部传入认证数据
	 * @param $file_name 上传文件名 **请保持与数据库字段一致的命名
	 */
	abstract function add_auth($data,$file_name = '');
	/**
	 * 删除认证申请--支持批量删除
	 * @param $auth_ids 待删除认证项编号 可以为数组
	 * @see keke_auth_base_class::del_auth()
	 */
	public function del_auth($auth_ids) {
		global $_lang;
		
		is_array($auth_ids) and $ids=implode(",",$auth_ids) or $ids=$auth_ids;//数组连接
		$auth_info=$this->get_auth_info($ids);//获取实名认证表记录
		$size=sizeof($auth_info);
		$size==0 and kekezu::admin_show_msg($this->auth_lang(). $_lang['apply_not_exist_delete_fail'],$_SERVER['HTTP_REFERER']);
		
		if($size==1){//单条记录。单个删除 通过的记录无法被删除  &&$auth_info[0]['auth_status']!='1'
			/** 修改space验证状态**/		
			keke_auth_base_class::space_auth_status_update($auth_info[0]['uid'], 'uid', $this->_auth_name, ' ');
			$this->_tab_obj->del($this->_primary_key,$auth_ids);
			$res = $this->del_auth_record($auth_info[0]['uid']);//删除record记录
			/**企业认证删除时重置用户身份**/		
			$this->_auth_code=='enterprise' and $this->set_user_role($auth_info[0][uid],'not_pass');
		}elseif($size>1){//多条记录。多个删除
			$this->_tab_obj->del($this->_primary_key,$auth_ids);
			foreach ($auth_info as $v){
				/** 修改space验证状态**/		
				keke_auth_base_class::space_auth_status_update($v['uid'], 'uid', $this->_auth_name, ' ');
				$res = $this->del_auth_record($v['uid']);
				/**企业认证删除时重置用户身份**/		
				$this->_auth_code=='enterprise' and $this->set_user_role($v[uid],'not_pass');
			}
		}
		kekezu::empty_cache();
		$res && kekezu::admin_show_msg($this->auth_lang(). $_lang['apply_delete_success'],$_SERVER['HTTP_REFERER'],3,'','success');
		kekezu::admin_show_msg($this->auth_lang(). '删除失败',$_SERVER['HTTP_REFERER'],3,'','warning');
			
	}
	
	/**
	 * 设置发送认证不通过的信息
	 * @auther Aaron
	 * @param $mes_title 信息标题
	 * @param $mes_content 信息内容
	 */
	public function set_nopass_mes($mes_title, $mes_content){ 
		
	}
	
	/**
	 * 认证审核   支持批量
	 * @param $item_ids 认证项编号 可以为数组
	 * @param $type 审核类型
	 */
	public function review_auth($auth_ids,$type='pass'){ 
		global $_lang,$kekezu;
		is_array($auth_ids) and $auth_ids=implode(",",$auth_ids);//数组连接	
		$auth_info=$this->get_auth_info($auth_ids); //认证信息获取
	
		$size=sizeof($auth_info);
		$size>0&&$type=='pass' and $status='1' or $status='2';//待变更状态

		$size==0 and kekezu::admin_show_msg($this->auth_lang(). $_lang['apply_not_exist_audit_fail'],$_SERVER['HTTP_REFERER']);
		if($size==1){//已通过的认证无法操作  &&$auth_info[0]['auth_status']!='1'
			
			$this->set_auth_status($auth_info[0][$this->_primary_key],$status);
			$this->set_auth_record_status($auth_info[0]['uid'], $status); 
			/** 修改space验证状态**/		
			keke_auth_base_class::space_auth_status_update($auth_info[0]['uid'], 'uid', $this->_auth_name, $status);
			
		}elseif($size>1){
			foreach ($auth_info as $v){
				//if($v['auth_status']!='1'){//已通过的认证无法操作
					$this->set_auth_record_status($v['uid'], $status);
					$this->set_auth_status($v[$this->_primary_key],$status);
					/** 修改space验证状态**/		
					keke_auth_base_class::space_auth_status_update($v['uid'], 'uid', $this->_auth_name, $status); //Aaron
			
			}
		}  

		switch ($type){
			case "pass": 
				kekezu::admin_system_log($this->auth_lang(). $_lang['apply_pass'] . "$auth_ids");
				foreach ($auth_info as $v){
					 $feed_arr = array(	
				 		"feed_username"=>array("content"=>$v[username],"url"=>"index.php?do=shop&u_id=$v[uid]"),
						"action"=>array("content"=>$_lang['has_pass'],"url"=>""),
						"event"=>array("content"=>$this->auth_lang(),"url"=>"")
				 	);
					kekezu::save_feed($feed_arr, $v['uid'],$v['username'],$this->_auth_name ); 
					
					kekezu::notify_user ( $this->auth_lang(). $_lang['through'], $_lang['your'].$this->auth_lang().$_lang['has_pass_please_to']."<a href=\'index.php?do=user&view=payitem&op=auth&auth_code=".$this->_auth_code."\'>".$_lang['auth_center']."</a>".$_lang['view_detail'],$v['uid'],$v['username']);
					/**
			 		 * 推广
			 	 	*/
					if($status==1){
				 		$kekezu->init_prom();
				 		$kekezu->_prom_obj->dispose_prom_event('auth','',$v['uid']);
					}
				}
				kekezu::empty_cache();				
				kekezu::admin_show_msg($this->auth_lang().$_lang['apply_audit_success'],$_SERVER['HTTP_REFERER'],3,'','success');
				break;
			case "not_pass": 
				kekezu::admin_system_log($this->auth_lang().$_lang['apply_not_pass']."$auth_ids");
				
				$msg_obj = new keke_msg_class();
				foreach ($auth_info as $v){		//var_dump($this->_auth_name);die();
					if($this->_auth_name == 'realname_auth' && $this->mes_content){ //身份认证审核失败
						$msg_obj->setEmail_content('<p>'.$v['username'].'：</p>'.$this->mes_content); 
						$msg_obj->send_message ( $v['uid'], $v['username'], 'realname_auth_fail', '身份认证审核失败');
					}elseif($this->_auth_name == 'bank_auth' && $this->mes_content){  //银行认证失败						
						$msg_obj->setEmail_content('<p>'.$v['username'].'：</p>'.$this->mes_content); 
						$msg_obj->send_message ( $v['uid'], $v['username'], 'bank_auth_fail', '银行认证失败');
					}else{
						kekezu::notify_user ( $this->auth_lang().$_lang['fail'], $_lang['your'].$this->auth_lang().$_lang['not_pass_please_to']."<a href=\'index.php?do=user&view=payitem&op=auth&auth_code=".$this->_auth_code."\'>".$_lang['auth_center']."</a>".$_lang['view_detail'], $v['uid'] , $v['username']);
					}
				}
				kekezu::empty_cache(); 
				kekezu::admin_show_msg($this->auth_lang().$_lang['apply_audit_not_pass'],$_SERVER['HTTP_REFERER'].'&close=1',3,'','success');
				break;
		}
	}
	/**
	 * 企业认证时更新用户角色
	 * @param $action 动作  pass not_pass
	 * @param $uid  用户ID
	 */
	public function set_user_role($uid,$action='pass'){
		$action=='pass' and $user_role='2' or $user_role='1';
		db_factory::execute(sprintf(" update %switkey_space set user_type='%d' where uid='%d'",TABLEPRE,$user_role,$uid));
	}

}