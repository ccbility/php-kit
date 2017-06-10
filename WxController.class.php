<?php
namespace Home\Controller;

use Think\Controller;

$Wx = new WxController(C('appid'), C('secret'));

$back_url = U('Login/index', '', false, true);
//把login中的redire_url 放在session中，就不要拼在 back_url中了，不然会出现重复编码的情况
$user_info = $Wx->get_snsapi_userinfo($back_url, I('get.code', 0));
if(!$user_info){
	die;
}

class WxController extends BaseController{
	private $_appid;
	private $_secret;
	private $_url;

	public function __construct($appid, $secret)
	{
		$this->_appid = $appid;
		$this->_secret = $secret;
	}

	//获取基础access_token
	public function get_base_access_token()
	{
		if(session('access_token')){
			return session('base_access_token');
		}else{
			$this->_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->_appid}&secret={$this->_secret}";
			
			$output = json_decode($this->curlBase(), true);
			if($output['access_token']){
				session(array('base_access_token' => $output['access_token'], 'expire'=>$output['expires_in']));
				return $output['access_token'];
			}else{
				die('获取access_token失败~');
			}
		}
	}

	//snsapi_userinfo
	public function get_snsapi_userinfo($back_url, $code)
	{
		//实现检查是否有 access_token
		//refresh_token压根用不着，30天的存活期，cookie都早过期了
		if(session('adv_access_token') && session('adv_openid')){
			return $this->getUserInfo(session('adv_access_token'), session('adv_openid'));
		}
		if($code){
			$this->_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->_appid}&secret={$this->_secret}&code={$code}&grant_type=authorization_code";
			$output = json_decode($this->curlBase(), true);

			if($output['access_token']){
				session(array('adv_access_token' => $output['access_token'], 'expire' => $output['expires_in']));
				session(array('adv_openid' => $output['openid'], 'expire' => $output['expires_in']));
				
				return $this->getUserInfo($output['access_token'], $output['openid']);
			}else{
				die('获取高级access_token失败');
			}
		}else{
			$back_url = urlencode($back_url);
			
			$this->_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_appid}&redirect_uri={$back_url}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

			redirect($this->_url);
			die;//结束程序，阻止主程序代码继续运行
		}

	}

	private function getUserInfo($access_token, $openid){
		$this->_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";

		$user_info = json_decode($this->curlBase(), true);
		return $user_info;
	}

	public function setUrl($url){
		$this->_url = $url;
	}

	public function get(){
		$opr = array(
			CURLOPT_HEADER => 1
			);

		$output = $this->curlBase($opr);
		return $output;
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
		// curl_setopt($ch,CURLOPT_PROXY,'127.0.0.1:8888');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36'); 
		// curl_setopt($ch, CURLOPT_REFERER, 'http://www.baidu.com/'); 

		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
