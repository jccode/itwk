<?php
  class Keke_witkey_task_track_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_t_id;  		 public $_task_id; 		 public $_task_title; 		 public $_uid; 		 public $_username; 		 public $_t_content; 		 public $_t_uid; 		 public $_t_username; 		 public $_t_status; 		 public $_remark; 		 public $_dateline; 
		 public $_ext; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_task_track_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_task_track";		 }	    
	    		public function getT_id(){			 return $this->_t_id ;		}		public function getTask_id(){			 return $this->_task_id ;		}		public function getTask_title(){			 return $this->_task_title ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getT_content(){			 return $this->_t_content ;		}		public function getT_uid(){			 return $this->_t_uid ;		}		public function getT_username(){			 return $this->_t_username ;		}		public function getT_status(){			 return $this->_t_status ;		}		public function getRemark(){			 return $this->_remark ;		}		public function getDateline(){			 return $this->_dateline ;		}
 	 	public function getExt(){
			 return $this->_ext ;
		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setT_id($value){ 			 $this->_t_id = $value;		}		public function setTask_id($value){ 			 $this->_task_id = $value;		}		public function setTask_title($value){ 			 $this->_task_title = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setT_content($value){ 			 $this->_t_content = $value;		}		public function setT_uid($value){ 			 $this->_t_uid = $value;		}		public function setT_username($value){ 			 $this->_t_username = $value;		}		public function setT_status($value){ 			 $this->_t_status = $value;		}		public function setRemark($value){ 			 $this->_remark = $value;		}		public function setDateline($value){ 			 $this->_dateline = $value;		}
		public function setExt($value){ 
			 $this->_ext = $value;
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
    	
	    /**		 * insert into  keke_witkey_task_track  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_task_track(){		 		 $data =  array(); 					if(!is_null($this->_t_id)){ 				 $data['t_id']=$this->_t_id;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_task_title)){ 				 $data['task_title']=$this->_task_title;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_t_content)){ 				 $data['t_content']=$this->_t_content;			}			if(!is_null($this->_t_uid)){ 				 $data['t_uid']=$this->_t_uid;			}			if(!is_null($this->_t_username)){ 				 $data['t_username']=$this->_t_username;			}			if(!is_null($this->_t_status)){ 				 $data['t_status']=$this->_t_status;			}			if(!is_null($this->_remark)){ 				 $data['remark']=$this->_remark;			}			if(!is_null($this->_dateline)){ 				 $data['dateline']=$this->_dateline;			}
			if(!is_null($this->_ext)){ 
				 $data['ext']=$this->_ext;
			}			 return $this->_t_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_task_track		 * @return int affected_rows		 */		function edit_keke_witkey_task_track(){ 		 		 $data =  array();  					if(!is_null($this->_t_id)){ 				 $data['t_id']=$this->_t_id;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_task_title)){ 				 $data['task_title']=$this->_task_title;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_t_content)){ 				 $data['t_content']=$this->_t_content;			}			if(!is_null($this->_t_uid)){ 				 $data['t_uid']=$this->_t_uid;			}			if(!is_null($this->_t_username)){ 				 $data['t_username']=$this->_t_username;			}			if(!is_null($this->_t_status)){ 				 $data['t_status']=$this->_t_status;			}			if(!is_null($this->_remark)){ 				 $data['remark']=$this->_remark;			}			if(!is_null($this->_dateline)){ 				 $data['dateline']=$this->_dateline;			}			if(!is_null($this->_ext)){ 
				 $data['ext']=$this->_ext;
			}
						if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('t_id' => $this->_t_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_task_track,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_task_track($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_task_track records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_task_track(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_task_track, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_task_track(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where t_id = $this->_t_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>