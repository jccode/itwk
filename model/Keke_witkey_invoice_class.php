<?php
  class Keke_witkey_invoice_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_iv_id;  
		 public $_iv_item_cash; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_invoice_class(){ 
	    		public function getIv_id(){
		public function getIv_item_cash(){
			 return $this->_iv_item_cash ;
		}
	    		public function setIv_id($value){ 
		public function setIv_item_cash($value){ 
			 $this->_iv_item_cash = $value;
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
			if(!is_null($this->_iv_item_cash)){ 
				 $data['iv_item_cash']=$this->_iv_item_cash;
			}
	    /**
			if(!is_null($this->_iv_item_cash)){ 
				 $data['iv_item_cash']=$this->_iv_item_cash;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>