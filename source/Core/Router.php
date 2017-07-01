<?php
namespace Core;

class Router{
    
    public function __construct() {
        if(array_key_exists('route', $_GET)) $route = $_GET['route']; else $route='Home';        
        foreach (\Registry::$Data->Menu as $pagename => $info) 
            if(strtolower(substr($route, 0, strlen($pagename)))==$pagename) \Registry::$Data->Page = ucfirst($pagename);        
        (new \Core\Action(\Registry::$Data->Page, "post".ucfirst(substr($route, strlen($pagename)-1))))->Execute();
    }
}

