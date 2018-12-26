<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@vip.qq.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

namespace Files\Controller;

use Think\Controller;

/**
 * 错误邮件处理
 */
class ErrorController extends Controller {

    /**
     * 发送错误邮件
     */
    public function sendmail() {
        $data = $_POST;
        $mssage = "";
        foreach ($data as $k => $v) {
            $mssage.=strtoupper($k) . ":" . str_replace('#', "<br />#", $v) . "<br />";
        }
        $msg = think_send_mail("850643841@qq.com", "异常信息", strip_tags($data['message']), $mssage);
        $this->success($msg);
    }

}
