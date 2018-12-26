<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@ipoxiao.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------
class alipay_config {

    static public $alipay_config = [
        //商户合作号
        'partner' => '2088021544644543',
        //接收支付宝帐号
        'seller_id' => '2088021544644543',
        //商户私钥路径
        'private_key_path' => '/key/rsa_private_key.pem',
        //商户公钥路径
        'ali_public_key_path' => '/key/alipay_public_key.pem',
        //支付宝公钥
        'ali_public' => '/key/ali_public_key.pem',
        //签名类型
        'sign_type' => 'RSA',
        //编码方式
        'input_charset' => 'utf-8',
        //ca证书路径地址，用于curl中ssl校验
        'cacert' => '/key/cacert.pem',
        'transport' => 'http',
        //回调地址
        'notify_url' => 'http://www.dianme.net/Home/Pay/alipaylog/',
        //返回地址
        'return_url' => 'http://www.dianme.net/Home/Pay/return_url/',
        'payment_type' => 1
    ];

}
