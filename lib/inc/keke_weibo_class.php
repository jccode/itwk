<?php

/** 
 * @author Administrator
 * @version 2.0
 * @property 微博的业务工厂类，本类可以实现，新浪，腾讯，163，搜狐微博的业务操作，主要操作有
 * 1.发送(带图)微博 ;2.转发指定微薄;3.关注指定用户；4.取消指定用户关注;
 * 5.获取指定微博信息;6.获取指定用户微博列表;7.获取指定用户粉丝列表
 * 8.获取指定用户的信息,9.评论某个微博 ,10.删除指定微表,11,获取用户优质粉丝列表
 * 
 * 
 */
// is_file(S_ROOT.'/config/lic.php') or die('Unauthorized domain name, access denied');
// list($key,$domain) =implode('_', $_K['ci']);
// keke_encrypt_class::$_key = 'f18724d0acbae50fdf25dc0673fe516a';
// $real_domain = keke_encrypt_class::decode ( $domain );
// $localhost_ip = $_SERVER ['HTTP_HOST'];
// if (! ($localhost_ip == '127.0.0.1' || $localhost_ip == 'localhost' || strpos ( $localhost_ip, '192.168' ) !== false)) {
// 	if (! (strpos ( $localhost_ip, $real_domain ) !== false)) {
// 		die ( 'Unauthorized domain name, access denied' );
// 	}
// }
class keke_weibo_class extends keke_oauth_base_class {
 
	function __construct($wb_type,$call_back=null,$url=null){
	  parent::__construct ( $wb_type );
      $oo= new keke_oauth_login_class($wb_type);
      $_SESSION['auth_'.$wb_type]['last_key'] or   	$oo->login($call_back, $url);
    }    

    /**
     * 发送带图片微博
     * @param $img 带http的路径,只支持jpg,png,gif,最大只支持5M
     */
    function post_wb($txt,$img=null){
    	return oauth_api_factory::post_wb($txt,$img);
    }
    /**
     * statuses/repost 转发一条微博信息 
     */
    function repost_wb($sid, $text = null){
    	return oauth_api_factory::repost_wb($sid, $text);
    }
    /**
     * friendships/create 关注某用户 
     */
    function focus_by_uid($uid_or_name){
    	return oauth_api_factory::follow_wb_user($uid_or_name,$this->_wb_type,$this->_app_id,$this->_app_secret);
    }
    /**
     * 取消关注 friendships/destroy 取消关注某用户 
     */
    function unfocus_by_uid(){
    	
    }
    /**
     * 获取指定微博
     * @param $param可以是url(根据url判断是否存在)
     */
    function get_weibo_by_sid($sid){
    	return oauth_api_factory::get_wb_info($sid);    	
    }
    /**
     * 获取用户微博列表
     */
    function get_weibo_list_by_uid(){
    	
    }
    /**
     * 获取粉丝列表(默认为当前用户)
     */
    function get_followers_by_uid($wb_uid=null,$count=20){
    	return oauth_api_factory::get_fans_list($wb_uid,'',$count);
    }
    /**
     *获取优质粉丝列表
     */
	function get_good_followers_by_uid(){
    	
    } 
    /**
     * 获取指定用户微博信息
     */
    function get_user_info_by_uid(){
    	
    }
    /**
     * 评论指定微博
     */
    function comment_wb_by_wid($sid,$text){
    	return oauth_api_factory::send_comment($sid,$text);
    }
    /**
     * 删除指定微博
     */
    function del_wb_by_wid(){
    	
    }
    /**
     * 根据mid获取sid,仅仅适用于新浪微博
     * @param unknown_type $sid
     */
    function query_sid($mid){
    	if ($this->_wb_type!='sina'){
    		return;
    	}
    	return oauth_api_factory::query_sid($mid);
    }
    /**
     * 生成微博信息的URL
     * @param string $wb_type  微博类型
     * @param int $account_id  微博UID
     * @param int $sid   微博ID
     * @return string
     */
    static function build_wb_url($wb_type,$account_id,$sid){
		$r = "";
		switch ($wb_type) {
			case 'sina':
				$r ="http://api.t.sina.com.cn/{$account_id}/statuses/{$sid}";
			break;
			case 'ten':
				$r = "http://t.qq.com/p/t/{$sid}";
			break;
			case 'netease':
				$r = "http://t.163.com/{$account_id}/status/{$sid}";
			break;
			case 'souhu':
				$r = "http://t.sohu.com/u/$sid";
			break;
		}
		return $r;
	 
	}

}

?>