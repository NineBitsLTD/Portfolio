<?php
namespace PayPal;

class Payment {
    /**
     *  Имя вашего PayPal API аккаунта
     */
    static public $PAYPAL_USER = "phpacademy.test2017.gmail.com";
    /**
     * Пароль вашего PayPal API аккаунта
     * 
     * @var string
     */
    static public $PAYPAL_PWD = "000000000000000";
    /**
     * Номер версии NVP API, к примеру 74
     * 
     * @var integer
     */
    static public $PAYPAL_VERSION = 104;
    /**
     * Электронная подпись PayPal API. Параметр следует использовать только в том случае, если вы используете сертификат для авторизации;
     * 
     * @var string
     */
    static public $PAYPAL_SIGNATURE = "DFjksdfgskjfhdfaslALkfhalsgkjsdkgukg";
    /**
     * Адрес, на который будет перенаправлен пользователь после успешного совершения платежа.
     * 
     * @var string
     */
    static public $PAYPAL_RETURN_URL = "payment/success";
    /**
     * Адрес, на который будет перенаправлен пользователь, если во время платежа произошла ошибка.
     * 
     * @var string
     */
    static public $PAYPAL_CANCEL_URL = "payment/error";
    /**
     * Последние сообщения об ошибках
     * @var array
     */
    protected $_errors = array();
    /**
     * Данные API
     * Обратите внимание на то, что для песочницы нужно использовать соответствующие данные
     * @var array
     */
    protected $_credentials = array('USER' => '', 'PWD' => '', 'SIGNATURE'=>'');
    /**
     * Указываем, куда будет отправляться запрос
     * 
     * Реальные условия - https://api.paypal.com/nvp
     * Песочница - https://api.sandbox.paypal.com/nvp
     * @var string
     */
    protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
    /**
     * Адрес, на который будет перенаправлен пользователь после успешного совершения платежа.
     */
    protected $_successPoint;
    /**
     * Адрес, на который будет перенаправлен пользователь, если во время платежа произошла ошибка.
     */
    protected $_cancelPoint;
    /**
     * Версия API
     * @var string
     */
    protected $_version;
    
    /**
     * Конструктор
     * 
     * @param string $user Имя вашего PayPal API аккаунта 
     * @param string $pwd Пароль вашего PayPal API аккаунта 
     * @param string $signature Электронная подпись PayPal API, по умолчанию пусто
     * @param boolean $demo Режим отправки true - тесторый, false - реальный, по умолчанию true
     * @param string $success_url Адрес, на который будет перенаправлен пользователь после успешного совершения платежа, по умолчанию пусто
     * @param string $cancel_url Адрес, на который будет перенаправлен пользователь, если во время платежа произошла ошибка, по умолчанию пусто
     * @param string $cert_path Путь к файлу SSL сертификата, если равно null загружается cacert.pem из коренной папки , по умолчанию null
     * @param string $version '74.0'
     * 
     */
    public function __construct($user, $pwd, $signature='', $demo=true, $success_url='', $cancel_url='', $version=74) {
        $this->_credentials=array(
            'USER' => $user,
            'PWD' => $pwd,
            'SIGNATURE'=>$signature
        );
        if(!$demo) $this->_endPoint = 'https://api-3t.paypal.com/nvp';
        $this->_successPoint = $success_url;
        $this->_cancelPoint = $cancel_url;
        $this->_version = $version;
    }

    /**
     * Отправка запроса
     *
     * @param string $method Данные о вызываемом методе перевода
     * @param array $params Дополнительные параметры
     * @return array / boolean Response array / boolean false on failure
     */
    protected function request($method, $params = array()) {
        $this -> _errors = array();
        if(empty($method) ) { // Проверяем, указан ли способ платежа
           $this -> _errors = array('Не указан метод перевода средств');
           return false;
        }
        // Параметры нашего запроса
        $requestParams = array(
           'METHOD' => $method,
           'VERSION' => $this->_version
        ) + $this->_credentials + $params;
        // Сформировываем данные для NVP
        $request = http_build_query($requestParams);

        // Настраиваем cURL
        $curlOptions = array (
            \CURLOPT_URL => $this->_endPoint,
            \CURLOPT_VERBOSE => 1,
            \CURLOPT_RETURNTRANSFER => 1,
            \CURLOPT_POST => 1,
            \CURLOPT_POSTFIELDS => $request,
            \CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($request)
            )
        );

        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);
        parse_str($response,$responseArray);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            $this -> _errors = curl_error($ch);
            curl_close($ch);
            return false;
        } else  {
            curl_close($ch);
            $responseArray = array();
            parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }

    /**
     * Создание счета
     * 
     * @param array $value Перечень позиций в платеже. Дробная часть отделяется точкой. ['cost'=>Сумма за единицу,'count'=>Количество,'tytle'=>Описание]
     * @param string $lng Текущий язык
     * @param string $currency Валюта в формате ISO 4217
     * @return array Ответ с результатом создания платежа
     */
    public function CreateInvoice($value, $lng='rus', $currency='USD') {
        $requestParams = array(
            'RETURNURL'=>$this->_successPoint,
            'CANCELURL'=>$this->_cancelPoint,
        );
        $orderParams = array(
            // Итоговая сумма для перевода. Дробная часть отделяется точкой.
            'PAYMENTREQUEST_0_AMT'=>0,
            // Валюта в формате https://ru.wikipedia.org/wiki/ISO_4217
            'PAYMENTREQUEST_0_CURRENCYCODE'=>$currency,                
            'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',              
        );
        $i=0;
        $summ = 0;
        foreach ($value as $key => $item) if(array_key_exists('cost', $item) && array_key_exists('count', $item)){
            // Описание перевода.
            $orderParams["L_PAYMENTREQUEST_0_AMT{$i}"] = (float)$item['cost'];
            $orderParams["L_PAYMENTREQUEST_0_QTY{$i}"]=(int)$item['count'];
            $orderParams["L_PAYMENTREQUEST_0_NAME{$i}"] = (array_key_exists('title', $item) && is_array($item['title']) && array_key_exists($lng, $item['title']))?$item['title'][$lng]:"Item {$i}";
            $summ += (float)$item['cost']*(int)$item['count'];
            $i++;
        }
        $orderParams["PAYMENTREQUEST_0_AMT"] = $summ;
        $response = $this->request('SetExpressCheckout',$requestParams + $orderParams);            
        return ['response'=>$response];
    }
    /**
     * Экспресс-платеж 
     * 
     * @param array $response Ответ с результатом создания платежа
     * @return array $response Ответ с результатом создания платежа, если платеж не удалось создать в противном случае переадресация для оплаты.
     */
    public function ExpressPayment($response){
        if(is_array($response) && $response['ACK'] == 'Success') { 
            // Запрос был успешно принят
            $token = $response['TOKEN'];
            header( 'Location: https://www.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
            exit();
        }
        else return ['response' => $response];
    }
    /**
     * Ответ по завершению платежа
     * 
     * @param string $token Уникальный ID платежа
     * @return array Ответ с результатом и информацией о платеже
     */
    public function FinalizeInvoice($token){
        $result= [];
        if( isset($token) && !empty($token) ) { // Токен присутствует
            // Получаем детали оплаты, включая информацию о покупателе.
            // Эти данные могут пригодиться в будущем для создания, к примеру, базы постоянных покупателей
            $result['details'] = $this->request('GetExpressCheckoutDetails', array('TOKEN' => $token));
            if(array_key_exists('TOKEN', $result['details']) && array_key_exists('PAYERID', $result['details']) && array_key_exists('AMT', $result['details']) && 
                (!array_key_exists('PAYMENTREQUESTINFO_0_SEVERITYCODE', $result['details']) || $result['details']['PAYMENTREQUESTINFO_0_SEVERITYCODE']!='Error')){
                // Завершаем транзакцию если платеж удался
                $requestParams = array(
                   'TOKEN' => $result['details']['TOKEN'],
                   'PAYERID' => $result['details']['PAYERID'],
                   'AMT' => $result['details']['AMT'],
                   'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                );
                $result['response'] = $this->request('DoExpressCheckoutPayment',$requestParams);
            }
            return $result;
        }
    }        
}
        
?>