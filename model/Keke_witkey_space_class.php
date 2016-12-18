<?php

  class Keke_witkey_space_class{

        public $_db;

        public $_tablename;

	    public $_dbop;

	    	 public $_uid;  
		 public $_username; 
		 public $_password; 
		 public $_sec_code; 
		 public $_email; 
		 public $_group_id; 
		 public $_isvip; 
		 public $_istop; 
		 public $_listorder; 
		 public $_status; 
		 public $_user_role;
		 public $_user_type; 
		 public $_sex; 
		 public $_residency; 
		 public $_address; 
		 public $_truename; 
		 public $_qq; 
		 public $_msn; 
		 public $_phone; 
		 public $_mobile; 
		 public $_skill_ids; 
		 public $_experience; 
		 public $_reg_time; 
		 public $_reg_ip; 
		 public $_domain; 
		 public $_credit; 
		 public $_balance; 
		 public $_balance_status; 
		 public $_pub_num; 
		 public $_take_num; 
		 public $_nominate_num; 
		 public $_accepted_num; 
		 public $_vip_start_time; 
		 public $_vip_end_time; 
		 public $_score; 
		 public $_buyer_credit; 
		 public $_buyer_good_num; 
		 public $_buyer_level; 
		 public $_buyer_total_num; 
		 public $_seller_credit; 
		 public $_seller_good_num; 
		 public $_seller_level; 
		 public $_seller_total_num; 
		 public $_last_login_time; 
		 public $_track_type; 
		 public $_track_uid; 
		 public $_track_username; 
		 public $_last_track_time; 
		 public $_track_reserve; 
		 public $_auth_realname; 
		 public $_auth_email; 
		 public $_auth_mobile; 
		 public $_auth_bank; 
		 public $_w_level; 
		 public $_e_level; 
		 public $_shop_id; 
		 public $_shop_name; 
		 public $_w_good_rate; 
		 public $_e_good_rate; 
		 public $_shop_level; 
		 public $_city_match; 
		 public $_union;
		 public $_union_assoc;
		 public $_union_rid;


	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );

	    public $_replace=0;

	    public $_where;

	     function  keke_witkey_space_class(){ 
			 $this->_db = new db_factory ( );
			 $this->_dbop = $this->_db->create(DBTYPE);
			 $this->_tablename = TABLEPRE."witkey_space";
		 }


	    public function getUid(){
			 return $this->_uid ;
		}
		public function getUsername(){
			 return $this->_username ;
		}
		public function getPassword(){
			 return $this->_password ;
		}
		public function getSec_code(){
			 return $this->_sec_code ;
		}
		public function getEmail(){
			 return $this->_email ;
		}
		public function getGroup_id(){
			 return $this->_group_id ;
		}
		public function getIsvip(){
			 return $this->_isvip ;
		}
		public function getIstop(){
			 return $this->_istop ;
		}
		public function getListorder(){
			 return $this->_listorder ;
		}
		public function getStatus(){
			 return $this->_status ;
		}
		public function getUser_type(){
			 return $this->_user_type ;
		}
		public function getUser_role(){
			 return $this->_user_role ;
		}
		public function getSex(){
			 return $this->_sex ;
		}
		public function getResidency(){
			 return $this->_residency ;
		}
		public function getAddress(){
			 return $this->_address ;
		}
		public function getTruename(){
			 return $this->_truename ;
		}
		public function getQq(){
			 return $this->_qq ;
		}
		public function getMsn(){
			 return $this->_msn ;
		}
		public function getPhone(){
			 return $this->_phone ;
		}
		public function getMobile(){
			 return $this->_mobile ;
		}
		public function getSkill_ids(){
			 return $this->_skill_ids ;
		}
		public function getExperience(){
			 return $this->_experience ;
		}
		public function getReg_time(){
			 return $this->_reg_time ;
		}
		public function getReg_ip(){
			 return $this->_reg_ip ;
		}
		public function getDomain(){
			 return $this->_domain ;
		}
		public function getCredit(){
			 return $this->_credit ;
		}
		public function getBalance(){
			 return $this->_balance ;
		}
		public function getBalance_status(){
			 return $this->_balance_status ;
		}
		public function getPub_num(){
			 return $this->_pub_num ;
		}
		public function getTake_num(){
			 return $this->_take_num ;
		}
		public function getNominate_num(){
			 return $this->_nominate_num ;
		}
		public function getAccepted_num(){
			 return $this->_accepted_num ;
		}
		public function getVip_start_time(){
			 return $this->_vip_start_time ;
		}
		public function getVip_end_time(){
			 return $this->_vip_end_time ;
		}
		public function getScore(){
			 return $this->_score ;
		}
		public function getBuyer_credit(){
			 return $this->_buyer_credit ;
		}
		public function getBuyer_good_num(){
			 return $this->_buyer_good_num ;
		}
		public function getBuyer_level(){
			 return $this->_buyer_level ;
		}
		public function getBuyer_total_num(){
			 return $this->_buyer_total_num ;
		}
		public function getSeller_credit(){
			 return $this->_seller_credit ;
		}
		public function getSeller_good_num(){
			 return $this->_seller_good_num ;
		}
		public function getSeller_level(){
			 return $this->_seller_level ;
		}
		public function getSeller_total_num(){
			 return $this->_seller_total_num ;
		}
		public function getLast_login_time(){
			 return $this->_last_login_time ;
		}
		public function getTrack_type(){
			 return $this->_track_type ;
		}
		public function getTrack_uid(){
			 return $this->_track_uid ;
		}
		public function getTrack_username(){
			 return $this->_track_username ;
		}
		public function getLast_track_time(){
			 return $this->_last_track_time ;
		}
		public function getTrack_reserve(){
			 return $this->_track_reserve ;
		}
		public function getAuth_realname(){
			 return $this->_auth_realname ;
		}
		public function getAuth_email(){
			 return $this->_auth_email ;
		}
		public function getAuth_mobile(){
			 return $this->_auth_mobile ;
		}
		public function getAuth_bank(){
			 return $this->_auth_bank ;
		}
		public function getW_level(){
			 return $this->_w_level ;
		}
		public function getE_level(){
			 return $this->_e_level ;
		}
		public function getShop_id(){
			 return $this->_shop_id ;
		}
		public function getShop_name(){
			 return $this->_shop_name ;
		}
		public function getW_good_rate(){
			 return $this->_w_good_rate ;
		}
		public function getE_good_rate(){
			 return $this->_e_good_rate ;
		}
		public function getShop_level(){
			 return $this->_shop_level ;
		}
		public function getCity_match(){
			 return $this->_city_match ;
		}
		public function getUnion_user(){
			return $this->_union_user ;
		}
		public function getUnion_assoc(){
			return $this->_union_assoc ;
		}
		public function getUnion_rid(){
			return $this->_union_rid ;
		}
		public function getWhere(){
			 return $this->_where ;
		}
		public function getCache_config() {
			return $this->_cache_config;
		}


	    		public function setUid($value){ 
			 $this->_uid = $value;
		}
		public function setUsername($value){ 
			 $this->_username = $value;
		}
		public function setPassword($value){ 
			 $this->_password = $value;
		}
		public function setSec_code($value){ 
			 $this->_sec_code = $value;
		}
		public function setEmail($value){ 
			 $this->_email = $value;
		}
		public function setGroup_id($value){ 
			 $this->_group_id = $value;
		}
		public function setIsvip($value){ 
			 $this->_isvip = $value;
		}
		public function setIstop($value){ 
			 $this->_istop = $value;
		}
		public function setListorder($value){ 
			 $this->_listorder = $value;
		}
		public function setStatus($value){ 
			 $this->_status = $value;
		}
		public function setUser_type($value){ 
			 $this->_user_type = $value;
		}
		public function setUser_role($value){ 
			 $this->_user_role = $value;
		}
		public function setSex($value){ 
			 $this->_sex = $value;
		}
		public function setResidency($value){ 
			 $this->_residency = $value;
		}
		public function setAddress($value){ 
			 $this->_address = $value;
		}
		public function setTruename($value){ 
			 $this->_truename = $value;
		}
		public function setQq($value){ 
			 $this->_qq = $value;
		}
		public function setMsn($value){ 
			 $this->_msn = $value;
		}
		public function setPhone($value){ 
			 $this->_phone = $value;
		}
		public function setMobile($value){ 
			 $this->_mobile = $value;
		}
		public function setSkill_ids($value){ 
			 $this->_skill_ids = $value;
		}
		public function setExperience($value){ 
			 $this->_experience = $value;
		}
		public function setReg_time($value){ 
			 $this->_reg_time = $value;
		}
		public function setReg_ip($value){ 
			 $this->_reg_ip = $value;
		}
		public function setDomain($value){ 
			 $this->_domain = $value;
		}
		public function setCredit($value){ 
			 $this->_credit = $value;
		}
		public function setBalance($value){ 
			 $this->_balance = $value;
		}
		public function setBalance_status($value){ 
			 $this->_balance_status = $value;
		}
		public function setPub_num($value){ 
			 $this->_pub_num = $value;
		}
		public function setTake_num($value){ 
			 $this->_take_num = $value;
		}
		public function setNominate_num($value){ 
			 $this->_nominate_num = $value;
		}
		public function setAccepted_num($value){ 
			 $this->_accepted_num = $value;
		}
		public function setVip_start_time($value){ 
			 $this->_vip_start_time = $value;
		}
		public function setVip_end_time($value){ 
			 $this->_vip_end_time = $value;
		}
		public function setScore($value){ 
			 $this->_score = $value;
		}
		public function setBuyer_credit($value){ 
			 $this->_buyer_credit = $value;
		}
		public function setBuyer_good_num($value){ 
			 $this->_buyer_good_num = $value;
		}
		public function setBuyer_level($value){ 
			 $this->_buyer_level = $value;
		}
		public function setBuyer_total_num($value){ 
			 $this->_buyer_total_num = $value;
		}
		public function setSeller_credit($value){ 
			 $this->_seller_credit = $value;
		}
		public function setSeller_good_num($value){ 
			 $this->_seller_good_num = $value;
		}
		public function setSeller_level($value){ 
			 $this->_seller_level = $value;
		}
		public function setSeller_total_num($value){ 
			 $this->_seller_total_num = $value;
		}
		public function setLast_login_time($value){ 
			 $this->_last_login_time = $value;
		}
		public function setTrack_type($value){ 
			 $this->_track_type = $value;
		}
		public function setTrack_uid($value){ 
			 $this->_track_uid = $value;
		}
		public function setTrack_username($value){ 
			 $this->_track_username = $value;
		}
		public function setLast_track_time($value){ 
			 $this->_last_track_time = $value;
		}
		public function setTrack_reserve($value){ 
			 $this->_track_reserve = $value;
		}
		public function setAuth_realname($value){ 
			 $this->_auth_realname = $value;
		}
		public function setAuth_email($value){ 
			 $this->_auth_email = $value;
		}
		public function setAuth_mobile($value){ 
			 $this->_auth_mobile = $value;
		}
		public function setAuth_bank($value){ 
			 $this->_auth_bank = $value;
		}
		public function setW_level($value){ 
			 $this->_w_level = $value;
		}
		public function setE_level($value){ 
			 $this->_e_level = $value;
		}
		public function setShop_id($value){ 
			 $this->_shop_id = $value;
		}
		public function setShop_name($value){ 
			 $this->_shop_name = $value;
		}
		public function setW_good_rate($value){ 
			 $this->_w_good_rate = $value;
		}
		public function setE_good_rate($value){ 
			 $this->_e_good_rate = $value;
		}
		public function setShop_level($value){ 
			 $this->_shop_level = $value;
		}
		public function setCity_match($value){ 
			 $this->_city_match = $value;
		}
		public function setUnion_user($value){
			$this->_union_user=$value;
		}
		public function setUnion_assoc($value){
			$this->_union_assoc=$value;
		}
		public function setUnion_rid($value){
			$this->_union_rid=$value;
		}
		public function setWhere($value){ 
			 $this->_where = $value;
		}
		public function setCache_config($_cache_config) {
			 $this->_cache_config = $_cache_config; 
		}


	    

    	   public  function __set($property_name, $value) {


		 		$this->$property_name = $value; 


			}


			public function __get($property_name) { 


				if (isset ( $this->$property_name )) { 


					return $this->$property_name; 


				} else { 


					return null; 


				} 


			}


    	

	    /**
		 * insert into  keke_witkey_space  ,or add new record
		 * @return int last_insert_id
		 */
		function create_keke_witkey_space(){
		 		 $data =  array(); 

					if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_password)){ 
				 $data['password']=$this->_password;
			}
			if(!is_null($this->_sec_code)){ 
				 $data['sec_code']=$this->_sec_code;
			}
			if(!is_null($this->_email)){ 
				 $data['email']=$this->_email;
			}
			if(!is_null($this->_group_id)){ 
				 $data['group_id']=$this->_group_id;
			}
			if(!is_null($this->_isvip)){ 
				 $data['isvip']=$this->_isvip;
			}
			if(!is_null($this->_istop)){ 
				 $data['istop']=$this->_istop;
			}
			if(!is_null($this->_listorder)){ 
				 $data['listorder']=$this->_listorder;
			}
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}
			if(!is_null($this->_user_type)){ 
				 $data['user_type']=$this->_user_type;
			}
			if(!is_null($this->_user_role)){ 
				 $data['user_role']=$this->_user_role;
			}
			if(!is_null($this->_sex)){ 
				 $data['sex']=$this->_sex;
			}
			if(!is_null($this->_residency)){ 
				 $data['residency']=$this->_residency;
			}
			if(!is_null($this->_address)){ 
				 $data['address']=$this->_address;
			}
			if(!is_null($this->_truename)){ 
				 $data['truename']=$this->_truename;
			}
			if(!is_null($this->_qq)){ 
				 $data['qq']=$this->_qq;
			}
			if(!is_null($this->_msn)){ 
				 $data['msn']=$this->_msn;
			}
			if(!is_null($this->_phone)){ 
				 $data['phone']=$this->_phone;
			}
			if(!is_null($this->_mobile)){ 
				 $data['mobile']=$this->_mobile;
			}
			if(!is_null($this->_skill_ids)){ 
				 $data['skill_ids']=$this->_skill_ids;
			}
			if(!is_null($this->_experience)){ 
				 $data['experience']=$this->_experience;
			}
			if(!is_null($this->_reg_time)){ 
				 $data['reg_time']=$this->_reg_time;
			}
			if(!is_null($this->_reg_ip)){ 
				 $data['reg_ip']=$this->_reg_ip;
			}
			if(!is_null($this->_domain)){ 
				 $data['domain']=$this->_domain;
			}
			if(!is_null($this->_credit)){ 
				 $data['credit']=$this->_credit;
			}
			if(!is_null($this->_balance)){ 
				 $data['balance']=$this->_balance;
			}
			if(!is_null($this->_balance_status)){ 
				 $data['balance_status']=$this->_balance_status;
			}
			if(!is_null($this->_pub_num)){ 
				 $data['pub_num']=$this->_pub_num;
			}
			if(!is_null($this->_take_num)){ 
				 $data['take_num']=$this->_take_num;
			}
			if(!is_null($this->_nominate_num)){ 
				 $data['nominate_num']=$this->_nominate_num;
			}
			if(!is_null($this->_accepted_num)){ 
				 $data['accepted_num']=$this->_accepted_num;
			}
			if(!is_null($this->_vip_start_time)){ 
				 $data['vip_start_time']=$this->_vip_start_time;
			}
			if(!is_null($this->_vip_end_time)){ 
				 $data['vip_end_time']=$this->_vip_end_time;
			}
			if(!is_null($this->_score)){ 
				 $data['score']=$this->_score;
			}
			if(!is_null($this->_buyer_credit)){ 
				 $data['buyer_credit']=$this->_buyer_credit;
			}
			if(!is_null($this->_buyer_good_num)){ 
				 $data['buyer_good_num']=$this->_buyer_good_num;
			}
			if(!is_null($this->_buyer_level)){ 
				 $data['buyer_level']=$this->_buyer_level;
			}
			if(!is_null($this->_buyer_total_num)){ 
				 $data['buyer_total_num']=$this->_buyer_total_num;
			}
			if(!is_null($this->_seller_credit)){ 
				 $data['seller_credit']=$this->_seller_credit;
			}
			if(!is_null($this->_seller_good_num)){ 
				 $data['seller_good_num']=$this->_seller_good_num;
			}
			if(!is_null($this->_seller_level)){ 
				 $data['seller_level']=$this->_seller_level;
			}
			if(!is_null($this->_seller_total_num)){ 
				 $data['seller_total_num']=$this->_seller_total_num;
			}
			if(!is_null($this->_last_login_time)){ 
				 $data['last_login_time']=$this->_last_login_time;
			}
			if(!is_null($this->_track_type)){ 
				 $data['track_type']=$this->_track_type;
			}
			if(!is_null($this->_track_uid)){ 
				 $data['track_uid']=$this->_track_uid;
			}
			if(!is_null($this->_track_username)){ 
				 $data['track_username']=$this->_track_username;
			}
			if(!is_null($this->_last_track_time)){ 
				 $data['last_track_time']=$this->_last_track_time;
			}
			if(!is_null($this->_track_reserve)){ 
				 $data['track_reserve']=$this->_track_reserve;
			}
			if(!is_null($this->_auth_realname)){ 
				 $data['auth_realname']=$this->_auth_realname;
			}
			if(!is_null($this->_auth_email)){ 
				 $data['auth_email']=$this->_auth_email;
			}
			if(!is_null($this->_auth_mobile)){ 
				 $data['auth_mobile']=$this->_auth_mobile;
			}
			if(!is_null($this->_auth_bank)){ 
				 $data['auth_bank']=$this->_auth_bank;
			}
			if(!is_null($this->_w_level)){ 
				 $data['w_level']=$this->_w_level;
			}
			if(!is_null($this->_e_level)){ 
				 $data['e_level']=$this->_e_level;
			}
			if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_shop_name)){ 
				 $data['shop_name']=$this->_shop_name;
			}
			if(!is_null($this->_w_good_rate)){ 
				 $data['w_good_rate']=$this->_w_good_rate;
			}
			if(!is_null($this->_e_good_rate)){ 
				 $data['e_good_rate']=$this->_e_good_rate;
			}
			if(!is_null($this->_shop_level)){ 
				 $data['shop_level']=$this->_shop_level;
			}
			if(!is_null($this->_city_match)){ 
				 $data['city_match']=$this->_city_match;
			}
			if(!is_null($this->_union_user)){
				$data['union_user']=$this->_union_user;
			}
			if(!is_null($this->_union_assoc)){
				$data['union_assoc']=$this->_union_assoc;
			}
			if(!is_null($this->_union_rid)){
				$data['_union_rid']=$this->_union_rid;
			}

			 return $this->_uid = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 
		 } 


	    /**
		 * edit table keke_witkey_space
		 * @return int affected_rows
		 */
		function edit_keke_witkey_space(){ 
		 		 $data =  array(); 
 
					if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_password)){ 
				 $data['password']=$this->_password;
			}
			if(!is_null($this->_sec_code)){ 
				 $data['sec_code']=$this->_sec_code;
			}
			if(!is_null($this->_email)){ 
				 $data['email']=$this->_email;
			}
			if(!is_null($this->_group_id)){ 
				 $data['group_id']=$this->_group_id;
			}
			if(!is_null($this->_isvip)){ 
				 $data['isvip']=$this->_isvip;
			}
			if(!is_null($this->_istop)){ 
				 $data['istop']=$this->_istop;
			}
			if(!is_null($this->_listorder)){ 
				 $data['listorder']=$this->_listorder;
			}
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}
			if(!is_null($this->_user_type)){ 
				 $data['user_type']=$this->_user_type;
			}
			if(!is_null($this->_user_role)){ 
				 $data['user_role']=$this->_user_role;
			}
			if(!is_null($this->_sex)){ 
				 $data['sex']=$this->_sex;
			}
			if(!is_null($this->_residency)){ 
				 $data['residency']=$this->_residency;
			}
			if(!is_null($this->_address)){ 
				 $data['address']=$this->_address;
			}
			if(!is_null($this->_truename)){ 
				 $data['truename']=$this->_truename;
			}
			if(!is_null($this->_qq)){ 
				 $data['qq']=$this->_qq;
			}
			if(!is_null($this->_msn)){ 
				 $data['msn']=$this->_msn;
			}
			if(!is_null($this->_phone)){ 
				 $data['phone']=$this->_phone;
			}
			if(!is_null($this->_mobile)){ 
				 $data['mobile']=$this->_mobile;
			}
			if(!is_null($this->_skill_ids)){ 
				 $data['skill_ids']=$this->_skill_ids;
			}
			if(!is_null($this->_experience)){ 
				 $data['experience']=$this->_experience;
			}
			if(!is_null($this->_reg_time)){ 
				 $data['reg_time']=$this->_reg_time;
			}
			if(!is_null($this->_reg_ip)){ 
				 $data['reg_ip']=$this->_reg_ip;
			}
			if(!is_null($this->_domain)){ 
				 $data['domain']=$this->_domain;
			}
			if(!is_null($this->_credit)){ 
				 $data['credit']=$this->_credit;
			}
			if(!is_null($this->_balance)){ 
				 $data['balance']=$this->_balance;
			}
			if(!is_null($this->_balance_status)){ 
				 $data['balance_status']=$this->_balance_status;
			}
			if(!is_null($this->_pub_num)){ 
				 $data['pub_num']=$this->_pub_num;
			}
			if(!is_null($this->_take_num)){ 
				 $data['take_num']=$this->_take_num;
			}
			if(!is_null($this->_nominate_num)){ 
				 $data['nominate_num']=$this->_nominate_num;
			}
			if(!is_null($this->_accepted_num)){ 
				 $data['accepted_num']=$this->_accepted_num;
			}
			if(!is_null($this->_vip_start_time)){ 
				 $data['vip_start_time']=$this->_vip_start_time;
			}
			if(!is_null($this->_vip_end_time)){ 
				 $data['vip_end_time']=$this->_vip_end_time;
			}
			if(!is_null($this->_score)){ 
				 $data['score']=$this->_score;
			}
			if(!is_null($this->_buyer_credit)){ 
				 $data['buyer_credit']=$this->_buyer_credit;
			}
			if(!is_null($this->_buyer_good_num)){ 
				 $data['buyer_good_num']=$this->_buyer_good_num;
			}
			if(!is_null($this->_buyer_level)){ 
				 $data['buyer_level']=$this->_buyer_level;
			}
			if(!is_null($this->_buyer_total_num)){ 
				 $data['buyer_total_num']=$this->_buyer_total_num;
			}
			if(!is_null($this->_seller_credit)){ 
				 $data['seller_credit']=$this->_seller_credit;
			}
			if(!is_null($this->_seller_good_num)){ 
				 $data['seller_good_num']=$this->_seller_good_num;
			}
			if(!is_null($this->_seller_level)){ 
				 $data['seller_level']=$this->_seller_level;
			}
			if(!is_null($this->_seller_total_num)){ 
				 $data['seller_total_num']=$this->_seller_total_num;
			}
			if(!is_null($this->_last_login_time)){ 
				 $data['last_login_time']=$this->_last_login_time;
			}
			if(!is_null($this->_track_type)){ 
				 $data['track_type']=$this->_track_type;
			}
			if(!is_null($this->_track_uid)){ 
				 $data['track_uid']=$this->_track_uid;
			}
			if(!is_null($this->_track_username)){ 
				 $data['track_username']=$this->_track_username;
			}
			if(!is_null($this->_last_track_time)){ 
				 $data['last_track_time']=$this->_last_track_time;
			}
			if(!is_null($this->_track_reserve)){ 
				 $data['track_reserve']=$this->_track_reserve;
			}
			if(!is_null($this->_auth_realname)){ 
				 $data['auth_realname']=$this->_auth_realname;
			}
			if(!is_null($this->_auth_email)){ 
				 $data['auth_email']=$this->_auth_email;
			}
			if(!is_null($this->_auth_mobile)){ 
				 $data['auth_mobile']=$this->_auth_mobile;
			}
			if(!is_null($this->_auth_bank)){ 
				 $data['auth_bank']=$this->_auth_bank;
			}
			if(!is_null($this->_w_level)){ 
				 $data['w_level']=$this->_w_level;
			}
			if(!is_null($this->_e_level)){ 
				 $data['e_level']=$this->_e_level;
			}
			if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_shop_name)){ 
				 $data['shop_name']=$this->_shop_name;
			}
			if(!is_null($this->_w_good_rate)){ 
				 $data['w_good_rate']=$this->_w_good_rate;
			}
			if(!is_null($this->_e_good_rate)){ 
				 $data['e_good_rate']=$this->_e_good_rate;
			}
			if(!is_null($this->_shop_level)){ 
				 $data['shop_level']=$this->_shop_level;
			}
			if(!is_null($this->_city_match)){ 
				 $data['city_match']=$this->_city_match;
			}
			if(!is_null($this->_union_user)){
				$data['union_user']=$this->_union_user;
			}
			if(!is_null($this->_union_assoc)){
				$data['union_assoc']=$this->_union_assoc;
			}
			if(!is_null($this->_union_rid)){
				$data['_union_rid']=$this->_union_rid;
			}

			if($this->_where){ 
				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 
			 } 
			 else{ 
				 $where = array('uid' => $this->_uid); 
				 return $this->_db->updatetable($this->_tablename,$data,$where); 
			} 
		 } 


	    /**
		 * query table: keke_witkey_space,if isset where return where record,else return all record
		 * @return array 
		 */
		function query_keke_witkey_space($is_cache=0, $cache_time=0){ 
			 if($this->_where){ 
				 $sql = "select * from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "select * from $this->_tablename"; 
			 } 
			if ($is_cache) {
			 $this->_cache_config ['is_cache'] = $is_cache;
			}
			if ($cache_time) {
			 $this->_cache_config ['time'] = $cache_time;
			 }
			 if ($this->_cache_config ['is_cache']) {
			     if (CACHE_TYPE) {
					 $keke_cache = new keke_cache_class ( CACHE_TYPE );
					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');
						$data = $keke_cache->get ( $id );
							if ($data) {
								return $data; 
							} else { 
								$res = $this->_dbop->query ( $sql ); 
								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 
					 			$this->_where = ""; 
								return $res;
 							} 
						} 
			 }else{
			 	$this->_where = ""; 
				return  $this->_dbop->query ( $sql );
 			 }
		 } 


	    /**
		 * query count keke_witkey_space records,if iset where query by where 
		 * @return int count records
		 */
		function count_keke_witkey_space(){ 
			 if($this->_where){ 
				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "select count(*) as count from $this->_tablename"; 
			 } 
			 $this->_where = ""; 
			 return $this->_dbop->getCount($sql); 
		 } 


	    /**
		 * delete table keke_witkey_space, if isset where delete by where 
		 * @return int deleted affected_rows 
		 */
		function del_keke_witkey_space(){ 
			 if($this->_where){ 
				 $sql = "delete from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "delete from $this->_tablename where uid = $this->_uid "; 
			 } 
			 $this->_where = ""; 
			 return $this->_dbop->execute($sql); 
		 } 








   }

 ?>