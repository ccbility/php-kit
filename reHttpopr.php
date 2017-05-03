<?php
class HttpRequest{
	private $_url;
	private $_cookie = '';

	public function __CONSTRUCT($url){
		$this->_url = $url;
	}

	public function setUrl($url){
		$this->_url = $url;
	}

	public function post($data, $del_func = 'http_build_query'){
		$opr = array(
				CURLOPT_HEADER => 1,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $del_func($data)
				);
		if($this->_cookie){
			$opr['CURLOPT_COOKIE'] = $this->_cookie;
		}
		$output = $this->curlBase($opr);
		$this->_setCookie($output);
		return $output;
	}

	public function get(){
		$opr = array(
			CURLOPT_HEADER => 1
			);
		if($this->_cookie){
			$opr['CURLOPT_COOKIE'] = $this->_cookie;
		}
		$output = $this->curlBase($opr);
		$this->_setCookie($output);
		return $output;
	}

	private function _setCookie($output){
		preg_match("/set\-cookie:([^\r\n]*)/i", $output, $matches);
		if(isset($matches[1])){
			//拼接整个回话中的cookie
			$this->_cookie .= $matches[1];
		}
	}

	private function curlBase($optArr= NULL){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_TIMEOUT,60); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		if(!is_null($optArr)){
			curl_setopt_array($ch, $optArr);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_PROXY,'127.0.0.1:8888');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36'); 
		curl_setopt($ch, CURLOPT_REFERER, 'http://www.baidu.com/'); 

		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}