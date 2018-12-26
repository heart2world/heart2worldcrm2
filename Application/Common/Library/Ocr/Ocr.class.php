<?php
namespace Common\Library\Ocr;

class Ocr {

    const APP_ID =10450794;
    const API_KEY = 'dkARxipYzFLC3GY4PyHzBdKF';
    const SECRET_KEY = 'Y8Eu7tRl1jxFI6ZaPYkZoRuiMZplUjB0';

    public static function img($img = 'http://www.qdxxg.com/inc/img.aspx?value=cyUHvp4ijHGpdTego4RLQw%3d%3d'){

        import("Common.Library.Orc.AipOcr");
        $words = 0;
        $aipOcr = new \AipOcr(self::APP_ID, self::API_KEY, self::SECRET_KEY);
        $result = $aipOcr->webImage(file_get_contents($img));
        if(isset($result['words_result'])){
            $number = end($result['words_result']);
            if(isset($number['words'])){
                $words =  $number['words'];
            }
        }

        return $words;

    }

}