<?php

class Http{
	/**
	* @url 字符串，网址
	*/
	private $url;

	public function __CONSTRUCT($url){
		$this->url = $url;
	}

	public function setUrl($url){
		$this->url = $url;
	}

	public function postRequest($data){
		return $this->curlBase(
			array(
				CURLOPT_POST=>1, 
				CURLOPT_POSTFIELDS=>http_build_query($data)
			));
	}
	//post方式下，拿到cookie
	public function getPostCookie($data){
		$output = $this->curlBase(array(
			CURLOPT_HEADER => 1,
			CURLOPT_POST => 1, 
			CURLOPT_POSTFIELDS => http_build_query($data)
			));
		preg_match_all("/set\-cookie:([^\r\n]*)/i", $output, $matches);
		return implode(';', $matches[1]);
	}

	//get方式下拿到的cookie
	public function getGetCookie(){
		$output = $this->curlBase(array(
			CURLOPT_HEADER => 1
			));
		preg_match_all("/set\-cookie:([^\r\n]*)/i", $output, $matches);
		return implode(';', $matches[1]);
	}

	//携带cookie发起post请求
	public function postWithCookie($cookie, $data = array()){
		return $this->curlBase(
			array(
				CURLOPT_COOKIE => $cookie,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => http_build_query($data)
				)
			);
	}

	public function getWithCookie($cookie){
		return $this->curlBase(
			array(
				CURLOPT_COOKIE => $cookie,
				)
			);
	}

	//匹配出所有的cookie
	public function matchCookies($cont){
		preg_match_all("/set\-cookie:([^\r\n]*)/i", $cont, $matches);
		return implode(';', $matches[1]);
	}

	//把cookie以本文形式存在本地，考虑到有读取两步操作，故不用
	// public function getGetLoginFielCookie(){
	// 	$cookie_jar = tempnam('./', 'cookie_');
	// 	$this->curlBase(
	// 		array(
	// 			CURLOPT_COOKIEJAR => $cookie_jar
	// 			)
	// 		);
	// 	return $cookie_jar;
	// }

	// public function getWithFileCookie($cookie_jar){
	// 	return $this->curlBase(
	// 		array(
	// 			CURLOPT_COOKIEFILE => $cookie_jar
	// 			)
	// 		);
	// }
	
	// $optArr, 多个数组
	public function curlBase($optArr= NULL){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_TIMEOUT,60); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch,CURLOPT_PROXY,'127.0.0.1:8888');
		if(!is_null($optArr)){
			curl_setopt_array($ch, $optArr);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36'); 
		// curl_setopt($ch, CURLOPT_REFERER, '10010.com'); 

		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}