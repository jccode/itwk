<?php
  class Keke_witkey_task_work_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_work_id;  
		 public $_mark_status;
		 public $_pay_status;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_task_work_class(){ 
	    		public function getWork_id(){
		public function getMark_status(){
			 return $this->_mark_status ;
		}
		public function getPay_status(){
			return $this->_pay_status;
		}
	    		public function setWork_id($value){ 
		public function setMark_status($value){
			 return $this->_mark_status = $value ;
		}
		public function setPay_status($value){
			return $this->_pay_status = $value;
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
			if(!is_null($this->_mark_status)){ 
				 $data['mark_status']=$this->_mark_status;
			}
			if(!is_null($this->_pay_status)){
				$data['pay_status']=$this->_pay_status;
			}
	    /**
			if(!is_null($this->_mark_status)){ 
				 $data['mark_status']=$this->_mark_status;
			}
			if(!is_null($this->_pay_status)){
				$data['pay_status']=$this->_pay_status;
			}
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>