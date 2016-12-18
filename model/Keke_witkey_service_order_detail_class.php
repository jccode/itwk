<?php
  class Keke_witkey_service_order_detail_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_detail_id;  		 public $_order_id; 		 public $_step_num; 		 public $_step_cash; 		 public $_step_detail; 		 public $_step_status; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_service_order_detail_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_service_order_detail";		 }	    
	    		public function getDetail_id(){			 return $this->_detail_id ;		}		public function getOrder_id(){			 return $this->_order_id ;		}		public function getStep_num(){			 return $this->_step_num ;		}		public function getStep_cash(){			 return $this->_step_cash ;		}		public function getStep_detail(){			 return $this->_step_detail ;		}		public function getStep_status(){			 return $this->_step_status ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setDetail_id($value){ 			 $this->_detail_id = $value;		}		public function setOrder_id($value){ 			 $this->_order_id = $value;		}		public function setStep_num($value){ 			 $this->_step_num = $value;		}		public function setStep_cash($value){ 			 $this->_step_cash = $value;		}		public function setStep_detail($value){ 			 $this->_step_detail = $value;		}		public function setStep_status($value){ 			 $this->_step_status = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_service_order_detail  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_service_order_detail(){		 		 $data =  array(); 					if(!is_null($this->_detail_id)){ 				 $data['detail_id']=$this->_detail_id;			}			if(!is_null($this->_order_id)){ 				 $data['order_id']=$this->_order_id;			}			if(!is_null($this->_step_num)){ 				 $data['step_num']=$this->_step_num;			}			if(!is_null($this->_step_cash)){ 				 $data['step_cash']=$this->_step_cash;			}			if(!is_null($this->_step_detail)){ 				 $data['step_detail']=$this->_step_detail;			}			if(!is_null($this->_step_status)){ 				 $data['step_status']=$this->_step_status;			}			 return $this->_detail_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_service_order_detail		 * @return int affected_rows		 */		function edit_keke_witkey_service_order_detail(){ 		 		 $data =  array();  					if(!is_null($this->_detail_id)){ 				 $data['detail_id']=$this->_detail_id;			}			if(!is_null($this->_order_id)){ 				 $data['order_id']=$this->_order_id;			}			if(!is_null($this->_step_num)){ 				 $data['step_num']=$this->_step_num;			}			if(!is_null($this->_step_cash)){ 				 $data['step_cash']=$this->_step_cash;			}			if(!is_null($this->_step_detail)){ 				 $data['step_detail']=$this->_step_detail;			}			if(!is_null($this->_step_status)){ 				 $data['step_status']=$this->_step_status;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('detail_id' => $this->_detail_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_service_order_detail,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_service_order_detail($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_service_order_detail records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_service_order_detail(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_service_order_detail, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_service_order_detail(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where detail_id = $this->_detail_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>