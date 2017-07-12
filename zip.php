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

$filename="source.zip";
$zip = zip_open($filename);
while ($zip_entry = zip_read($zip)) {
    $name = zip_entry_name($zip_entry);
    $size = zip_entry_filesize($zip_entry);
    if($opened = zip_entry_open($zip, $zip_entry)){
        $content = zip_entry_read($zip_entry, $size);
        zip_entry_close($zip_entry);
    }
    var_dump([$name,$size,$opened]);
}
zip_close($zip);
echo 'Ok';
