<?php
  class Keke_witkey_score_log_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_score_log_id;  		 public $_score_log_type; 		 public $_uid; 		 public $_score_num; 		 public $_on_date; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_score_log_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_score_log";		 }	    
	    		public function getScore_log_id(){			 return $this->_score_log_id ;		}		public function getScore_log_type(){			 return $this->_score_log_type ;		}		public function getUid(){			 return $this->_uid ;		}		public function getScore_num(){			 return $this->_score_num ;		}		public function getOn_date(){			 return $this->_on_date ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setScore_log_id($value){ 			 $this->_score_log_id = $value;		}		public function setScore_log_type($value){ 			 $this->_score_log_type = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setScore_num($value){ 			 $this->_score_num = $value;		}		public function setOn_date($value){ 			 $this->_on_date = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_score_log  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_score_log(){		 		 $data =  array(); 					if(!is_null($this->_score_log_id)){ 				 $data['score_log_id']=$this->_score_log_id;			}			if(!is_null($this->_score_log_type)){ 				 $data['score_log_type']=$this->_score_log_type;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_score_num)){ 				 $data['score_num']=$this->_score_num;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			 return $this->_score_log_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_score_log		 * @return int affected_rows		 */		function edit_keke_witkey_score_log(){ 		 		 $data =  array();  					if(!is_null($this->_score_log_id)){ 				 $data['score_log_id']=$this->_score_log_id;			}			if(!is_null($this->_score_log_type)){ 				 $data['score_log_type']=$this->_score_log_type;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_score_num)){ 				 $data['score_num']=$this->_score_num;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('score_log_id' => $this->_score_log_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_score_log,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_score_log($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_score_log records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_score_log(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_score_log, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_score_log(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where score_log_id = $this->_score_log_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>