<?php
  class Keke_witkey_corp_site_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_corp_site_id;  		 public $_uid; 		 public $_on_time; 		 public $_corp_site_username; 		 public $_corp_site_note; 		 public $_corp_site; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_corp_site_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."witkey_corp_site";		 }	    
	    		public function getCorp_site_id(){			 return $this->_corp_site_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getCorp_site_username(){			 return $this->_corp_site_username ;		}		public function getCorp_site_note(){			 return $this->_corp_site_note ;		}		public function getCorp_site(){			 return $this->_corp_site ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setCorp_site_id($value){ 			 $this->_corp_site_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setCorp_site_username($value){ 			 $this->_corp_site_username = $value;		}		public function setCorp_site_note($value){ 			 $this->_corp_site_note = $value;		}		public function setCorp_site($value){ 			 $this->_corp_site = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_corp_site  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_corp_site(){		 		 $data =  array(); 					if(!is_null($this->_corp_site_id)){ 				 $data['corp_site_id']=$this->_corp_site_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_corp_site_username)){ 				 $data['corp_site_username']=$this->_corp_site_username;			}			if(!is_null($this->_corp_site_note)){ 				 $data['corp_site_note']=$this->_corp_site_note;			}			if(!is_null($this->_corp_site)){ 				 $data['corp_site']=$this->_corp_site;			}			 return $this->_corp_site_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_corp_site		 * @return int affected_rows		 */		function edit_keke_witkey_corp_site(){ 		 		 $data =  array();  					if(!is_null($this->_corp_site_id)){ 				 $data['corp_site_id']=$this->_corp_site_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if(!is_null($this->_corp_site_username)){ 				 $data['corp_site_username']=$this->_corp_site_username;			}			if(!is_null($this->_corp_site_note)){ 				 $data['corp_site_note']=$this->_corp_site_note;			}			if(!is_null($this->_corp_site)){ 				 $data['corp_site']=$this->_corp_site;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('corp_site_id' => $this->_corp_site_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_corp_site,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_corp_site($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_corp_site records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_corp_site(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    		function del_keke_witkey_corp_site(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where corp_site_id = $this->_corp_site_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>