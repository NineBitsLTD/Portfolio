<?php

namespace Helper;

class File{
    
    public static function GetConditionalContents($url, $params="", $cookie=null){
        // http://php.net/manual/ru/function.curl-setopt.php
        $ch = curl_init();
        if(strtolower((substr($url,0,5))=='https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // FALSE для остановки cURL от проверки сертификата узла сети.
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 0 чтобы не проверять имена в сертификатах и также его совпадения с указанным хостом.
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // TRUE для следования любому заголовку "Location: ", отправленному сервером в своем ответе     
        if($params=="") curl_setopt($ch, CURLOPT_POST, 0);
        else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        if(isset($cookie)){
            curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].$cookie.'.cookie');
        }
        //AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1
        //Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
        curl_setopt($ch, CURLOPT_HEADER, 0); // TRUE для включения заголовков в вывод
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // TRUE для возврата результата передачи в качестве строки из curl_exec() вместо прямого вывода в браузер.
        curl_setopt($ch, CURLOPT_TIMEOUT, 100); // Максимально позволенное количество секунд для выполнения cURL-функций.
        $szContents = curl_exec($ch);
        // http://php.net/manual/ru/function.curl-getinfo.php
        $aInfo = curl_getinfo($ch);        
        curl_close($ch);
        if($aInfo['http_code'] === 200)
        {            
            //var_dump($szContents); exit();
            return $szContents;
        }
        return false;
    }
}

