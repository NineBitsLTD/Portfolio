<?php
namespace Controller;

class Payment extends \Core\Controller {
    public function getIndex() {
        $response = $this->Create(0.05); // USD
        $view = new \View\Base();        
        $view->Content = print_r($response);
        $view->printContent();
    }
    public function getSuccess(){
        if(array_key_exists('TOKEN', $_GET) && !empty($_GET['TOKEN'])) $data['item']['info'] = $this->Confirm($_GET['TOKEN']);
        $view = new \View\Base();        
        $view->Content = 'Payment success';
        $view->printContent();
    }
    public function getError(){
        $view = new \View\Base();
        $view->Content = 'Payment error';
        $view->printContent();
    }
    protected function Create($value){        
        $paypal = new \PayPal\Payment(
            \PayPal\Payment::$PAYPAL_USER, 
            \PayPal\Payment::$PAYPAL_PWD, 
            \PayPal\Payment::$PAYPAL_SIGNATURE, 
            false, 
            \PayPal\Payment::$PAYPAL_RETURN_URL, 
            \PayPal\Payment::$PAYPAL_CANCEL_URL,
            \PayPal\Payment::$PAYPAL_VERSION
        );
        $request = $paypal->CreateInvoice(0.05, 'rus', 'USD');
        return $paypal->ExpressPayment($request['response']);
    }
    protected function Confirm($token){
        $paypal = new \PayPal\Payment(
            \PayPal\Payment::$PAYPAL_USER, 
            \PayPal\Payment::$PAYPAL_PWD, 
            \PayPal\Payment::$PAYPAL_SIGNATURE, 
            false, 
            \PayPal\Payment::$PAYPAL_RETURN_URL, 
            \PayPal\Payment::$PAYPAL_CANCEL_URL,
            \PayPal\Payment::$PAYPAL_VERSION
        );
        return $paypal->FinalizeInvoice($token);
    }
}

