<?php

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = '') {
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time() : 0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}
	return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
	$key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = str_replace(array('-', '_'), array('+', '/'), $data);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data = substr($data, 10);

	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}

		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 判断字符串是否以指定字符开头
 * @param type $str 字符串
 * @param type $needle 指定字符
 * @return type
 */
function startWith($str, $needle) {
	return strpos($str, $needle) === 0;
}

/**
 *
 * @return int
 */
function is_login() {
	$user = session('user_auth');

	if (empty($user)) {
		$user['uid'] = UID;
		return $user;
//		return [];
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user : [];
	}
}

/**
 * 生成唯一字符串
 */
function getCode($int = -32) {
	$str = uniqid(mt_rand(), 1);
	return substr(sha1($str), $int);
}

function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
 * url base64编码
 * @param type $string
 * @return typeurl base64编码
 */
function urlsafe_b64encode($string) {
	$data = base64_encode($string);
	$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
	return $data;
}

/**
 * url base64 解码
 * @param type $string
 * @return type
 */
function urlsafe_b64decode($string) {
	$data = str_replace(array('-', '_'), array('+', '/'), $string);

	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data, true);
}

/**
 * 获取当前完成的URl
 * @return string
 */
function GetCurUrl($is_referer = false) {
	if ($is_referer && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
		return $_SERVER['HTTP_REFERER'];
	}
	$url = 'http://';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$url = 'https://';
	}
	if ($_SERVER['SERVER_PORT'] != '80') {
		$url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . __SELF__;
	} else if (empty($_REQUEST['from'])) {
		$url .= $_SERVER['HTTP_HOST'] . __SELF__;
	} else {
		$url .= $_SERVER['HTTP_HOST'] . __SELF__;
	}
	// 兼容后面的参数组装
	if (stripos($url, '?') === false) {
		$url .= '?t=' . time();
	}
	return $url;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}

/**
 * 发送http get请求
 * @param type $url
 * @return type
 */
function http_get($url) {
	$header[] = "content-type: application/x-www-form-urlencoded;
            charset = UTF-8";
	$ch1 = curl_init();
	$timeout = 5;
	curl_setopt($ch1, CURLOPT_URL, $url);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
	$accesstxt = curl_exec($ch1);
	curl_close($ch1);
	return json_decode($accesstxt, true);
}

/**
 * 发送http post请求
 * @param type $url
 * @param type $post_data
 */
function http_post($url, $post_data) {
	$header[] = "content-type: application/x-www-form-urlencoded;
            charset = UTF-8";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($ch);
	curl_close($ch);
	return json_decode($res, true);
}

/**
 * 获取分类可用的（别名） 通过汉字直接转成拼音
 * @param type $mid 模型id
 * @param type $character 汉字
 * @param type $aliaslist 数据列表
 * @return string
 */
function getCateAlias($mid, $character, $alias = "") {
	static $aliaslist = "";
	$alias && $aliaslist = $alias;
	$ch = new \Common\Library\Pinyin\ChinesePinyinApi();
	$name = strtolower($ch->TransformWithoutTone($character));
	if (empty($aliaslist)) {
		$cate = M('category')->field("id,alias")->where(array("mid" => $mid))->select();
		$aliaslist = array_column($cate, "id", "alias");
	}
	for ($i = 0; $i < 10; $i++) {
		if ($aliaslist[$name]) {
			$name = $name . $i;
		} else {
			break;
		}
	}
	return $name;
}

/**
 * 检查手机号码格式是否正确(支持多个手机号码验证，多个手机号码也逗号分割)
 */
function checkMobile($mobile) {
	$rule = '/^(13[0-9]|14[0-9]|15[0-9]|16[0-9]|17[0-9]|18[0-9]|19[0-9])\d{8}$/';
	$mobile = explode(", ", $mobile);
	foreach ($mobile as $k => $v) {
		if (preg_match($rule, $v) !== 1) {
			return false;
		}
	}

	return true;
}

/**
 * author fenghao@ipoxiao.com
 * date 2015/07/17 18:50
 * 替换银行卡、手机号码为**。
 * @param type $str 要替换的字符串
 * @param type $startlen 开始长度 默认4
 * @param type $endlen
 * 结束长度 默认3
 * @return type
 */
function strreplace($str, $startlen = 4, $endlen = 3) {
	$repstr = "";
	if (strlen($str) < ($startlen + $endlen + 1)) {
		return $str;
	}
	$count = strlen($str) - $startlen - $endlen;
	for ($i = 0; $i < $count; $i++) {
		$repstr .= "*";
	}
	return preg_replace('/(\d{' . $startlen . '})\d+(\d{' . $endlen . '})/', '${1}' . $repstr . '${2}', $str);
}

/**
 * 生成唯一订单号
 * @return type
 */
function createOrderCode() {
	return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 生成令牌
 * @return string
 */
function getToken() {
	$token = sha1(uniqid() . rand(100, 999));
	return $token;
}

/**
 * 检查邮箱格式
 * @param $email
 * @return int
 */
function checkEmail($email) {
	return preg_match('/^([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i', trim($email));
}

/**
 * 检查身份证号格式
 * @param $idCard
 */
function checkIDCard($idCard) {
	return true;
	return preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/', trim($idCard));
}

/**
 * 计算文件大小
 * @param $size
 * @param string $initCeil
 * @return string
 */
function calculate_size($size, $initCeil = 'b', $divider = '') {
	static $ceils = ['T', 'G', 'M', 'KB', 'B'];
	$initCeil = strtoupper($initCeil);
	$index = array_keys($ceils, $initCeil)[0];
	$size = intval($size);
	if ($size < 1024 || $index == 0) {
		return $size . $divider . $initCeil;
	} else {
		$size /= 1024;
		$initCeil = $ceils[$index - 1];
		return calculate_size($size, $initCeil, $divider);
	}
}

/**
 * 删除文件
 * @param $path
 * @param bool|false $delSelf
 */
function delDir($path, $delSelf = false) {
	if (is_string($path)) {
		$path = rtrim($path, "/");
		$path = rtrim($path, "\\");
	}
	if (file_exists($path)) {
		if (is_dir($path)) {
			$resource = opendir($path);
			while ($a = readdir($resource)) {
				if ($a == '.' || $a == '..') {
					continue;
				}
				$subPath = $path . '/' . $a;
				if (is_dir($subPath)) {
					delDir($subPath, true);
				} else {
					unlink($subPath);
				}
			}
			;
		}
		if ($delSelf) {
			if (is_dir($path)) {
				chmod($path, 0777);
				rmdir($path);
			} else {
				unlink($path);
			}
		}
	}
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}

		foreach ($list as $key => $data) {

			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[$data[$pk]] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent = &$refer[$parentId];
					$parent[$child][$data[$pk]] = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC) {
	if (is_array($multi_array)) {
		foreach ($multi_array as $row_array) {
			if (is_array($row_array)) {
				$key_array[] = $row_array[$sort_key];
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
	array_multisort($key_array, $sort, $multi_array);
	return $multi_array;
}

/**
 * @author leoben
 * 价格单位转换
 * @param [int] $price [价格]
 */
function unit($price) {
	if ($price >= 10000) {
		$price = ceil($price / 10000) . '亿';
	} else {
		$price = ceil($price) . '万';
	}
	return $price;
}

/**
 * 生成随机字符床
 * @param  [type] $length [字符串长度]
 * @return [type]         [description]
 */
function getRandChar($length) {
	$str = null;
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($strPol) - 1;

	for ($i = 0; $i < $length; $i++) {
		$str .= $strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
	}

	return $str;
}

/**
 * 二维数组按某一字段排序
 * @param  [type] $arr   [二维数组]
 * @param  [type] $field [关键字段]
 * @param  [type] $order [排序方式]
 * @return [type]        [description]
 */
function array_sort($arr, $field, $order) {
	$sort = array(
		'direction' => 'SORT_' . $order, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
		'field' => $field, //排序字段
	);
	$arrSort = array();
	foreach ($arr AS $uniqid => $row) {
		foreach ($row AS $key => $value) {
			$arrSort[$key][$uniqid] = $value;
		}
	}
	if ($sort['direction']) {
		array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
	}
	return $arr;
}

/**
 * 时间类型
 */
function get_time_type($type) {
	switch ($type) {
	case 1: //今日
		$start_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$end_time = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
		break;
	case 2: //昨日
		$start_time = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
		$end_time = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
		break;
	case 3: //本周
		$start_time = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
		$end_time = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y'));
		break;
	case 4: //上周
		$start_time = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'));
		$end_time = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y'));
		break;
	case 5: //本月
		$start_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
		break;
	case 6: //上月
		$start_time = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
		$end_time = mktime(23, 59, 59, date('m'), 0, date('Y'));
		break;
	default:
		break;
	}
	$time = '';
	if ($start_time && $end_time) {
		$time = ['between', [$start_time, $end_time]];
	}
	return $time;
}

function handle_special_time($data){
	$data = explode(' - ',$data);
	$start_time = strtotime($data[0]);
	$end_time = strtotime($data[1]);
	$time = '';
	if ($start_time && $end_time) {
		$time = ['between', [$start_time, $end_time]];
	}
	return $time;
}

/**
 * 获取文件连接
 * @param type $type 图片或者文件或者新闻内容
 */
function getFileUrl($val, $type = "head") {
	$res = "";
	$url = 'http://' . $_SERVER["HTTP_HOST"];
	switch ((string) $type) {
		case "image":
			if ($val) {
				if (is_array($val)) {
					$imgpic = $val;
				} else {
					$imgpic = explode(",", $val);
				}
				$imgs = array();
				foreach ($imgpic as $img) {
					if ($img == '') {
						continue;
					}
					$imgs[] = $url . __ROOT__ . "/files/Image/index/sha1/" . $img;
				}
				$res = $imgs;
			} else {
				$res = array();
			}
			break;
		case "videos":
			if ($val) {
				$imgpic = explode(",", $val);
				$imgs = array();
				foreach ($imgpic as $img) {
					if ($img == '') {
						continue;
					}

					$imgs[] = $url . __ROOT__ . "/files/File/index/sha1/" . $img;
				}
				$res = $imgs;
			} else {
				$res = array();
			}
			break;
		case "content":
			$res = $url . __ROOT__ . "/Home/ItemWap/info/id/" . $val;
			break;
		case "head":

			if (strpos($val, '/')) {
				$res = $val;
			} else {
				$res = $val ? $url . __ROOT__ . "/files/Image/index/sha1/" . $val : "";
			}
			break;
		case "subimage":
			$res = $val ? $url . __ROOT__ . "/files/Image/subindex/sha1/" . $val : "";
			break;
		case "wxpay":
			$res = $val ? $url . __ROOT__ . "/Home/Pay/wxpay/?ordercode=" . $val : "";
			break;
		case "alipay":
			$res = $val ? $url . __ROOT__ . "/Home/Pay/alipay/ordercode/" . $val : "";
			break;
		case 'video':
			$file_data = M('file')->where(['sha1' => $val])->field('sha1,thumb')->find();
			$res = [
					'thumb_path' => $file_data['thumb'] ? $url . __ROOT__ . "/files/Image/index/sha1/" . $file_data['thumb'] : "",
					'video' => $file_data['sha1'] ? $url . __ROOT__ . "/files/File/index/sha1/" . $file_data['sha1'] : "",
			];
			break;
		case 'file':
//			$file_data = M('file')->where(['sha1' => $val])->field('sha1')->find();
			$res = $val ? $url . __ROOT__ . "/files/File/index/sha1/" . $val : "";
			break;
			break;
		case 'file_cli':
			$file_data = M('file')->where(['sha1' => $val])->field('savepath,savename,sha1,thumb')->find();
			$res = $file_data['sha1'] ? './Uploads/Download/' . $file_data['savepath'] . $file_data['savename'] : "";
			break;

		case "images":
			if ($val) {
				$imgpic = json_decode($val, true);
				if (!is_array($imgpic) || empty($imgpic)) {
					$res = array();
					break;
				}
				foreach ($imgpic as $key => $value) {
					if (empty($value)) {
						continue;
					}
					$res[$key]['thumb_link'] = $url . __ROOT__ . "/files/Image/index/sha1/" . $value['hash'];
					$res[$key]['thumb'] = $value['hash'];
					$res[$key]['title'] = $value['title'];
					$res[$key]['url'] = '';
					$res[$key]['type'] = 1;
				}
				$res = array_values($res);
			} else {
				$res = array();
			}
			break;
		case "hvideo":
			if (!empty($val)) {
				$video = json_decode($val, true);
				if (!is_array($video) || empty($video)) {
					$res = array();
					break;
				}
				foreach ($video as $key => $value) {
					if (empty($value['hash'])) {
						continue;
					}
					$thumb = M('file')->where(array('sha1' => $value['hash']))->getField('thumb');
					$res[$key]['thumb_link'] = $url . __ROOT__ . "/files/Image/index/sha1/" . $thumb;
					$res[$key]['thumb'] = $value['hash'];
					$res[$key]['title'] = $value['title'];
					$res[$key]['url'] = $url . __ROOT__ . "/files/File/index/sha1/" . $value['hash'];
					$res[$key]['type'] = 2;
				}
				$res = array_values($res);
			} else {
				$res = array();
			}
			break;
		default:
			if (strpos($val, '/')) {
				$res = $val;
			} else {
				$res = $val ? $url . __ROOT__ . "/files/Image/index/sha1/" . $val : "";
			}

			break;
	}
	return $res;
}

/**
 * 手机固话格式检验
 * @param  [type] $mobile [description]
 * @return [type]         [description]
 */
function checkMobilePhone($mobile) {
	if ($mobile) {
		$first = substr($mobile, 0, 1);
	} else {
		return ['status' => false, 'msg' => '请填写号码'];
	}
	switch ($first) {
	case 1:
		if (preg_match('/^(1[3456789]\d{9})$/', $mobile)) {
			return ['status' => true, 'msg' => '手机号码格式正确'];
		} else {
			return ['status' => false, 'msg' => '手机号码格式不正确'];
		}
		break;
	default:
		if (preg_match('/^(0\d{9,11})$/', $mobile)) {
			return ['status' => true, 'msg' => '固话格式正确'];
		} else {
			return ['status' => false, 'msg' => '固话格式不正确'];
		}
		break;
	}
}

/**
 * 手机格式检验
 * @param  [type] $mobile [description]
 * @return [type]         [description]
 */
function checkPhone($mobile) {
	if (preg_match('/^(1[3456789]\d{9})$/', $mobile)) {
		return ['status' => true, 'msg' => '手机号码格式正确'];
	} else {
		return ['status' => false, 'msg' => '手机号码格式不正确'];
	}
}
/**
 * 判断是否为合法的身份证号码
 * @param $mobile
 * @return int
 */
function checkCard($num) {
	$city = array(
		'11', '12', '13', '14', '15', '21', '22',
		'23', '31', '32', '33', '34', '35', '36',
		'37', '41', '42', '43', '44', '45', '46',
		'50', '51', '52', '53', '54', '61', '62',
		'63', '64', '65', '71', '81', '82', '91',
	);
	//位数判断
	if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $num)) {
		return ['msg' => '身份证格式不对', 'status' => 400];
	}
	//城市判断
	if (!in_array(substr($num, 0, 2), $city)) {
		return ['msg' => '身份证格式不对', 'status' => 400];
	}
	$num = preg_replace('/[xX]$/i', 'a', $num);
	$length = strlen($num);
	//日期判断
	if ($length == 18) {
		$birthday = substr($num, 6, 4) . '-' . substr($num, 10, 2) . '-' . substr($num, 12, 2);
	} else {
		$birthday = '19' . substr($num, 6, 2) . '-' . substr($num, 8, 2) . '-' . substr($num, 10, 2);
	}
	if (date('Y-m-d', strtotime($birthday)) != $birthday) {
		return ['msg' => '身份证格式不对', 'status' => 400];
	}
	return ['status' => 200];
}

/**
 * 提取字符串中的数字
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function number($str) {
	return preg_replace('/\D/s', '', $str);
}

/**
 * 提取字符串中的数字
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function decimal($str) {
	preg_match_all('(\d+(\.\d+)?) ', $str, $arr);
	return $arr;
}

/**
 * 0-9 中文转阿拉伯数字
 */
function chrToNum($chr) {
	$arr1 = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
	$arr2 = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
	return $arr2[array_search($chr, $arr1)];
}

/**
 * 截取字符串，返回被截取的字符串之外的字符串
 * @param type $start 开始字符串
 * @param type $end  结束字符串
 * @param type $Str  需要截取的字符串
 * @return string 返回除被截取的字符串之外的字符串
 */
function delStr($start, $end, $Str) {
	$temp = $Str;
	while (strpos($temp, $start) && strpos($temp, $end)) {
		$temp = substr($temp, 0, strpos($temp, $start)) . substr($temp, strpos($temp, $end) + strlen($end));
	}
	return $temp;
}

/**
 * 截取字符串，返回截取的字符串
 * @param string $kw1  需要截取的字符串
 * @param string $mark1  字符串前面的标志字符串
 * @param string $mark2  字符串后面的标志字符串
 * @return string   返回截取的字符串
 */
function getNeedBetween($kw1, $mark1, $mark2) {
	$kw = $kw1;
	$st = stripos($kw, $mark1);
	$ed = stripos($kw, $mark2);
	if ($ed === false) {
		$ed = strlen($kw);
	}

	if (($st === false) || $st >= $ed) {
		return '';
	}

	$kw = substr($kw, ($st + strlen($mark1)), ($ed - $st - strlen($mark1)));
	return $kw;
}

/**
 * 检查并创建目录
 * @param string $dir
 * @return array 错误信息
 */
function checkDir($dir) {
	$msg = true;
	$dirArr = explode('/', $dir);
	//$dirTemp = $dirArr[0] ? $dirArr[0] : substr(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), 0, strlen($_SERVER['SCRIPT_FILENAME']) - strlen($_SERVER['PHP_SELF']));
	$dirTemp = $dirArr[0] ? $dirArr[0] : dirname($_SERVER['SCRIPT_FILENAME']);
	$num = count($dirArr);
	for ($n = 1; $n <= $num; $n++) {
//            echo '<br />dirTemp:'.$dirTemp.',n:'.$n.'';
		if (!is_dir($dirTemp)) {
			if (!mkdir($dirTemp, 0777)) {
				//                      $msg[] = 'Make dir false. Dir: '.$dirTemp;
				return ['msg' => 'Make dir false. Dir: ' . $dirTemp, 'status' => 400];
			}
		}
		$dirTemp .= '/' . $dirArr[$n];
	}
	return ['msg' => $msg, 'status' => 200];
}

/**
 * 格式化url中get参数
 * @param string $url
 * @return array
 */
function param_gets($url) {
	$query = parse_url(urldecode($url))['query'];
	preg_match_all('/([^=]+)=([^&]+)[&]{0,1}/', $query, $matchs, 2);
	$gets = [];
	foreach ($matchs as $match) {
		$gets[$match[1]] = $match[2];
	}
	return $gets;
}

/**
 * 获得毫秒数
 * @return
 */
function getMillisecond() {
	list($t1, $t2) = explode(' ', microtime());
	return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/**
 * js escape php 实现
 * @param $string           the sting want to be escaped
 * @param $in_encoding
 * @param $out_encoding
 */
function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2') {
	$return = '';
	if (function_exists('mb_get_info')) {
		for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
			$str = mb_substr($string, $x, 1, $in_encoding);
			if (strlen($str) > 1) {
				// 多字节字符
				$return .= '%u' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
			} else {
				$return .= '%' . strtoupper(bin2hex($str));
			}
		}
	}
	return $return;
}

/**
 * js unescape php 实现
 * @param $string           the sting want to be unescape
 */
function unescape($str) {
	$ret = '';
	$len = strlen($str);
	for ($i = 0; $i < $len; $i++) {
		if ($str[$i] == '%' && $str[$i + 1] == 'u') {
			$val = hexdec(substr($str, $i + 2, 4));
			if ($val < 0x7f) {
				$ret .= chr($val);
			} else
			if ($val < 0x800) {
				$ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
			} else {
				$ret .= chr(0xe0 | ($val >> 12)) .
				chr(0x80 | (($val >> 6) & 0x3f)) .
				chr(0x80 | ($val & 0x3f));
			}

			$i += 5;
		} else
		if ($str[$i] == '%') {
			$ret .= urldecode(substr($str, $i, 3));
			$i += 2;
		} else {
			$ret .= $str[$i];
		}

	}
	return $ret;
}

/**
 * 价格单位格式，万以上转换
 * @param type $value
 * @return type
 */
function formatMoney($value) {
	if ($value >= 10000) {
		return sprintf("%.2f", $value / 10000);
	} else {
		return $value;
	}
}

function jsontoarray($jsons) {
	$arr = [];
	foreach ($jsons as $key => $jsons_value) {
		$arr[] = $jsons_value;
	}
	return $arr;
}
//货币格式化
function doFormatMoney($money, $format_money = '') {
	if (!is_numeric($money)) {
		return 0;
	}
	$tmp_money = strrev($money);
	for ($i = 3; $i < strlen($money); $i += 3) {
		$format_money .= substr($tmp_money, 0, 3) . ",";
		$tmp_money = substr($tmp_money, 3);
	}
	$format_money .= $tmp_money;
	$format_money = strrev($format_money);
	return $format_money;
}

/***
 * 求字符串的字符数，UTF-8编码。备注：只实用普通情况，如果出现中文扩展区，一个字符的长度为4字节时，会出现bug。
 * @param $str string 秋长度的字符串
 * @return int 字符串长度
 */
function mb__strlen($str) {
	$en = preg_replace('/[^(\x20-\x7F)]*/', '', $str); //去掉非asicc字符
	$zh = preg_replace('/[(\x20-\x7F)]*/', '', $str); //去掉asicc字符，就是中文字符串
	return strlen($en) + strlen($zh) / 3;
}

function _errorhandler() {
	return false;
}

/**
 * 字符串中某个字符串所有的位置
 * @param $str  字符串
 * @param $s   需要找位置的字符串
 * @return array
 */
function str_all_pos($str, $s) {
	$count = substr_count($str, $s);
	$n = 0;
	$arr = [];
	for ($i = 1; $i <= $count; $i++) {
		$n = strpos($str, $s, $n);
		for ($j = 1; $j <= $count; $j++) {
			if ($i == $j) {
				$arr[] = $n;
			}
		}
		$n++;
	}
	return $arr;
}

function yc_phone($str) {
	$str = $str;
	$resstr = substr_replace($str, '****', 3, 4);
	return $resstr;
}

/**
 * 去掉字符左右的空字符
 */
function set_trim($data){
	if(empty($data)){
		return '';
	}
	if(!is_array($data)){
		return trim($data);
	}
	foreach ($data as $key => $value) {
		if(!is_array($value)){
			$data[$key] = trim($value);
		}
	}
	return $data;
}
