<?php

namespace Common\Library\BaiduMap;

/**
 * 百度地图
 */
class BaiduMap {

	protected $url = "http://api.map.baidu.com";

	//参数提交方式
	protected $method = 'GET';

	protected $ak = 'oSxCtoIssCCFFb6WtqfrQrtO';

	//结果状态码
	public $status = 200;
	//错误信息
	public $error = "";

	/**
	 * 根据ip获取城市地址和城市编码
	 * @return [type] [description]
	 */
	public function ipGetAddress() {
		$url = $this->url . '/location/ip?coor=bd09ll';
		return $this->httpGet($url);
	}

	/**
	 * 根据地质获取经纬度
	 * @param [type] $title [description]
	 */
	public function addGetLat($title) {
		$url = $this->url . '/geocoder/v2/?address=' . $title . '&output=json';
		return $this->httpGet($url);
	}

	/**
	 * 根据经纬度获取城市编码
	 * @param  [type] $lat [description]
	 * @param  [type] $lng [description]
	 * @return [type]      [description]
	 */
	public function latGetCitycode($lat, $lng) {
		$url = $this->url . '/geocoder?location=' . $lat . ',' . $lng . '&output=json';

		return $this->httpGet($url);
	}
	/**
	 * 发送http请求带参数
	 * @param type $url 请求url
	 * @param type $token 是否提交token
	 */
	protected function httpCurl($url) {
		$header = ['Content-Type:application/json;charset=UTF-8'];
		$url = $url . '&ak=' . $this->ak;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body_params));
		$res = json_decode(curl_exec($ch), true);
		$this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($this->status >= 400) {
			if (isset(self::$status_code[$this->status])) {
				$this->error = self::$status_code[$this->status];
			} else {
				$this->error = curl_error($ch);
			}
			return false;
		}
		curl_close($ch);
		return $res;
	}

	/**
	 * 发送http请求（不带参数）
	 * @param type $url
	 * @return type
	 */
	protected function httpGet($url, $is_token = false) {
		$header = ['Content-Type:application/json;charset=UTF-8'];
		$url = $url . '&ak=' . $this->ak;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = json_decode(curl_exec($ch), true);
		$this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($this->status >= 400) {
			if (isset(self::$status_code[$this->status])) {
				$this->error = self::$status_code[$this->status];
			} else {
				$this->error = curl_error($ch);
			}
			return false;
		}
		curl_close($ch);
		return $res;
	}
}