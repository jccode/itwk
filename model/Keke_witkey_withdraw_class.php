<?php
  class Keke_witkey_withdraw_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_withdraw_id;  		 public $_withdraw_cash; 		 public $_uid; 		 public $_username; 		 public $_pay_username; 		 public $_withdraw_status; 		 public $_applic_time; 		 public $_process_uid; 		 public $_process_username; 		 public $_process_time; 		 public $_pay_account; 		 public $_pay_type; 
		 public $_fee;		 public $_bank_address; 		 public $_remark; 
		 public $_brand; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_withdraw_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_withdraw";		 }	    
	    		public function getWithdraw_id(){			 return $this->_withdraw_id ;		}		public function getWithdraw_cash(){			 return $this->_withdraw_cash ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getPay_username(){			 return $this->_pay_username ;		}		public function getWithdraw_status(){			 return $this->_withdraw_status ;		}		public function getApplic_time(){			 return $this->_applic_time ;		}		public function getProcess_uid(){			 return $this->_process_uid ;		}		public function getProcess_username(){			 return $this->_process_username ;		}		public function getProcess_time(){			 return $this->_process_time ;		}		public function getPay_account(){			 return $this->_pay_account ;		}		public function getPay_type(){			 return $this->_pay_type ;		}
		public function getFee(){
			 return $this->_fee ;
		}		public function getBank_address(){			 return $this->_bank_address ;		}		public function getRemark(){			 return $this->_remark ;		}
        public function getBrand(){
			 return $this->_brand ;
		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setWithdraw_id($value){ 			 $this->_withdraw_id = $value;		}		public function setWithdraw_cash($value){ 			 $this->_withdraw_cash = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setPay_username($value){ 			 $this->_pay_username = $value;		}		public function setWithdraw_status($value){ 			 $this->_withdraw_status = $value;		}		public function setApplic_time($value){ 			 $this->_applic_time = $value;		}		public function setProcess_uid($value){ 			 $this->_process_uid = $value;		}		public function setProcess_username($value){ 			 $this->_process_username = $value;		}		public function setProcess_time($value){ 			 $this->_process_time = $value;		}		public function setPay_account($value){ 			 $this->_pay_account = $value;		}		public function setPay_type($value){ 			 $this->_pay_type = $value;		}		public function setBank_address($value){ 			 $this->_bank_address = $value;		}
		public function setFee($value){ 
			 $this->_fee = $value;
		}		public function setRemark($value){ 			 $this->_remark = $value;		}
        public function setBrand($value){ 
			 $this->_brand = $value;
		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_withdraw  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_withdraw(){		 		 $data =  array(); 					if(!is_null($this->_withdraw_id)){ 				 $data['withdraw_id']=$this->_withdraw_id;			}			if(!is_null($this->_withdraw_cash)){ 				 $data['withdraw_cash']=$this->_withdraw_cash;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_pay_username)){ 				 $data['pay_username']=$this->_pay_username;			}			if(!is_null($this->_withdraw_status)){ 				 $data['withdraw_status']=$this->_withdraw_status;			}			if(!is_null($this->_applic_time)){ 				 $data['applic_time']=$this->_applic_time;			}			if(!is_null($this->_process_uid)){ 				 $data['process_uid']=$this->_process_uid;			}			if(!is_null($this->_process_username)){ 				 $data['process_username']=$this->_process_username;			}			if(!is_null($this->_process_time)){ 				 $data['process_time']=$this->_process_time;			}			if(!is_null($this->_pay_account)){ 				 $data['pay_account']=$this->_pay_account;			}			if(!is_null($this->_pay_type)){ 				 $data['pay_type']=$this->_pay_type;			}
			if(!is_null($this->_fee)){ 
				 $data['fee']=$this->_fee;
			}			if(!is_null($this->_bank_address)){ 				 $data['bank_address']=$this->_bank_address;			}			if(!is_null($this->_remark)){ 				 $data['remark']=$this->_remark;			}
		    if(!is_null($this->_brand)){ 
				 $data['brand']=$this->_brand;
			}			 return $this->_withdraw_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_withdraw		 * @return int affected_rows		 */		function edit_keke_witkey_withdraw(){ 		 		 $data =  array();  					if(!is_null($this->_withdraw_id)){ 				 $data['withdraw_id']=$this->_withdraw_id;			}			if(!is_null($this->_withdraw_cash)){ 				 $data['withdraw_cash']=$this->_withdraw_cash;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_pay_username)){ 				 $data['pay_username']=$this->_pay_username;			}			if(!is_null($this->_withdraw_status)){ 				 $data['withdraw_status']=$this->_withdraw_status;			}			if(!is_null($this->_applic_time)){ 				 $data['applic_time']=$this->_applic_time;			}			if(!is_null($this->_process_uid)){ 				 $data['process_uid']=$this->_process_uid;			}			if(!is_null($this->_process_username)){ 				 $data['process_username']=$this->_process_username;			}			if(!is_null($this->_process_time)){ 				 $data['process_time']=$this->_process_time;			}			if(!is_null($this->_pay_account)){ 				 $data['pay_account']=$this->_pay_account;			}			if(!is_null($this->_pay_type)){ 				 $data['pay_type']=$this->_pay_type;			}
			if(!is_null($this->_fee)){ 
				 $data['fee']=$this->_fee;
			}			if(!is_null($this->_bank_address)){ 				 $data['bank_address']=$this->_bank_address;			}			if(!is_null($this->_remark)){ 				 $data['remark']=$this->_remark;			}
		   if(!is_null($this->_brand)){ 
				 $data['brand']=$this->_brand;
			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('withdraw_id' => $this->_withdraw_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_withdraw,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_withdraw($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_withdraw records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_withdraw(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_withdraw, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_withdraw(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where withdraw_id = $this->_withdraw_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>