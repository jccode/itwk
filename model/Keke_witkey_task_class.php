<?php
  class Keke_witkey_task_class{
        public $_db;
        public $_tablename;
	    public $_dbop;
	    	 public $_task_id;  
		 public $_wiki_num;
	    public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	    public $_replace=0;
	    public $_where;
	     function  keke_witkey_task_class(){ 
	    		public function getTask_id(){
		public function getWiki_num(){
			 return $this->_wiki_num ;
		}
	    		public function setTask_id($value){ 
		public function setWiki_num($value){ 
			 $this->_wiki_num = $value;
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
			if(!is_null($this->_wiki_num)){ 
				 $data['wiki_num']=$this->_wiki_num;
			}
	    /**
			if(!is_null($this->_wiki_num)){ 
				 $data['wiki_num']=$this->_wiki_num;
			}
	    /**
	    /**
	    /**



   }
 ?>