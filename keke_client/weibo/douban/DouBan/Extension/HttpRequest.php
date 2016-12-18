<?php
class HttpRequest {
	var $sHostAdd;
	var $sUri;
	var $iPort;
	
	var $sRequestHeader;
	
	var $sResponse;
	
	function __construct($sUrl) {
		
		$sPatternUrlPart = '/http:\/\/([a-z-\.0-9]+)(:(\d+)){0,1}(.*)/i';
		$arMatchUrlPart = array ();
		preg_match ( $sPatternUrlPart, $sUrl, $arMatchUrlPart );
		
		$this->sHostAdd = gethostbyname ( $arMatchUrlPart [1] );
		if (empty ( $arMatchUrlPart [4] )) {
			$this->sUri = '/';
		} else {
			$this->sUri = $arMatchUrlPart [4];
		}
		if (empty ( $arMatchUrlPart [3] )) {
			$this->iPort = 80;
		} else {
			$this->iPort = $arMatchUrlPart [3];
		}
		
		$this->addRequestHeader ( 'Host: ' . $arMatchUrlPart [1] );
		$this->addRequestHeader ( 'Connection: Close' );
	
	}
	
	function addRequestHeader($sHeader) {
		$this->sRequestHeader .= trim ( $sHeader ) . "\r\n";
	}
	
	function sendRequest($sMethod = 'GET', $sPostData = '') {
		$sRequest = $sMethod . " " . $this->sUri . " HTTP/1.1\r\n";
		$sRequest .= $this->sRequestHeader;
		if ($sMethod == 'POST') {
			$sRequest .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$sRequest .= "Content-Length: " . strlen ( $sPostData ) . "\r\n";
			$sRequest .= "\r\n";
			$sRequest .= $sPostData . "\r\n";
		}
		$sRequest .= "\r\n";
		
		$sockHttp = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
		if (! $sockHttp) {
			die ( 'socket_create() failed!' );
		}
		
		$resSockHttp = socket_connect ( $sockHttp, $this->sHostAdd, $this->iPort );
		if (! $resSockHttp) {
			die ( 'socket_connect() failed!' );
		}
		
		socket_write ( $sockHttp, $sRequest, strlen ( $sRequest ) );
		
		$this->sResponse = '';
		while ( $sRead = socket_read ( $sockHttp, 4096 ) ) {
			$this->sResponse .= $sRead;
		}
		
		socket_close ( $sockHttp );
	}
	
	function getResponse() {
		return $this->sResponse;
	}
	
	function getResponseBody() {
		$sPatternSeperate = '/\r\n\r\n/';
		$arMatchResponsePart = preg_split ( $sPatternSeperate, $this->sResponse, 2 );
		return $arMatchResponsePart [1];
	}
	
	function getResponseHead() {
		$sPatternSeperate = '/\r\n\r\n/';
		$arMatchResponsePart = preg_split ( $sPatternSeperate, $this->sResponse, 2 );
		return $arMatchResponsePart [0];
	}
}