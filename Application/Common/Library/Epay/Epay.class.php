<?php

namespace Common\Library\Epay;

use Common\Library\Epay\Message;
use Common\Library\Epay\RSA;

class Epay {

    public $antistate = 0;
    public $host = 'http://218.4.234.150:88/main/loan/'; //测试接口地址
    //  private $host = 'https://register.moneymoremore.com/loan/';  //正式接口地址
    public $PlatformMoneymoremore = "p1752"; //平台乾多多标识
    public $msg = '';
    private $status = '200';
    private $postData = "";
    public $data = array();
    private $_rsa = "";
    private $_message = '';
    private $resultData = array();
    private $funIng = ''; //调用的函数名称

    public function __construct($options = array()) {
        $this->_rsa = new Rsa();
        $this->_message = new Message();
    }

    /**
     * 注册开户
     * @param type $parame 需要的数组
     * 参数依次为：
     * phone 手机号码
     * name 真实姓名
     * credentialsnum 身份证号码/企业营业执照
     * username 平台账号
     */
    public function registerBind($parame) {
        $url = $this->host . "toloanregisterbind.action";
        $data = array(
            'RegisterType' => 1,
            'AccountType' => '',
            'Mobile' => $parame['tel'],
            'RealName' => $parame['realname'],
            'IdentificationNo' => $parame['identificationno'],
            'LoanPlatformAccount' => $parame['username'],
            'PlatformMoneymoremore' => $this->PlatformMoneymoremore,
            'ReturnURL' => "http://" . $_SERVER["HTTP_HOST"] . __ROOT__ . "/loan/registerbind/registerbindreturn.php",
            'NotifyURL' => 'http://www.126.com',
        );
        $this->funIng = __FUNCTION__;
        $this->sign($data);
        return $this->httpPost($url);
    }

    /**
     * 签名
     * @return type
     */
    private function sign($data) {
        $signInfo = "";
        foreach ($data as $k => $v) {
            $signInfo .= str_replace("null", "", $v);
            switch ($k) {
                case "LoanJsonList":
                    $v = urlencode($v);
                    break;
                case "CardNo":
                    $v && $v = urlencode($this->_rsa->encrypt($v));
                    break;
                case "IdentityJsonList":
                    $v && $v = urlencode($v);
                    break;
            }
            $this->data[$k] = $v;
            $this->postData .= $this->postData ? "&" . $k . "=" . $v : $k . "=" . $v;
        }
        $this->data['SignInfo'] = urlencode($this->_rsa->sign($signInfo));
        $this->postData .= "&SignInfo=" . $this->data['SignInfo'];
    }

    /**
     * 验证签名 
     * @param type $data
     */
    public function checkSign($data) {
        $SignInfo = $data['SignInfo'];
        unset($data['SignInfo']);
        $this->sign($data);
        return $this->data['SignInfo'] === $SignInfo ? true : false;
    }

    /** 11	姓名匹配接口
     * @param $parame
     * @return mixed
     */
    public function IdentityMatching($parame) {
        $url = "http://218.4.234.150:88/main/authentication/identityMatching.action";
        $IdentityJsonList = [
            ['realName' =>  $parame['realname'] ,'identificationNo' => $parame['identificationno']]
        ];
        $data = array(
            //平台乾多多标识
            'PlatformMoneymoremore' => $this->PlatformMoneymoremore, 
             //姓名匹配列表
            'IdentityJsonList' => json_encode( $IdentityJsonList , JSON_UNESCAPED_UNICODE ),
            //随机时间戳
//            'RandomTimeStamp' => rand(10,99) . date('YmdHis').rand(100,999), 
            //后台通知网址
            'NotifyURL' => 'http://www.126.com'//"http://" . $_SERVER["HTTP_HOST"] . __ROOT__ . "/api/pay/notify_name",
        );
        $this->sign( $data );
        return $this->httpPost($url);
    }

    /**
     * http模拟from 请求
     * @param type $url
     * @param type $data
     */
    private function httpPost($url) {
        $header[] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch);
        print_r($this->resultInit($result));exit;
        curl_close($ch);
        if ($httpCode['http_code'] == 200) {                        
            return $this->resultInit($result);
        } else if ($httpCode['http_code'] == 0) {
            $this->msg = "请求数据错误！";
            return $this->resultData = array("resultcode" => 0);
        } else {
            $this->msg = "远程服务器错误！";
            return $this->resultData = array("resultcode" => 0);
        }
    }

    /**
     * 返回数据初始化
     * @param type $result
     */
    private function resultInit($result) {
        $this->resultData = json_decode($result, true);
        if (!empty($this->resultData)) {
            $this->resultData = array_key_to_case($this->resultData);
            $this->msg = $this->_message->getVal($this->funIng, $this->resultData['resultcode']);
        }
        return $this->resultData;
    }

    private function request($url, $data, $method = 'POST') {
        $string = '';
        foreach ($data as $k => $v) {
            $string .= "$k=" . urlencode($v) . '&';
        }
        $post_string = substr($string, 0, -1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->host . $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * 清除对应平台信息(测试平台生效)
     */
    public function delinfo() {
        $this->data['p'] = '1752';
        $this->data['s'] = $this->_rsa->sign('1752'); // urlencode($this->_rsa->sign(1752));
        return $this->data;
    }

    public function getAntiState() {
        return $this->antistate;
    }

    public function getRandomNum($length) {
        $output = '';
        for ($a = 0; $a < $length; $a++) {
            $output .= chr(mt_rand(48, 57)); //生成php随机数
        }
        return $output;
    }

    public function getTimeStamp() {
        $output = gmdate('YmdHis', time() + 3600 * 8) . floor(microtime() * 1000);
        return $output;
    }

    public function getRandomTimeStamp() {
        $output = $this->getRandomNum(2) . $this->getTimeStamp();
        return $output;
    }

}
