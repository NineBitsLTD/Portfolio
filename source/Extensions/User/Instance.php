<?php

namespace User;

class Instance {
    /**
     * Пароль пользователя
     * 
     * @var string
     */
    protected $Password="";
    
    /**
     * �?дентификатор пользователя
     * 
     * @var int
     */
    public $Id=0;
    /**
     *
     * @var \User\Status 
     */
    public $Status=null;    
    /**
     * �?мя или емаил или телефон пользователя
     * 
     * @var string
     */
    public $Name="";
    /**
     * Группа пользователей
     *
     * @var \Sys\User\Group 
     */
    public $Group = null;
    /**
     * Группа прав доступа
     * 
     * @var \User\Role 
     */
    public $Role = null;
    /**
     * Адресс пользователя
     *
     * @var string
     */
    public $AddressIP = "";
    /**
     * Пользователь авторизирован
     *
     * @var boolean
     */
    public $IsLogged = false;    
    public $Data = [];
    /**
     *
     * @var array
     */
    public $Rights = [];    
    public $CountNewContacts = 0;
    public $WaitNewContacts = 0;


    /**
     * �?нициализация пользователя
     * 
     * @param \Sys\Registry $registry Конфигурация проекта
     */
    public function __construct() {
        $this->Login();
    }
    public function __destruct() {}

    /**
     * �?нициализация пользователя
     * 
     * @param \Sys\DataBase\ActiveRecord $userRecord Запись о пользователе из базы данных
     */
    public function Login($name="", $password="", $remember=false, $reset_token=null){  
        $userModel = new \User\Model\User($this->Reg);
        if(isset($reset_token) && $reset_token!=null){
            $userRecord = $userModel->GetAll("`password_reset_token` IS NOT NULL AND `password_reset_token` = '".$userModel->Escape($reset_token)."'");
            if(array_key_exists('id', $userRecord->Row) && $userRecord->Row['updated_at']==''){
                $userRecord->Row['password'] = \Sys\Helper::$Security->TokenNumericCreate(8);
                $userModel->Update([
                    'id' => $userRecord->Row['id'],
                    'password' => $userRecord->Row['password'],
                ]);
                $this->Reg->MessageTrigger('user/firstlogin', $userRecord->Row['email'], $userRecord->Row);
                $sponsorRecord = $userModel->GetById($userRecord->Row['user_id']);
                if(array_key_exists('id', $sponsorRecord->Row)) 
                    $this->Reg->MessageTrigger('user/sponsor/firstlogin', $sponsorRecord->Row['email'], $userRecord->Row);
            }
        } else if($name=="" || $password==""){
            if( array_key_exists('user', $this->Reg->Session->Data) && 
                array_key_exists('id', $this->Reg->Session->Data['user']))
                $userRecord = $userModel->GetById($this->Reg->Session->Data['user']['id']);
            else if(array_key_exists('KEY', $this->Reg->Request->Cookie)){
                $userRecord = $userModel->GetAll("`auth_key`='{$this->Reg->Request->Cookie['KEY']}'");
                if(!array_key_exists('updated_at',$userRecord->Row) || (strtotime("+30 days", strtotime($userRecord->Row['updated_at']))-time())<0) {
                    $userRecord = new \Sys\DataBase\ActiveRecord($this->Reg, $this->Reg->DB->Provider);
                } else {
                    $remember=true;                
                }
            } else {
                $userRecord = new \Sys\DataBase\ActiveRecord($this->Reg, $this->Reg->DB->Provider);
            }
        } else {            
            $this->Name = $name;
            $this->Password = \Sys\Helper::$Security->EscapeDecodeHtml($password);
            $userRecord = $userModel->GetByPassword($this->Name, $this->Password);
            if($userRecord->Count==1 && boolval($remember)) {    
                setcookie('KEY', $userRecord->Row['auth_key'], time()+60*60*24*30, '/');
            }
        }
        $groupModel = new \User\Model\Group($this->Reg);
        if($userRecord->Count==1 && array_key_exists('id', $userRecord->Row) && array_key_exists('username', $userRecord->Row)){
            $this->Reg->Session->Data['user'] = [
                'id'=>$userRecord->Row['id'],
                'ip'=>$this->Reg->Request->Server['REMOTE_ADDR'],
            ];
            $this->Data = $userRecord->Row;
            $this->Id = $userRecord->Row['id'];
            $this->Name = $userRecord->Row['username'];
            if(array_key_exists('status', $userRecord->Row)){
                $this->Status = new \User\Status($this->Reg, $userRecord->Row['status']);
            } 
            if(array_key_exists('group_id', $userRecord->Row)) $this->Group = $userRecord->Row['group_id'];
            if(array_key_exists('role_id', $userRecord->Row)){
                $this->Role = new \User\Role($this->Reg, $userRecord->Row['role_id']);
            }
            $this->AddressIP = $this->Reg->Request->Server['REMOTE_ADDR'];
            $modelUserPhones = new \Model\User\Phones($this->Reg);
            $this->Data['phones'] = $modelUserPhones->GetAll("`user_id`='{$this->Id}'")->Rows;
            $modelUserSocial = new \Model\User\Social($this->Reg);
            $this->Data['social'] = $modelUserSocial->GetAll("`user_id`='{$this->Id}'")->Rows;
            $this->Data['ip'] = $this->AddressIP;
            $userModel->Update([
                'id' => $this->Id,
                'ip' => $this->Data['ip']
            ]);
            if($this->IsLeave()){
                $modelLeave = new \Model\User\Leave($this->Reg);
                $leave = $modelLeave->GetAll("`user_id`={$this->Id} AND `deactivated_at` IS NULL", 0,0,[],[],true)->Rows;
                foreach ($leave as $key => $row) $modelLeave->Save(['item'=>['id'=>$row['id'], 'deactivated_at'=>date('Y-m-d H:i:s')]]);
            }
            if($this->Data['role_id']==7) $this->Reg->Request->PathDefault = "guest";
            else $this->Reg->Request->PathDefault = "home";
            $this->IsLogged = true;
            $this->SetNewContacts($this->Reg);
        } else {
            if($name!="" || $password!=""){
                if($name=="" ) $this->Reg->Data['msg']=$this->Reg->Translate('MsgEnterLogin');
                else if($password=="") $this->Reg->Data['msg']=$this->Reg->Translate('MsgEnterPassword');
                else $this->Reg->Data['msg']=$this->Reg->Translate('MsgErrorLoginOrPassword');
            } else if(isset($reset_token) && $reset_token!=null){
                $this->Reg->Data['msg']=$this->Reg->Translate('MsgLinkExpired');
            }
            $this->IsLogged = false;
        }
    }
    public function Logout(){
        unset($this->Reg->Session->Data['user']);
        $this->Id = 0;
        $this->Password = "";
        $this->Name = "";
        $this->Group = null;
        $this->Role = null;
        $this->AddressIP = "";
        $this->IsLogged = false;
        $this->Reg->Session->Destroy();
    }
    /**
     * Печать информации пользователя
     * 
     * @param string $id �?дентификатор пользователя
     * @param boolean $edit Разрешена ли правка пользователя
     * @param boolean $info Отображать ли дополнительную информацию
     * @return string
     */
    public function PrintInfo($id, $edit = false, $info = false, $statistic = false){
        $modelUser = new \User\Model\User($this->Reg);
        $data = [            
            'edit' => $edit,            
            'info' => $info,
            'statistic' => $statistic,
            'base'=> $this->Reg->Link("",[],false),
            'link'=> $this->Reg->Link(),
            'theme' => $this->Reg->View->Theme,
            'lng' => $this->Reg->Lng->GetCode(),
        ];
        $data['item'] = $modelUser->GetById($id)->Row;
        $modelUserPhones = new \Model\User\Phones($this->Reg);
        if(array_key_exists('id', $data['item'])) $data['item']['phones'] = $modelUserPhones->GetAll("`user_id`='{$data['item']['id']}'")->Rows;
        else $data['item']['phones'] =[];
        $modelUserSocial = new \Model\User\Social($this->Reg);
        if(array_key_exists('id', $data['item'])) {
            $data['item']['social'] = [];
            foreach ($modelUserSocial->GetAll("`user_id`='{$data['item']['id']}'")->Rows as $key => $value) {
                $data['item']['social'][$value['social_id']] = $value;
            }
        } else $data['item']['social'] =[];
        if(array_key_exists('id', $data['item'])) {
            $data['item']['childrens'] = $modelUser->GetTotal("`user_id`=".(int)$data['item']['id']);
        } else $data['item']['childrens'] = [];
        $modelSocials = new \Model\Social($this->Reg);
        $data['socials'] = [];
        foreach ($modelSocials->GetAll("`status`=1")->Rows as $key => $value) {
            $data['socials'][$value['id']] = $value;
        }
        $data['FormSocial'] = $this->Reg->View->Render('msg_popup', $data+[
            'title' => $this->Reg->Translate('PopUpSocial'),
            'maxWidth' => '850px',
            'message' => $this->Reg->View->Render('profile/social', $data),
        ]);
        return $this->Reg->View->Render('profile_item', $data);
    }
    /**
     * User is payment
     * 
     * @return boolean
     */
    public function IsPayment(){
        return (strtotime($this->Data['expirate_at'])>time());
    }
    /**
     * User is leave
     * 
     * @return boolean
     */
    public function IsLeave(){
        return ((strtotime($this->Data['updated_at'])+(int)$this->Reg->Settings['UserLeaveFindInterval']*3600)<time());
    }
    
    public function GetLeftTimeNewContacts(){
        return (strtotime($this->Data['take_contacts_at'])+($this->WaitNewContacts*60)-time());
    }
    
    /**
     * 
     * @param \Sys\Registry $reg
     */
    public function SetNewContacts($reg){
        $this->CountNewContacts = 0;
        $this->WaitNewContacts = (int)$reg->Settings['NewContactsWaitMin'];
        if($this->IsLogged){
            $modelStatuses = new \Model\Statuses($reg);
            if((int)$this->Data['new_contacts_wait']<$this->WaitNewContacts || $this->WaitNewContacts==0) $this->WaitNewContacts = (int)$this->Data['new_contacts_wait'];
            if((int)$this->Role->Data['new_contacts_wait']<$this->WaitNewContacts || $this->WaitNewContacts==0) $this->WaitNewContacts = (int)$this->Role->Data['new_contacts_wait'];
            foreach ($modelStatuses->GetList("`new_contacts_wait`>0 AND POW(2,`id`-1) & {$this->Data['status']}","new_contacts_wait","id") as $key => $value) 
                if((int)$value<$this->WaitNewContacts || $this->WaitNewContacts==0) $this->WaitNewContacts = (int)$value;
            if($this->GetLeftTimeNewContacts()<=0){
                if((int)$this->Data['count_new_contacts']>$this->CountNewContacts) $this->CountNewContacts = (int)$this->Data['count_new_contacts'];
                if((int)$this->Role->Data['count_new_contacts']>$this->CountNewContacts) $this->CountNewContacts = (int)$this->Role->Data['count_new_contacts'];
                foreach ($modelStatuses->GetList("`count_new_contacts`>0 AND POW(2,`id`-1) & {$this->Data['status']}","count_new_contacts","id") as $key => $value) 
                    if((int)$value>$this->CountNewContacts) $this->CountNewContacts = (int)$value;
            }
        }
    }
}

