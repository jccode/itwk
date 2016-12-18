<?php
  class Keke_witkey_shop_domain_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_d_id;  		 public $_d_key; 		 public $_shop_id; 		 public $_uid; 		 public $_username; 		 public $_d_status; 		 public $_d_type; 		 public $_on_time; 		 public $_op_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_domain_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_shop_domain";		 }	    
	    		public function getD_id(){			 return $this->_d_id ;		}		public function getD_key(){			 return $this->_d_key ;		}		public function getShop_id(){			 return $this->_shop_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getD_status(){			 return $this->_d_status ;		}		public function getD_type(){			 return $this->_d_type ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getOp_time(){			 return $this->_op_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setD_id($value){ 			 $this->_d_id = $value;		}		public function setD_key($value){ 			 $this->_d_key = $value;		}		public function setShop_id($value){ 			 $this->_shop_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setD_status($value){ 			 $this->_d_status = $value;		}		public function setD_type($value){ 			 $this->_d_type = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setOp_time($value){ 			 $this->_op_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_domain  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_domain(){		 		 $data =  array(); 					if(!is_null($this->_d_id)){ 				 $data['d_id']=$this->_d_id;			}			if(!is_null($this->_d_key)){ 				 $data['d_key']=$this->_d_key;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_d_status)){ 				 $data['d_status']=$this->_d_status;			}			if(!is_null($this->_d_type)){ 				 $data['d_type']=$this->_d_type;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_op_time)){ 				 $data['op_time']=$this->_op_time;			}			 return $this->_d_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_domain		 * @return int affected_rows		 */		function edit_keke_witkey_shop_domain(){ 		 		 $data =  array();  					if(!is_null($this->_d_id)){ 				 $data['d_id']=$this->_d_id;			}			if(!is_null($this->_d_key)){ 				 $data['d_key']=$this->_d_key;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_d_status)){ 				 $data['d_status']=$this->_d_status;			}			if(!is_null($this->_d_type)){ 				 $data['d_type']=$this->_d_type;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_op_time)){ 				 $data['op_time']=$this->_op_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('d_id' => $this->_d_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_domain,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_domain($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_domain records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_domain(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_domain, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_domain(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where d_id = $this->_d_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>