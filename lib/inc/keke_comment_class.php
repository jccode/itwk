<?php

/**
 * @copyright keke-tech
 * @author lj
 * @version v 2.0
 * 2012-1-15上午11:52:51
 */
class keke_comment_class {
	
	protected $_comment_obj;
	protected $_comment_type;
	
	public static function get_instance($comment_type) {
		return new keke_comment_class ( $comment_type );
	}
	function __construct($comment_type) {
		global $kekezu;
		$this->_comment_obj = keke_table_class::get_instance ( "witkey_comment" );
		$this->_comment_type = $comment_type;
	} 
	/**
	 * 
	 * 获取留言信息
	 * @param int $comment_id
	 */
	function get_comment_info($comment_id){ 
		$comment_info = $this->_comment_obj->get_table_info('comment_id', $comment_id);
		return $comment_info;
	}
	/**
	 * 
	 * 获取留言列表（带分页）
	 * @param int $obj_id   
	 * @param string $url
	 * @param int $page
	 */
	function get_comment_list($obj_id,$url, $page){  
		$comment_arr = $this->_comment_obj->get_grid ( "obj_id='$obj_id' and obj_type='".$this->_comment_type."' and p_id=0 order by comment_id desc", $url, $page,10,null,1,'comment_page');
	 	return $comment_arr;
	}
	/**
	 * 
	 * 获取评论列表
	 * @param int $obj_id
	 */
	function get_reply_info($obj_id,$format_arr = false){
		$reply_arr = kekezu::get_table_data("*","witkey_comment","obj_type='".$this->_comment_type."' and obj_id='$obj_id' and p_id>0"," on_time desc");
		
		if($format_arr){
			$temp = array();
			foreach($reply_arr as $k=>$v){
				$temp[$v['p_id']][$v['comment_id']] = $v;
			}
			$reply_arr = $temp;
		}
		
		return $reply_arr; 
	}
	/**
	 * 
	 * 写入留言和回复留言
	 * @param array $comment_arr   ---留言信息的键值对数组
	 * @param int $is_reply		---- 为true代表添加新留言，false代表留言回复
	 *
	 */
	function save_comment($comment_arr,$obj_id,$is_reply=false){
		global $_lang,$kekezu;
		strtolower ( CHARSET ) == 'gbk' and $comment_arr ['content'] = kekezu::utftogbk ( kekezu::escape($comment_arr ['content']) );
		if(kekezu::k_match(array($kekezu->_sys_config['ban_content']),$comment_arr['content'])){
			return 3;
			die();
		}
		$comment_id = $this->_comment_obj->save($comment_arr);
		if(!$is_reply){
		
			if($this->_comment_type=='task'){
				$res = db_factory::execute(sprintf(" update %switkey_task set leave_num =ifnull(leave_num,0)+1 where task_id='%d'",TABLEPRE,$obj_id));
			}elseif($this->_comment_type=='service'){
				$res = db_factory::execute(sprintf(" update %switkey_service set leave_num =ifnull(leave_num,0)+1 where service_id='%d'",TABLEPRE,$obj_id));
			}
	
		}
		return $comment_id;
			
	} 
	/**
	 * 
	 * 留言删除
	 * @param unknown_type $comment_id
	 */
	function del_comment($comment_id,$obj_id,$is_reply=false){
		$res =  db_factory::execute(sprintf("delete from %switkey_comment where comment_id='%d' or p_id='%d'",TABLEPRE,$comment_id,$comment_id));
		if(!$is_reply){
			if($this->_comment_type=='task'){
			$res and 	$res = db_factory::execute(sprintf(" update %switkey_task set leave_num =ifnull(leave_num,0)-1 where task_id='%d'",TABLEPRE,$obj_id));
			}elseif($this->_comment_type=='service'){
			$res and $res = db_factory::execute(sprintf(" update %switkey_service set leave_num =ifnull(leave_num,0)-1 where service_id='%d'",TABLEPRE,$obj_id));
			}
			
		}
		return $res;
	}
	
	
	

}