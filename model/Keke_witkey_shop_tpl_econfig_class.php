<?php
  class Keke_witkey_shop_tpl_econfig_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_econfig_id;  		 public $_tpl_direction; 		 public $_shop_logo; 		 public $_ad_text; 		 public $_banner_img; 		 public $_banner_id; 		 public $_skin_color; 		 public $_background; 		 public $_ac_menu_color; 		 public $_font_color; 		 public $_shop_id; 		 public $_uid; 		 public $_on_time; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_shop_tpl_econfig_class(){ 			 $this->_db = new db_factory ( );			 $this->_dbop = $this->_db->create(DBTYPE);			 $this->_tablename = TABLEPRE."itkey_shop_tpl_econfig";		 }	    
	    		public function getEconfig_id(){			 return $this->_econfig_id ;		}		public function getTpl_direction(){			 return $this->_tpl_direction ;		}		public function getShop_logo(){			 return $this->_shop_logo ;		}		public function getAd_text(){			 return $this->_ad_text ;		}		public function getBanner_img(){			 return $this->_banner_img ;		}		public function getBanner_id(){			 return $this->_banner_id ;		}		public function getSkin_color(){			 return $this->_skin_color ;		}		public function getBackground(){			 return $this->_background ;		}		public function getAc_menu_color(){			 return $this->_ac_menu_color ;		}		public function getFont_color(){			 return $this->_font_color ;		}		public function getShop_id(){			 return $this->_shop_id ;		}		public function getUid(){			 return $this->_uid ;		}		public function getOn_time(){			 return $this->_on_time ;		}		public function getWhere(){			 return $this->_where ;		}		public function getCache_config() {			return $this->_cache_config;		}
	    		public function setEconfig_id($value){ 			 $this->_econfig_id = $value;		}		public function setTpl_direction($value){ 			 $this->_tpl_direction = $value;		}		public function setShop_logo($value){ 			 $this->_shop_logo = $value;		}		public function setAd_text($value){ 			 $this->_ad_text = $value;		}		public function setBanner_img($value){ 			 $this->_banner_img = $value;		}		public function setBanner_id($value){ 			 $this->_banner_id = $value;		}		public function setSkin_color($value){ 			 $this->_skin_color = $value;		}		public function setBackground($value){ 			 $this->_background = $value;		}		public function setAc_menu_color($value){ 			 $this->_ac_menu_color = $value;		}		public function setFont_color($value){ 			 $this->_font_color = $value;		}		public function setShop_id($value){ 			 $this->_shop_id = $value;		}		public function setUid($value){ 			 $this->_uid = $value;		}		public function setOn_time($value){ 			 $this->_on_time = $value;		}		public function setWhere($value){ 			 $this->_where = $value;		}		public function setCache_config($_cache_config) {			 $this->_cache_config = $_cache_config; 		}
	    
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
    	
	    /**		 * insert into  keke_witkey_shop_tpl_econfig  ,or add new record		 * @return int last_insert_id		 */		function create_keke_witkey_shop_tpl_econfig(){		 		 $data =  array(); 					if(!is_null($this->_econfig_id)){ 				 $data['econfig_id']=$this->_econfig_id;			}			if(!is_null($this->_tpl_direction)){ 				 $data['tpl_direction']=$this->_tpl_direction;			}			if(!is_null($this->_shop_logo)){ 				 $data['shop_logo']=$this->_shop_logo;			}			if(!is_null($this->_ad_text)){ 				 $data['ad_text']=$this->_ad_text;			}			if(!is_null($this->_banner_img)){ 				 $data['banner_img']=$this->_banner_img;			}			if(!is_null($this->_banner_id)){ 				 $data['banner_id']=$this->_banner_id;			}			if(!is_null($this->_skin_color)){ 				 $data['skin_color']=$this->_skin_color;			}			if(!is_null($this->_background)){ 				 $data['background']=$this->_background;			}			if(!is_null($this->_ac_menu_color)){ 				 $data['ac_menu_color']=$this->_ac_menu_color;			}			if(!is_null($this->_font_color)){ 				 $data['font_color']=$this->_font_color;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			 return $this->_econfig_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 		 } 
	    /**		 * edit table keke_witkey_shop_tpl_econfig		 * @return int affected_rows		 */		function edit_keke_witkey_shop_tpl_econfig(){ 		 		 $data =  array();  					if(!is_null($this->_econfig_id)){ 				 $data['econfig_id']=$this->_econfig_id;			}			if(!is_null($this->_tpl_direction)){ 				 $data['tpl_direction']=$this->_tpl_direction;			}			if(!is_null($this->_shop_logo)){ 				 $data['shop_logo']=$this->_shop_logo;			}			if(!is_null($this->_ad_text)){ 				 $data['ad_text']=$this->_ad_text;			}			if(!is_null($this->_banner_img)){ 				 $data['banner_img']=$this->_banner_img;			}			if(!is_null($this->_banner_id)){ 				 $data['banner_id']=$this->_banner_id;			}			if(!is_null($this->_skin_color)){ 				 $data['skin_color']=$this->_skin_color;			}			if(!is_null($this->_background)){ 				 $data['background']=$this->_background;			}			if(!is_null($this->_ac_menu_color)){ 				 $data['ac_menu_color']=$this->_ac_menu_color;			}			if(!is_null($this->_font_color)){ 				 $data['font_color']=$this->_font_color;			}			if(!is_null($this->_shop_id)){ 				 $data['shop_id']=$this->_shop_id;			}			if(!is_null($this->_uid)){ 				 $data['uid']=$this->_uid;			}			if(!is_null($this->_on_time)){ 				 $data['on_time']=$this->_on_time;			}			if($this->_where){ 				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 			 } 			 else{ 				 $where = array('econfig_id' => $this->_econfig_id); 				 return $this->_db->updatetable($this->_tablename,$data,$where); 			} 		 } 
	    /**		 * query table: keke_witkey_shop_tpl_econfig,if isset where return where record,else return all record		 * @return array 		 */		function query_keke_witkey_shop_tpl_econfig($is_cache=0, $cache_time=0){ 			 if($this->_where){ 				 $sql = "select * from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select * from $this->_tablename"; 			 } 			if ($is_cache) {			 $this->_cache_config ['is_cache'] = $is_cache;			}			if ($cache_time) {			 $this->_cache_config ['time'] = $cache_time;			 }			 if ($this->_cache_config ['is_cache']) {			     if (CACHE_TYPE) {					 $keke_cache = new keke_cache_class ( CACHE_TYPE );					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');						$data = $keke_cache->get ( $id );							if ($data) {								return $data; 							} else { 								$res = $this->_dbop->query ( $sql ); 								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 					 			$this->_where = ""; 								return $res; 							} 						} 			 }else{			 	$this->_where = ""; 				return  $this->_dbop->query ( $sql ); 			 }		 } 
	    /**		 * query count keke_witkey_shop_tpl_econfig records,if iset where query by where 		 * @return int count records		 */		function count_keke_witkey_shop_tpl_econfig(){ 			 if($this->_where){ 				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "select count(*) as count from $this->_tablename"; 			 } 			 $this->_where = ""; 			 return $this->_dbop->getCount($sql); 		 } 
	    /**		 * delete table keke_witkey_shop_tpl_econfig, if isset where delete by where 		 * @return int deleted affected_rows 		 */		function del_keke_witkey_shop_tpl_econfig(){ 			 if($this->_where){ 				 $sql = "delete from $this->_tablename where ".$this->_where; 			 } 			 else{ 				 $sql = "delete from $this->_tablename where econfig_id = $this->_econfig_id "; 			 } 			 $this->_where = ""; 			 return $this->_dbop->execute($sql); 		 } 
	   
	    
	    
   }
 ?>