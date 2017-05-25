<?php
namespace DataBase;
/**
 * Cвязь между таблицами
 */
class TableRelationship{
    /**
     * Имя индексного поля в таблице источника
     * 
     * @var string
     */
    public $DestinationIndex;
    /**
     * Имя таблицы назначения
     * 
     * @var string
     */
    public $SourceName;
    /**
     * Имя индексного поля в таблице назначения
     * 
     * @var string
     */
    public $SourceIndex;
    /**
     *
     * @var array
     */
    public $SourceFieldsAs;
    /**
     * Типы связей между таблицами
     * 
     * @var int
     */
    public $RelationshipType;

    /**
     * Инициализация
     * 
     * @param string $destinationIndex Индекс назначения для сравнения
     * @param string $sourceName Название таблицы источника
     * @param string $sourceIndex Индекс источника для сравнения
     * @param array $sourceFieldsAs Переименовывание столбцов
     * @param int $relationshipType
     */
    public function __construct($destinationIndex, $sourceName, $sourceIndex, $relationshipType = 0, $sourceFieldsAs=[]) {
        $this->DestinationIndex = $destinationIndex;
        $this->SourceName = $sourceName;
        $this->SourceIndex = $sourceIndex;
        $this->RelationshipType = $relationshipType;
        $this->SourceFieldsAs = $sourceFieldsAs;
    }
    
}

