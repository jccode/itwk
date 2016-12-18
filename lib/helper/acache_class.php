<?php

abstract class acache_class {
	
	/**
	 * 获取一个缓存的值
	 *
	 * @param string  $id
	 * @return  cache value if false cache is not or cache is expired
	 */
	public abstract function get($id);
	/**
	 * 获取多个缓存的值
	 *
	 * @param array  $ids
	 * @return array list
	 */
	public abstract function mget($ids);
	/**
	 * 创建缓存
	 *
	 * @param string $id
	 * @param string $value
	 * @param int $expire
	 * @param int $dependency
	 * @return boolean true if the value is successfully stored into cache, false otherwises
	 * 
	 */
	public abstract function set($id, $value, $expire = 0, $dependency = null);
	/**
	 * 添加缓存
	 *
	 * @param string 缓存 ID   -$id
	 * @param string 缓存值           $value
	 * @param int    到期时间单位为秒   $expire
	 * @param int    依赖缓存的项目，如果有变动，缓存无效   $dependency
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 * 
	 */
	public abstract function add($id, $value, $expire = 0, $dependency = null) ;
	/**
	 * 删除指定缓存
	 *
	 * @param string $id
	 * @return boolean whether the deletion is successful
	 * 
	 */
	public abstract function del($id) ;
	/**
	 * 清空所有缓存
	 *
	 */
	public abstract function flush() ;

}

?>