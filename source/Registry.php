<?php

namespace {
    class Registry{
        /**
         * Autoload classes
         * 
         * @var \Core\Autoloader
         */
        public static $Autoloader;
        /**
         * База данных пользователей
         * 
         * @var \Core\DataBase\Provider
         */
        public static $DB;
        /**
         * @var \Registry\Data
         */
        public static $Data=[];
        /**
         *
         * @var \Core\View
         */
        public static $View;
        /**
         * @var \Core\Session
         */
        public static $Session;
        /**
         * 
         */
        public static function Dispatch(){
            new \Core\Router();
        }
        public static function Trans($key, $to='ru', $from='en'){
            if(class_exists("\Google\Translator")) return \Google\Translator::Get($from, $to, $key);
            else return $key;
        }
    }
}

namespace Registry {
    class Data{
        public $BaseLink='';
        public $Brand='';
        public $Components=[];
        public $Menu=[];
        public $Page='NotFound';
    }
}