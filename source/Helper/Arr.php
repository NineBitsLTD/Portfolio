<?php

namespace Helper;

class Arr{
    /**
     * Поиск по значению указанного поля
     * 
     * @param type $arr
     * @param type $name
     * @param type $field_name
     * @return type
     */
    public static function FindByField($arr, $name, $field_name){
        if(is_array($arr)) foreach ($arr as $key => $value) if(is_array($value) && array_key_exists($field_name, $value) && $value[$field_name]==$name) return $value[$field_name];
        return null;
    }
}
