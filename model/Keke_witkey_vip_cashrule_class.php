<?php
  class Keke_witkey_vip_cashrule_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_id;  		 public $_level_id; 		 public $_period; 		 public $_price; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_vip_cashrule_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_vip_cashrule";		 }	    
	    		public function getId(){			 return $this->_id ;		}		public function getLevel_id(){			 return $this->_level_id ;		}		public function getPeriod(){			 return $this->_period ;		}		public function getPrice(){			 return $this->_price ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setId($value){ 			 $this->_id = $value;		}		public function setLevel_id($value){ 			 $this->_level_id = $value;		}		public function setPeriod($value){ 			 $this->_period = $value;		}		public function setPrice($value){ 			 $this->_price = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_vip_cashrule  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_vip_cashrule(){		 		 $data =  array(); 					if(!is_null($this->_id)){ 				 $data['id']=$this->_id;			}			if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			if(!is_null($this->_period)){ 				 $data['period']=$this->_period;			}			if(!is_null($this->_price)){ 				 $data['price']=$this->_price;			}			 return $this->_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_vip_cashrule		 * @return int affected_rows		 */		function edit_keke_witkey_vip_cashrule(){ 		 		 $data =  array();  					if(!is_null($this->_id)){ 				 $data['id']=$this->_id;			}			if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			if(!is_null($this->_period)){ 				 $data['period']=$this->_period;			}			if(!is_null($this->_price)){ 				 $data['price']=$this->_price;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('id' => $this->_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_vip_cashrule,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_vip_cashrule($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_vip_cashrule records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_vip_cashrule(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_vip_cashrule, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_vip_cashrule(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where id = $this->_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>