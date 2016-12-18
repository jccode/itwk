<?php
  class Keke_witkey_credit_record_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_r_id;  		 public $_uid; 		 public $_username; 		 public $_c_use; 		 public $_c_remark; 		 public $_credit; 		 public $_left_credit; 		 public $_on_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_credit_record_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_credit_record";		 }	    
	    		public function getR_id(){			 return $this->_r_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getC_use(){			 return $this->_c_use ;		}		public function getC_remark(){			 return $this->_c_remark ;		}		public function getCredit(){			 return $this->_credit ;		}		public function getLeft_credit(){			 return $this->_left_credit ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setR_id($value){ 			 $this->_r_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setC_use($value){ 			 $this->_c_use = $value;		}		public function setC_remark($value){ 			 $this->_c_remark = $value;		}		public function setCredit($value){ 			 $this->_credit = $value;		}		public function setLeft_credit($value){ 			 $this->_left_credit = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_credit_record  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_credit_record(){		 		 $data =  array(); 					if(!is_null($this->_r_id)){ 				 $data['r_id']=$this->_r_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_c_use)){ 				 $data['c_use']=$this->_c_use;			}			if(!is_null($this->_c_remark)){ 				 $data['c_remark']=$this->_c_remark;			}			if(!is_null($this->_credit)){ 				 $data['credit']=$this->_credit;			}			if(!is_null($this->_left_credit)){ 				 $data['left_credit']=$this->_left_credit;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			 return $this->_r_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_credit_record		 * @return int affected_rows		 */		function edit_keke_witkey_credit_record(){ 		 		 $data =  array();  					if(!is_null($this->_r_id)){ 				 $data['r_id']=$this->_r_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_c_use)){ 				 $data['c_use']=$this->_c_use;			}			if(!is_null($this->_c_remark)){ 				 $data['c_remark']=$this->_c_remark;			}			if(!is_null($this->_credit)){ 				 $data['credit']=$this->_credit;			}			if(!is_null($this->_left_credit)){ 				 $data['left_credit']=$this->_left_credit;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('r_id' => $this->_r_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_credit_record,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_credit_record($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_credit_record records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_credit_record(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_credit_record, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_credit_record(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where r_id = $this->_r_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>