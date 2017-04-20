<?php
/********************************************************************
 * Пример четное нечетное
********************************************************************/
$arr = [1, 2, 7, 4, 9, 5, 6, 10];
$array = array();
sort($arr);
$value = current($arr);
while($value){
    if ($value%2 != 0){
        $el = [
            'odd'=>$value,
            'even'=>null,
        ];
        $value = next($arr);
        if($value%2 == 0 && $value >= $el['odd']){
            $el['even'] = $value;
            $value = next($arr);
        }
    } else {
        $el = [
            'odd'=>null,
            'even'=>$value,
        ];
        $value = next($arr);
        if($value%2 != 0 && $value <= $el['even']){
            $el['odd'] = $value;
            $value = next($arr);
        }
    }	
    $el['sum'] = $el['odd'] + $el['even'];
    $array[] = $el;
};

print_r($array);