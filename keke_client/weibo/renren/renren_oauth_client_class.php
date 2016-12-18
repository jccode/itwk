<?php 
require_once 'RenrenRestApiService.class.php';
class renren_oauth_client_class extends base_client_class {
	public $_renren_obj;  
	const  AUTH_URL ='https://graph.renren.com/oauth/authorize?';
	const ACCESSURL = 'https://api.renren.com/v2/user?access_token=';
	function __construct($app_key,$app_secret){
		parent::__construct($app_key,$app_secret);
		$this->_renren_obj = new RenrenRestApiService( $this->_app_key,$this->_app_secret);
	}
	//获得授权链接的
	function get_auth_url($callback){
		//$callback = "/slogin/callback.asp?type=renren";//测试用
		return self::AUTH_URL.'&client_id='.$this->_app_key.'&redirect_uri='.urlencode($callback).'&response_type=code';
	}
	
	//验证是否有授权
	function get_access_token(){
		return $_SESSION['auth_renren']['last_key'];
	}
	//销毁授权
	function clear_access_token(){
		unset($_SESSION['auth_renren']);
	}
	/**
	 * 通过授权
	 * @see base_client_class::create_access_token()
	 */
	function create_access_token($oauth_verifier=false){
		$code = $oauth_verifier['code'];//用户授权页面返回的auth code
		if($code){
			$token_url = self::AUTH_URL.'client_id='.$this->_app_key."&redirect_uri="
						 .urlencode($oauth_verifier['redirect_uri'])."&client_secret="
						 .$this->_app_secret.'&code='. $code; 
			$data = file_get_contents($token_url); 
			$last_key = '';
			parse_str($response,$last_key); //解析获取参数
			if($last_key['access_token']){
				$_SESSION['auth_renren']['last_key'] = $last_key['access_token'];
			}else{
				kekezu::error_handler( 001, 'access_token不存在或者已过期' );
				return false;
			}
		}else{
			kekezu::error_handler( 001,'Authorization Code missing!' );
		}
		return true;
	}
	
	function get_login_info(){
		global $_K;
		$p= $_SESSION['auth_renren']['last_key'];
		$graph_url = self::ACCESSURL.$p['access_token'];
		$data      = json_decode(file_get_contents($graph_url));
		$data = $this->user_data_format($data);
		var_dump($data);die();
		return $data;
		 
	}
	/**
	 * 使用accessToken 获取好友列表
	 */
	function get_friends(){
		$params = array('page'=>'1','count'=>'2','access_token'=>$_SESSION['auth_renren']['last_key']['access_token']);
		$res = $this->_renren_obj->rr_post_curl('friends.getFriends', $params);//curl函数发送请求
	}
	/**
	 * 发新鲜事
	 * @param $title 事件标题
	 * @param $desc  事件内容
	 * @param $url   传递链接
	 * @param $image 图片链接
	 */
	function post_wb($data,$image){
			 
		$params = array('name'=>$data['title'],
						'description'=>$data['content'],
						'url'=>$data['url'],
						'image'=>$data['image'],
						'action_name'=>$data['ac_name'],
						'action_link'=>$data['link'],
						'message'=>$data['msg'],
						'access_token'=>$_SESSION ['auth_sina'] ['last_key'] ['access_token']
		);//使用access_token调api的情况
		$res = $this->_renren_obj->rr_post_curl('feed.publishFeed', $params);//curl函数发送请求
	}
	/**
	 * 上传图片
	 * //网络图片上传的方式，格式如下
	 * name、tmp_name和type可以根据自己的情况修改，
	 * 	type一定要与图片的后缀类型对应起来
	 */
	function upload_photo($photo_name,$photo_url,$photo_ext='png'){
		$myfile=array('upload'=>array(
					'name'=>$photo_name,
					'tmp_name'=>$photo_url,//如果是服务器本地图片，可以这么写：'tmp_name'=>'c:/pic.jpg'
					'type'=>'image/'.$photo_ext
		));
		$params = array('caption'=>'description','access_token'=>$_SESSION ['auth_sina'] ['last_key'] ['access_token']);
		$res = $this->_renren_obj->rr_post_curl('photos.upload',$params,$myfile);	
	}
	
	//用户数据格式化
	function user_data_format($data){
		$r = array();
		 
		if(!$data){
		 	return false;
		}
		$r['account'] = $data['nickname'];
		$r["name"]=$data['nickname'];
		$r["location"]="";//$data['location'];
		$r['img']=$data['figureurl'];
		$r['url']="";
	 	$r['wb_count']="";
		$r['sex'] = $data['gender'];
		 
		return $r;
	}
	//微博数据格式化
	function wb_data_format($data){
		$r = array();
		$r['id']=$data['id'];
		$r['text']=$data['origtext'];
		$r['uid']=$data['name'];
		$r['username']=$data['nick'];
		$r['img'] = $data['image'][0]?$data['image'][0].'/120':null;
		$r['url']="http://t.qq.com/p/t/{$r['id']}";
		return $r;
	}
	
	function get_operate(){
		return $this->_renren_weibo_oauth;
	}
	
	function get_client(){
		return $this->_renren_weibo_client;
	}
}