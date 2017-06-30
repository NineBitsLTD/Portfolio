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
        public static $Data;
        /**
         *
         * @var \Core\View
         */
        public static $View;
        /**
         * 
         */
        public static function Dispatch(){
            new \Core\Router();
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