<?php
  class Keke_witkey_vip_level_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_level_id;  
		 public $_brand;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_vip_level_class(){ 
	    		public function getLevel_id(){
		public function getBrand() {
			return $this->_brand;
		}
	    		public function setLevel_id($value){ 
		public function setBrand($value){
			$this->_brand = $value;
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
			if(!is_null($this->_brand)){
				$data['brand']=$this->_brand;
			}
	    /**
			if(!is_null($this->_brand)){
				$data['brand']=$this->_brand;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>