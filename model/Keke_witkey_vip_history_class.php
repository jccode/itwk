<?php
  class Keke_witkey_vip_history_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_h_id;  		 public $_uid; 		 public $_username; 		 public $_start_time; 		 public $_end_time; 		 public $_day; 		 public $_cash_cost; 		 public $_credit_cost; 		 public $_h_status; 
		 public $_remark; 		 public $_level_id; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_vip_history_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_vip_history";		 }	    
	    		public function getH_id(){			 return $this->_h_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getStart_time(){			 return $this->_start_time ;		}		public function getEnd_time(){			 return $this->_end_time ;		}		public function getDay(){			 return $this->_day ;		}		public function getCash_cost(){			 return $this->_cash_cost ;		}		public function getCredit_cost(){			 return $this->_credit_cost ;		}		public function getH_status(){			 return $this->_h_status ;		}
		public function getRemark(){
			 return $this->_remark ;
		}		public function getLevel_id(){			 return $this->_level_id ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setH_id($value){ 			 $this->_h_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setStart_time($value){ 			 $this->_start_time = $value;		}		public function setEnd_time($value){ 			 $this->_end_time = $value;		}		public function setDay($value){ 			 $this->_day = $value;		}		public function setCash_cost($value){ 			 $this->_cash_cost = $value;		}		public function setCredit_cost($value){ 			 $this->_credit_cost = $value;		}		public function setH_status($value){ 			 $this->_h_status = $value;		}
		public function setRemark($value){ 
			 $this->_remark = $value;
		}		public function setLevel_id($value){ 			 $this->_level_id = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_vip_history  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_vip_history(){		 		 $data =  array(); 					if(!is_null($this->_h_id)){ 				 $data['h_id']=$this->_h_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			if(!is_null($this->_day)){ 				 $data['day']=$this->_day;			}			if(!is_null($this->_cash_cost)){ 				 $data['cash_cost']=$this->_cash_cost;			}			if(!is_null($this->_credit_cost)){ 				 $data['credit_cost']=$this->_credit_cost;			}			if(!is_null($this->_h_status)){ 				 $data['h_status']=$this->_h_status;			}
			if(!is_null($this->_remark)){ 
				 $data['remark']=$this->_remark;
			}			if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			 return $this->_h_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_vip_history		 * @return int affected_rows		 */		function edit_keke_witkey_vip_history(){ 		 		 $data =  array();  					if(!is_null($this->_h_id)){ 				 $data['h_id']=$this->_h_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			if(!is_null($this->_day)){ 				 $data['day']=$this->_day;			}			if(!is_null($this->_cash_cost)){ 				 $data['cash_cost']=$this->_cash_cost;			}			if(!is_null($this->_credit_cost)){ 				 $data['credit_cost']=$this->_credit_cost;			}			if(!is_null($this->_h_status)){ 				 $data['h_status']=$this->_h_status;			}
			if(!is_null($this->_remark)){ 
				 $data['remark']=$this->_remark;
			}			if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('h_id' => $this->_h_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_vip_history,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_vip_history($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_vip_history records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_vip_history(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_vip_history, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_vip_history(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where h_id = $this->_h_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>