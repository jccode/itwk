<?php
  class Keke_witkey_payitem_record_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_record_id;  
		 public $_status;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_payitem_record_class(){ 
	    		public function getRecord_id(){
		public function getStatus(){
			 return $this->_status ;
		}
	    		public function setRecord_id($value){ 
		public function setStatus($value){ 
			 $this->_status = $value;
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
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}
	    /**
			if(!is_null($this->_status)){ 
				 $data['status']=$this->_status;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>