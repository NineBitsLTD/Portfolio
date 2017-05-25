<?php

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

require_once('source/Core/Autoloader.php');

\Registry::$Autoloader = new \Core\Autoloader([''=>'source/'], "source/Extensions");
$db = new \DataBase\MySql(new \Config\DataBase());
$db->Connect();
$model = (new \Core\Model("component", $db))->Where("component='js'")->Where("id>3")->Get();

print_r([
    $model->Records->Rows,
    $model->GetWhere()
]);


