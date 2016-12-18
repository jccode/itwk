<?php
require_once 'DouBan/PeopleEntry.php';
require_once 'DouBan/PeopleFeed.php';
require_once 'DouBan/Extension/Attribute.php';
require_once 'client.php';

class douban_oauth_client_class extends base_client_class
{
	const SERVER_URL = 'http://api.douban.com';
	protected $_APIKey = NULL;
	protected $_client = NULL;
	
	public static $namespaces = array(
		'db' => 'http://www.douban.com/xmlns/',
		'gd' => 'http://schemas.google.com/g/2005',
	);
	
	public function __construct($app_key, $app_secret)
    	{
			$this->_app_key = $app_key;
			$this->_app_secret = $app_secret;
			$this->_APIKey = $apiKey;
			parent::__construct($app_key,$app_secret);
			$this->_client = new OAuthClient($app_key, $app_secret);
    	}
	
	//API authorization
	public function get_auth_url($callback = NULL)
	{	//$callback = '/slogin/callback.asp?type=douban';//测试用
		$request_token = $this->_client->getRequestToken();//临时token
		var_dump($request_token);die();
		return $this->_client->getAuthorizationUrl($this->_app_key,$this->_app_secret, $callback);
	}
	//验证是否有授权
	function get_access_token(){
		return $_SESSION['auth_douban']['last_key'];
	}
	//销毁授权
	function clear_access_token(){
		unset($_SESSION['auth_douban']);
	}
	//申请授权
	public function create_access_token($oauth_verifier=false){
		$token   = $this->getAccessToken($this->_app_key,$this->_app_secret,$r_token);
		if($token){
				$_SESSION['auth_douban']['last_key'] = $token;
			}else{
				kekezu::error_handler( 001, 'access_token不存在或者已过期' );
				return false;
			}
	}
	function get_login_info(){
		global $_K;
		var_dump($data);die();
		return $data;
		 
	}
	function post_wb($data,$image){}
	// 用户数据格式化
	function user_data_format($data) {
		global $k;
		$r = array ();
		///***/
		return $r;
	}
	function wb_data_format($data){
		return array();
	}
	function get_operate(){
		return true;
	}
	
	function get_client(){
		return true;
	}
	public function getRequestToken()
    {
        return $this->_client->getRequestToken();
    }

	public function getAccessToken($key = NULL, $secret = NULL, $token = NULL)
    {
        return $this->_client->getAccessToken($key, $secret);
    }
	public function programmaticLogin($tokenKey = NULL, $tokenSecret = NULL)
	{
		return $this->_client->login($tokenKey, $tokenSecret);
	}
	
	public function getEntry($url = NULL, $className = NULL)
	{
		$authHeaderArr = $this->_client->getAuthHeader('GET', $url);
		$authHeader = $authHeaderArr[0];
		$headerStr = $authHeaderArr[1];
		$this->_client->clearHeaders();
		if ($authHeader) {
			$this->_httpClient->setHeaders($authHeader);
		} 
	        else if ($this->_APIKey) {
			$param = 'apikey=' . urlencode($this->_APIKey);
			if (stristr($url, '?')) {
				$url = $url . '&' . $param;
			} else {
				$url = $url . '?' . $param;
			}
		}
		return parent::getEntry($url, $className);
	}
	
        public function getFeed($url = NULL, $className = NULL)
	{
		$authHeaderArr = $this->_client->getAuthHeader('GET', $url);
		$authHeader = $authHeaderArr[0];
		$headerStr = $authHeaderArr[1];
		$this->_client->clearHeaders();
		if ($authHeader) {
			$this->_httpClient->setHeaders($authHeader);
		} 
	        else if ($this->_APIKey) {
			$param = 'apikey=' . urlencode($this->_APIKey);
			if (stristr($url, '?')) {
				$url = $url . '&' . $param;
			} else {
				$url = $url . '?' . $param;
			}
		}
		return parent::getFeed($url, $className);
	}
	
	public function post($data, $uri = null, $remainingRedirects = null, 
			$contentType = null, $extraHeaders = null) 
	{
		if ($extraHeaders == NULL) {
			$extraHeaders = array();
		}
		$HeadersArr = $this->_client->getAuthHeader('POST', $uri);
		$Headers = $HeadersArr[0];
		$tmp = array();
		$tmp = array_merge($Headers, $extraHeaders);
		$extraHeaders = $tmp;
		return parent::post($data, $uri, $remainingRedirects, 
			$contentType, $extraHeaders);
	}
	
	public function put($data, $url = NULL, $remainingRedirects = null, 
			$contentType = null, $extraHeaders = null)
	{
		 if ($extraHeaders == NULL) {
                        $extraHeaders = array();
                }
                $HeadersArr = $this->_client->getAuthHeader('PUT', $url);
                $Headers = $HeadersArr[0];
                $tmp = array();
                $tmp = array_merge($Headers, $extraHeaders);
                $extraHeaders = $tmp;
		$this->_httpClient->setHeaders($extraHeaders);
		return parent::put($data, $url, $remainingRedirects, 
			$contentType, $extraHeaders);
	}
	
	public  function delete($url)
	{
		$extraHeadersArr = $this->_client->getAuthHeader('DELETE', $url);
                $extraHeaders = $extraHeadersArr[0];
                $headerStr = $extraHeadersArr[1];
		if (stristr($url, '?')) {
			$url = $url . '&' . $headerStr;
		} else {
			$url = $url . '?' . $headerStr;
		}
		$this->_httpClient->setHeaders($extraHeaders);
		$response = parent::delete($url);

	}

	//people	
	public function getAuthorizedUid()
	{
		$url = self::SERVER_URL . "/people/" . urlencode("@me");
		return $this->getEntry($url, 'Zend_Gdata_DouBan_PeopleEntry');

	}
	
	public function getFriends($uid = NULL)
	{
		if ($uid !== NULL) {
			$url = self::SERVER_URL . "/people/" . $uid . "/friends";
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_PeopleFeed');
	}
	
	public function getContacts($uid = NULL)
	{
		if ($uid !== NULL) {
			$url = self::SERVER_URL . "/people/" . $uid . "/contacts";
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_PeopleFeed');
	}

	public function getPeople($peopleId = NULL, $location = NULL)
	{
		if ($peopleId !== NULL) {
			$url = self::SERVER_URL . "/people/" . $peopleId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_PeopleEntry');
	}

	public function getPeopleFeed($location = NULL)
	{
		if ($location == NULL) {
			$url = self::SERVER_URI . "/people";
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_PeopleFeed');
	}
	
	public function searchPeople($queryText, $startIndex = NULL, $maxResults = NULL)
	{
		$query =new Zend_Gdata_Query(self::SERVER_URL . "/people/");
		$query->setQuery($queryText);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getPeopleFeed($query);
		
	}
	
	//book	
	public function getBook($bookId = NULL, $location = NULL)
	{
		if ($bookId !== NULL) {
			$url = self::SERVER_URL . "/book/subject/" . $bookId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_BookEntry');
	}

	public function getBookFeed($location = NULL)
	{
		if ($location == NULL) {
			$url = self::PEOPLE_URI . "/book/subjects";
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_BookFeed');
	}
	
	public function searchBook($queryText, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/book/subjects");
		$query->setQuery($queryText);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getBookFeed($query);
		
	}
	
	public function queryBookByTag($tag, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/book/subjects");
		$query->setParam('tag', $tag);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getBookFeed($query);
	
	}
	
	//music
	public function getMusic($musicId = NULL, $location = NULL)
	{
		if ($musicId !== NULL) {
			$url = self::SERVER_URL . "/music/subject/" . $musicId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_MusicEntry');
	}

	public function getMusicFeed($location = NULL)
	{
		if ($location == NULL) {
			$url = self::PEOPLE_URI . "/music/subjects";
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_MusicFeed');
	}
	
	public function searchMusic($queryText, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/music/subjects");
		$query->setQuery($queryText);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getMusicFeed($query);
		
	}
	
	public function queryMusicByTag($tag, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/music/subjects");
		$query->setParam('tag', $tag);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getMusicFeed($query);
	
	}
	
	//movie
	public function getMovie($movieId = NULL, $location = NULL)
	{
		if ($movieId !== NULL) {
			$url = self::SERVER_URL . "/movie/subject/" . $movieId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_MovieEntry');
	}

	public function getMovieFeed($location = NULL)
	{
		if ($location == NULL) {
			$url = self::PEOPLE_URL . "/movie/subjects";
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_MovieFeed');
	}
	
	public function searchMovie($queryText, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/movie/subjects");
		$query->setQuery($queryText);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getMovieFeed($query);
		
	}
	
	public function queryMovieByTag($tag, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/movie/subjects");
		$query->setParam('tag', $tag);
		$query->setMaxResults($maxResults);
		$query->setStartIndex($startIndex);
		return $this->getMovieFeed($query);
	
	}

	//tags
	public function getTagFeed($category = NULL, $subjectId = NULL)
	{
		if (($subjectId != NULL) && ($category != NULL)) {
			$url = self::SERVER_URL . "/" . $category . "/subject/" . 
				$subjectId . "/tags";
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_TagFeed');
	}
	
	//review
	public function getReview($reviewId = NULL, $location = NULL)
	{
		if ($reviewId !== NULL) {
			$url = self::SERVER_URL . "/review/" . $reviewId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_ReviewEntry');
	}
	
	public function getReviewFeed($subjectId = NULL, $cat = NULL, $orderby = "score")
	{
		if (($subjectId != NULL) && ($cat != NULL))
		{
			$url = self::SERVER_URL . "/" . $cat . "/subject/" . 
				$subjectId . "/reviews";
			$query = new Zend_Gdata_Query($url);
			$query->setParam("orderby", $orderby);
		}
		return $this->getFeed($query->getQueryUrl(), 'Zend_Gdata_DouBan_ReviewFeed');
	}

	public function getMyReview($myId = NULL, $location = NULL)
	{
		if ($myId != NULL) {
			$url = self::SERVER_URL . "/people/" . $myId . "/reviews";
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_ReviewFeed');
	}
	
	public function createReview($title = NULL, $content = NULL, $subject = NULL, 
			$rating = NULL)
	{
		$subId =  $subject->getId();
		$subRating =  $subject->getRating();
		$subject = new Zend_Gdata_DouBan_Subject();
		$subject->setId($subId);
		$subject->setRating($subRating);
		$entry = new Zend_Gdata_DouBan_ReviewEntry();
		$entry->setSubject($subject);
		if ($rating) {
			$rating = new Zend_Gdata_DouBan_Extension_Rating($rating);
			$entry->setRating($rating);
		}
		$title = new Zend_Gdata_App_Extension_Title($title);
		$content = new Zend_Gdata_App_Extension_Content($content);
		$entry->setContent($content);
		$entry->setTitle($title);
		$url = self::SERVER_URL . "/reviews";
		$response =  $this->post($entry, $url, NULL, "application/atom+xml; charset=utf-8");
                $result = new Zend_Gdata_DouBan_ReviewEntry();
                $result->transferFromXML($response->getBody());
                return $result;

	}

	public function updateReview($entry = NULL, $title = NULL, $content = NULL, $rating = NULL)
	{
		$title = new Zend_Gdata_App_Extension_Title($title);
                $content = new Zend_Gdata_App_Extension_Content($content);
                $entry->setContent($content);
                $entry->setTitle($title);
		if ($rating) {
			$rating = new Zend_Gdata_DouBan_Extension_Rating($rating);
                        $entry->setRating($rating);
                }
		$response =  $this->put($entry, $entry->getId()->getText(), 
				NULL, "application/atom+xml; charset=utf-8");
		$result = new Zend_Gdata_DouBan_ReviewEntry();
                $result->transferFromXML($response->getBody());
		return $result;

	}
	
	public function deleteReview($entry)
	{
		$url = $entry->getId()->getText();
		return $this->delete($url);
	}

	//collection
#	public function getCollection($collectionId = NULL, $location = NULL)
#	{
	public function getCollection($url = NULL)
	{
		#if ($collectionId !== NULL) {
                #        $url = self::SERVER_URL . "/collection/" . $collectionId;
                #} else if ($location instanceof Zend_Gdata_Query) {
                #        $url = $location->getQueryUrl();
                #} else {
                #        $url = $location;
                #}
                return $this->getEntry($url, 'Zend_Gdata_DouBan_CollectionEntry');

	}
	
	public function getMyCollection($uid, $cat, $tag, $status = NULL, $startIndex = NULL, 
			$maxResults = NULL, $updatedMax = NULL, $updatedMin = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/people/" . $uid . "/collection");
                if ($tag) $query->setParam('tag', $tag);
                if ($cat) $query->setParam('cat', $cat);
		if ($status) $query->setParam('status', $status);
                if ($maxResults) $query->setMaxResults($maxResults);
                if ($startIndex) $query->setStartIndex($startIndex);
                return $this->getFeed($query->getQueryUrl(), 'Zend_Gdata_DouBan_CollectionFeed');	
	}

	public function getCollectionFeed($peopleId = NULL, $cat = NULL)
	{
		if ($peopleId !== NULL) {
			$url = self::SERVER_URL . "/people/" . $peopleId . 
				"/collection?cat=" . $cat;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getFeed($url, 'Zend_Gdata_DouBan_CollectionFeed');

	}
	
	public function addCollection($status = NULL, $subject = NULL, $rating = NULL, 
			$tags = array(), $private = False)
	{
		$subId =  $subject->getId();
		$subject = new Zend_Gdata_DouBan_Subject();
                $subject->setId($subId);
                $entry = new Zend_Gdata_DouBan_CollectionEntry();
                $entry->setSubject($subject);
		if ($rating) {
                        $rating = new Zend_Gdata_DouBan_Extension_Rating($rating);
                        $entry->setRating($rating);
                }
		if ($private) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("public");
			$entry->setAttribute($attribute);
		}
		foreach ($tags as $tag) {
			$obj = new Zend_Gdata_DouBan_Extension_Tag();
			$obj->setName($tag);
			$entry->setTag($obj);
		}
		$status = new Zend_Gdata_DouBan_Extension_Status($status);
		$entry->setStatus($status);
		$url = self::SERVER_URL . "/collection";
		$response = $this->post($entry, $url, NULL, "application/atom+xml; charset=utf-8");
		$result = new Zend_Gdata_DouBan_CollectionEntry();
		$result->transferFromXML($response->getRawBody());
		return $result;

	}
	
	public function updateCollection($entry = NULL, $status = NULL, $tags = array(), 
			$rating = NULL, $private = False)
	{
		$status = new Zend_Gdata_DouBan_Extension_Status($status);
		$entry->setStatus($status);
		if ($rating) {
			$rating = new Zend_Gdata_DouBan_Extension_Rating($rating);
                        $entry->setRating($rating);
		}
		if ($tags) {
         	       	foreach ($tags as $tag) {
                        	$obj = new Zend_Gdata_DouBan_Extension_Tag();
                        	$obj->setName($tag);
				$entry->setTag($obj);
                	}
	
		}
		if ($private) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("public");
			$entry->setAttribute($attribute);
		}
		$response =  $this->put($entry, $entry->getId()->getText(), NULL,  
				"application/atom+xml; charset=utf-8");
                $result = new Zend_Gdata_DouBan_CollectionEntry();
                $result->transferFromXML($response->getBody());
                return $result;


	}
	
	public function deleteCollection($entry = NULL)
	{
		$url = $entry->getId()->getText();
                return $this->delete($url);
	}

	//Broadcasting
	public function getBroadcastingFeed($userID = NULL,  $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/people/" . 
				$userID . "/miniblog");
		if ($maxResults) {
			$query->setMaxResults($maxResults);
		}
		if ($startIndex) {
			$query->setStartIndex($startIndex);
		}
		return $this->getFeed($query->getQueryUrl(), 'Zend_Gdata_DouBan_BroadcastingFeed');
	}

	public function getContactsBroadcastingFeed($userID = NULL,  $startIndex = NULL, 
			$maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/people/" . 
			$userID . "/miniblog/contacts");
		if ($maxResults) {
			$query->setMaxResults($maxResults);
		}
		if ($startIndex) {
			$query->setStartIndex($startIndex);
		}
		return $this->getFeed($query->getQueryUrl(), 'Zend_Gdata_DouBan_BroadcastingFeed');
	}
	
	public function addBroadcasting($category = NULL, $entry = NULL)
	{
		$url = self::SERVER_URL . "/miniblog/" . $category;
		$response = $this->post($entry, $url, NULL, "application/atom+xml; charset=utf-8");
		$result = new Zend_Gdata_DouBan_BroadcastingEntry();
		$result->transferFromXML($response->getRawBody());
		return $result;
	}

	 public function deleteBroadcasting($entry)
        {
                $url = $entry->getId()->getText();
                return $this->delete($url);
        }

	public function getNote($noteId = NULL, $location = NULL)
	{
		if ($noteId !== NULL) {
			$url = self::SERVER_URL . "/note/" . $noteId;
		} else if ($location instanceof Zend_Gdata_Query) {
			$url = $location->getQueryUrl();
		} else {
			$url = $location;
		}
		return $this->getEntry($url, 'Zend_Gdata_DouBan_NoteEntry');
	}

	public function getMyNotes($uid, $startIndex = NULL, $maxResults = NULL)
	{
		$query = new Zend_Gdata_Query(self::SERVER_URL . "/people/" . $uid . "/notes");
                $query->setMaxResults($maxResults);
                $query->setStartIndex($startIndex);
		return $this->getFeed($query->getQueryUrl(), 'Zend_Gdata_DouBan_NoteFeed');

	}
	
	public function addNote($entry, $private = False, $canReply = True)
	{
		if ($private) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("public");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		if ($canReply) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("yes");
			$attribute->setName("can_reply");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("no");
                        $attribute->setName("can_reply");
                        $entry->setAttribute($attribute);
		}
				
		$url = self::SERVER_URL . "/notes";
		$response = $this->post($entry, $url, NULL, "application/atom+xml; charset=utf-8");
		$result = new Zend_Gdata_DouBan_NoteEntry();
		$result->transferFromXML($response->getRawBody());
		return $result;
	}
	
	public function updateNote($entry = NULL, $title, $content, $private = NULL, $canReply = NULL)
	{
		$title = new Zend_Gdata_App_Extension_Title($title);
                $content = new Zend_Gdata_App_Extension_Content($content);
                $entry->setContent($content);
                $entry->setTitle($title);
		if ($private) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("private");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("public");
			$attribute->setName("privacy");
			$entry->setAttribute($attribute);
		}
		if ($canReply) {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("yes");
			$attribute->setName("can_reply");
			$entry->setAttribute($attribute);
		}
		else {
			$attribute = new Zend_Gdata_DouBan_Extension_Attribute("no");
                        $attribute->setName("can_reply");
                        $entry->setAttribute($attribute);
		}
		$response =  $this->put($entry, $entry->getId()->getText(), 
				NULL, "application/atom+xml; charset=utf-8");
		$result = new Zend_Gdata_DouBan_NoteEntry();
                $result->transferFromXML($response->getBody());
		return $result;
	}

	public function deleteNote($entry)
        {
                $url = $entry->getId()->getText();
                return $this->delete($url);
        }
}
?>
