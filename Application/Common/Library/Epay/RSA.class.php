<?php

namespace Common\Library\EPay;

/**
 * RSA算法类
 * 签名及密文编码：base64字符串/十六进制字符串/二进制字符串流
 * 填充方式: PKCS1Padding（加解密）/NOPadding（解密）
 *
 * Notice:Only accepts a single block. Block size is equal to the RSA key size!
 * 如密钥长度为1024 bit，则加密时数据需小于128字节，加上PKCS1Padding本身的11字节信息，所以明文需小于117字节
 *
 * @author: linvo
 * @version: 1.0.0
 * @date: 2013/1/23
 */
class RSA {

    private $pubKey = null;
    public $priKey = null;
    private $noresource_pubKey = null;
    private $noresource_priKey = null;

    /**
     * 自定义错误处理
     */
    private function _error($msg) {
        die('RSA Error:' . $msg); //TODO
    }

    /**
     * 构造函数
     *
     * @param string 公钥（验签和加密时传入）
     * @param string 私钥（签名和解密时传入）
     */
    public function __construct() {
        //关闭防抵赖
        //私钥由双乾提供
        $private_key = "MIICXQIBAAKBgQCt5Kg4/ggwObLZNkH9N1Z8qK078HyQzUnjjIQuClGMcoArAXhWlPTtHrenc9xe/+gtEQgHmANuKo3ny1ASVj1NkfKmr69QINJnVxnxCPsH+jTL6FqBLbb/ygG/3ZWnkKf51NmK5/B5iVQX2+pGrE4J4rFNqUmvawEQycmfkkWSQQIDAQABAoGAd5LZHj+IT+kNE5HctIs93IB8hs/qSAPyABeauLH9u27stSXQovDQrtDFhs8DxQuBkqO4eshL65A1fiNvDvzgL3mjj2PTqeNiWA1zwIiGGpZPXoaPyt57JDBcfNuew4+28eyqDXcqzzk7wi46fruyJWrjEkbVgLMS/BvUAWVHkLECQQDXxzK9/M/rmJSoAloLAGX36R5828SC9tz5sHUgFqqw4Z9Kpx0EydStt64jCmzo/NXAKAmd2gXpCI82fhqQMJnlAkEAzk68R54HfMi8Mf5FZh0buI+C6twf/gA6MsEXnQthETpcZ8LE3O3VvWmeoF6qm0ThsOK63jmRUOoE9ZNrMBQhLQJBANHa39S6nZSqTlmf/+aXOpSDWq1gJ5yfboZAQYk1wkhJBlzabnSLvpY8/9UAfK+TxmceCUxGEF11f6MlvbviKKkCQFNV+LYRPsMILwUeyhfCgFUgG2kVfBLVMq0X1JsKYq5b3cHIKk93/xPhG0N6mf3YaDZUj9l+dZWywkgUwYKNTw0CQQDEhdmClISS3lz9ruN9rZgJ+0TMjQ4g30f+J3qtn8SpTbol5DGteoguL6518IqLOcNmv7GoZL0qOfhwSNaYd9RN";

        //公钥由双乾提供
        $public_key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCt5Kg4/ggwObLZNkH9N1Z8qK078HyQzUnjjIQuClGMcoArAXhWlPTtHrenc9xe/+gtEQgHmANuKo3ny1ASVj1NkfKmr69QINJnVxnxCPsH+jTL6FqBLbb/ygG/3ZWnkKf51NmK5/B5iVQX2+pGrE4J4rFNqUmvawEQycmfkkWSQQIDAQAB";

        //开启防抵赖
        //此处私钥需要自己生成
        /*
          $private_key = "MIICXQIBAAKBgQC74Qtypg9OVHaT3yuRv5KHwDvIz/lagMaGjJEb/LqSCKIDPoz+".
          "N0c+EkE7NT3ufGl+sjyIRnyboiPJ9XnMM77jA1M7xuyo2jW2gV0oVEKxCXV8QTG5".
          "KHh0CEXlt/EYPK8IneZATzFBjsvRXez8mlhVQcEgNdXTpBLDFFzMTIYqRwIDAQAB".
          "AoGAKXto0amEm6DehYuyzP1lVv/Es3Pn9GmWa8LBj1JfxzqMuvamnsKJwlS5fl8l".
          "C3EAwe6MmvIlNR+5ky/V8pZCQPAM4dSxwhC4CQ65HHJ9DW81ncnHk8VtBfbN4Dso".
          "rj2V4QymNZtMnDXmtuJ6/RoEpnINxMivmmlOP7u+ICf0CPECQQDwULlAkQt9NvXW".
          "aIxCiAzwgzmA+S1QyfFDUJZAtvgaYn4KM8jeRMr4DUnD52NmH6aC5R0UVRfjEUDt".
          "VNAyJAV7AkEAyCQydZflE8jnHcWrnBZtY8yhYI4ThUtymiWgQ3HdgIX1JJIM+w6n".
          "BRbVSGTt2+iQ554TgWKsYaKyjqwPYr1GpQJBAIzya5To/VoVcB6u9wTWkvBFpuZ0".
          "PxXRO4YFr/qI1f9zoQUO1lM2+ex+rrMN9YiiK6E+C84vEnGFXxVT10BxTB0CQQCL".
          "kvK9n1haG9lRofCzwdA3sRU5yNtEMgGSDntdjaLzZng3MMNssiM4IVxMSFa47c9g".
          "N5VSvgWJcXUkmkmAio8hAkBILEJ3lQVXY3IFLgX66UQPMFaeskU7MdyHjMQtVb4z".
          "YoXUpl9hRd81wzVeDCGtHR9nMxazEo7T2AcrZlDlLv1M";

          //公钥由双乾提供
          $public_key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/ixmVsk052wqOlaD4UYJUWPAgE".
          "7YQZmQQgVuwtIHLpQBiENH+BnUGf+Wv6eCBcMVdU6tVwkl1gHOgj5sGvmYPkJGN08".
          "+YJNPdfp8LERUh2Lvh1k7/9ACxxOxa05g0+q1+TqtU4I4Fq4mrCrVtWOXDmPy+FwM".
          "3++Xy16UpTV3VdwIDAQAB";
         */


        $pemPriKey = chunk_split($private_key, 64, "\n");
        $pemPriKey = "-----BEGIN RSA PRIVATE KEY-----\n" . $pemPriKey . "-----END RSA PRIVATE KEY-----\n";

        $pemPubKey = chunk_split($public_key, 64, "\n");
        $pemPubKey = "-----BEGIN PUBLIC KEY-----\n" . $pemPubKey . "-----END PUBLIC KEY-----\n";

        //$this->priKey = openssl_get_privatekey($pemPriKey);
        //$this->pubKey = openssl_get_publickey($pemPubKey);

        $this->priKey = $pemPriKey;
        $this->pubKey = $pemPubKey;
    }

    /**
     * 生成签名
     *
     * @param string 签名材料
     * @param string 签名编码（base64/hex/bin）
     * @return 签名值
     */
    public function sign($data, $code = 'base64') {
        $ret = false;
        if (openssl_sign($data, $ret, $this->priKey)) {
            $ret = $this->_encode($ret, $code);
        }
        return $ret;
    }

    /**
     * 验证签名
     *
     * @param string 签名材料
     * @param string 签名值
     * @param string 签名编码（base64/hex/bin）
     * @return bool
     */
    public function verify($data, $sign, $code = 'base64') {
        $ret = false;
        $sign = $this->_decode($sign, $code);

        if ($sign !== false) {
            switch (openssl_verify($data, $sign, $this->pubKey)) {
                case 1: $ret = true;
                    break;
                case 0:
                case -1:
                default: $ret = false;
            }
        }


        return $ret;
    }

    /**
     * 加密
     *
     * @param string 明文
     * @param string 密文编码（base64/hex/bin）
     * @param int 填充方式（貌似php有bug，所以目前仅支持OPENSSL_PKCS1_PADDING）
     * @return string 密文
     */
    public function encrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING) {
        $ret = false;
        if (!$this->_checkPadding($padding, 'en'))
            $this->_error('padding error');
        if (openssl_public_encrypt($data, $result, $this->pubKey, $padding)) {
            $ret = $this->_encode($result, $code);
        }
        return $ret;
    }

    /**
     * 解密
     *
     * @param string 密文
     * @param string 密文编码（base64/hex/bin）
     * @param int 填充方式（OPENSSL_PKCS1_PADDING / OPENSSL_NO_PADDING）
     * @param bool 是否翻转明文（When passing Microsoft CryptoAPI-generated RSA cyphertext, revert the bytes in the block）
     * @return string 明文
     */
    public function decrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false) {
        $ret = false;
        $data = $this->_decode($data, $code);
        if (!$this->_checkPadding($padding, 'de'))
            $this->_error('padding error');
        if ($data !== false) {
            if (openssl_private_decrypt($data, $result, $this->priKey, $padding)) {
                $ret = $rev ? rtrim(strrev($result), "\0") : '' . $result;
            }
        }
        return $ret;
    }

    /**
     * 生成密钥
     */
    public function GenerateKey($dn = NULL, $config = NULL, $passphrase = NULL) {

        if (!$dn) {
            $dn = array(
                "countryName" => "CN",
                "stateOrProvinceName" => "JIANGSU",
                "localityName" => "Suzhou",
                "organizationName" => "95epay",
                "organizationalUnitName" => "Moneymoremore",
                "commonName" => "www.moneymoremore.com",
                "emailAddress" => "csreason@95epay.com"
            );
        }
        /*
          if (!$config)
          {
          $config = array(
          "digest_alg" => "sha1",
          "private_key_bits" => 1024,
          "private_key_type" => OPENSSL_KEYTYPE_RSA,
          "encrypt_key" => false
          );
          }
         */
        $privkey = openssl_pkey_new();
        echo "private key:";
        echo "<br>";
        if ($passphrase != NULL) {
            openssl_pkey_export($privkey, $privatekey, $passphrase);
        } else {
            openssl_pkey_export($privkey, $privatekey);
        }
        echo $privatekey;
        echo "<br><br>";

        /*
          $csr = openssl_csr_new($dn, $privkey);
          $sscert = openssl_csr_sign($csr, null, $privkey, 65535);
          echo "CSR:";
          echo "<br>";
          openssl_csr_export($csr, $csrout);

          echo "Certificate: public key";
          echo "<br>";
          openssl_x509_export($sscert, $publickey);
         */
        $publickey = openssl_pkey_get_details($privkey);
        $publickey = $publickey["key"];

        echo "public key:";
        echo "<br>";
        echo $publickey;

        $this->noresource_pubKey = $publickey;
        $this->noresource_priKey = $privatekey;
    }

    // 私有方法

    /**
     * 检测填充类型
     * 加密只支持PKCS1_PADDING
     * 解密支持PKCS1_PADDING和NO_PADDING
     *
     * @param int 填充模式
     * @param string 加密en/解密de
     * @return bool
     */
    private function _checkPadding($padding, $type) {
        if ($type == 'en') {
            switch ($padding) {
                case OPENSSL_PKCS1_PADDING:
                    $ret = true;
                    break;
                default:
                    $ret = false;
            }
        } else {
            switch ($padding) {
                case OPENSSL_PKCS1_PADDING:
                case OPENSSL_NO_PADDING:
                    $ret = true;
                    break;
                default:
                    $ret = false;
            }
        }
        return $ret;
    }

    private function _encode($data, $code) {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_encode('' . $data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _decode($data, $code) {
        $data = urldecode($data);
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = $this->_hex2bin($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _getPublicKey($file) {
        $key_content = $this->_readFile($file);
        if ($key_content) {
            $this->pubKey = openssl_get_publickey($key_content);
        }
    }

    private function _getPrivateKey($file) {
        $key_content = $this->_readFile($file);
        if ($key_content) {
            $this->priKey = openssl_get_privatekey($key_content);
        }
    }

    private function _readFile($file) {
        $ret = false;
        if (!file_exists($file)) {
            $this->_error("The file {$file} is not exists");
        } else {
            $ret = file_get_contents($file);
        }
        return $ret;
    }

    private function _hex2bin($hex = false) {
        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;
        return $ret;
    }

}
