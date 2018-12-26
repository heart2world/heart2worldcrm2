<?php

namespace Common\Library\Download;

class DownloadImage {

    /**
     * 微信图片处理
     * @param type $httpcode
     * @param type $file_content
     * @param type $media_id
     */
    private function _save($httpCode, $file_content, $media_id, $Picture) {
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
            case 'image/jpeg':
                $filename.=".jpg";
                break;
        }
        return $filename;
    }

}
