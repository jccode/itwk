<?php
  class Keke_witkey_user_auth_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_u_auth_id;  		 public $_uid; 		 public $_username; 		 public $_id_num; 		 public $_real_name; 		 public $_origo; 		 public $_licen_pic; 		 public $_status; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_user_auth_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_user_auth";		 }	    
	    		public function getU_auth_id(){			 return $this->_u_auth_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getUsername(){			 return $this->_username ;		}		public function getId_num(){			 return $this->_id_num ;		}		public function getReal_name(){			 return $this->_real_name ;		}		public function getOrigo(){			 return $this->_origo ;		}		public function getLicen_pic(){			 return $this->_licen_pic ;		}		public function getStatus(){			 return $this->_status ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setU_auth_id($value){ 			 $this->_u_auth_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setUsername($value){ 			 $this->_username = $value;		}		public function setId_num($value){ 			 $this->_id_num = $value;		}		public function setReal_name($value){ 			 $this->_real_name = $value;		}		public function setOrigo($value){ 			 $this->_origo = $value;		}		public function setLicen_pic($value){ 			 $this->_licen_pic = $value;		}		public function setStatus($value){ 			 $this->_status = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_user_auth  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_user_auth(){		 		 $data =  array(); 					if(!is_null($this->_u_auth_id)){ 				 $data['u_auth_id']=$this->_u_auth_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_id_num)){ 				 $data['id_num']=$this->_id_num;			}			if(!is_null($this->_real_name)){ 				 $data['real_name']=$this->_real_name;			}			if(!is_null($this->_origo)){ 				 $data['origo']=$this->_origo;			}			if(!is_null($this->_licen_pic)){ 				 $data['licen_pic']=$this->_licen_pic;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			 return $this->_u_auth_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_user_auth		 * @return int affected_rows		 */		function edit_keke_witkey_user_auth(){ 		 		 $data =  array();  					if(!is_null($this->_u_auth_id)){ 				 $data['u_auth_id']=$this->_u_auth_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_username)){ 				 $data['username']=$this->_username;			}			if(!is_null($this->_id_num)){ 				 $data['id_num']=$this->_id_num;			}			if(!is_null($this->_real_name)){ 				 $data['real_name']=$this->_real_name;			}			if(!is_null($this->_origo)){ 				 $data['origo']=$this->_origo;			}			if(!is_null($this->_licen_pic)){ 				 $data['licen_pic']=$this->_licen_pic;			}			if(!is_null($this->_status)){ 				 $data['status']=$this->_status;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('u_auth_id' => $this->_u_auth_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_user_auth,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_user_auth($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_user_auth records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_user_auth(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_user_auth, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_user_auth(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where u_auth_id = $this->_u_auth_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>