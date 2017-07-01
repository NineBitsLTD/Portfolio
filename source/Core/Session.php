<?php

namespace Core;
/**
 * Сессия пользователя
 * 
 */
class Session {
    /**
     * Пользователь
     * 
     * @var \Code\User
     */
    public $User = null;
    /**
     * Данные переданные через сессию
     * 
     * @var array
     */
    public $Data = [];
        
    /**
     * Старт сесии
     * 
     * @param type $session_id Номер сессии
     * @param type $key Уникальній ключ сессии
     * @return boolean
     * @throws \Exception
     */
    public function __construct($session_id = '', $key = 'default') {  
        if (!$this->GetId()) {
            ini_set('session.use_only_cookies', 'Off');
            ini_set('session.use_cookies', 'On');
            ini_set('session.use_trans_sid', 'Off');
            ini_set('session.cookie_httponly', 'On'); 
            if ($session_id) {
                session_id($session_id);
            }
            if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $_COOKIE[session_name()])) {
                throw new \Exception('Error: Invalid session ID!'); exit();
            }
            session_set_cookie_params(0, '/');
            session_start();            
        }
        if (!isset($_SESSION[$key]))  $_SESSION[$key] = [];
        //print_r($_SESSION);
        $this->Data =& $_SESSION[$key];
        return true;			
    }
    /**
     * 
     */
    public function Destroy() {
	if ( session_id() ) {
            session_unset();
            setcookie(session_name(), session_id(), time()-60*60*24, '/');
            setcookie('KEY', '', time()-60*60*24, '/');
            session_destroy();
            $this->User = null;
	}
    }
    /**
     * Получить ID сессии
     * 
     * @return string
     */
    public function GetId() {
        return session_id();
    }
    
    public function IsLogged(){
        return ($this->User!=null && $this->User->IsLogged);
    }
}

