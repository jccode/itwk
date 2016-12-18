<?php
/**
 * 
 * this is not free software
 * 
 * @example     $c_obj = new keke_cache_class('file');
 * $c_obj->set("file1", "value");
 * $c_obj->get("file1");
 * $c_obj = new keke_cache_class("eacc");
 * $c_obj->set('eacc',"eacc_value");
 * $c_obj->get("eacc");
 * $config_arr = array(0=>array("host"=>"192.168.1.69"));
 * $m_obj = new keke_cache_class('mem',$config_arr);
 * $m_obj->set('mem',"mem_value");
 * $m_obj->get("mem");
 * @author michael
 *
 */
class keke_cache_class {
	
	public $_config = array ();
	public $_ext = array ();
	public $_memory;
	public $_enable = false;
	private static $_cache_obj = array ();
	
	public function get_config() {
		return $this->_config;
	}
	/**
	 * 
	 * construct cache calss 
	 * @var string $cache_type  -- 'file' ,'eacc','apc','mem'
	 * @var array $config   --array(0=>array("host"=>"127.0.0.1","port"=>"11211"))
	 * @return cache obj 
	 */
	public function __construct($cache_type = CACHE_TYPE, $config = array()) {
		return $this->_memory = $this->get_instance ( $cache_type, $config );
	}
	
	public function get_instance($cache_type, $config) {
		static $cache_obj = null;
		
		$class_name = $cache_type . "_cache_class";
		if ($cache_obj [$cache_type] == null) {
			$cache_obj [$cache_type] = new $class_name ( $config );
		}
		return $cache_obj [$cache_type];
	}
	public function generate_id($id) {
		return TABLEPRE . substr(md5 ($id),0,8);
	}
	public function get($id, $use_expires = true) {
		  return $this->_memory->get ( $id, $use_expires );
	}
	public static function mget($ids) {
		if (! IS_CACHE) {
			return false;
		}
		return $this->_memory->mget ( $ids );
	}
	public function set($id, $value, $expire = 0, $dependency = null) {
		if (! IS_CACHE) {
			return false;
		}
		return $this->_memory->set ( $id, $value, $expire, $dependency );
	}
	public function add($id, $value, $expire = 0, $dependency = null) {
		return $this->_memory->add ( $id, $value, $expire, $dependency );
	}
	public function del($id) {
		return $this->_memory->del ( $id );
	}
	public function flush($dirname = '') {
		return $this->_memory->flush ( $dirname );
	}
	public function setConfig($_config) {
		$this->_memory->setServers ( $_config );
	}
	public function gc(){
		$this->_memory->flush();
	}
	

}

?>