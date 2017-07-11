<?php
namespace Core;

class Router{
    
    public function __construct() {
        if(array_key_exists('route', $_GET)) $route = $_GET['route']; else $route='Home';  
        \Registry::$Data->Page = 'Home';
        foreach (\Registry::$Data->Menu as $pagename => $info)
            if(strtolower(substr($route, 0, strlen($pagename)))==$pagename) \Registry::$Data->Page = ucfirst($pagename);
        //print_r([\Registry::$Data->Page, $route, mb_substr($route, mb_strlen(\Registry::$Data->Page)+1)]);
        (new \Core\Action(\Registry::$Data->Page, "post".ucfirst(mb_substr($route,  mb_strlen(\Registry::$Data->Page)+1))))->Execute();
    }
}

