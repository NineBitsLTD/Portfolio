<?php
namespace Core;

abstract class User{
    /**
     * Пользователь авторизирован
     *
     * @var boolean
     */
    public $IsLogged = false; 
    /**
     *
     * @var array
     */
    public $Data = [];


    public function Login(){
        
    }
    public function Logout(){
        \Registry::$Session->Destroy();
    }
}

