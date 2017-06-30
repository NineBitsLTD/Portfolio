<?php

namespace Core;
/**
 * Действие запускающее контроллер (раздел сайта)
 * 
 * Данный компонент требуется для запуска метода контроллера заданного через адресную строку браузера.
 * 
 */
class Action
{    
    public $Class;
    public $Method;
    
    public function __construct($class, $method) {
        $this->Class = $class;
        $this->Method = $method;
    }

    /**
     * Запуск метода контроллера с передачей параметров
     * 
     * @return $mixed Возвращает результат выполнения метода
     */
    public function Execute() {  
        $class = "\\Controller\\".$this->Class;
        if (!class_exists($class)) { 
            throw new \Exception(__FILE__.". Class not found: {$class}. ".__METHOD__.": ".__LINE__);
        } else {
            $controller = new $class();
        }
        $reflection = new \ReflectionClass($class);
        if(!$reflection->hasMethod($this->Method)) $this->Method = 'getIndex';
        if($reflection->hasMethod($this->Method) &&  $reflection->getMethod($this->Method)->getNumberOfRequiredParameters() == 0 ) {
            return call_user_func_array([$controller, $this->Method],[]);
        } else {
            throw new \Exception(__FILE__.": Method not found or wrong count params. ".__METHOD__.": ".__LINE__);
        }
    }
}
