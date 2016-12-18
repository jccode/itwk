<?php
  class Keke_witkey_promotion_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_prom_id;  		 public $_prom_uid; 		 public $_pub_uid; 		 public $_join_uid; 		 public $_task_id; 		 public $_prom_money; 		 public $_prom_status; 		 public $_prom_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_promotion_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_promotion";		 }	    
	    		public function getProm_id(){			 return $this->_prom_id ;		}		public function getProm_uid(){			 return $this->_prom_uid ;		}		public function getPub_uid(){			 return $this->_pub_uid ;		}		public function getJoin_uid(){			 return $this->_join_uid ;		}		public function getTask_id(){			 return $this->_task_id ;		}		public function getProm_money(){			 return $this->_prom_money ;		}		public function getProm_status(){			 return $this->_prom_status ;		}		public function getProm_time(){			 return $this->_prom_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setProm_id($value){ 			 $this->_prom_id = $value;		}		public function setProm_uid($value){ 			 $this->_prom_uid = $value;		}		public function setPub_uid($value){ 			 $this->_pub_uid = $value;		}		public function setJoin_uid($value){ 			 $this->_join_uid = $value;		}		public function setTask_id($value){ 			 $this->_task_id = $value;		}		public function setProm_money($value){ 			 $this->_prom_money = $value;		}		public function setProm_status($value){ 			 $this->_prom_status = $value;		}		public function setProm_time($value){ 			 $this->_prom_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_promotion  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_promotion(){		 		 $data =  array(); 					if(!is_null($this->_prom_id)){ 				 $data['prom_id']=$this->_prom_id;			}			if(!is_null($this->_prom_uid)){ 				 $data['prom_uid']=$this->_prom_uid;			}			if(!is_null($this->_pub_uid)){ 				 $data['pub_uid']=$this->_pub_uid;			}			if(!is_null($this->_join_uid)){ 				 $data['join_uid']=$this->_join_uid;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_prom_money)){ 				 $data['prom_money']=$this->_prom_money;			}			if(!is_null($this->_prom_status)){ 				 $data['prom_status']=$this->_prom_status;			}			if(!is_null($this->_prom_time)){ 				 $data['prom_time']=$this->_prom_time;			}			 return $this->_prom_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_promotion		 * @return int affected_rows		 */		function edit_keke_witkey_promotion(){ 		 		 $data =  array();  					if(!is_null($this->_prom_id)){ 				 $data['prom_id']=$this->_prom_id;			}			if(!is_null($this->_prom_uid)){ 				 $data['prom_uid']=$this->_prom_uid;			}			if(!is_null($this->_pub_uid)){ 				 $data['pub_uid']=$this->_pub_uid;			}			if(!is_null($this->_join_uid)){ 				 $data['join_uid']=$this->_join_uid;			}			if(!is_null($this->_task_id)){ 				 $data['task_id']=$this->_task_id;			}			if(!is_null($this->_prom_money)){ 				 $data['prom_money']=$this->_prom_money;			}			if(!is_null($this->_prom_status)){ 				 $data['prom_status']=$this->_prom_status;			}			if(!is_null($this->_prom_time)){ 				 $data['prom_time']=$this->_prom_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('prom_id' => $this->_prom_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_promotion,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_promotion($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_promotion records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_promotion(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_promotion, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_promotion(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where prom_id = $this->_prom_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>