<?php
  class Keke_witkey_shop_banner_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_banner_id;  		 public $_banner_type; 		 public $_img_file; 		 public $_img_name; 		 public $_indus_id; 		 public $_list_order; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_banner_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_shop_banner";		 }	    
	    		public function getBanner_id(){			 return $this->_banner_id ;		}		public function getBanner_type(){			 return $this->_banner_type ;		}		public function getImg_file(){			 return $this->_img_file ;		}		public function getImg_name(){			 return $this->_img_name ;		}		public function getIndus_id(){			 return $this->_indus_id ;		}		public function getList_order(){			 return $this->_list_order ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setBanner_id($value){ 			 $this->_banner_id = $value;		}		public function setBanner_type($value){ 			 $this->_banner_type = $value;		}		public function setImg_file($value){ 			 $this->_img_file = $value;		}		public function setImg_name($value){ 			 $this->_img_name = $value;		}		public function setIndus_id($value){ 			 $this->_indus_id = $value;		}		public function setList_order($value){ 			 $this->_list_order = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_banner  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_banner(){		 		 $data =  array(); 					if(!is_null($this->_banner_id)){ 				 $data['banner_id']=$this->_banner_id;			}			if(!is_null($this->_banner_type)){ 				 $data['banner_type']=$this->_banner_type;			}			if(!is_null($this->_img_file)){ 				 $data['img_file']=$this->_img_file;			}			if(!is_null($this->_img_name)){ 				 $data['img_name']=$this->_img_name;			}			if(!is_null($this->_indus_id)){ 				 $data['indus_id']=$this->_indus_id;			}			if(!is_null($this->_list_order)){ 				 $data['list_order']=$this->_list_order;			}			 return $this->_banner_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_banner		 * @return int affected_rows		 */		function edit_keke_witkey_shop_banner(){ 		 		 $data =  array();  					if(!is_null($this->_banner_id)){ 				 $data['banner_id']=$this->_banner_id;			}			if(!is_null($this->_banner_type)){ 				 $data['banner_type']=$this->_banner_type;			}			if(!is_null($this->_img_file)){ 				 $data['img_file']=$this->_img_file;			}			if(!is_null($this->_img_name)){ 				 $data['img_name']=$this->_img_name;			}			if(!is_null($this->_indus_id)){ 				 $data['indus_id']=$this->_indus_id;			}			if(!is_null($this->_list_order)){ 				 $data['list_order']=$this->_list_order;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('banner_id' => $this->_banner_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_banner,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_banner($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_banner records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_banner(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_banner, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_banner(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where banner_id = $this->_banner_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>