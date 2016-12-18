<?php
  class Keke_witkey_talent_link_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_tid;  		 public $_catid; 		 public $_uid; 		 public $_level; 		 public $_create_date; 		 public $_modify_date; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_talent_link_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_talent_link";		 }	    
	    		public function getTid(){			 return $this->_tid ;		}		public function getCatid(){			 return $this->_catid ;		}		public function getUid(){			 return $this->_uid ;		}		public function getLevel(){			 return $this->_level ;		}		public function getCreate_date(){			 return $this->_create_date ;		}		public function getModify_date(){			 return $this->_modify_date ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setTid($value){ 			 $this->_tid = $value;		}		public function setCatid($value){ 			 $this->_catid = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setLevel($value){ 			 $this->_level = $value;		}		public function setCreate_date($value){ 			 $this->_create_date = $value;		}		public function setModify_date($value){ 			 $this->_modify_date = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_talent_link  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_talent_link(){		 		 $data =  array(); 					if(!is_null($this->_tid)){ 				 $data['tid']=$this->_tid;			}			if(!is_null($this->_catid)){ 				 $data['catid']=$this->_catid;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_level)){ 				 $data['level']=$this->_level;			}			if(!is_null($this->_create_date)){ 				 $data['create_date']=$this->_create_date;			}			if(!is_null($this->_modify_date)){ 				 $data['modify_date']=$this->_modify_date;			}			 return $this->_tid = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_talent_link		 * @return int affected_rows		 */		function edit_keke_witkey_talent_link(){ 		 		 $data =  array();  					if(!is_null($this->_tid)){ 				 $data['tid']=$this->_tid;			}			if(!is_null($this->_catid)){ 				 $data['catid']=$this->_catid;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_level)){ 				 $data['level']=$this->_level;			}			if(!is_null($this->_create_date)){ 				 $data['create_date']=$this->_create_date;			}			if(!is_null($this->_modify_date)){ 				 $data['modify_date']=$this->_modify_date;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('tid' => $this->_tid); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_talent_link,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_talent_link($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_talent_link records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_talent_link(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    		function del_keke_witkey_talent_link(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where tid = $this->_tid "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>