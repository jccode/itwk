<?php
  class Keke_witkey_withdraw_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    public $_withdraw_id;  
		 public $_fee;
		 public $_brand; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_withdraw_class(){ 
	    		public function getWithdraw_id(){
		public function getFee(){
			 return $this->_fee ;
		}
        public function getBrand(){
			 return $this->_brand ;
		}
	    		public function setWithdraw_id($value){ 
		public function setFee($value){ 
			 $this->_fee = $value;
		}
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
			if(!is_null($this->_fee)){ 
				 $data['fee']=$this->_fee;
			}
		    if(!is_null($this->_brand)){ 
				 $data['brand']=$this->_brand;
			}
	    /**
			if(!is_null($this->_fee)){ 
				 $data['fee']=$this->_fee;
			}
		   if(!is_null($this->_brand)){ 
				 $data['brand']=$this->_brand;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>