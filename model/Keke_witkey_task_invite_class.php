<?php
  class Keke_witkey_task_invite_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    		 public $_i_id; 		 public $_uid; 		 public $_username; 		 public $_i_uid; 		 public $_i_username; 		 public $_task_id; 		 public $_on_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_task_invite_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_task_invite";		 }	    
	    		public function getI_id(){			 return $this->_i_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getI_uid(){			 return $this->_i_uid ;		}		public function getI_username(){			 return $this->_i_username ;		}		public function getTask_id(){			 return $this->_task_id ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setI_id($value){ 			 $this->_i_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setI_uid($value){ 			 $this->_i_uid = $value;		}		public function setI_username($value){ 			 $this->_i_username = $value;		}		public function setTask_id($value){ 			 $this->_task_id = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_task_invite  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_task_invite(){		 		 $data =  array(); 					if(!is_null($this->_i_id)){ 				 $data['i_id']=$this->_i_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_i_uid)){ 				 $data['i_uid']=$this->_i_uid;			}			if(!is_null($this->_i_username)){ 				 $data['i_username']=$this->_i_username;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			 return $this->_frost_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_task_invite		 * @return int affected_rows		 */		function edit_keke_witkey_task_invite(){ 		 		 $data =  array();  					if(!is_null($this->_i_id)){ 				 $data['i_id']=$this->_i_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_i_uid)){ 				 $data['i_uid']=$this->_i_uid;			}			if(!is_null($this->_i_username)){ 				 $data['i_username']=$this->_i_username;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('frost_id' => $this->_frost_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_task_invite,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_task_invite($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_task_invite records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_task_invite(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_task_invite, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_task_invite(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where frost_id = $this->_frost_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>