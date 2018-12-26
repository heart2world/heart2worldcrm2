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

/**
 * 文件上传
 */
class FileController extends Controller {

	public function index() {
		$sha1 = I('get.sha1');
		if (!$sha1) {
			exit();
		}
		$logic = D('File');
		if (!$logic->download($sha1)) {
			$this->error($logic->getError());
		}
	}

	/**
	 * 上传文件
	 */
	public function upload() {

		$return = array('code' => 200, 'status' => 1, 'msg' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		//$this->dl_file_resume($_FILES);
		$info = $File->upload(
			$_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG")
		);
		/* 记录附件信息 */
		if ($info) {
			$files = "";
			foreach ($info as $key => $val) {
				$return["data"] .= "," . $val['sha1'];
				$files .= $files ? ',' . $val['sha1'] : $val['sha1'];
				$return["title"] = $val['name'];
			}
			$return["data"] = trim($return["data"], ',');
		} else {
			$return['code'] = 400;
			$return['status'] = 0;
			$return['msg'] = $File->getError();
		}
		/* 返回JSON数据 */
		$this->ajaxReturn($return, 'JSON', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 接受流
	 * @param  [type] $receiveFile [文件名]
	 * @param  [type] $path        [文件储存地址]
	 * @return [type]              [description]
	 */
	public function receiveStreamFile($receiveFile, $path) {
		$streamData = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
		if (empty($streamData)) {
			$streamData = file_get_contents('php://input');
		}
		//目录不存在创建目录
		$this->_mkdir($path);
		//判断需要上传文件长度与实际上传文件上次
		if ($_SERVER['HTTP_SIZE'] > $_SERVER['CONTENT_LENGTH']) {
			return false;
		}
		if ($streamData != '') {
			//文件不存在新建文件
			if (!is_file($path . $receiveFile)) {
				//删除其余临时文件
				$this->delALL($path);
				//文件不存在新建文件
				$ret = file_put_contents($path . $receiveFile, $streamData, true);
			}
			//文件存在继续写入
			else {
				$res = fopen($path . $receiveFile, "a");
				fwrite($res, $streamData);
				fclose($res);
			}
			//文件传完修改后缀
			if (isset($_SERVER['HTTP_SUFFIX']) && !empty($_SERVER['HTTP_SUFFIX'])) {
				$filename = $receiveFile . '.' . $_SERVER['HTTP_SUFFIX'];
				rename($path . $receiveFile, $path . $filename);
				//获取文件类型
				$finfo = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $path . $filename);
				finfo_close($finfo);
				//接受二进制流处理
				$new = preg_match('/([^;]+);?.*$/', $mimetype, $match);
				if ($new) {
					$mimetype = trim($match[1]);
				}
				//存储
				$file = [
					'name' => $filename,
					'savename' => $filename,
					'ext' => $_SERVER['HTTP_SUFFIX'],
					'sha1' => sha1_file($path . $filename),
					'md5' => md5_file($path . $filename),
					'mime' => $mimetype,
					'size' => filesize($path . $filename),
					'create_time' => time(),
					'type' => 3,
					'savepath' => ltrim($path, '.'),
				];
				M('file')->create($file);
				if (M('file')->add() === false) {
					return false;
				}
				return sha1_file($path . $filename);
			}
		} else {
			$ret = false;
		}
	}
	public function delAll($path) {
		$files = glob($path . '/*');
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
	/**
	 * 创建目录
	 * @param  string $savepath 要创建的穆里
	 * @return boolean          创建状态，true-成功，false-失败
	 */
	public function _mkdir($path) {
		if (is_dir($path)) {
			return true;
		}
		if (mkdir($path, 0777, true)) {
			return true;
		} else {
			return false;
		}
	}

	public function house_import() {
		$return = array('code' => 200, 'status' => 1, 'msg' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		//$this->dl_file_resume($_FILES);
		$info = $File->upload(
				$_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG")
		);
		/* 记录附件信息 */
		if ($info) {
			$files = "";

			foreach ($info as $key => $val) {
				$return["data"] .= "," . $val['sha1'];
				$files .= $files ? ',' . $val['sha1'] : $val['sha1'];
				$return["title"] = I('post.title');
			}
			if ($data["table"] && $data["id"]) {
				$tabel = M($data["table"]);
				$tabel->where("id=" . $data["id"])->setField("files", $files);
			}
			$return["data"] = trim($return["data"], ',');
		} else {
			$return['code'] = 400;
			$return['status'] = 0;
			$return['msg'] = $File->getError();
		}
		$new =I('get.new');
		if($new){
			$return['status'] ==1 ? $return['success'] = true :  $return['success'] = false;
		}

		$company_id = I('post.company_id');
		$file_type = I('post.file_type');
		$system_type = I('post.system_type');
		$data =[
			'company_id'=>$company_id,
				'file_type'=>$file_type,
				'system_type'=>$system_type,
				'status'=>1,
			    'create_time'=>time(),
			'hash'=>$return['data']
		];
		if($return['status'] == 1 && !HouseImportModel::getInfoByCompanyIdType($company_id,$file_type,$system_type)){
			HouseImportModel::add($data);
		}
		/* 返回JSON数据 */
		$this->ajaxReturn($return, 'JSON', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}
}
