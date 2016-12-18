<?php
  class Keke_witkey_rule_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_rule_id;  		 public $_rule_key; 		 public $_rule_group; 		 public $_rule; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_rule_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_rule";		 }	    
	    		public function getRule_id(){			 return $this->_rule_id ;		}		public function getRule_key(){			 return $this->_rule_key ;		}		public function getRule_group(){			 return $this->_rule_group ;		}		public function getRule(){			 return $this->_rule ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setRule_id($value){ 			 $this->_rule_id = $value;		}		public function setRule_key($value){ 			 $this->_rule_key = $value;		}		public function setRule_group($value){ 			 $this->_rule_group = $value;		}		public function setRule($value){ 			 $this->_rule = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_rule  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_rule(){		 		 $data =  array(); 					if(!is_null($this->_rule_id)){ 				 $data['rule_id']=$this->_rule_id;			}			if(!is_null($this->_rule_key)){ 				 $data['rule_key']=$this->_rule_key;			}			if(!is_null($this->_rule_group)){ 				 $data['rule_group']=$this->_rule_group;			}			if(!is_null($this->_rule)){ 				 $data['rule']=$this->_rule;			}			 return $this->_rule_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_rule		 * @return int affected_rows		 */		function edit_keke_witkey_rule(){ 		 		 $data =  array();  					if(!is_null($this->_rule_id)){ 				 $data['rule_id']=$this->_rule_id;			}			if(!is_null($this->_rule_key)){ 				 $data['rule_key']=$this->_rule_key;			}			if(!is_null($this->_rule_group)){ 				 $data['rule_group']=$this->_rule_group;			}			if(!is_null($this->_rule)){ 				 $data['rule']=$this->_rule;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('rule_id' => $this->_rule_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_rule,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_rule($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_rule records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_rule(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_rule, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_rule(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where rule_id = $this->_rule_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>