<?php
  class Keke_witkey_auth_realname_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_realname_a_id;  
		 public $_id_starttime;
		 public $_id_address;
		 public $_id_type;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_auth_realname_class(){ 
	    		public function getRealname_a_id(){
		public function getId_starttime() {
			return $this->_id_starttime;
		}
		public function getId_address() {
			return $this->_id_address;
		}
		public function getId_type() {
			return $this->_id_type;
		}
	    		public function setRealname_a_id($value){ 
		public function setId_starttime($value){
			$this->_id_starttime = $value;
		}
		public function setId_address($value){
			$this->_id_address = $value;
		}
		public function setId_type($value){
			$this->_id_type = $value;
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
			if(!is_null($this->_id_starttime)){
				$data['id_starttime']=$this->_id_starttime;
			}
			if(!is_null($this->_id_address)){
				$data['id_address']=$this->_id_address;
			}
			if(!is_null($this->_id_type)){
				$data['id_type']=$this->_id_type;
			}
	    /**
			if(!is_null($this->_id_starttime)){
				$data['id_starttime']=$this->_id_starttime;
			}
			if(!is_null($this->_id_address)){
				$data['id_address']=$this->_id_address;
			}
			if(!is_null($this->_id_type)){
				$data['id_type']=$this->_id_type;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>