<?php
  class Keke_witkey_member_bank_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_bank_id;  		 public $_uid; 		 public $_real_name; 		 public $_company; 		 public $_card_id; 		 public $_bank_name; 		 public $_bank_address; 		 public $_bank_sub_name; 		 public $_card_num; 		 public $_bank_type; 		 public $_bind_status; 		 public $_on_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_member_bank_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_member_bank";		 }	    
	    		public function getBank_id(){			 return $this->_bank_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getReal_name(){			 return $this->_real_name ;		}		public function getCompany(){			 return $this->_company ;		}		public function getCard_id(){			 return $this->_card_id ;		}		public function getBank_name(){			 return $this->_bank_name ;		}		public function getBank_address(){			 return $this->_bank_address ;		}		public function getBank_sub_name(){			 return $this->_bank_sub_name ;		}		public function getCard_num(){			 return $this->_card_num ;		}		public function getBank_type(){			 return $this->_bank_type ;		}		public function getBind_status(){			 return $this->_bind_status ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setBank_id($value){ 			 $this->_bank_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setReal_name($value){ 			 $this->_real_name = $value;		}		public function setCompany($value){ 			 $this->_company = $value;		}		public function setCard_id($value){ 			 $this->_card_id = $value;		}		public function setBank_name($value){ 			 $this->_bank_name = $value;		}		public function setBank_address($value){ 			 $this->_bank_address = $value;		}		public function setBank_sub_name($value){ 			 $this->_bank_sub_name = $value;		}		public function setCard_num($value){ 			 $this->_card_num = $value;		}		public function setBank_type($value){ 			 $this->_bank_type = $value;		}		public function setBind_status($value){ 			 $this->_bind_status = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_member_bank  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_member_bank(){		 		 $data =  array(); 					if(!is_null($this->_bank_id)){ 				 $data['bank_id']=$this->_bank_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_real_name)){ 				 $data['real_name']=$this->_real_name;			}			if(!is_null($this->_company)){ 				 $data['company']=$this->_company;			}			if(!is_null($this->_card_id)){ 				 $data['card_id']=$this->_card_id;			}			if(!is_null($this->_bank_name)){ 				 $data['bank_name']=$this->_bank_name;			}			if(!is_null($this->_bank_address)){ 				 $data['bank_address']=$this->_bank_address;			}			if(!is_null($this->_bank_sub_name)){ 				 $data['bank_sub_name']=$this->_bank_sub_name;			}			if(!is_null($this->_card_num)){ 				 $data['card_num']=$this->_card_num;			}			if(!is_null($this->_bank_type)){ 				 $data['bank_type']=$this->_bank_type;			}			if(!is_null($this->_bind_status)){ 				 $data['bind_status']=$this->_bind_status;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			 return $this->_bank_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_member_bank		 * @return int affected_rows		 */		function edit_keke_witkey_member_bank(){ 		 		 $data =  array();  					if(!is_null($this->_bank_id)){ 				 $data['bank_id']=$this->_bank_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_real_name)){ 				 $data['real_name']=$this->_real_name;			}			if(!is_null($this->_company)){ 				 $data['company']=$this->_company;			}			if(!is_null($this->_card_id)){ 				 $data['card_id']=$this->_card_id;			}			if(!is_null($this->_bank_name)){ 				 $data['bank_name']=$this->_bank_name;			}			if(!is_null($this->_bank_address)){ 				 $data['bank_address']=$this->_bank_address;			}			if(!is_null($this->_bank_sub_name)){ 				 $data['bank_sub_name']=$this->_bank_sub_name;			}			if(!is_null($this->_card_num)){ 				 $data['card_num']=$this->_card_num;			}			if(!is_null($this->_bank_type)){ 				 $data['bank_type']=$this->_bank_type;			}			if(!is_null($this->_bind_status)){ 				 $data['bind_status']=$this->_bind_status;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('bank_id' => $this->_bank_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_member_bank,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_member_bank($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_member_bank records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_member_bank(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_member_bank, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_member_bank(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where bank_id = $this->_bank_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>