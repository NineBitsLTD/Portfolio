<?php
/**
* Режим отладки
* 
* Если равен 1, при переводе текста выполняется проверка 
* отсутствующих переводов и их запись в базу
*/
define("DEBUG_MODE", 1);
/**
 * Вывод текста ошибок и исключений при выполнении кода когда DEBUG_MODE = 1
 */
if(DEBUG_MODE){
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
} else {
    error_reporting();
    ini_set('display_errors', 0);
}
if(!isset($root)) $root = "";


require_once('source/Core/Autoloader.php');

\Registry::$Autoloader = new \Core\Autoloader([''=>'source/'], "source/Extensions");
\Registry::$DB = new \DataBase\MySql(new \Config\DataBase());
\Registry::$DB->Connect();
\Registry::$Session = new \Core\Session();
\Registry::$Session->User = new \User\Instance();
\Registry::$Data = new \Registry\Data();
\Registry::$Data->Components = (new \Model\Component())->Get("component")->GetResult()->Rows;
\Registry::$Data->Menu = (new \Model\Menu())->Get("key")->GetResult()->Rows;
\Registry::$Data->Brand = "NineBits LTD";
\Registry::$Data->BaseLink = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";

\Registry::Dispatch();