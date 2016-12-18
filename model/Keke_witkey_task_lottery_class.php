<?php
  class Keke_witkey_task_lottery_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_lottery_id;  		 public $_task_id; 		 public $_uid; 		 public $_username; 		 public $_l_number; 		 public $_join_time; 		 public $_get_ratio; 		 public $_get_cash; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_task_lottery_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_task_lottery";		 }	    
	    		public function getLottery_id(){			 return $this->_lottery_id ;		}		public function getTask_id(){			 return $this->_task_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getL_number(){			 return $this->_l_number ;		}		public function getJoin_time(){			 return $this->_join_time ;		}		public function getGet_ratio(){			 return $this->_get_ratio ;		}		public function getGet_cash(){			 return $this->_get_cash ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setLottery_id($value){ 			 $this->_lottery_id = $value;		}		public function setTask_id($value){ 			 $this->_task_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setL_number($value){ 			 $this->_l_number = $value;		}		public function setJoin_time($value){ 			 $this->_join_time = $value;		}		public function setGet_ratio($value){ 			 $this->_get_ratio = $value;		}		public function setGet_cash($value){ 			 $this->_get_cash = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_task_lottery  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_task_lottery(){		 		 $data =  array(); 					if(!is_null($this->_lottery_id)){ 				 $data['lottery_id']=$this->_lottery_id;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_l_number)){ 				 $data['l_number']=$this->_l_number;			}			if(!is_null($this->_join_time)){ 				 $data['join_time']=$this->_join_time;			}			if(!is_null($this->_get_ratio)){ 				 $data['get_ratio']=$this->_get_ratio;			}			if(!is_null($this->_get_cash)){ 				 $data['get_cash']=$this->_get_cash;			}			 return $this->_lottery_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_task_lottery		 * @return int affected_rows		 */		function edit_keke_witkey_task_lottery(){ 		 		 $data =  array();  					if(!is_null($this->_lottery_id)){ 				 $data['lottery_id']=$this->_lottery_id;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_l_number)){ 				 $data['l_number']=$this->_l_number;			}			if(!is_null($this->_join_time)){ 				 $data['join_time']=$this->_join_time;			}			if(!is_null($this->_get_ratio)){ 				 $data['get_ratio']=$this->_get_ratio;			}			if(!is_null($this->_get_cash)){ 				 $data['get_cash']=$this->_get_cash;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('lottery_id' => $this->_lottery_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_task_lottery,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_task_lottery($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_task_lottery records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_task_lottery(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_task_lottery, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_task_lottery(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where lottery_id = $this->_lottery_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>