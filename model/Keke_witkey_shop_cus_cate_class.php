<?php
  class Keke_witkey_shop_cus_cate_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_cus_cate_id;  		 public $_obj_type; 		 public $_obj_id; 		 public $_cate_name; 		 public $_shop_id; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_cus_cate_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_shop_cus_cate";		 }	    
	    		public function getCus_cate_id(){			 return $this->_cus_cate_id ;		}		public function getObj_type(){			 return $this->_obj_type ;		}		public function getObj_id(){			 return $this->_obj_id ;		}		public function getCate_name(){			 return $this->_cate_name ;		}		public function getShop_id(){			 return $this->_shop_id ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setCus_cate_id($value){ 			 $this->_cus_cate_id = $value;		}		public function setObj_type($value){ 			 $this->_obj_type = $value;		}		public function setObj_id($value){ 			 $this->_obj_id = $value;		}		public function setCate_name($value){ 			 $this->_cate_name = $value;		}		public function setShop_id($value){ 			 $this->_shop_id = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_cus_cate  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_cus_cate(){		 		 $data =  array(); 					if(!is_null($this->_cus_cate_id)){ 				 $data['cus_cate_id']=$this->_cus_cate_id;			}			if(!is_null($this->_obj_type)){ 				 $data['obj_type']=$this->_obj_type;			}			if(!is_null($this->_obj_id)){ 				 $data['obj_id']=$this->_obj_id;			}			if(!is_null($this->_cate_name)){ 				 $data['cate_name']=$this->_cate_name;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			 return $this->_cus_cate_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_cus_cate		 * @return int affected_rows		 */		function edit_keke_witkey_shop_cus_cate(){ 		 		 $data =  array();  					if(!is_null($this->_cus_cate_id)){ 				 $data['cus_cate_id']=$this->_cus_cate_id;			}			if(!is_null($this->_obj_type)){ 				 $data['obj_type']=$this->_obj_type;			}			if(!is_null($this->_obj_id)){ 				 $data['obj_id']=$this->_obj_id;			}			if(!is_null($this->_cate_name)){ 				 $data['cate_name']=$this->_cate_name;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('cus_cate_id' => $this->_cus_cate_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_cus_cate,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_cus_cate($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_cus_cate records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_cus_cate(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_cus_cate, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_cus_cate(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where cus_cate_id = $this->_cus_cate_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>