<?php
  class Keke_witkey_vip_level_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_level_id;  		 public $_level_name; 		 public $_level_desc; 		 public $_allow_buy; 		 public $_listorder; 		 public $_price_config; 		 public $_rule_config; 
		 public $_brand;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_vip_level_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_vip_level";		 }	    
	    		public function getLevel_id(){			 return $this->_level_id ;		}		public function getLevel_name(){			 return $this->_level_name ;		}		public function getLevel_desc(){			 return $this->_level_desc ;		}		public function getAllow_buy(){			 return $this->_allow_buy ;		}		public function getListorder(){			 return $this->_listorder ;		}		public function getPrice_config(){			 return $this->_price_config ;		}		public function getRule_config(){			 return $this->_rule_config ;		}
		public function getBrand() {
			return $this->_brand;
		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setLevel_id($value){ 			 $this->_level_id = $value;		}		public function setLevel_name($value){ 			 $this->_level_name = $value;		}		public function setLevel_desc($value){ 			 $this->_level_desc = $value;		}		public function setAllow_buy($value){ 			 $this->_allow_buy = $value;		}		public function setListorder($value){ 			 $this->_listorder = $value;		}		public function setPrice_config($value){ 			 $this->_price_config = $value;		}		public function setRule_config($value){ 			 $this->_rule_config = $value;		}
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
    	
	    /**		 * insert into  keke_witkey_vip_level  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_vip_level(){		 		 $data =  array(); 					if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			if(!is_null($this->_level_name)){ 				 $data['level_name']=$this->_level_name;			}			if(!is_null($this->_level_desc)){ 				 $data['level_desc']=$this->_level_desc;			}			if(!is_null($this->_allow_buy)){ 				 $data['allow_buy']=$this->_allow_buy;			}			if(!is_null($this->_listorder)){ 				 $data['listorder']=$this->_listorder;			}			if(!is_null($this->_price_config)){ 				 $data['price_config']=$this->_price_config;			}			if(!is_null($this->_rule_config)){ 				 $data['rule_config']=$this->_rule_config;			}
			if(!is_null($this->_brand)){
				$data['brand']=$this->_brand;
			}			 return $this->_level_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_vip_level		 * @return int affected_rows		 */		function edit_keke_witkey_vip_level(){ 		 		 $data =  array();  					if(!is_null($this->_level_id)){ 				 $data['level_id']=$this->_level_id;			}			if(!is_null($this->_level_name)){ 				 $data['level_name']=$this->_level_name;			}			if(!is_null($this->_level_desc)){ 				 $data['level_desc']=$this->_level_desc;			}			if(!is_null($this->_allow_buy)){ 				 $data['allow_buy']=$this->_allow_buy;			}			if(!is_null($this->_listorder)){ 				 $data['listorder']=$this->_listorder;			}			if(!is_null($this->_price_config)){ 				 $data['price_config']=$this->_price_config;			}			if(!is_null($this->_rule_config)){ 				 $data['rule_config']=$this->_rule_config;			}
			if(!is_null($this->_brand)){
				$data['brand']=$this->_brand;
			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('level_id' => $this->_level_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_vip_level,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_vip_level($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_vip_level records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_vip_level(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_vip_level, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_vip_level(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where level_id = $this->_level_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>