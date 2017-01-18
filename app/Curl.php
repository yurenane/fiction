<?php

namespace App;

//use App\PrintCss;

/**
 * curl类
 * ======
 * @author 简强
 * @version 17.1.12
 */
class Curl {

	//请求路径
	private $url = '';
	//post提交数据
	private $request = array();
	//是否获取cookie
	private $open_cookie = false;
	//保存cookie路径
	public $cookie = '';
	//请求来源
	private $referer = 'http://www.baidu.com';
	//超时设置
	private $time_out = 10;
	//响应结果
	public $response;
	//请求消息头
	private $http_header = array(
	  'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
		// 'CLIENT-IP:27.148.151.82',
		// 'X-FORWARDED-FOR:27.148.151.82'
	);

	function __construct() {
//		$this->cookie = tempnam(__DIR__./tmp/', 'cookie');
		$this->cookie =__DIR__.'/tmp/cookie.txt';
	}

	/**
	 * 设置header
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function setHeader($header) {
		foreach ($header as $val) {
			$this->http_header[] = $val;
		}
	}

	/**
	 * 设置referer
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function setReferer($referer) {
		$this->referer = $referer;
	}

	/**
	 * 设置开启cookie状态
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function setCookieJar($is) {
		$this->open_cookie=$is;
	}

	/**
	 * post提交数据
	 * ======
	 * @param $url 	请求地址
	 * @param $data 	请求数据
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function post($url, $data) {
		$this->url = $url;
		$this->request = $data;
		return $this->curl();
	}

	/**
	 * get获取数据
	 * ======
	 * @param $url 		请求地址
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	public function get($url) {
		$this->url = $url;
		return $this->curl();
	}
	/**
	 * 使用curl
	 * ======
	 * @author 简强
	 * @version 17.1.12
	 */
	private function curl() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		if ($this->request) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		if ($this->open_cookie) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		}
//		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->time_out);
		curl_setopt($ch, CURLOPT_REFERER, $this->referer);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->http_header);
		$result = curl_exec($ch);
		$this->response = curl_getinfo($ch);
		return $result;
	}

}
