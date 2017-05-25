<?php

namespace DataBase;

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
}

