<?php
namespace DataBase;
/**
 * Связи между таблицами
 */
class TableRelationshipList{
    /**
     * Один к одному
     * 
     * Выбор только тех записей которые найдены соответстенно требуемого условия
     * и в таблице источника и в таблице назначения
     */
    const ONE_TO_ONE = 0;
    /**
     * Один ко многим
     * 
     * Выбор только тех записей которые найдены соответстенно требуемого условия
     * в таблице источника и всех из таблицы назначения
     */
    const ONE_TO_MANY = 1;
    /**
     * Многте к одному
     * 
     * Выбор только тех записей которые найдены соответстенно требуемого условия
     * в таблице назначения и всех из таблицы источника
     */
    const MANY_TO_ONE = 2;
    /**
     * Многте ко многим
     * 
     * Выбор всех записей и из таблицы источника и из таблицы назначения
     */
    const MANY_TO_MANY = 3;
    
    /**
     * Перечень связей между таблицами
     * 
     * @var array
     */
    protected $Items = [];
    
    /**
     * Инициализация
     * 
     * @param mixed $relationships
     */
    public function __construct($relationships = []) {
        $this->Add($relationships);
    }
    /**
     * Добавление связей
     * 
     * @param mixed $relationship
     */
    public function Add($relationships = null){
        if($relationships instanceof \DataBase\TableRelationship) 
            $this->Items[$relationships->DestinationIndex] = $relationship;
        else if(is_array($relationships)){
            foreach($relationships as $relationship) {
                if($relationship instanceof \DataBase\TableRelationship && isset($relationship->DestinationIndex))
                    $this->Items[$relationship->DestinationIndex] = $relationship;
            }
        }
    }
    /**
     * Получить связь по имени индексного поля источника
     * 
     * @param string $name
     * @return \DataBase\TableRelationship
     */
    public function Get($name){
        return $this->Items[$name]; 
    }
    /**
     * Получить перечень связей
     * 
     * @return array
     */
    public function GetItems(){
        return $this->Items;
    }
    /**
     * Получить количество связей
     * 
     * @return int
     */
    public function Count(){
        return count($this->Items);
    }
}

