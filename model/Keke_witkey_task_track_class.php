<?php
  class Keke_witkey_task_track_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_t_id;  
		 public $_ext; 
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;  
	    public $_where;      
	     function  keke_witkey_task_track_class(){ 
	    		public function getT_id(){
 	 	public function getExt(){
			 return $this->_ext ;
		}
	    		public function setT_id($value){ 
		public function setExt($value){ 
			 $this->_ext = $value;
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
			if(!is_null($this->_ext)){ 
				 $data['ext']=$this->_ext;
			}
	    /**
				 $data['ext']=$this->_ext;
			}
			
	    /**
	    /**
	    /**
	   
	    
	    
   }
 ?>