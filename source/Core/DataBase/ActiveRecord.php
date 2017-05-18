<?php

namespace Core\DataBase;

/**
 * Данные полученные в результате выполнения запроса к базе данных
 * 
 * 
 */
class ActiveRecord{
    /**
     * Количество результирующих строк
     * 
     * @var int
     */
    public $Count = 0;
    /**
     * Первая строка результата
     * 
     * @var array
     */
    public $Row = [];
    /**
     * Строки с данными результата
     * 
     * @var array
     */
    public $Rows = [];
    /**
     * Ссылка на провайдера базы данных от которой сформирован данный результат 
     * 
     * @var \Core\DataBase\Provider
     */
    public $Provider = null;
    /**
     * Инициализация
     * 
     * @param \Sys\Registry $registry Конфигурация проекта
     * @param \Sys\DataBase\Provider $provider Ссылка на провайдера базы данных от которой сформирован данный результат
     */
    public function __construct($provider) {        
        $this->Provider = $provider;
    }
}

