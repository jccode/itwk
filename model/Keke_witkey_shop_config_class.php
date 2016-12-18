<?php
  class Keke_witkey_shop_config_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_config_id;  		 public $_service_prorate; 		 public $_down_prorate; 		 public $_service_min_amount; 		 public $_step_min_amount; 		 public $_shop_is_close; 		 public $_on_date; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_config_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_shop_config";		 }	    
	    		public function getConfig_id(){			 return $this->_config_id ;		}		public function getService_prorate(){			 return $this->_service_prorate ;		}		public function getDown_prorate(){			 return $this->_down_prorate ;		}		public function getService_min_amount(){			 return $this->_service_min_amount ;		}		public function getStep_min_amount(){			 return $this->_step_min_amount ;		}		public function getShop_is_close(){			 return $this->_shop_is_close ;		}		public function getOn_date(){			 return $this->_on_date ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setConfig_id($value){ 			 $this->_config_id = $value;		}		public function setService_prorate($value){ 			 $this->_service_prorate = $value;		}		public function setDown_prorate($value){ 			 $this->_down_prorate = $value;		}		public function setService_min_amount($value){ 			 $this->_service_min_amount = $value;		}		public function setStep_min_amount($value){ 			 $this->_step_min_amount = $value;		}		public function setShop_is_close($value){ 			 $this->_shop_is_close = $value;		}		public function setOn_date($value){ 			 $this->_on_date = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_config  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_config(){		 		 $data =  array(); 					if(!is_null($this->_config_id)){ 				 $data['config_id']=$this->_config_id;			}			if(!is_null($this->_service_prorate)){ 				 $data['service_prorate']=$this->_service_prorate;			}			if(!is_null($this->_down_prorate)){ 				 $data['down_prorate']=$this->_down_prorate;			}			if(!is_null($this->_service_min_amount)){ 				 $data['service_min_amount']=$this->_service_min_amount;			}			if(!is_null($this->_step_min_amount)){ 				 $data['step_min_amount']=$this->_step_min_amount;			}			if(!is_null($this->_shop_is_close)){ 				 $data['shop_is_close']=$this->_shop_is_close;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			 return $this->_config_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_config		 * @return int affected_rows		 */		function edit_keke_witkey_shop_config(){ 		 		 $data =  array();  					if(!is_null($this->_config_id)){ 				 $data['config_id']=$this->_config_id;			}			if(!is_null($this->_service_prorate)){ 				 $data['service_prorate']=$this->_service_prorate;			}			if(!is_null($this->_down_prorate)){ 				 $data['down_prorate']=$this->_down_prorate;			}			if(!is_null($this->_service_min_amount)){ 				 $data['service_min_amount']=$this->_service_min_amount;			}			if(!is_null($this->_step_min_amount)){ 				 $data['step_min_amount']=$this->_step_min_amount;			}			if(!is_null($this->_shop_is_close)){ 				 $data['shop_is_close']=$this->_shop_is_close;			}			if(!is_null($this->_on_date)){ 				 $data['on_date']=$this->_on_date;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('config_id' => $this->_config_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_config,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_config($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_config records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_config(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_config, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_config(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where config_id = $this->_config_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>