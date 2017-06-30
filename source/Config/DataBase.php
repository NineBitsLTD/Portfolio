<?php
namespace Config;
/** 
 * Формирует HTML контент
 * 
 * Класс содержит системные функции для формирования HTML контента. 
 */
class DataBase
{
    /**
     * Адресс сервера базы данных
     * 
     * @var string
     */
    public $Host = "127.0.0.1";
    /**
     * Номер порта базы данных
     * @var int
     */
    public $Port = '3306';
    /**
     * Префикс таблиц базы данных
     * 
     * @var string
     */
    public $Prefix = "";
    /**
     * �?мя базы данных
     * 
     * @var string
     */
    public $DBName = "test";
    /**
     * �?мя пользователя базы данных
     * 
     * @var string
     */
    public $User = "root";
    /**
     * Пароль пользователя базы данных
     * 
     * @var string
     */
    public $Password = "";
    /**
     * Кодировка базы данных
     * 
     * @var string
     */
    public $Charset = "utf8";
}
