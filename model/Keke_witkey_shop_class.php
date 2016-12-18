<?php
  class Keke_witkey_shop_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_shop_id;  
		 public $_uid; 
		 public $_username; 
		 public $_shop_type; 
		 public $_shop_level; 
		 public $_shop_name; 
		 public $_shop_desc; 
		 public $_shop_background; 
		 public $_logo; 
		 public $_shop_skin; 
		 public $_shop_active; 
		 public $_is_close; 
		 public $_views; 
		 public $_focus_num; 
		 public $_on_time; 
		 public $_homebanner; 
		 public $_domain; 
		 public $_seo_title; 
		 public $_seo_keywords; 
		 public $_seo_desc; 
		 public $_istop; 
		 public $_isvip; 
		 public $_shop_info; 
		 public $_global_match; 
		 public $_listorder; 
		 public $_service_qq; 
		 public $_shop_intro; 
		 public $_shop_honor; 
		 public $_shop_power; 
		 public $_shop_report; 
		 public $_contact_type; 

	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;
	    public $_where;
	     function  keke_witkey_shop_class(){ 
			 $this->_db = new db_factory ( );
			 $this->_dbop = $this->_db->create(DBTYPE);
			 $this->_tablename = TABLEPRE."witkey_shop";
		 }

	    		public function getShop_id(){
			 return $this->_shop_id ;
		}
		public function getUid(){
			 return $this->_uid ;
		}
		public function getUsername(){
			 return $this->_username ;
		}
		public function getShop_type(){
			 return $this->_shop_type ;
		}
		public function getShop_level(){
			 return $this->_shop_level ;
		}
		public function getShop_name(){
			 return $this->_shop_name ;
		}
		public function getShop_desc(){
			 return $this->_shop_desc ;
		}
		public function getShop_background(){
			 return $this->_shop_background ;
		}
		public function getLogo(){
			 return $this->_logo ;
		}
		public function getShop_skin(){
			 return $this->_shop_skin ;
		}
		public function getShop_active(){
			 return $this->_shop_active ;
		}
		public function getIs_close(){
			 return $this->_is_close ;
		}
		public function getViews(){
			 return $this->_views ;
		}
		public function getFocus_num(){
			 return $this->_focus_num ;
		}
		public function getOn_time(){
			 return $this->_on_time ;
		}
		public function getHomebanner(){
			 return $this->_homebanner ;
		}
		public function getDomain(){
			 return $this->_domain ;
		}
		public function getSeo_title(){
			 return $this->_seo_title ;
		}
		public function getSeo_keywords(){
			 return $this->_seo_keywords ;
		}
		public function getSeo_desc(){
			 return $this->_seo_desc ;
		}
		public function getIstop(){
			 return $this->_istop ;
		}
		public function getIsvip(){
			 return $this->_isvip ;
		}
		public function getShop_info(){
			 return $this->_shop_info ;
		}
		public function getGlobal_match(){
			 return $this->_global_match ;
		}
		public function getListorder(){
			 return $this->_listorder ;
		}
		public function getService_qq(){
			 return $this->_service_qq ;
		}
		public function getShop_intro(){
			 return $this->_shop_intro ;
		}
		public function getShop_honor(){
			 return $this->_shop_honor ;
		}
		public function getShop_power(){
			 return $this->_shop_power ;
		}
		public function getShop_report(){
			 return $this->_shop_report ;
		}
		public function getContact_type(){
			 return $this->_contact_type ;
		}
		public function getWhere(){
			 return $this->_where ;
		}
		public function getCache_config() {
			return $this->_cache_config;
		}

	    		public function setShop_id($value){ 
			 $this->_shop_id = $value;
		}
		public function setUid($value){ 
			 $this->_uid = $value;
		}
		public function setUsername($value){ 
			 $this->_username = $value;
		}
		public function setShop_type($value){ 
			 $this->_shop_type = $value;
		}
		public function setShop_level($value){ 
			 $this->_shop_level = $value;
		}
		public function setShop_name($value){ 
			 $this->_shop_name = $value;
		}
		public function setShop_desc($value){ 
			 $this->_shop_desc = $value;
		}
		public function setShop_background($value){ 
			 $this->_shop_background = $value;
		}
		public function setLogo($value){ 
			 $this->_logo = $value;
		}
		public function setShop_skin($value){ 
			 $this->_shop_skin = $value;
		}
		public function setShop_active($value){ 
			 $this->_shop_active = $value;
		}
		public function setIs_close($value){ 
			 $this->_is_close = $value;
		}
		public function setViews($value){ 
			 $this->_views = $value;
		}
		public function setFocus_num($value){ 
			 $this->_focus_num = $value;
		}
		public function setOn_time($value){ 
			 $this->_on_time = $value;
		}
		public function setHomebanner($value){ 
			 $this->_homebanner = $value;
		}
		public function setDomain($value){ 
			 $this->_domain = $value;
		}
		public function setSeo_title($value){ 
			 $this->_seo_title = $value;
		}
		public function setSeo_keywords($value){ 
			 $this->_seo_keywords = $value;
		}
		public function setSeo_desc($value){ 
			 $this->_seo_desc = $value;
		}
		public function setIstop($value){ 
			 $this->_istop = $value;
		}
		public function setIsvip($value){ 
			 $this->_isvip = $value;
		}
		public function setShop_info($value){ 
			 $this->_shop_info = $value;
		}
		public function setGlobal_match($value){ 
			 $this->_global_match = $value;
		}
		public function setListorder($value){ 
			 $this->_listorder = $value;
		}
		public function setService_qq($value){ 
			 $this->_service_qq = $value;
		}
		public function setShop_intro($value){ 
			 $this->_shop_intro = $value;
		}
		public function setShop_honor($value){ 
			 $this->_shop_honor = $value;
		}
		public function setShop_power($value){ 
			 $this->_shop_power = $value;
		}
		public function setShop_report($value){ 
			 $this->_shop_report = $value;
		}
		public function setContact_type($value){ 
			 $this->_contact_type = $value;
		}
		public function setWhere($value){ 
			 $this->_where = $value;
		}
		public function setCache_config($_cache_config) {
			 $this->_cache_config = $_cache_config; 
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

    	
	    /**
		 * insert into  keke_witkey_shop  ,or add new record
		 * @return int last_insert_id
		 */
		function create_keke_witkey_shop(){
		 		 $data =  array(); 

					if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_shop_type)){ 
				 $data['shop_type']=$this->_shop_type;
			}
			if(!is_null($this->_shop_level)){ 
				 $data['shop_level']=$this->_shop_level;
			}
			if(!is_null($this->_shop_name)){ 
				 $data['shop_name']=$this->_shop_name;
			}
			if(!is_null($this->_shop_desc)){ 
				 $data['shop_desc']=$this->_shop_desc;
			}
			if(!is_null($this->_shop_background)){ 
				 $data['shop_background']=$this->_shop_background;
			}
			if(!is_null($this->_logo)){ 
				 $data['logo']=$this->_logo;
			}
			if(!is_null($this->_shop_skin)){ 
				 $data['shop_skin']=$this->_shop_skin;
			}
			if(!is_null($this->_shop_active)){ 
				 $data['shop_active']=$this->_shop_active;
			}
			if(!is_null($this->_is_close)){ 
				 $data['is_close']=$this->_is_close;
			}
			if(!is_null($this->_views)){ 
				 $data['views']=$this->_views;
			}
			if(!is_null($this->_focus_num)){ 
				 $data['focus_num']=$this->_focus_num;
			}
			if(!is_null($this->_on_time)){ 
				 $data['on_time']=$this->_on_time;
			}
			if(!is_null($this->_homebanner)){ 
				 $data['homebanner']=$this->_homebanner;
			}
			if(!is_null($this->_domain)){ 
				 $data['domain']=$this->_domain;
			}
			if(!is_null($this->_seo_title)){ 
				 $data['seo_title']=$this->_seo_title;
			}
			if(!is_null($this->_seo_keywords)){ 
				 $data['seo_keywords']=$this->_seo_keywords;
			}
			if(!is_null($this->_seo_desc)){ 
				 $data['seo_desc']=$this->_seo_desc;
			}
			if(!is_null($this->_istop)){ 
				 $data['istop']=$this->_istop;
			}
			if(!is_null($this->_isvip)){ 
				 $data['isvip']=$this->_isvip;
			}
			if(!is_null($this->_shop_info)){ 
				 $data['shop_info']=$this->_shop_info;
			}
			if(!is_null($this->_global_match)){ 
				 $data['global_match']=$this->_global_match;
			}
			if(!is_null($this->_listorder)){ 
				 $data['listorder']=$this->_listorder;
			}
			if(!is_null($this->_service_qq)){ 
				 $data['service_qq']=$this->_service_qq;
			}
			if(!is_null($this->_shop_intro)){ 
				 $data['shop_intro']=$this->_shop_intro;
			}
			if(!is_null($this->_shop_honor)){ 
				 $data['shop_honor']=$this->_shop_honor;
			}
			if(!is_null($this->_shop_power)){ 
				 $data['shop_power']=$this->_shop_power;
			}
			if(!is_null($this->_shop_report)){ 
				 $data['shop_report']=$this->_shop_report;
			}
			if(!is_null($this->_contact_type)){ 
				 $data['contact_type']=$this->_contact_type;
			}

			 return $this->_shop_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 
		 } 

	    /**
		 * edit table keke_witkey_shop
		 * @return int affected_rows
		 */
		function edit_keke_witkey_shop(){ 
		 		 $data =  array(); 
 
					if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_shop_type)){ 
				 $data['shop_type']=$this->_shop_type;
			}
			if(!is_null($this->_shop_level)){ 
				 $data['shop_level']=$this->_shop_level;
			}
			if(!is_null($this->_shop_name)){ 
				 $data['shop_name']=$this->_shop_name;
			}
			if(!is_null($this->_shop_desc)){ 
				 $data['shop_desc']=$this->_shop_desc;
			}
			if(!is_null($this->_shop_background)){ 
				 $data['shop_background']=$this->_shop_background;
			}
			if(!is_null($this->_logo)){ 
				 $data['logo']=$this->_logo;
			}
			if(!is_null($this->_shop_skin)){ 
				 $data['shop_skin']=$this->_shop_skin;
			}
			if(!is_null($this->_shop_active)){ 
				 $data['shop_active']=$this->_shop_active;
			}
			if(!is_null($this->_is_close)){ 
				 $data['is_close']=$this->_is_close;
			}
			if(!is_null($this->_views)){ 
				 $data['views']=$this->_views;
			}
			if(!is_null($this->_focus_num)){ 
				 $data['focus_num']=$this->_focus_num;
			}
			if(!is_null($this->_on_time)){ 
				 $data['on_time']=$this->_on_time;
			}
			if(!is_null($this->_homebanner)){ 
				 $data['homebanner']=$this->_homebanner;
			}
			if(!is_null($this->_domain)){ 
				 $data['domain']=$this->_domain;
			}
			if(!is_null($this->_seo_title)){ 
				 $data['seo_title']=$this->_seo_title;
			}
			if(!is_null($this->_seo_keywords)){ 
				 $data['seo_keywords']=$this->_seo_keywords;
			}
			if(!is_null($this->_seo_desc)){ 
				 $data['seo_desc']=$this->_seo_desc;
			}
			if(!is_null($this->_istop)){ 
				 $data['istop']=$this->_istop;
			}
			if(!is_null($this->_isvip)){ 
				 $data['isvip']=$this->_isvip;
			}
			if(!is_null($this->_shop_info)){ 
				 $data['shop_info']=$this->_shop_info;
			}
			if(!is_null($this->_global_match)){ 
				 $data['global_match']=$this->_global_match;
			}
			if(!is_null($this->_listorder)){ 
				 $data['listorder']=$this->_listorder;
			}
			if(!is_null($this->_service_qq)){ 
				 $data['service_qq']=$this->_service_qq;
			}
			if(!is_null($this->_shop_intro)){ 
				 $data['shop_intro']=$this->_shop_intro;
			}
			if(!is_null($this->_shop_honor)){ 
				 $data['shop_honor']=$this->_shop_honor;
			}
			if(!is_null($this->_shop_power)){ 
				 $data['shop_power']=$this->_shop_power;
			}
			if(!is_null($this->_shop_report)){ 
				 $data['shop_report']=$this->_shop_report;
			}
			if(!is_null($this->_contact_type)){ 
				 $data['contact_type']=$this->_contact_type;
			}

			if($this->_where){ 
				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 
			 } 
			 else{ 
				 $where = array('shop_id' => $this->_shop_id); 
				 return $this->_db->updatetable($this->_tablename,$data,$where); 
			} 
		 } 

	    /**
		 * query table: keke_witkey_shop,if isset where return where record,else return all record
		 * @return array 
		 */
		function query_keke_witkey_shop($is_cache=0, $cache_time=0){ 
			 if($this->_where){ 
				 $sql = "select * from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "select * from $this->_tablename"; 
			 } 
			if ($is_cache) {
			 $this->_cache_config ['is_cache'] = $is_cache;
			}
			if ($cache_time) {
			 $this->_cache_config ['time'] = $cache_time;
			 }
			 if ($this->_cache_config ['is_cache']) {
			     if (CACHE_TYPE) {
					 $keke_cache = new keke_cache_class ( CACHE_TYPE );
					 $id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');
						$data = $keke_cache->get ( $id );
							if ($data) {
								return $data; 
							} else { 
								$res = $this->_dbop->query ( $sql ); 
								$keke_cache->set ( $id, $res,$this->_cache_config['time'] ); 
					 			$this->_where = ""; 
								return $res;
 							} 
						} 
			 }else{
			 	$this->_where = ""; 
				return  $this->_dbop->query ( $sql );
 			 }
		 } 

	    /**
		 * query count keke_witkey_shop records,if iset where query by where 
		 * @return int count records
		 */
		function count_keke_witkey_shop(){ 
			 if($this->_where){ 
				 $sql = "select count(*) as count from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "select count(*) as count from $this->_tablename"; 
			 } 
			 $this->_where = ""; 
			 return $this->_dbop->getCount($sql); 
		 } 

	    /**
		 * delete table keke_witkey_shop, if isset where delete by where 
		 * @return int deleted affected_rows 
		 */
		function del_keke_witkey_shop(){ 
			 if($this->_where){ 
				 $sql = "delete from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "delete from $this->_tablename where shop_id = $this->_shop_id "; 
			 } 
			 $this->_where = ""; 
			 return $this->_dbop->execute($sql); 
		 } 




   }
 ?>