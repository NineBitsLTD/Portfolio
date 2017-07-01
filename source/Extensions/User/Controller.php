<?php

namespace User;
/**
 * Информация пользователя
 * 
 * @uses \Sys\Config\Mailer
 * 
 */
class Controller extends \Core\Controller { 
    
    public function getIndex() {
        \Registry::$Session->User->Login(
            (array_key_exists('login', $_POST)?$_POST['login']:null), 
            (array_key_exists('password', $_POST)?$_POST['password']:null), 
            (array_key_exists('remember', $_POST)?$_POST['remember']:false));
        if(\Registry::$Session->IsLogged()){
            (new \Controller\Home())->getIndex();
            exit();
        }
        $view = new \View\Base();
        $view->Content = new \View\Login();
        if(isset(\Registry::$Session->Data['msg'])) {
            $view->Content->Error = \Registry::$Session->Data['msg']['error'];
            unset(\Registry::$Session->Data['msg']);
        }
        $view->printContent();
    }
    public function getSignup() {
        /*$this->Reg->Session->Data['msg'] = [];
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
        $this->Reg->View->RenderJson($response);*/
    }
    public function getLogout() {
        if(\Registry::$Session->IsLogged()) \Registry::$Session->User->Logout();
        (new \Controller\Home())->getIndex();
    }
}
