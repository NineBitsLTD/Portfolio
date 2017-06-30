<?php

namespace User;
/**
 * Информация пользователя
 * 
 * @uses \Sys\Config\Mailer
 * 
 */
class Controller extends \Sys\Controller { 
    
    public function methodIndex($data = array()) {
        $this->Reg->Session->User->Login(
            (array_key_exists('login', $this->Reg->Request->Post)?$this->Reg->Request->Post['login']:null), 
            (array_key_exists('password', $this->Reg->Request->Post)?$this->Reg->Request->Post['password']:null), 
            (array_key_exists('remember', $this->Reg->Request->Post)?$this->Reg->Request->Post['remember']:false),
            (array_key_exists('key', $this->Reg->Request->Get)?$this->Reg->Request->Get['key']:null));
        if($this->Reg->Session->IsLogged()){
            if(array_key_exists('key', $this->Reg->Request->Get) && $this->Reg->Request->Get['key']!='') {
                if($this->Reg->Session->User->Data['role_id']==7) $this->Reg->Redirect('guest');
                else $this->Reg->Redirect('profile');
            } else $this->Reg->Redirect($this->Reg->Request->PathDefault);
        }
        if(array_key_exists('msg', $this->Reg->Data)) {
            $data['msg'] = $this->Reg->Data['msg'];
            unset($this->Data['msg']);
        }
        $data['base'] = $this->Reg->Link([],[],false);
        $data['link'] = $this->Reg->Link();
        $data['theme'] = $this->Reg->View->Theme;
        $data['link_password_reset'] = $this->Reg->Link(\Sys\Helper::$Route->Normalize($this->Reg->Request->PathStart)."/password_reset");
        $data['link_signup'] = $this->Reg->Link(\Sys\Helper::$Route->Normalize($this->Reg->Request->PathStart)."/signup");
        $data['link_login'] = $this->Reg->Link(\Sys\Helper::$Route->Normalize($this->Reg->Request->PathStart)."/login");
        $data['ajax']=$this->Reg->IsAjax();
        $data['lng_menu'] = $this->GetLngMenu('languages', true);
        echo $this->Render($data);
    }
    public function methodLogin($data = array()) {
        $this->Action->Method = "Index";
        $this->methodIndex($data);
    }
    public function methodSignup($data = array()) {
        $this->Reg->Session->Data['msg'] = [];
        $response = [
            'success'=>'',
            'error'=>''
        ];
        $modelSalesMsg = new \Model\SalesMsg($this->Reg);
        $lng = $this->Reg->Lng->GetCode();
        if(array_key_exists('email',$this->Reg->Request->Post) &&
            array_key_exists('firstname',$this->Reg->Request->Post)){
            $modelUser = new \User\Model\User($this->Reg);
            $code = $modelUser->UserExists($this->Reg->Request->Post['email']);
            if((int)$code>0){
                if((int)$code==1) $response['error'] .= $modelSalesMsg->GetAll("`name`='EmailExists'")->Row['title'][$lng];
                else  $response['error'] .= $modelSalesMsg->GetAll("`name`='EmailMustNotEmpty'")->Row['title'][$lng];
            } else {
                $user_data = $this->Reg->Request->Post;                
                $user_data['user_id']=(array_key_exists('sponsor',$this->Reg->Request->Post) && (int)$this->Reg->Request->Post['sponsor']>0?(int)$this->Reg->Request->Post['sponsor']:0);
                $user_data['sales_id']=(array_key_exists('sales',$this->Reg->Request->Post) && (int)$this->Reg->Request->Post['sales']>0?(int)$this->Reg->Request->Post['sales']:$this->Reg->Settings[DefaultSales]);
                $user_data['lng_default']=$this->Reg->Lng->GetCode();
                $res = $modelUser->Add($user_data);
                $user_data = array_merge($user_data, $res);
                $user_data['link'] = $this->Reg->Link('main',['key'=>$res['password_reset_token']]);
                $msg = $this->Reg->MessageTrigger('user/signup', $this->Reg->Request->Post['email'], $user_data);
                if($msg!='') {
                    $response['error'] .= $modelSalesMsg->GetAll("`name`='FailedSendEmail'")->Row['title'][$lng]." ".$msg;
                }
            }
        } else {
            if(!array_key_exists('email',$this->Reg->Request->Post)) $response['error'] .= $modelSalesMsg->GetAll("`name`='NotEmail'")->Row['title'][$lng];
            if(!array_key_exists('firstname',$this->Reg->Request->Post)) $response['error'] .= $modelSalesMsg->GetAll("`name`='NotFirstName'")->Row['title'][$lng];
        }
        $data['link_signup'] = $this->Reg->Link(\Sys\Helper::$Route->Normalize($this->Reg->Request->PathStart)."/signup");
        $data['link'] = $this->Reg->Link(\Sys\Helper::$Route->Normalize($this->Reg->Request->PathStart));
        if($response['error']=='') $response['success'] = $modelSalesMsg->GetAll("`name`='Success'")->Row['title'][$lng];
        $this->Reg->View->RenderJson($response);
    }
    public function methodPasswordReset($data = array()) {
        if(array_key_exists('login', $this->Reg->Request->Post) && $this->Reg->Request->Post['login']==''){
            $data['msg']=$this->Reg->Translate('MsgEnterLogin');
        } else if(array_key_exists('login', $this->Reg->Request->Post)){
            $ReCaptchaResponse = $this->Reg->ReCaptcha->VerifyResponse(
                $this->Reg->Request->Server['REMOTE_ADDR'], 
                $this->Reg->Request->Post['g-recaptcha-response']
            );
            if($ReCaptchaResponse->IsValid){
                $modelUser = new \User\Model\User($this->Reg);
                if($modelUser->UserExists($this->Reg->Request->Post['login'])===true){
                    $user = $modelUser->Forgot([
                        'email'=>$this->Reg->Request->Post['login'],
                        'password_reset_token'=> \Sys\Helper::$Security->TokenCreate(16)
                    ]);
                    if(array_key_exists('id', $user)){
                        $user_data = [
                            "id" => $user['id'],
                            "firstname" => $user['firstname'],
                            "lastname" => $user['lastname'],
                            "link" => $this->Reg->Link('main',["key"=>$user['password_reset_token']]),
                        ];
                        $msg = $this->Reg->MessageTrigger('user/password_reset', $this->Reg->Request->Post['login'], $user_data);
                        if($msg!='') {
                            $this->Reg->Session->Data['msg'] = $this->Reg->Translate("MsgAuthFailedSendEmail").": ".$msg;
                        } else {
                            $this->Reg->Session->Data['msg'] = $this->Reg->Translate("MsgAuthForgotSuccess");
                        }
                        $this->Reg->Redirect('main');
                    } else {
                        $data['msg'] = $this->Reg->Translate("MsgEmailNotExists");
                    }
                } else {
                    $data['msg'] = $this->Reg->Translate("MsgEmailNotExists");
                }
            } else {
                $data['msg'] = $this->Reg->Translate("MsgCaptchaError");
            }
        }
        $data['base'] = $this->Reg->Link([],[],false);
        $data['link'] = $this->Reg->Link();
        $data['theme'] = $this->Reg->View->Theme;
        $data['ajax']=$this->Reg->IsAjax();
        $data['lng_menu'] = $this->GetLngMenu('languages', true);
        echo $this->Render($data);
    }
    public function methodLoginFacebook($data = array()) {
        $this->methodIndex($data);
    }
    public function methodLoginGoogle($data = array()) {
        $this->methodIndex($data);
    }
    public function methodLoginOdnoklassniki($data = array()) {
        $this->methodIndex($data);
    }
    public function methodLoginVk($data = array()) {
        $this->methodIndex($data);
    }
    public function methodLogout($data = array()) {
        if($this->Reg->Session->IsLogged()) $this->Reg->Session->User->Logout();
        $this->Reg->Redirect('main');
    }
}
