<?php
  class Keke_witkey_shop_match_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_m_id;  		 public $_uid; 		 public $_username; 		 public $_shop_id; 		 public $_shop_name; 		 public $_m_status; 		 public $_start_time; 		 public $_end_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_match_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_shop_match";		 }	    
	    		public function getM_id(){			 return $this->_m_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getShop_id(){			 return $this->_shop_id ;		}		public function getShop_name(){			 return $this->_shop_name ;		}		public function getM_status(){			 return $this->_m_status ;		}		public function getStart_time(){			 return $this->_start_time ;		}		public function getEnd_time(){			 return $this->_end_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setM_id($value){ 			 $this->_m_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setShop_id($value){ 			 $this->_shop_id = $value;		}		public function setShop_name($value){ 			 $this->_shop_name = $value;		}		public function setM_status($value){ 			 $this->_m_status = $value;		}		public function setStart_time($value){ 			 $this->_start_time = $value;		}		public function setEnd_time($value){ 			 $this->_end_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_match  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_match(){		 		 $data =  array(); 					if(!is_null($this->_m_id)){ 				 $data['m_id']=$this->_m_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_shop_name)){ 				 $data['shop_name']=$this->_shop_name;			}			if(!is_null($this->_m_status)){ 				 $data['m_status']=$this->_m_status;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			 return $this->_m_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_match		 * @return int affected_rows		 */		function edit_keke_witkey_shop_match(){ 		 		 $data =  array();  					if(!is_null($this->_m_id)){ 				 $data['m_id']=$this->_m_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_shop_name)){ 				 $data['shop_name']=$this->_shop_name;			}			if(!is_null($this->_m_status)){ 				 $data['m_status']=$this->_m_status;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('m_id' => $this->_m_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_match,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_match($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_match records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_match(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_match, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_match(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where m_id = $this->_m_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>