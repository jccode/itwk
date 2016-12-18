<?php
  class Keke_witkey_payitem_record_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_record_id;  		 public $_item_code; 		 public $_use_type; 		 public $_uid; 		 public $_username; 		 public $_obj_type; 		 public $_obj_id; 		 public $_origin_id; 		 public $_use_cash; 		 public $_use_num; 		 public $_on_time; 
		 public $_status;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_payitem_record_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_payitem_record";		 }	    
	    		public function getRecord_id(){			 return $this->_record_id ;		}		public function getItem_code(){			 return $this->_item_code ;		}		public function getUse_type(){			 return $this->_use_type ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getObj_type(){			 return $this->_obj_type ;		}		public function getObj_id(){			 return $this->_obj_id ;		}		public function getOrigin_id(){			 return $this->_origin_id ;		}		public function getUse_cash(){			 return $this->_use_cash ;		}		public function getUse_num(){			 return $this->_use_num ;		}		public function getOn_time(){			 return $this->_on_time ;		}
		public function getStatus(){
			 return $this->_status ;
		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setRecord_id($value){ 			 $this->_record_id = $value;		}		public function setItem_code($value){ 			 $this->_item_code = $value;		}		public function setUse_type($value){ 			 $this->_use_type = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setObj_type($value){ 			 $this->_obj_type = $value;		}		public function setObj_id($value){ 			 $this->_obj_id = $value;		}		public function setOrigin_id($value){ 			 $this->_origin_id = $value;		}		public function setUse_cash($value){ 			 $this->_use_cash = $value;		}		public function setUse_num($value){ 			 $this->_use_num = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}
		public function setStatus($value){ 
			 $this->_status = $value;
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
    	
	    /**		 * insert into  keke_witkey_payitem_record  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_payitem_record(){		 		 $data =  array(); 					if(!is_null($this->_record_id)){ 				 $data['record_id']=$this->_record_id;			}			if(!is_null($this->_item_code)){ 				 $data['item_code']=$this->_item_code;			}			if(!is_null($this->_use_type)){ 				 $data['use_type']=$this->_use_type;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_obj_type)){ 				 $data['obj_type']=$this->_obj_type;			}			if(!is_null($this->_obj_id)){ 				 $data['obj_id']=$this->_obj_id;			}			if(!is_null($this->_origin_id)){ 				 $data['origin_id']=$this->_origin_id;			}			if(!is_null($this->_use_cash)){ 				 $data['use_cash']=$this->_use_cash;			}			if(!is_null($this->_use_num)){ 				 $data['use_num']=$this->_use_num;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}			 return $this->_record_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_payitem_record		 * @return int affected_rows		 */		function edit_keke_witkey_payitem_record(){ 		 		 $data =  array();  					if(!is_null($this->_record_id)){ 				 $data['record_id']=$this->_record_id;			}			if(!is_null($this->_item_code)){ 				 $data['item_code']=$this->_item_code;			}			if(!is_null($this->_use_type)){ 				 $data['use_type']=$this->_use_type;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_obj_type)){ 				 $data['obj_type']=$this->_obj_type;			}			if(!is_null($this->_obj_id)){ 				 $data['obj_id']=$this->_obj_id;			}			if(!is_null($this->_origin_id)){ 				 $data['origin_id']=$this->_origin_id;			}			if(!is_null($this->_use_cash)){ 				 $data['use_cash']=$this->_use_cash;			}			if(!is_null($this->_use_num)){ 				 $data['use_num']=$this->_use_num;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('record_id' => $this->_record_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_payitem_record,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_payitem_record($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_payitem_record records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_payitem_record(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_payitem_record, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_payitem_record(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where record_id = $this->_record_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>