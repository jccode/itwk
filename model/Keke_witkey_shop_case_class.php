<?php
  class Keke_witkey_shop_case_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_case_id;  		 public $_cate_id; 		 public $_shop_id; 		 public $_uid; 		 public $_username; 		 public $_case_name; 		 public $_case_desc; 		 public $_case_pic; 		 public $_case_url; 		 public $_start_time; 		 public $_end_time; 		 public $_on_time; 		 public $_case_flash; 
		 public $_listorder;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;
	    public $_where;
	    function  keke_witkey_shop_case_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_shop_case";		 }
	    public function getCase_id(){			 return $this->_case_id ;		}		public function getCate_id(){			 return $this->_cate_id ;		}		public function getShop_id(){			 return $this->_shop_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getCase_name(){			 return $this->_case_name ;		}		public function getCase_desc(){			 return $this->_case_desc ;		}		public function getCase_pic(){			 return $this->_case_pic ;		}		public function getCase_url(){			 return $this->_case_url ;		}		public function getStart_time(){			 return $this->_start_time ;		}		public function getEnd_time(){			 return $this->_end_time ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getCase_flash(){			 return $this->_case_flash ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
		public function getListorder() {
			return $this->_listorder;
		}
	    public function setCase_id($value){ 			 $this->_case_id = $value;		}		public function setCate_id($value){ 			 $this->_cate_id = $value;		}		public function setShop_id($value){ 			 $this->_shop_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setCase_name($value){ 			 $this->_case_name = $value;		}		public function setCase_desc($value){ 			 $this->_case_desc = $value;		}		public function setCase_pic($value){ 			 $this->_case_pic = $value;		}		public function setCase_url($value){ 			 $this->_case_url = $value;		}		public function setStart_time($value){ 			 $this->_start_time = $value;		}		public function setEnd_time($value){ 			 $this->_end_time = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setCase_flash($value){ 			 $this->_case_flash = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
		public function setListorder($_listorder) {
			$this->_listorder = $_listorder;
		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_case  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_case(){		 		 $data =  array(); 			if(!is_null($this->_case_id)){ 				 $data['case_id']=$this->_case_id;			}			if(!is_null($this->_cate_id)){ 				 $data['cate_id']=$this->_cate_id;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_case_name)){ 				 $data['case_name']=$this->_case_name;			}			if(!is_null($this->_case_desc)){ 				 $data['case_desc']=$this->_case_desc;			}			if(!is_null($this->_case_pic)){ 				 $data['case_pic']=$this->_case_pic;			}			if(!is_null($this->_case_url)){ 				 $data['case_url']=$this->_case_url;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_case_flash)){ 				 $data['case_flash']=$this->_case_flash;			}
			if(!is_null($this->_listorder)){
				$data['listorder']=$this->_listorder;
			}			 return $this->_case_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_case		 * @return int affected_rows		 */		function edit_keke_witkey_shop_case(){ 		 		 $data =  array();  			if(!is_null($this->_case_id)){ 				 $data['case_id']=$this->_case_id;			}			if(!is_null($this->_cate_id)){ 				 $data['cate_id']=$this->_cate_id;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_case_name)){ 				 $data['case_name']=$this->_case_name;			}			if(!is_null($this->_case_desc)){ 				 $data['case_desc']=$this->_case_desc;			}			if(!is_null($this->_case_pic)){ 				 $data['case_pic']=$this->_case_pic;			}			if(!is_null($this->_case_url)){ 				 $data['case_url']=$this->_case_url;			}			if(!is_null($this->_start_time)){ 				 $data['start_time']=$this->_start_time;			}			if(!is_null($this->_end_time)){ 				 $data['end_time']=$this->_end_time;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_case_flash)){ 				 $data['case_flash']=$this->_case_flash;			}
			if(!is_null($this->_listorder)){
				$data['listorder']=$this->_listorder;
			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('case_id' => $this->_case_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_case,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_case($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_case records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_case(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_case, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_case(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where case_id = $this->_case_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
   }
 ?>