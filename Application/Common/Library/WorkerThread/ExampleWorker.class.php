<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@ipoxiao.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

namespace Common\Library\WorkerThread;

class ExampleWorker extends \Worker {

    public static $modelclass;
    public $modelname = '';

    public function __construct($modelname = '') {
        $this->modelname = $modelname;
    }

    /*
     * The run method should just prepare the environment for the work that is coming ...
     */

    public function run() {
    
        print_r(new demo());
        self::$modelclass = $demo->test();
    }

    public function getConnection() {
        return self::$modelclass;
    }

}
