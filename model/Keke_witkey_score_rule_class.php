<?php
  class Keke_witkey_score_rule_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_score_rule_id;  		 public $_min_score; 		 public $_max_score; 		 public $_unit_count; 		 public $_unit_id; 		 public $_unit_title; 		 public $_unit_ico; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_score_rule_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_score_rule";		 }	    
	    		public function getScore_rule_id(){			 return $this->_score_rule_id ;		}		public function getMin_score(){			 return $this->_min_score ;		}		public function getMax_score(){			 return $this->_max_score ;		}		public function getUnit_count(){			 return $this->_unit_count ;		}		public function getUnit_id(){			 return $this->_unit_id ;		}		public function getUnit_title(){			 return $this->_unit_title ;		}		public function getUnit_ico(){			 return $this->_unit_ico ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setScore_rule_id($value){ 			 $this->_score_rule_id = $value;		}		public function setMin_score($value){ 			 $this->_min_score = $value;		}		public function setMax_score($value){ 			 $this->_max_score = $value;		}		public function setUnit_count($value){ 			 $this->_unit_count = $value;		}		public function setUnit_id($value){ 			 $this->_unit_id = $value;		}		public function setUnit_title($value){ 			 $this->_unit_title = $value;		}		public function setUnit_ico($value){ 			 $this->_unit_ico = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_score_rule  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_score_rule(){		 		 $data =  array(); 					if(!is_null($this->_score_rule_id)){ 				 $data['score_rule_id']=$this->_score_rule_id;			}			if(!is_null($this->_min_score)){ 				 $data['min_score']=$this->_min_score;			}			if(!is_null($this->_max_score)){ 				 $data['max_score']=$this->_max_score;			}			if(!is_null($this->_unit_count)){ 				 $data['unit_count']=$this->_unit_count;			}			if(!is_null($this->_unit_id)){ 				 $data['unit_id']=$this->_unit_id;			}			if(!is_null($this->_unit_title)){ 				 $data['unit_title']=$this->_unit_title;			}			if(!is_null($this->_unit_ico)){ 				 $data['unit_ico']=$this->_unit_ico;			}			 return $this->_score_rule_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_score_rule		 * @return int affected_rows		 */		function edit_keke_witkey_score_rule(){ 		 		 $data =  array();  					if(!is_null($this->_score_rule_id)){ 				 $data['score_rule_id']=$this->_score_rule_id;			}			if(!is_null($this->_min_score)){ 				 $data['min_score']=$this->_min_score;			}			if(!is_null($this->_max_score)){ 				 $data['max_score']=$this->_max_score;			}			if(!is_null($this->_unit_count)){ 				 $data['unit_count']=$this->_unit_count;			}			if(!is_null($this->_unit_id)){ 				 $data['unit_id']=$this->_unit_id;			}			if(!is_null($this->_unit_title)){ 				 $data['unit_title']=$this->_unit_title;			}			if(!is_null($this->_unit_ico)){ 				 $data['unit_ico']=$this->_unit_ico;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('score_rule_id' => $this->_score_rule_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_score_rule,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_score_rule($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_score_rule records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_score_rule(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_score_rule, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_score_rule(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where score_rule_id = $this->_score_rule_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>