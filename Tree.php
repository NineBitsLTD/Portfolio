<?php

$arr = [
    1=>null,
    2=>null,
    3=>null,
    4=>5,
    5=>1,
    6=>'1',
    7=>9,
    'b8'=>3,
    9=>7,
    10=>3
];

foreach ($arr as $key => $value) {
    if(!is_array($arr[$key])) {
        $arr[$key]=[
            'parent'=>$arr[$key],
            'items'=>[],
        ];
    }
    if(is_string($arr[$key]['parent']) || is_numeric($arr[$key]['parent'])){
        if(!is_array($arr[$arr[$key]['parent']])) {
            $arr[$arr[$key]['parent']]=[
                'parent'=>$arr[$arr[$key]['parent']],
                'items'=>[],
            ];
        }    
        $arr[$arr[$key]['parent']]['items'][$key] = & $arr[$key];
    }   
    print_r($arr[$key]);
}

print_r($arr);