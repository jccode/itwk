<?php
  class Keke_witkey_vip_history_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_h_id;  
		 public $_remark; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_vip_history_class(){ 
	    		public function getH_id(){
		public function getRemark(){
			 return $this->_remark ;
		}
	    		public function setH_id($value){ 
		public function setRemark($value){ 
			 $this->_remark = $value;
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
			if(!is_null($this->_remark)){ 
				 $data['remark']=$this->_remark;
			}
	    /**
			if(!is_null($this->_remark)){ 
				 $data['remark']=$this->_remark;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>