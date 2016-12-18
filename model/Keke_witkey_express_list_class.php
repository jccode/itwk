<?php
  class Keke_witkey_express_list_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_single_id;  		 public $_express_name; 		 public $_single_num; 		 public $_status; 		 public $_on_time; 		 public $_uid; 		 public $_username; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_express_list_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_express_list";		 }	    
	    		public function getSingle_id(){			 return $this->_single_id ;		}		public function getExpress_name(){			 return $this->_express_name ;		}		public function getSingle_num(){			 return $this->_single_num ;		}		public function getStatus(){			 return $this->_status ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setSingle_id($value){ 			 $this->_single_id = $value;		}		public function setExpress_name($value){ 			 $this->_express_name = $value;		}		public function setSingle_num($value){ 			 $this->_single_num = $value;		}		public function setStatus($value){ 			 $this->_status = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_express_list  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_express_list(){		 		 $data =  array(); 					if(!is_null($this->_single_id)){ 				 $data['single_id']=$this->_single_id;			}			if(!is_null($this->_express_name)){ 				 $data['express_name']=$this->_express_name;			}			if(!is_null($this->_single_num)){ 				 $data['single_num']=$this->_single_num;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			 return $this->_single_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_express_list		 * @return int affected_rows		 */		function edit_keke_witkey_express_list(){ 		 		 $data =  array();  					if(!is_null($this->_single_id)){ 				 $data['single_id']=$this->_single_id;			}			if(!is_null($this->_express_name)){ 				 $data['express_name']=$this->_express_name;			}			if(!is_null($this->_single_num)){ 				 $data['single_num']=$this->_single_num;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('single_id' => $this->_single_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_express_list,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_express_list($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_express_list records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_express_list(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_express_list, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_express_list(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where single_id = $this->_single_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>