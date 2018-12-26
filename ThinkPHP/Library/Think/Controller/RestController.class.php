<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Controller;

use Think\Controller;

/**
 * ThinkPHP REST控制器类
 */
class RestController extends Controller {

    // 当前请求类型
    protected $_method = '';
    // 当前请求的资源类型
    protected $_type = '';
    // REST允许的请求类型列表
    protected $allowMethod = array('get', 'post', 'put', 'delete');
    // REST默认请求类型
    protected $defaultMethod = 'get';
    // REST允许请求的资源类型列表
    protected $allowType = array('html', 'xml', 'json', 'rss');
    // 默认的资源类型
    protected $defaultType = 'html';
    protected $_p = 1; //分页页数
    protected $_row = 10; //每页条数
    protected $_allcount = 0;
    // REST允许输出的资源类型列表
    protected $allowOutputType = array(
        'xml' => 'application/xml',
        'json' => 'application/json',
        'html' => 'text/html',
    );
    protected $_map = ""; //get参数
    protected $_order = ""; //排序
    protected $_user = ""; //登录对象
    protected $_post = ""; //post参数
    protected $_model = ""; //模型对象
    protected $_configs = ""; //系统配置
    protected $_fileds = "";
    protected $_modelclass = '';
    protected $_token = '';
    protected $_id = 0; //数据id
    protected $_allowfunction = [ //允许不登录 就可以直接访问的函数
        'users' => ',login,register,sendcode,getcity,updatepwd,getconfig,help,demo,',
        'item' => ',index_get,ad_get,',
        'membergroup' => ',index_get,',
        'category' => ',index_get,membergroup_get,',
        'dynamic' => ',index_get,detail,',
        'app' => '*',
        'pay' => '*',
    ];

    /**
     * 架构函数
     * @access public
     */
    public function __construct() {
        // 资源类型检测
        if ('' == __EXT__) { // 自动检测资源类型
            $this->_type = $this->getAcceptType();
        } elseif (!in_array(__EXT__, $this->allowType)) {
            // 资源类型非法 则用默认资源类型访问
            $this->_type = $this->defaultType;
        } else {
            $this->_type = __EXT__;
        }
        $this->_method = strtolower(REQUEST_METHOD);
        $this->_post = I("request.");
        if (IS_PUT) {
            parse_str(file_get_contents("php://input"), $data);
            $this->_post = array_merge($this->_post, $data);
        }
        $this->_getdata();
        $this->_configs = S('DB_CONFIG_DATA');
        if (!$this->_configs) {
            $this->_configs = api('Config/lists');
            S('DB_CONFIG_DATA', $this->_configs);
        }
        $this->_checkEmpty();
        $this->_init();
        parent::__construct();
    }

    /**
     * get参数初始化
     */
    private function _getdata() {

        $getdata = I("get.");
        foreach ($getdata as $k => $v) {
            switch ($k) {
                case "order":
                    strpos($v, "-") > -1 ? $v = str_replace("-", " ", $v) : $v = $v . " desc";
                    $this->_order = $v;
                    break;
                case startWith($k, "search"):
                    $this->_map[str_replace("search", "", $k)] = array("like", "%" . $v . "%");
                    break;
                case 'name':
                case 'title':
                    $this->_map[$k] = array('like', '%' . $v . '%');
                    break;
                case "p":
                    $this->_p = $v;
                    break;
                case "row":
                    $this->_row = $v;
                    break;
                case 'fields':
                    $this->_fileds = $v;
                    break;
                case 'id':
                    $this->_id = $v;
                    $this->_map[$k] = $v;
                    break;
                case 'model':
                    $this->_model = getModel($v, "");
                    break;
                case "debug":
                    defined("DEBUG") || define("DEBUG", $v);
                    break;
                case 'dist':

                    if (!empty($v) && $v != 'null') {
                        $this->_map[$k] = $v;
                    }
                    break;
                default :
                    $this->_map[$k] = $v;
                    break;
            }
        }
        if ($this->_model) {
            $this->_map['mid'] = $this->_model['id'];
            $this->_post['mid'] = $this->_model['id'];
        }
        $this->_checktoken();
        $this->_crontab();
    }

    protected function _checktoken() {
        $c_name = strtolower(CONTROLLER_NAME);
        $a_name = strtolower(ACTION_NAME);
        if (!empty($this->_model)) {
            $a_name = $this->_model['alias'] . '_' . $this->_method;
        } elseif ($a_name == 'index') {
            $a_name.='_' . $this->_method;
        }
        if (isset($_SERVER['HTTP_ACCESSTOKEN'])) {
            $this->_token = $_SERVER['HTTP_ACCESSTOKEN'];
            $this->_user = F($this->_token, '', './Data/Users/');
            if (!empty($this->_user) && $this->_user['token_end_time'] < NOW_TIME) {
                $this->_postReturn('您的登录已经过期，请重新登录！', 401);
            }
        }
        $checkfunction = isset($this->_allowfunction[$c_name]) ? $this->_allowfunction[$c_name] : '';
        if (!empty($checkfunction) && ($checkfunction == '*' || strpos($checkfunction, sprintf(',%s,', $a_name)) > -1)) {
            if (!empty($this->_user)) {
                defined('UID') || define("UID", $this->_user['id']);
                $this->_post['uid'] = UID;
            }
        } else {
            if (empty($this->_token)) {
                $this->_postReturn('验证令牌为空！', 401);
            }
            if (empty($this->_user)) {
                $this->_postReturn('您的帐号已经在其他地方登录，请重新登录！', 401);
            }
            defined('UID') || define("UID", $this->_user['id']);
            $this->_post['uid'] = UID;
        }
    }

    /**
     * 定时检查会员是否过期 （过期就修改会员状态发送通知可客户端）
     */
    private function _crontab() {
        if (!defined('UID') || $this->_user['is_vip'] == 0 || $this->_user['end_time'] > NOW_TIME) {//检查是否登录或者是否为会员,并且已经过期
            return;
        }
        M('member')->where(['id' => UID])->save(['is_vip' => 0]); //修改会员状态
        $this->_user['is_vip'] = 0;
        F($this->_token, $this->_user, './Data/Users/'); //修改缓存会员状态
        sendJpush(sprintf('您的会员已于%s过期', date('Y-m-d H:i:s', $this->_user['end_time'])), UID, 2); //发送通知
    }

    /**
     * 检查字段是否为空
     */
    protected function _checkEmpty() {
        $c_name = strtoupper(CONTROLLER_NAME);
        $a_name = strtoupper(ACTION_NAME);
        $configfields = C($c_name . '_' . $a_name);
        if (empty($configfields)) {
            return;
        }
        isset($configfields['allowMethod']) && $this->allowMethod = $configfields['allowMethod'];
        // 请求方式检测
        if (!in_array($this->_method, $this->allowMethod)) {
            // 请求方式非法 则用默认请求方法
            $this->_postReturn('请求方法不被允许', 415, '只允许' . implode(',', $this->allowMethod) . '请求');
        }
        $this->_getfields($configfields);
        switch ($this->_method) {
            case 'put':
                $this->_post = array_merge($this->_post, I('put.'));
            case 'post':
            case 'delete':
                if(empty($this->_model)){
                    $checkfield=  isset($configfields[$this->_method])?$configfields[$this->_method]:'';
                }  else {
                    $checkfield=  isset($configfields[$this->_model['alias']][$this->_method])?$configfields[$this->_model['alias']][$this->_method]:'';
                } 
                $this->_checkfiled($checkfield);
                break;
        }
    }

    private function _getfields($configfields) {
        if (empty($configfields)) {
            return;
        }
        $this->_fileds = $configfields;
        if ($this->_model && isset($configfields[$this->_model['alias']])) {//判断是否传模型
            $this->_fileds = $configfields[$this->_model['alias']];
        }
        if (!empty($this->_fileds)) {
            if ($this->_id) {//判断返回列表还是详情
                $this->_fileds = isset($this->_fileds['detail']) ? $this->_fileds['detail'] : true; //如果没有设置则返回所有数据
            } else {
                $this->_fileds = isset($this->_fileds['list']) ? $this->_fileds['list'] : true; //如果没有设置则返回所有数据
            }
        }
    }

    protected function _checkfiled($checkfield) {
        if (empty($checkfield)) {
            return;
        }
        $fields = '';
        $type = 'and';
        if (!isset($checkfield['fields']) || empty($checkfield['fields'])) {
            return;
        }
        $fields = $checkfield['fields'];
        if (isset($fields['__type'])) {//判断是否设置type
            $type = $fields['__type'];
            unset($fields['__type']);
        }
        $isbool = false;
        foreach ($fields as $key => $val) {
            if (!isset($this->_post[$key]) || empty($this->_post[$key])) {
                if (is_numeric($this->_post[$key]) && $this->_post[$key] == 0) {
                    continue;
                }
                $type == 'and' && $this->_postReturn($val . '不能为空！', 422);
            } else if ($type == 'or') {
                $isbool = true;
                break;
            }
        }
        if ($type == 'or' && $isbool === false) {
            $this->_postReturn('请至少选择一项输入！', 422);
        }
    }

    /**
     * 自动实现方法
     */
    protected function _init() {
        $a_name = strtolower(ACTION_NAME);
        if (!empty($this->_model)) {
            $fun = $this->_model['alias'] . '_' . $this->_method;
            method_exists($this, $fun) && $this->$fun();
        } elseif ($a_name == 'index') {
            $a_name.='_' . $this->_method;
            method_exists($this, $a_name) && $this->$a_name();
        } else {
            return;
        }
        empty($this->_modelclass) && $this->_modelclass = CONTROLLER_NAME;
        $this->_model = D($this->_modelclass);
        $param_arr = [];
        switch ($this->_method) {
            case 'get':
                $method_name = 'lists';
                $param_arr = [$this->_map, $this->_p, $this->_row, $this->_order];
                break;
            case 'post':
                $method_name = 'insert';
                $param_arr = [$this->_post];
                break;
            case 'put':
                $method_name = 'update';
                $param_arr = [$this->_post];
                break;
            case 'delete':
                $method_name = 'del';
                $param_arr = [$this->_map];
                break;
        }
        if (!method_exists($this->_model, $method_name)) {
            $this->_postReturn('非法操作' . $method_name, 404);
        }
        if ($this->_fileds) {
            $this->_model->return_fields = $this->_fileds;
        }
        $data = call_user_func_array([$this->_model, $method_name], $param_arr);
        if ($this->_method == 'get') {
            $this->_allcount = $this->_model->count;
            $this->_getReturn($data);
        } else {
            $this->_postReturn($this->_model->getError(), $this->_model->status, $data);
        }
    }

    /**
     * post delete put返回
     * @param type $msg
     * @param type $status
     * @param type $data
     */
    protected function _postReturn($msg, $status = 200, $data = null) {
        $data = [
            'status' => $status,
            'msg' => $msg,
            'data' => $this->_filter($data),
        ];
        $this->response($data, 'json', $status, $msg);
    }

    protected function _getReturn($data) {
        $this->_page();
        $data = $this->_filter($data);
        $this->response($data, 'json', 200);
    }

    //默认数据处理
    protected function _filter($data) {
        if (empty($data)) {
            return is_array($data) ? [] : '';
        }
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $link = false;
                $data[$k] = $this->_extend($k, $v, $data, $link);
                $link === false || $data[$k . '_link'] = $link;
            } else {
                $data[$k] = $this->_filter($v);
            }
        }
        return $data;
    }

    //数据处理
    private function _extend($key, $value, $data, &$link) {
        $result = "";
        switch ((string) $key) {
            case 'head':
            case "imgpic":
                $link = '';
                $result = getFileUrl($value);
                if ($result) {
                    $link = $result;
                }
                $result = str_replace(NULL, "", $value);
                break;
            case 'imgpiclist':
                $link = [];
                $result = getFileUrl($value, 'image');
                if ($result) {
                    $link = $result;
                }
                $result = str_replace(NULL, "", $value);
                break;
            case 'cityindex':
                $link = '';
                $result = getCityFormat($value);
                if ($result) {
                    $link = $result;
                }
                $result = str_replace(NULL, "", $value);
                break;
            case 'files':
                $result = getFileUrl($value, 'files');
                if ($result) {
                    $link = $result;
                }
                $result = str_replace(NULL, "", $value);
                break;
            default :
                $link = false;
                if (is_numeric($value)) {
                    $result = $value;
                } else {
                    $result = str_replace(NULL, "", $value);
                }
                break;
        }
        return $result;
    }

    /**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method, $args) {

        if (0 === strcasecmp($method, ACTION_NAME . C('ACTION_SUFFIX'))) {
            if (method_exists($this, $method . '_' . $this->_method . '_' . $this->_type)) { // RESTFul方法支持
                $fun = $method . '_' . $this->_method . '_' . $this->_type;
                $this->$fun();
            } elseif ($this->_method == $this->defaultMethod && method_exists($this, $method . '_' . $this->_type)) {
                $fun = $method . '_' . $this->_type;
                $this->$fun();
            } elseif ($this->_type == $this->defaultType && method_exists($this, $method . '_' . $this->_method)) {
                $fun = $method . '_' . $this->_method;
                $this->$fun();
            } elseif (method_exists($this, '_empty')) {
                // 如果定义了_empty操作 则调用
                $this->_empty($method, $args);
            } elseif (file_exists_case($this->view->parseTemplate())) {
                // 检查是否存在默认模版 如果有直接输出模版
                $this->display();
            } else {
                E(L('_ERROR_ACTION_') . ':' . ACTION_NAME);
            }
        }
    }

    /**
     * 获取当前请求的Accept头信息
     * @return string
     */
    protected function getAcceptType() {
        $type = array(
            'xml' => 'application/xml,text/xml,application/x-xml',
            'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
            'js' => 'text/javascript,application/javascript,application/x-javascript',
            'css' => 'text/css',
            'rss' => 'application/rss+xml',
            'yaml' => 'application/x-yaml,text/yaml',
            'atom' => 'application/atom+xml',
            'pdf' => 'application/pdf',
            'text' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv',
            'html' => 'text/html,application/xhtml+xml,*/*'
        );
        foreach ($type as $key => $val) {
            $array = explode(',', $val);
            foreach ($array as $k => $v) {
                if (isset($_SERVER['HTTP_ACCEPT']) && stristr($_SERVER['HTTP_ACCEPT'], $v)) {
                    return $key;
                }
            }
        }
        return false;
    }

    // 发送Http状态信息
    protected function sendHttpStatus($code, $msg = '') {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'unprocessable entity',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        $msg = $msg ? urlencode($msg) : $_status[$code];
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $msg);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $msg);
        }
    }

    /**
     * 编码数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @return string
     */
    protected function encodeData($data, $type = '') {
        switch ($type) {
            case 'json': // 返回JSON数据格式到客户端 包含状态信息
                $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                break;
            case 'xml':// 返回xml格式数据
                $data = xml_encode($data, 'xml');
                break;
            case 'php':
                $data = serialize($data);
                break;
        }
        $this->setContentType($type);
        //header('Content-Length: ' . strlen($data));
        return $data;
    }

    /**
     * 设置页面输出的CONTENT_TYPE和编码
     * @access public
     * @param string $type content_type 类型对应的扩展名
     * @param string $charset 页面输出编码
     * @return void
     */
    public function setContentType($type, $charset = '') {
        if (headers_sent())
            return;
        if (empty($charset))
            $charset = C('DEFAULT_CHARSET');
        $type = strtolower($type);
        if (isset($this->allowOutputType[$type])) //过滤content_type
            header('Content-Type: ' . $this->allowOutputType[$type] . '; charset=' . $charset);
    }

    /**
     * 输出返回数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @param integer $code HTTP状态
     * @return void
     */
    protected function response($data, $type = 'json', $code = 200, $msg = '') {
        $this->sendHttpStatus($code, $msg);
        header('X-Request-Method: ' . $this->_method);
        header('X-End-Time:' . NOW_TIME);
        exit($this->encodeData($data, strtolower($type)));
    }

    /**
     * 输出分页
     */
    protected function _page() {
        $page_count = $this->_allcount > $this->_row ? ceil($this->_allcount / $this->_row) : 1;
        header('X-Pagination-Total-Count:' . $this->_allcount);
        header('X-Pagination-Page-Count:' . $page_count);
        header('X-Pagination-Current-Page:' . $this->_p);
        header('X-Pagination-Per-Page:' . $this->_row);
    }

}
