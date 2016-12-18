<?php
  class Keke_witkey_job_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_job_id;  
		 public $_shop_id; 
		 public $_uid; 
		 public $_username; 
		 public $_job_title; 
		 public $_job_type; 
		 public $_number; 
		 public $_sex; 
		 public $_education; 
		 public $_age; 
		 public $_salary; 
		 public $_experience_year; 
		 public $_job_address; 
		 public $_need_obj; 
		 public $_intro; 
		 public $_pub_time; 
		 public $_cut_time; 
		 public $_contact_people; 
		 public $_contact_tel; 
		 public $_contact_fax; 
		 public $_contact_address; 
		 public $_is_top; 

	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_job_class(){ 
			 $this->_db = new db_factory ( );
			 $this->_dbop = $this->_db->create(DBTYPE);
			 $this->_tablename = TABLEPRE."witkey_job";
		 }
	    
	    		public function getJob_id(){
			 return $this->_job_id ;
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
		public function getJob_title(){
			 return $this->_job_title ;
		}
		public function getJob_type(){
			 return $this->_job_type ;
		}
		public function getNumber(){
			 return $this->_number ;
		}
		public function getSex(){
			 return $this->_sex ;
		}
		public function getEducation(){
			 return $this->_education ;
		}
		public function getAge(){
			 return $this->_age ;
		}
		public function getSalary(){
			 return $this->_salary ;
		}
		public function getExperience_year(){
			 return $this->_experience_year ;
		}
		public function getJob_address(){
			 return $this->_job_address ;
		}
		public function getNeed_obj(){
			 return $this->_need_obj ;
		}
		public function getIntro(){
			 return $this->_intro ;
		}
		public function getPub_time(){
			 return $this->_pub_time ;
		}
		public function getCut_time(){
			 return $this->_cut_time ;
		}
		public function getContact_people(){
			 return $this->_contact_people ;
		}
		public function getContact_tel(){
			 return $this->_contact_tel ;
		}
		public function getContact_fax(){
			 return $this->_contact_fax ;
		}
		public function getContact_address(){
			 return $this->_contact_address ;
		}
		public function getIs_top(){
			 return $this->_is_top ;
		}
		public function getWhere(){
			 return $this->_where ;
		}
		public function getCache_config() {
			return $this->_cache_config;
		}

	    		public function setJob_id($value){ 
			 $this->_job_id = $value;
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
		public function setJob_title($value){ 
			 $this->_job_title = $value;
		}
		public function setJob_type($value){ 
			 $this->_job_type = $value;
		}
		public function setNumber($value){ 
			 $this->_number = $value;
		}
		public function setSex($value){ 
			 $this->_sex = $value;
		}
		public function setEducation($value){ 
			 $this->_education = $value;
		}
		public function setAge($value){ 
			 $this->_age = $value;
		}
		public function setSalary($value){ 
			 $this->_salary = $value;
		}
		public function setExperience_year($value){ 
			 $this->_experience_year = $value;
		}
		public function setJob_address($value){ 
			 $this->_job_address = $value;
		}
		public function setNeed_obj($value){ 
			 $this->_need_obj = $value;
		}
		public function setIntro($value){ 
			 $this->_intro = $value;
		}
		public function setPub_time($value){ 
			 $this->_pub_time = $value;
		}
		public function setCut_time($value){ 
			 $this->_cut_time = $value;
		}
		public function setContact_people($value){ 
			 $this->_contact_people = $value;
		}
		public function setContact_tel($value){ 
			 $this->_contact_tel = $value;
		}
		public function setContact_fax($value){ 
			 $this->_contact_fax = $value;
		}
		public function setContact_address($value){ 
			 $this->_contact_address = $value;
		}
		public function setIs_top($value){ 
			 $this->_is_top = $value;
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
		 * insert into  keke_witkey_job  ,or add new record
		 * @return int last_insert_id
		 */
		function create_keke_witkey_job(){
		 		 $data =  array(); 

					if(!is_null($this->_job_id)){ 
				 $data['job_id']=$this->_job_id;
			}
			if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_job_title)){ 
				 $data['job_title']=$this->_job_title;
			}
			if(!is_null($this->_job_type)){ 
				 $data['job_type']=$this->_job_type;
			}
			if(!is_null($this->_number)){ 
				 $data['number']=$this->_number;
			}
			if(!is_null($this->_sex)){ 
				 $data['sex']=$this->_sex;
			}
			if(!is_null($this->_education)){ 
				 $data['education']=$this->_education;
			}
			if(!is_null($this->_age)){ 
				 $data['age']=$this->_age;
			}
			if(!is_null($this->_salary)){ 
				 $data['salary']=$this->_salary;
			}
			if(!is_null($this->_experience_year)){ 
				 $data['experience_year']=$this->_experience_year;
			}
			if(!is_null($this->_job_address)){ 
				 $data['job_address']=$this->_job_address;
			}
			if(!is_null($this->_need_obj)){ 
				 $data['need_obj']=$this->_need_obj;
			}
			if(!is_null($this->_intro)){ 
				 $data['intro']=$this->_intro;
			}
			if(!is_null($this->_pub_time)){ 
				 $data['pub_time']=$this->_pub_time;
			}
			if(!is_null($this->_cut_time)){ 
				 $data['cut_time']=$this->_cut_time;
			}
			if(!is_null($this->_contact_people)){ 
				 $data['contact_people']=$this->_contact_people;
			}
			if(!is_null($this->_contact_tel)){ 
				 $data['contact_tel']=$this->_contact_tel;
			}
			if(!is_null($this->_contact_fax)){ 
				 $data['contact_fax']=$this->_contact_fax;
			}
			if(!is_null($this->_contact_address)){ 
				 $data['contact_address']=$this->_contact_address;
			}
			if(!is_null($this->_is_top)){ 
				 $data['is_top']=$this->_is_top;
			}

			 return $this->_job_id = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace); 
		 } 

	    /**
		 * edit table keke_witkey_job
		 * @return int affected_rows
		 */
		function edit_keke_witkey_job(){ 
		 		 $data =  array(); 
 
					if(!is_null($this->_job_id)){ 
				 $data['job_id']=$this->_job_id;
			}
			if(!is_null($this->_shop_id)){ 
				 $data['shop_id']=$this->_shop_id;
			}
			if(!is_null($this->_uid)){ 
				 $data['uid']=$this->_uid;
			}
			if(!is_null($this->_username)){ 
				 $data['username']=$this->_username;
			}
			if(!is_null($this->_job_title)){ 
				 $data['job_title']=$this->_job_title;
			}
			if(!is_null($this->_job_type)){ 
				 $data['job_type']=$this->_job_type;
			}
			if(!is_null($this->_number)){ 
				 $data['number']=$this->_number;
			}
			if(!is_null($this->_sex)){ 
				 $data['sex']=$this->_sex;
			}
			if(!is_null($this->_education)){ 
				 $data['education']=$this->_education;
			}
			if(!is_null($this->_age)){ 
				 $data['age']=$this->_age;
			}
			if(!is_null($this->_salary)){ 
				 $data['salary']=$this->_salary;
			}
			if(!is_null($this->_experience_year)){ 
				 $data['experience_year']=$this->_experience_year;
			}
			if(!is_null($this->_job_address)){ 
				 $data['job_address']=$this->_job_address;
			}
			if(!is_null($this->_need_obj)){ 
				 $data['need_obj']=$this->_need_obj;
			}
			if(!is_null($this->_intro)){ 
				 $data['intro']=$this->_intro;
			}
			if(!is_null($this->_pub_time)){ 
				 $data['pub_time']=$this->_pub_time;
			}
			if(!is_null($this->_cut_time)){ 
				 $data['cut_time']=$this->_cut_time;
			}
			if(!is_null($this->_contact_people)){ 
				 $data['contact_people']=$this->_contact_people;
			}
			if(!is_null($this->_contact_tel)){ 
				 $data['contact_tel']=$this->_contact_tel;
			}
			if(!is_null($this->_contact_fax)){ 
				 $data['contact_fax']=$this->_contact_fax;
			}
			if(!is_null($this->_contact_address)){ 
				 $data['contact_address']=$this->_contact_address;
			}
			if(!is_null($this->_is_top)){ 
				 $data['is_top']=$this->_is_top;
			}

			if($this->_where){ 
				 return $this->_db->updatetable($this->_tablename,$data,$this->getWhere()); 
			 } 
			 else{ 
				 $where = array('job_id' => $this->_job_id); 
				 return $this->_db->updatetable($this->_tablename,$data,$where); 
			} 
		 } 

	    /**
		 * query table: keke_witkey_job,if isset where return where record,else return all record
		 * @return array 
		 */
		function query_keke_witkey_job($is_cache=0, $cache_time=0){ 
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
		 * query count keke_witkey_job records,if iset where query by where 
		 * @return int count records
		 */
		function count_keke_witkey_job(){ 
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
		 * delete table keke_witkey_job, if isset where delete by where 
		 * @return int deleted affected_rows 
		 */
		function del_keke_witkey_job(){ 
			 if($this->_where){ 
				 $sql = "delete from $this->_tablename where ".$this->_where; 
			 } 
			 else{ 
				 $sql = "delete from $this->_tablename where job_id = $this->_job_id "; 
			 } 
			 $this->_where = ""; 
			 return $this->_dbop->execute($sql); 
		 } 

	   
	    
	    
   }
 ?>