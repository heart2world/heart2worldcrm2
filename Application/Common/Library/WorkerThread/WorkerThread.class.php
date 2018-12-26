<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@ipoxiao.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

namespace Common\Library\WorkerThread;

/**
 * 多线程执行类
 */
//class WorkerThread extends \Stackable {
//
//    private $_method = '';
//    public $param_arr = [];
//    public $modelname = '';
//
//    public function __construct($method) {
//        $this->_method = $method;
//    }
//
//    public function run() {
//        $modelclass = $this->worker->getConnection();
//        print_r($modelclass);
//        call_user_func_array([$modelclass, $this->_method], $this->param_arr);
//    }
//
//}
/**
 * 多线程执行类
 */
class WorkerThread extends \Thread {

    private $_method = '';
    public $param_arr = [];
    public $modelname = '';

    public function __construct($method) {
        $this->_method = $method;
    }

    public function run() {
        echo $this->_method;
        global $std;
        Thread::globally(function() {
            $std = new \PDO('mysql:dbname=trade_online;host=192.168.1.15;port=3306;charset=utf8', 'admin', '123456');
        });
        print_r($std);
//        $modelclass = $this->worker->getConnection();
//        print_r($modelclass);
//        call_user_func_array([$modelclass, $this->_method], $this->param_arr);
    }

}
