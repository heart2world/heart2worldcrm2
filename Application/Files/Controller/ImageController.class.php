<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@ipoxiao.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

namespace Files\Controller;

use Think\Controller;

class ImageController extends Controller {

	public function index() {
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
//                header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
		$sha1 = I("get.sha1");
		if (empty($sha1)) {
			return;
		}

		$Picture = D('Picture');
		$res = $Picture->field("id,path,size,mime")->where(['sha1' => $sha1])->find();
		if ($res) {
			$path = "." . $res["path"];
			if (is_file($path)) {
				//判断文件是否存在
				header("Cache-Control:max-age=2592000'");
				header("Pragma: cache");
				$offset = 30 * 60 * 60 * 24; // cache 1 month
				$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
				header($ExpStr);
				header('etag:' . $sha1);
				header("Content-Description: File Transfer");
				header('Content-type: ' . $res['mime']);
				if ($res['size']) {
					header('Content-Length:' . $res['size']);
				}
				readfile($path);
			} else {
				//$Picture->removeTrash($res); //不存在删除数据库对应数据
			}
		}
	}
	public function subindex() {
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
//                header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
		$sha1 = I("get.sha1");
		if (empty($sha1)) {
			return;
		}

		$Picture = D('Picture');
		$res = $Picture->field("id,subpath,mime,subsize")->where(['subsha1' => $sha1])->find();
		if ($res) {
			$path = "." . $res["subpath"];
			if (is_file($path)) {
//判断文件是否存在
				header("Cache-Control: private, max-age=10800, pre-check=10800");
				header("Pragma: private");
				header("Expires: " . date(DATE_RFC822, strtotime(" 2 day")));
				header("Content-Description: File Transfer");
				header('Content-type: ' . $res['mime']);
				if ($res['size']) {
					header('Content-Length:' . $res['subsize']);
				}
				readfile($path);
			} else {
				//$Picture->removeTrash($res); //不存在删除数据库对应数据
			}
		}
	}
	/**
	 * 上传图片
	 * @author huajie <banhuajie@163.com>
	 */
	public function upload() {
		/* 返回标准数据 */
		$return = array('code' => 200, 'status' => 1, 'msg' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$Picture = D('Picture');
		$pic_driver = C('PICTURE_UPLOAD_DRIVER');
		if (I('get.from') == 1 || I('post.from') == 1) {
			C('PICTURE_UPLOAD.from', 1);
		}
		C('PICTURE_UPLOAD.is_water', false);
		$info = $Picture->upload(
			$_FILES, C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'), C("UPLOAD_{$pic_driver}_CONFIG")
		); //TODO:上传到远程服务器
		/* 记录图片信息 */
		if ($info) {
			$type = I('get.type', 1);
			if ($type == 1) {
				$return["data"] = '';
				$return["path"] = '';
				foreach ($info as $key => $val) {
					$return["data"] .= "," . $val['sha1'];
					$return["path"] .= "," . getFileUrl($val['sha1']);
				}
				$return["data"] = ltrim($return["data"], ',');
				$return["path"] = ltrim($return["path"], ',');
				$return["title"] = I('post.title');
			} else {
				$return["data"] = [];
				$return["path"] = [];
				foreach ($info as $key => $val) {
					array_push($return["data"], $val['sha1']);
					array_push($return["path"], getFileUrl($val['sha1']));
				}
			}
		} else {
			$return['code'] = 400;
			$return['status'] = 0;
			$return['msg'] = $Picture->getError();
		}
		/* 返回JSON数据 */
		$new = I('get.new');
		if ($new) {
			$return['status'] == 1 ? $return['success'] = true : $return['success'] = false;
		}
		$this->ajaxReturn($return, 'JSON', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 编辑器图片上传
	 */
	public function ke_upimg() {

		/* 返回标准数据 */
		$return = array('error' => 0, 'info' => '上传成功', 'data' => '');
		$setting = C('EDITOR_UPLOAD');
		/* 调用文件上传组件上传文件 */
		$Picture = D('Picture');
//        $uploader = new \Think\Upload($setting, 'Local');
		$info = $Picture->upload($_FILES, $setting);
		if ($info) {
			$return['url'] = getFileUrl($info['imgFile']['sha1']);
			unset($return['info'], $return['data']);
		} else {
			$return['error'] = 1;
			$return['message'] = $Picture->getError();
		}
		/* 返回JSON数据 */
		exit(json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	/**
	 * 下载微信图片到本地
	 * @param type $media_id 微信图片标识
	 */
	public function wximg($media_id) {
		/* 返回标准数据 */
		$return = array('status' => 1, 'msg' => '上传成功', 'data' => '');
		$media_ids = explode(',', $media_id);
		$lists = [];
		foreach ($media_ids as $k => $v) {
			$Picture = D('Picture');
			$info = $Picture->isFile(['media_id' => $v], 'media_id');
			if ($info) {
				$lists[] = $info;
				continue;
			}
			$config = F("weixin", "", "./Data/");
			$wechatauth = new \Com\WechatAuth($config['appid'], $config['appsecret']);
			$wechatauth->getAccessToken();
			$httpCode = '';
			$file_content = $wechatauth->download($v, $httpCode);
			if ($httpCode['http_code'] != 200) {
				$return['status'] = 0;
				$return['msg'] = "上传失败！";
				break;
			}
			$lists[] = $this->_wxupload($httpCode, $file_content, $media_id, $Picture);
		}
		$count = count($media_ids);
		foreach ($lists as $k => $v) {
			if ($count > 1) {
				$return['data'][] = $v['sha1'];
			} else {
				$return['data'] = $v['sha1'];
			}
		}
		exit(json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	/**
	 * 微信图片处理
	 * @param type $httpcode
	 * @param type $file_content
	 * @param type $media_id
	 */
	private function _wxupload($httpCode, $file_content, $media_id, $Picture) {
		$path = "./Uploads/Weixin/" . date('Y-m-d') . '/';
		$this->_mkdir($path); //检查目录是否存在不存在就创建
		$filename = $this->_getExt($httpCode['content_type'], uniqid()); //后去扩展名
		$fp = @fopen($path . $filename, "w"); //将文件绑定到流
		fwrite($fp, $file_content); //写入文件
		fclose($fp);
		$documentroot = $_SERVER['DOCUMENT_ROOT'] . ltrim(__ROOT__, '/') . ltrim($path, '.') . $filename;
		$file = [
			'sha1' => sha1_file($documentroot),
			'md5' => md5_file($documentroot),
			'mime' => $httpCode['content_type'],
			'size' => strlen($file_content),
			'path' => ltrim($path, '.') . $filename,
			'media_id' => $media_id,
		];
		$Picture->create($file) && $Picture->add();
		return $file;
	}

	/**
	 * 抓取百度地图
	 * @param type $lng
	 * @param type $lat
	 * @param type $width
	 * @param type $height
	 * @param type $ak
	 */
	public function bdupload($lng, $lat, $type = 0, $width = "400", $height = "300", $ak = "qvcG7zv0wclhgLjbstS2tVxXV8aFKuzl") {
		$return = array('code' => 200, 'msg' => '下载成功', 'data' => '');
		$Picture = D('Picture');
		$path = "./Uploads/Baidu/" . date('Y-m-d') . '/';
		$this->_mkdir($path); //检查目录是否存在不存在就创建
		$area = $lng . "," . $lat;
		$url = "http://api.map.baidu.com/staticimage/v2?ak={$ak}&width={$width}&height={$height}&center={$area}&markers={$area}&zoom=15&markerStyles=l,,0xff0000";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();
		$imginfo = curl_getinfo($ch);
		$filename = $this->_getExt($imginfo['content_type'], uniqid()); //后去扩展名
		$tp = @fopen($path . $filename, 'w');
		fwrite($tp, $return_content);
		fclose($tp);
		if (!file_exists($path . $filename)) {
			$return['code'] = 400;
			$return['msg'] = '下载失败';
			$this->ajaxReturn($return);
		}
		$documentroot = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(__ROOT__, '/') . ltrim($path, '.') . $filename;
		$file = [
			'sha1' => sha1_file($documentroot),
			'md5' => md5_file($documentroot),
			'mime' => $imginfo['content_type'],
			'size' => $imginfo['download_content_length'],
			'path' => ltrim($path, '.') . $filename,
		];
		$Picture->create($file) && $Picture->add();
		if ($type == 1) {
			return $file['sha1'];
		}
		$return = array('code' => 200, 'msg' => '下载成功', 'data' => $file['sha1'], 'path' => getFileUrl($file['sha1']));
		$this->ajaxReturn($return);
	}

	/**
	 * 创建目录
	 * @param  string $savepath 要创建的穆里
	 * @return boolean          创建状态，true-成功，false-失败
	 */
	private function _mkdir($path) {
		if (is_dir($path)) {
			return true;
		}
		if (mkdir($path, 0777, true)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @param type $type
	 * @param string $filename
	 */
	private function _getExt($type, $filename) {
		switch ($type) {
		case 'image/jpg':
		case 'image/jpeg':
			$filename .= ".jpg";
			break;
		case 'image/png':
			$filename .= ".png";
			break;
		case 'image/gif':
			$filename .= ".gif";
			break;
		}
		return $filename;
	}

	public function qrcode($url) {
		vendor("phpqrcode.phpqrcode");
		$url = urldecode($url);
		\QRcode::png($url);
	}

	/**
	 * 生成二维码
	 * @param  [type]  $url           [地址]
	 * @param  [type]  $filename      [存储地址]
	 * @param  integer $pixelPerPoint [文件大小]
	 * @param  [type]  $logo          [二维码内部头像]
	 * @return [type]                 [description]
	 */
	public function qr_code($url, $logo) {
		$Picture = D('Picture');
		if (empty($logo)) {
			$logo = '418cf25bdfe20931b3733ed3d78fdc6ef9e28fdd';
		}
		$filename = md5($url . $logo);
		$qrcache_path = "./Uploads/Qrcode/";
		$this->_mkdir($qrcache_path); //检查目录是否存在不存在就创建
		$qrcache_path = "./Uploads/Qrcode/" . $filename . '.png';
		$sha1 = sha1($qrcache_path);
		if (is_file($qrcache_path) && file_get_contents($qrcache_path)) {
			return $sha1;
		}
		vendor("phpqrcode.phpqrcode");
		$url = urldecode($url);
		//保存二维码
		\QRcode::png($url, $qrcache_path, $level = QR_ECLEVEL_H, 80);
		$info = $Picture->where(['sha1' => $logo])->find();
		if (empty($info) || !is_file('.' . $info['path'])) {
			$info['path'] = '/Uploads/Picture/2016-07-22/5791ea4de599b.jpg';
		}
		$head = new \Imagick('.' . $info['path']);
		$qrcode = new \Imagick($qrcache_path);
		$qrcodeWH = $qrcode->getImageGeometry();
		$qr_w = $qrcodeWH['width'] / 4;
		$qr_h = $qrcodeWH['height'] / 4;

		$head->setImageResolution(0.1, 0.3);
		$head->roundCorners(40, 40);

		$wxWH = $head->getImageGeometry();
//        if ($wxWH['width'] > $qr_w) {
		//            $wxW = $qr_w;
		//            $wxH =$qr_w; //$wxW / $wxWH['width'] * $wxWH['height'];
		//        } elseif ($wxWH['width'] > $qr_h) {
		//            $wxH = $qr_h;
		//            $wxW =$qr_w; //$wxH / $wxWH['height'] * $wxWH['width'];
		//        } else {
		//            $wxW = $wxWH['width'];
		//            $wxH = $wxWH['height'];
		//        }
		//        echo $qr_h,'--',$qr_w;

		$head->thumbnailImage($qr_w, $qr_h, false); //强制缩放
		$qr_h += 20;
		$qr_w += 20;
		$canvas = new \Imagick();
		$canvas->newimage($qr_w, $qr_h, 'white', 'png');
		$canvas->setImageResolution(0.1, 0.3);
		$canvas->roundCorners(20, 20);
		$canvas->compositeimage($head, \Imagick::COMPOSITE_OVER, 10, 10);
		$canvas->setImageCompressionQuality(80);
		$canvas->writeImage("123.jpg");

		$w = ($qrcodeWH['width'] - $qr_w) / 2;
		$h = ($qrcodeWH['height'] - $qr_h) / 2;
		//$canvas->setimagealphachannel(6);
		$qrcode->compositeImage($canvas, \Imagick::COMPOSITE_OVER, $w, $h); //合成二维码
		$qrcode->setImageCompressionQuality(80);
		$qrcode->writeImage($qrcache_path);
		if ($sha1) {
			//存储
			$file = [
				'sha1' => $sha1,
				'md5' => $filename,
				'mime' => 'image/png',
				'size' => filesize($qrcache_path),
				'create_time' => time(),
				'status' => 1,
				'path' => ltrim($qrcache_path, '.'),
			];
			$Picture->create($file) && $Picture->add();
		}

		return $sha1;
	}

	//
	public function radius() {

	}

	public function demo($url, $logo) {
		$Picture = D('Picture');
		$filename = md5($url);
		$qrcache_path = "./Uploads/Qrcode/";
		$this->_mkdir($qrcache_path); //检查目录是否存在不存在就创建
		$qrcache_path = "./Uploads/Qrcode/" . $filename . '.png';
		vendor("phpqrcode.phpqrcode");
		$url = urldecode($url);
		//保存二维码
		\QRcode::png($url, $qrcache_path, $level = QR_ECLEVEL_L, 80);

		$info = $Picture->where(['sha1' => $logo])->find();
//        if (empty($info) || !is_file('.' . $info['path'])) {
		//            echo '头像不存在!!';
		//            die();
		//        }
		$info['path'] = './Uploads/Picture/2016-07-22/5791ea4de599b.jpg';
		$head = new \Imagick($info['path']);
		$qrcode = new \Imagick($qrcache_path);
		$qrcodeWH = $qrcode->getImageGeometry();
		$qr_w = $qrcodeWH['width'] / 4;
		$qr_h = $qrcodeWH['height'] / 4;

		$head->setImageResolution(0.1, 0.3);
		$head->roundCorners(40, 40);

		$wxWH = $head->getImageGeometry();
//        if ($wxWH['width'] > $qr_w) {
		//            $wxW = $qr_w;
		//            $wxH =$qr_w; //$wxW / $wxWH['width'] * $wxWH['height'];
		//        } elseif ($wxWH['width'] > $qr_h) {
		//            $wxH = $qr_h;
		//            $wxW =$qr_w; //$wxH / $wxWH['height'] * $wxWH['width'];
		//        } else {
		//            $wxW = $wxWH['width'];
		//            $wxH = $wxWH['height'];
		//        }
		//        echo $qr_h,'--',$qr_w;

		$head->thumbnailImage($qr_w, $qr_h, false); //强制缩放
		$qr_h += 20;
		$qr_w += 20;
		$canvas = new \Imagick();
		$canvas->newimage($qr_w, $qr_h, 'white', 'png');
		$canvas->setImageResolution(0.1, 0.3);
		$canvas->roundCorners(20, 20);
		$canvas->compositeimage($head, \Imagick::COMPOSITE_OVER, 10, 10);
		$canvas->setImageCompressionQuality(80);
		$canvas->writeImage("123.jpg");

		$w = ($qrcodeWH['width'] - $qr_w) / 2;
		$h = ($qrcodeWH['height'] - $qr_h) / 2;

		$qrcode->compositeImage($canvas, \Imagick::COMPOSITE_OVER, $w, $h); //合成二维码
		$qrcode->setImageCompressionQuality(80);
		$qrcode->writeImage($qrcache_path);
		print_r($filename);
		print_r($qrcache_path);
	}

	//微信图片处理
	public function wx_pic_download($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$root_path = '/Uploads/Picture/';
		$path = date('Y-m-d') . '/';
		mkdir(dirname(APP_PATH) . $root_path . $path, 0755, true);
		$filename = uniqid() . '.jpg';
		$save_paths = dirname(APP_PATH) . $root_path . $path . $filename;
		file_put_contents($save_paths, $output);
		//判断
		$model = M('picture');
		$sha1 = sha1_file($save_paths);
		$check = $model->field('id')->where(array('sha1'=>$sha1))->find();
		if($check){
			return ['msg'=>'操作成功','status'=>200,'data'=>$sha1];
		}
		$md5 = md5_file($save_paths);
		$img_info = getimagesize($save_paths);
		$add_data['path'] = $root_path . $path . $filename;
		$add_data['md5'] = $md5;
		$add_data['sha1'] = $sha1;
		$add_data['mime'] = $img_info['mime'];
		$add_data['size'] = filesize($save_paths);
		$add_data['create_time'] = NOW_TIME;
		$res = $model->add($add_data);
		if($res === false){
			return ['msg'=>'操作失败','status'=>400];
		}
		return ['msg'=>'操作成功','status'=>200,'data'=>$sha1];
	}

}
