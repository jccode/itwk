<?php
  class Keke_witkey_studio_apply_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_apply_id;  		 public $_studio_id; 		 public $_uid; 		 public $_username; 		 public $_content; 		 public $_apply_type; 		 public $_on_date; 		 public $_apply_status; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_studio_apply_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_studio_apply";		 }	    
	    		public function getApply_id(){			 return $this->_apply_id ;		}		public function getStudio_id(){			 return $this->_studio_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getContent(){			 return $this->_content ;		}		public function getApply_type(){			 return $this->_apply_type ;		}		public function getOn_date(){			 return $this->_on_date ;		}		public function getApply_status(){			 return $this->_apply_status ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setApply_id($value){ 			 $this->_apply_id = $value;		}		public function setStudio_id($value){ 			 $this->_studio_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setContent($value){ 			 $this->_content = $value;		}		public function setApply_type($value){ 			 $this->_apply_type = $value;		}		public function setOn_date($value){ 			 $this->_on_date = $value;		}		public function setApply_status($value){ 			 $this->_apply_status = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_studio_apply  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_studio_apply(){		 		 $data =  array(); 					if(!is_null($this->_apply_id)){ 				 $data['apply_id']=$this->_apply_id;			}			if(!is_null($this->_studio_id)){ 				 $data['studio_id']=$this->_studio_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_content)){ 				 $data['content']=$this->_content;			}			if(!is_null($this->_apply_type)){ 				 $data['apply_type']=$this->_apply_type;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			if(!is_null($this->_apply_status)){ 				 $data['apply_status']=$this->_apply_status;			}			 return $this->_apply_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_studio_apply		 * @return int affected_rows		 */		function edit_keke_witkey_studio_apply(){ 		 		 $data =  array();  					if(!is_null($this->_apply_id)){ 				 $data['apply_id']=$this->_apply_id;			}			if(!is_null($this->_studio_id)){ 				 $data['studio_id']=$this->_studio_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_content)){ 				 $data['content']=$this->_content;			}			if(!is_null($this->_apply_type)){ 				 $data['apply_type']=$this->_apply_type;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			if(!is_null($this->_apply_status)){ 				 $data['apply_status']=$this->_apply_status;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('apply_id' => $this->_apply_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_studio_apply,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_studio_apply($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_studio_apply records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_studio_apply(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_studio_apply, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_studio_apply(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where apply_id = $this->_apply_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>