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
     * Имя или емаил или телефон пользователя
     * 
     * @var string
     */
    public $Name="";
    public $Data = [];
    public $ErrorList = [
        'UserNotFound'=>'With this password, this user not found.',
    ];

    /**
     * Инициализация пользователя
     * 
     * @param \Sys\Registry $registry Конфигурация проекта
     */
    public function __construct() {
        $this->Login();
    }
    public function __destruct() {}

    /**
     * Инициализация пользователя
     * 
     * @param \Sys\DataBase\ActiveRecord $userRecord Запись о пользователе из базы данных
     */
    public function Login($name="", $password="", $remember=false){  
        $userModel = new \User\Model\User();
        if($name=="" || $password==""){
            //print_r(\Registry::$Session->Data);
            if( array_key_exists('user', \Registry::$Session->Data) && 
                array_key_exists('id', \Registry::$Session->Data['user']))
                $userRecord = $userModel->GetById(\Registry::$Session->Data['user']['id'])->Get()->GetResult();
            else if(array_key_exists('KEY', $_COOKIE)){
                $userRecord = $userModel->Where("`key`='{$_COOKIE['KEY']}'")->Get()->GetResult();
                $remember=true;
            } else {
                $userRecord = null;
            }
        } else {            
            $this->Name = $name;
            $this->Password = html_entity_decode($password);
            //print_r([$this->Password, md5($this->Password)]);
            $userRecord = $userModel->Where("`email` = '{$this->Name}' AND `pwd_hash`='".md5($this->Password)."'")->Get();
            //var_dump($userRecord);
            $userRecord = $userRecord->GetResult();
            /*if($userRecord->Count==1 && boolval($remember)) {    
                setcookie('KEY', $userRecord->Row['auth_key'], time()+60*60*24*30, '/');
            }*/
        }
        if( isset($userRecord) && 
            $userRecord->Count==1 && 
            array_key_exists('id', $userRecord->Row)){
            \Registry::$Session->Data['user'] = [
                'id'=>$userRecord->Row['id']
            ];
            $this->Data = $userRecord->Row;
            $this->IsLogged = true;
        } else {
            if($name!="" || $password!=""){
                \Registry::$Session->Data['msg']=[
                    'error'=>$this->ErrorList['UserNotFound']
                ];
            }
            $this->IsLogged = false;
        }
    }
    public function Logout(){
        $this->Password = "";
        $this->Name = "";
        $this->IsLogged = false;
        if(isset(\Registry::$Session)) \Registry::$Session->Destroy();
    }
}

