<?php
  class Keke_witkey_brand_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	     public $_brand_id;  
		 public $_is_recommend; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_brand_class(){ 
	    		public function getBrand_id(){
		public function getIs_recommend(){
			 return $this->_is_recommend ;
		}
	    		public function setBrand_id($value){ 
		public function setIs_recommend($value){ 
			 $this->_is_recommend = $value;
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
			if(!is_null($this->_is_recommend)){ 
				 $data['is_recommend']=$this->_is_recommend;
			}
	    /**
			if(!is_null($this->_is_recommend)){ 
				 $data['is_recommend']=$this->_is_recommend;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>