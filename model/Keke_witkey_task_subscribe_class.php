<?php
  class Keke_witkey_task_subscribe_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_id;  		 public $_email; 		 public $_typeid; 		 public $_status; 		 public $_checkcode; 		 public $_booktime; 		 public $_checktime; 		 public $_uid; 		 public $_username; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;
	    public $_where;
	     function  keke_witkey_task_subscribe_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_task_subscribe";		 }
	    		public function getId(){			 return $this->_id ;		}		public function getEmail(){			 return $this->_email ;		}		public function getTypeid(){			 return $this->_typeid ;		}		public function getStatus(){			 return $this->_status ;		}		public function getCheckcode(){			 return $this->_checkcode ;		}		public function getBooktime(){			 return $this->_booktime ;		}		public function getChecktime(){			 return $this->_checktime ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setId($value){ 			 $this->_id = $value;		}		public function setEmail($value){ 			 $this->_email = $value;		}		public function setTypeid($value){ 			 $this->_typeid = $value;		}		public function setStatus($value){ 			 $this->_status = $value;		}		public function setCheckcode($value){ 			 $this->_checkcode = $value;		}		public function setBooktime($value){ 			 $this->_booktime = $value;		}		public function setChecktime($value){ 			 $this->_checktime = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_task_subscribe  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_task_subscribe(){		 		 $data =  array(); 					if(!is_null($this->_id)){ 				 $data['id']=$this->_id;			}			if(!is_null($this->_email)){ 				 $data['email']=$this->_email;			}			if(!is_null($this->_typeid)){ 				 $data['typeid']=$this->_typeid;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			if(!is_null($this->_checkcode)){ 				 $data['checkcode']=$this->_checkcode;			}			if(!is_null($this->_booktime)){ 				 $data['booktime']=$this->_booktime;			}			if(!is_null($this->_checktime)){ 				 $data['checktime']=$this->_checktime;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			 return $this->_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_task_subscribe		 * @return int affected_rows		 */		function edit_keke_witkey_task_subscribe(){ 		 		 $data =  array();  					if(!is_null($this->_id)){ 				 $data['id']=$this->_id;			}			if(!is_null($this->_email)){ 				 $data['email']=$this->_email;			}			if(!is_null($this->_typeid)){ 				 $data['typeid']=$this->_typeid;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			if(!is_null($this->_checkcode)){ 				 $data['checkcode']=$this->_checkcode;			}			if(!is_null($this->_booktime)){ 				 $data['booktime']=$this->_booktime;			}			if(!is_null($this->_checktime)){ 				 $data['checktime']=$this->_checktime;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('id' => $this->_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_task_subscribe,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_task_subscribe($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_task_subscribe records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_task_subscribe(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_task_subscribe, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_task_subscribe(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where id = $this->_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 



   }
 ?>