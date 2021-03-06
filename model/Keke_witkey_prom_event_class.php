<?php
  class Keke_witkey_prom_event_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_event_id;  		 public $_event_desc; 		 public $_uid; 		 public $_username; 		 public $_parent_uid; 		 public $_parent_username; 
		 public $_task_id; 		 public $_work_id; 		 public $_rake_cash; 		 public $_rake_credit; 		 public $_event_status; 		 public $_event_time; 		 public $_action; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_prom_event_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_prom_event";		 }	    
	    		public function getEvent_id(){			 return $this->_event_id ;		}		public function getEvent_desc(){			 return $this->_event_desc ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getParent_uid(){			 return $this->_parent_uid ;		}		public function getParent_username(){			 return $this->_parent_username ;		}
		public function getTask_id(){
			 return $this->_task_id ;
		}		public function getWork_id(){			 return $this->_work_id ;		}		public function getRake_cash(){			 return $this->_rake_cash ;		}		public function getRake_credit(){			 return $this->_rake_credit ;		}		public function getEvent_status(){			 return $this->_event_status ;		}		public function getEvent_time(){			 return $this->_event_time ;		}		public function getAction(){			 return $this->_action ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setEvent_id($value){ 			 $this->_event_id = $value;		}		public function setEvent_desc($value){ 			 $this->_event_desc = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setParent_uid($value){ 			 $this->_parent_uid = $value;		}		public function setParent_username($value){ 			 $this->_parent_username = $value;		}
		public function setTask_id($value){ 
			 $this->_task_id = $value;
		}		public function setWork_id($value){ 			 $this->_work_id = $value;		}		public function setRake_cash($value){ 			 $this->_rake_cash = $value;		}		public function setRake_credit($value){ 			 $this->_rake_credit = $value;		}		public function setEvent_status($value){ 			 $this->_event_status = $value;		}		public function setEvent_time($value){ 			 $this->_event_time = $value;		}		public function setAction($value){ 			 $this->_action = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_prom_event  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_prom_event(){		 		 $data =  array(); 					if(!is_null($this->_event_id)){ 				 $data['event_id']=$this->_event_id;			}			if(!is_null($this->_event_desc)){ 				 $data['event_desc']=$this->_event_desc;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_parent_uid)){ 				 $data['parent_uid']=$this->_parent_uid;			}			if(!is_null($this->_parent_username)){ 				 $data['parent_username']=$this->_parent_username;			}
			if(!is_null($this->_task_id)){ 
				 $data['task_id']=$this->_task_id;
			}			if(!is_null($this->_work_id)){ 				 $data['work_id']=$this->_work_id;			}			if(!is_null($this->_rake_cash)){ 				 $data['rake_cash']=$this->_rake_cash;			}			if(!is_null($this->_rake_credit)){ 				 $data['rake_credit']=$this->_rake_credit;			}			if(!is_null($this->_event_status)){ 				 $data['event_status']=$this->_event_status;			}			if(!is_null($this->_event_time)){ 				 $data['event_time']=$this->_event_time;			}			if(!is_null($this->_action)){ 				 $data['action']=$this->_action;			}			 return $this->_event_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_prom_event		 * @return int affected_rows		 */		function edit_keke_witkey_prom_event(){ 		 		 $data =  array();  					if(!is_null($this->_event_id)){ 				 $data['event_id']=$this->_event_id;			}			if(!is_null($this->_event_desc)){ 				 $data['event_desc']=$this->_event_desc;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_parent_uid)){ 				 $data['parent_uid']=$this->_parent_uid;			}			if(!is_null($this->_parent_username)){ 				 $data['parent_username']=$this->_parent_username;			}
			if(!is_null($this->_task_id)){ 
				 $data['task_id']=$this->_task_id;
			}			if(!is_null($this->_work_id)){ 				 $data['work_id']=$this->_work_id;			}			if(!is_null($this->_rake_cash)){ 				 $data['rake_cash']=$this->_rake_cash;			}			if(!is_null($this->_rake_credit)){ 				 $data['rake_credit']=$this->_rake_credit;			}			if(!is_null($this->_event_status)){ 				 $data['event_status']=$this->_event_status;			}			if(!is_null($this->_event_time)){ 				 $data['event_time']=$this->_event_time;			}			if(!is_null($this->_action)){ 				 $data['action']=$this->_action;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('event_id' => $this->_event_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_prom_event,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_prom_event($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_prom_event records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_prom_event(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_prom_event, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_prom_event(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where event_id = $this->_event_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>