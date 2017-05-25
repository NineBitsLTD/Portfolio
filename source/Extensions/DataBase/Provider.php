<?php

namespace DataBase;

/**
 * Провайдер базы данных
 * 
 * Абстрактный клас, задает структуру для провайдеров базы данных.
 * 
 */
abstract class Provider
{ 
    /**
     * Адресс сервера базы данных
     * 
     * @var string
     */
    public $Host = "";
    /**
     * Номер порта базы данных
     * @var int
     */
    public $Port = 0;
    /**
     * Префикс таблиц базы данных
     * 
     * @var string
     */
    public $Prefix = "";
    /**
     * Кодировка базы данных
     * 
     * @var string
     */
    public $Charset = "";
    /**
     * Имя базы данных
     * 
     * @var string
     */
    public $DBName = "";
    /**
     * Имя пользователя базы данных
     * 
     * @var string
     */
    protected $User = "";
    /**
     * Пароль пользователя базы данных
     * 
     * @var string
     */
    protected $Password = "";
    
    /**
     * Подключение к базе данных
     * 
     * @param \Config\DataBase $config Конфигурация базы данных
     */
    public function __construct($config) {
        if($config){
            $this->Host = $config->Host;
            $this->DBName = $config->DBName;
            $this->User = $config->User;
            $this->Password = $config->Password;
            $this->Charset = $config->Charset;
            $this->Prefix = $config->Prefix;
            $this->Port = $config->Port;
        }
        $this->Connect();
    }
    
    /**
     * Выполнение запроса к базе данных
     * 
     * @return \DataBase\ActiveRecord
     */
    abstract public function Query($sql, $index = null, $params = []);
    /**
     * Количество затронутых записей
     */
    abstract public function CountAffected();
    /**
     * Экранирование данных
     */
    abstract public function Escape($value);
    /**
     * Получение последнего индекса записи после запроса INSERT
     */
    abstract public function GetLastId();
    /**
     * Подключение к базе данных
     */
    abstract public function Connect();
}

