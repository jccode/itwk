<?php
  class Keke_witkey_message_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_msg_id;  		 public $_msg_pid; 		 public $_uid; 		 public $_username; 		 public $_recive_uid; 		 public $_recive_username; 		 public $_msg_status; 		 public $_view_status; 		 public $_title; 		 public $_content; 		 public $_on_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_message_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_message";		 }	    
	    		public function getMsg_id(){			 return $this->_msg_id ;		}		public function getMsg_pid(){			 return $this->_msg_pid ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getRecive_uid(){			 return $this->_recive_uid ;		}		public function getRecive_username(){			 return $this->_recive_username ;		}		public function getMsg_status(){			 return $this->_msg_status ;		}		public function getView_status(){			 return $this->_view_status ;		}		public function getTitle(){			 return $this->_title ;		}		public function getContent(){			 return $this->_content ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setMsg_id($value){ 			 $this->_msg_id = $value;		}		public function setMsg_pid($value){ 			 $this->_msg_pid = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setRecive_uid($value){ 			 $this->_recive_uid = $value;		}		public function setRecive_username($value){ 			 $this->_recive_username = $value;		}		public function setMsg_status($value){ 			 $this->_msg_status = $value;		}		public function setView_status($value){ 			 $this->_view_status = $value;		}		public function setTitle($value){ 			 $this->_title = $value;		}		public function setContent($value){ 			 $this->_content = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_message  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_message(){		 		 $data =  array(); 					if(!is_null($this->_msg_id)){ 				 $data['msg_id']=$this->_msg_id;			}			if(!is_null($this->_msg_pid)){ 				 $data['msg_pid']=$this->_msg_pid;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_recive_uid)){ 				 $data['recive_uid']=$this->_recive_uid;			}			if(!is_null($this->_recive_username)){ 				 $data['recive_username']=$this->_recive_username;			}			if(!is_null($this->_msg_status)){ 				 $data['msg_status']=$this->_msg_status;			}			if(!is_null($this->_view_status)){ 				 $data['view_status']=$this->_view_status;			}			if(!is_null($this->_title)){ 				 $data['title']=$this->_title;			}			if(!is_null($this->_content)){ 				 $data['content']=$this->_content;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			 return $this->_msg_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_message		 * @return int affected_rows		 */		function edit_keke_witkey_message(){ 		 		 $data =  array();  					if(!is_null($this->_msg_id)){ 				 $data['msg_id']=$this->_msg_id;			}			if(!is_null($this->_msg_pid)){ 				 $data['msg_pid']=$this->_msg_pid;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_recive_uid)){ 				 $data['recive_uid']=$this->_recive_uid;			}			if(!is_null($this->_recive_username)){ 				 $data['recive_username']=$this->_recive_username;			}			if(!is_null($this->_msg_status)){ 				 $data['msg_status']=$this->_msg_status;			}			if(!is_null($this->_view_status)){ 				 $data['view_status']=$this->_view_status;			}			if(!is_null($this->_title)){ 				 $data['title']=$this->_title;			}			if(!is_null($this->_content)){ 				 $data['content']=$this->_content;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('msg_id' => $this->_msg_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_message,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_message($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_message records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_message(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_message, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_message(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where msg_id = $this->_msg_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>