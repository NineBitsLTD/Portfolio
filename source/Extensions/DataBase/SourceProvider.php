<?php

namespace DataBase;

class SourceProvider implements \Core\SourceProvider{    
    public $ErrorList=[
        1,2,3,4,5,6,7
    ];
    public static function CreateSource($name, $params, $db=null) {
        if(!($db instanceof \DataBase\Provider)) $db = \Registry::$DB->GetProvider();
    }
    public static function DeleteSource($name, $db=null) {
        if(!($db instanceof \DataBase\Provider)) $db = \Registry::$DB->GetProvider();
    }
    /**
     * Существует ли таблица $name в базе
     * 
     * @param string $name Имя таблицы
     * @param \DataBase\Provider $db База данных для поиска
     * @return boolean Возвращает True если таблица найдена в противном случае False
     */
    public static function ExistsSource($name, $db = null) {
        if(!($db instanceof \DataBase\Provider)) $db = \Registry::$DB->GetProvider();
        $result = $db->Query("SELECT * FROM `information_schema`.`TABLES` WHERE `TABLE_NAME` = '{$name}' AND `TABLE_SCHEMA` = '{$this->Provider->ProviderName}'");
        return ($result->Count>0);        
    }
    /**
     * Перечень полей таблицы и их типы
     * 
     * Выполняет запрос для получения списка полей таблицы,
     * результат записывается и читается в дальнейшем из параметра $this->Fields
     * 
     * @param type $force Если False и параметр $this->Fields не пустой - возвращает $this->Fields, в противном случае то выполняется запрос на получение данных из бызы
     * @return array
     */
    public static function FieldsSource($name='', $db = null, $force=false){
        if(!($db instanceof \DataBase\Provider)) $db = \Registry::$DB->GetProvider();
        return $db->Query("SELECT `COLUMN_NAME` as `name`, `DATA_TYPE` as `type`, `COLUMN_DEFAULT` as `default`, `TABLE_NAME` AS `table`
            FROM `information_schema`.`COLUMNS` WHERE `TABLE_NAME` = '{$name}' AND `TABLE_SCHEMA` = '{$db->DBName}'")->Rows;
    }
    
    /**
     * Ссылка на базу данных
     * 
     * @var \DataBase\Provider
     */
    public $Provider;
    
    /**
     * Название таблицы
     * 
     * @var string
     */
    protected $Name;
    /**
     * Текущий фильтр
     * 
     * @var string
     */
    protected $ValueWhere;
    /**
     * Текущий набор сортировок
     * 
     * @var array 
     */
    protected $ValueSort;
    /**
     * Текущее ограничение записей
     * 
     * @var int
     */
    protected $ValueLimit;
    /**
     * Текущее смещение
     * 
     * @var int
     */
    protected $ValueOffset;
    /**
     * Перечень отображаемых полей таблицы
     * @var array
     */
    protected $ValueFieldsAs;
    /**
     * Cвязи с другими таблицами
     *  
     * @var \DataBase\TableRelationshipList
     */
    protected $ValueRelationshipList;
    /**
     * Перечень полей таблицы
     * 
     * @var array 
     */
    protected $Fields;
    /**
     * Перечень полей таблицы
     * 
     * @var array 
     */
    protected $AllFields;
    /**
     * Результат выполнения последнего из запросов на выборку данных
     * 
     * @var \DataBase\ActiveRecord
     */
    protected $Records;
    /**
     * Последний подготовленный запрос
     * 
     * @var string
     */
    protected $Sql;
    /**
     * Последний индекс записи
     * 
     * @var mixed
     */
    protected $Id;
    
    /**
     * Инициализация
     * 
     * @param string $table_name
     * @param \DataBase\Provider $db
     * @param mixed $list Перечень возможных сообщений с ошибками
     * @param string $pattern Шаблон для вывода текста ошибки
     */
    public function __construct($table_name=null, $db = null, $list = null, $pattern = null) {
        if(!isset($list)) $list=[
            0=>"Данные успешно сохранены",
            1=>"Не удалось сохранить данные",
            2=>"Выбранные данные успешно удалены",
            3=>"Данные успешно удалены",
            4=>"Не выбраны данные для удаления",
            5=>"Записи успешно скопированы",
            6=>"Некоторые записи не удалось скопировать",
            7=>"Запись успешно скопирована",
            8=>"Не удалось скопировать выбранные записи"
        ];
        if(!($db instanceof \DataBase\Provider)) $db = & \Registry::$DB;
        $this->Provider = & $db;
        if(isset($table_name) && $table_name!="") $this->Name=$table_name;
        $this->Clear();
    }

    /**
     * Задать имя таблицы
     * 
     * @param string $name
     * @return \Core\SourceProvider
     */
    public function SetName($name){
        $this->Name = $name;
        return $this;
    }
    /**
     * Получить имя таблицы
     * 
     * @return string
     */
    public function GetName(){
        return $this->Provider->Prefix.$this->Name;
    }
    /**
     * Последний результат выполнения запроса
     * 
     * @return \DataBase\ActiveRecord
     */
    public function GetResult(){
        return $this->Records;
    }
    /**
     * Перечень полей таблицы и их типы
     * 
     * Выполняет запрос для получения списка полей таблицы,
     * результат записывается и читается в дальнейшем из параметра $this->Fields
     * 
     * @param type $force Если False и параметр $this->Fields не пустой - возвращает $this->Fields, в противном случае то выполняется запрос на получение данных из бызы
     * @return array
     */
    public function GetFields($force=false){
        if($this->Fields==null || $force) $this->Fields = self::FieldsSource($this->GetName(), $this->Provider, $force);
        return $this->Fields;
    }
    /**
     * Существует ли поле $name в перечне полей таблици
     * 
     * @param type $name
     * @return boolean
     */
    public function ExistsField($name, $searchName='name'){
        $val = \Helper\Arr::FindByField($this->GetFields(), $name, $searchName);
        return (isset($val)===true);
    }
    /**
     * Перечень полей таблицы и их типы с учетом связей таблиц
     * 
     * Выполняет запрос для получения списка полей таблиц,
     * результат записывается и читается в дальнейшем из параметра $this->AllFields
     * 
     * @param type $force Если False и параметр $this->AllFields не пустой - возвращает $this->AllFields, в противном случае то выполняется запрос на получение данных из бызы
     * @return array
     */
    public function GetAllFields($force=false){
        if($this->AllFields==null || $force) {
            $sql = "SELECT `COLUMN_NAME` AS `name`, `DATA_TYPE` AS `type`, `COLUMN_DEFAULT` AS `default`, `TABLE_NAME` AS `table`
            FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '{$this->Provider->DBName}' AND (`TABLE_NAME` = '{$this->GetName()}'";
            foreach ($this->ValueRelationshipList->GetItems() as $key=>$relationship) if($relationship instanceof \DataBase\TableRelationship && $relationship->SourceName!=''){
                $sql .=" OR `TABLE_NAME` = '{$relationship->SourceName}'";
            }
            $sql .=");";
            $this->AllFields = $this->Provider->Query($sql)->Rows;
            foreach ($this->AllFields as $key => $value) {
                if(array_key_exists($value['table'], $this->ValueFieldsAs) && array_key_exists($value['name'], $this->ValueFieldsAs[$value['table']])){
                    $this->AllFields[$key]['name_old'] = $this->AllFields[$key]['name'];
                    $this->AllFields[$key]['name'] = $this->ValueFieldsAs[$value['table']][$value['name']];
                } else { $this->AllFields[$key]['name_old'] = $this->AllFields[$key]['name']; }
            }
        } 
        return $this->AllFields;
    }
    /**
     * Существует ли поле $name в перечне полей таблицы с учетом связей таблиц
     * 
     * @param type $name
     * @return boolean
     */
    public function ExistsAllField($name, $searchName='name'){
        $res = false;
        foreach($this->GetAllFields() as $value) {
            $res = $res || ($value[$searchName]==$name);
            if($res) break;   
        }
        return $res;
    }
    /**
     * Выборка записей из таблицы
     * 
     * Перед началом использования данной функции воспользуйтесь 
     * другими (Clear, Where, Like, Limit, Sort, Group) для конфигурирования запроса
     *  
     * @param string $index Имя колонки из которой значения устанавливать как ключи рядков
     * @return \Core\SourceProvider
     */
    public function Get($index='id', $debug=false){
        $this->Sql = "SELECT{$this->_fieldsAsToSql()} FROM `{$this->Provider->DBName}`.`".$this->GetName()."`".$this->_relationshipToSql().$this->ValueWhere.$this->_sortToSql().$this->_groupToSql().$this->_limitToSql();
        $this->Records = $this->Provider->Query($this->Sql, [], $index);
        if($debug) { 
            print_r([$this->AllFields, $this->ValueFieldsAs, $this->ValueWhere, $this->ValueSort, $this->ValueRelationshipList, $this->ValueLimit, $this->ValueOffset, $this->Sql, $this->Provider, $this->Records ]);
            exit();
        }
        return $this;
    }
    /**
     * Получить запись по id
     * 
     * Упрощенный шаблон для использования $this->Get();
     * 
     * @param string $id Значение колонки $name_id
     * @param string $name_id Имя колонки для поиска $id
     * @return \Core\SourceProvider
     */
    public function GetById($id, $nameId='id', $debug=false){   
        return $this->Where("{$nameId}={$id}")->Get('', $debug);
    }   
    /**
     * Количество записей соответствующих указанному условию
     * 
     * @return int
     */
    public function GetTotal(){
        $records = $this->Provider->Query("SELECT count(*) as 'count' FROM `{$this->GetName()}`".$this->ValueWhere);
        return (array_key_exists('count', $records->Row)?(int)$records->Row['count']:0);
    }    
    /**
     * Сохранение записи в базе данных
     * 
     * @param array $params Массив сохраняемых данных
     * @return \Core\SourceProvider Идентификатор записи
     */
    public function Set(array $params, $nameId='id', $debug=false) {
        if($debug) print_r($params);
        $id = "";
        $where = $this->ValueWhere;
        if(is_array($params) && array_key_exists($nameId, $params) && $this->Where("`{$nameId}`={$this->Escape($params[$nameId])}")->GetTotal()>0){
            $this->ValueWhere = $where;
            $fields = $this->GetFields();
            $sep="";
            $values="";
            $id = $params[$nameId];
            foreach($fields as $field){
                if($field['name']!=$nameId && array_key_exists($field['name'], $params)){
                    if(is_null($params[$field['name']])) $values .= $sep . "`{$field['name']}` = NULL ";
                    else if(!is_array($params[$field['name']])) $values .= $sep ."`{$field['name']}` = '" . $this->Escape((string)$params[$field['name']]) . "' ";
                    else if($this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                        $this->TranslateSave($field['name'], $params[$nameId], $params[$field['name']]);
                        $values .= $sep ."`{$field['name']}` = '" . implode(',',array_keys($params[$field['name']])) . "' ";
                    } else $values .= $sep ."`{$field['name']}` = '' ";
                    $sep = ",";
                } else if($field['name']=='updated_at' && $field['type']=='timestamp'){
                    $values .= $sep ."`updated_at` = CURRENT_TIMESTAMP ";
                    $sep = ",";
                }
            }
            
            $this->Where("`{$nameId}`='{$this->Escape($params[$nameId])}'");
            $this->Sql = "UPDATE `{$this->GetName()}` SET {$values}".$this->ValueWhere;
            if($debug) { 
                print_r([$this->ValueWhere, $this->ValueSort, $this->ValueRelationshipList, $this->ValueLimit, $this->ValueOffset, $this->Sql]);
                exit();
            }
            $this->Provider->Query($this->Sql);
            $this->Id = $params[$nameId];
        } else if(is_array($params)) {
            $this->ValueWhere = $where;
            $fields = $this->GetFields();
            $sep="";
            $head="";
            $values="";
            foreach($fields as $field){
                if($field['name']!=$nameId && array_key_exists($field['name'], $params)){
                    $head .= $sep . "`{$field['name']}`";
                    if(is_null($params[$field['name']])) $values .= $sep . "NULL ";
                    else if(!is_array($params['item'][$field['name']])) $values .= $sep ."'" . $this->Escape((string)$params[$field['name']]) . "' ";
                    else if($this->IsMultiLng() && in_array($field['name'], $this->LngColumns)){
                        $values .= $sep ."'" . implode(',',  array_keys($params[$field['name']])) . "' ";
                    } else $values .= $sep ."'' ";
                    $sep = ",";
                }
            }                
            $this->Sql="INSERT INTO `{$this->GetName()}` ({$head}) VALUES({$values})";
            if($debug) { 
                print_r([$this->ValueWhere, $this->ValueSort, $this->ValueRelationshipList, $this->ValueLimit, $this->ValueOffset, $this->Sql]);
                exit();
            }
            $this->Provider->Query($this->Sql);
            $this->Id = $this->Provider->GetLastId();
        }
        if(isset(\Registry::$Session)){
            if((int)$id!="") {
                \Registry::$Session->Data['msg']['success'] = $this->ErrorList[0];
            } else {
                \Registry::$Session->Data['msg']['error'] = $this->ErrorList[1];
            }
        }
        return $this;
    }
    /**
     * Удаление записи или запись из базы
     * 
     * @param mixed $params Должен содержать елемент select содержащий список индексов или \Registry::$Request->Get должен содержать индекс записи
     * @param type $where Дополнительное условие отбора
     * @return \Core\SourceProvider
     */
    public function Delete($ids=[], $nameId='id', $debug=false){
        if(is_array($ids)){
            foreach ($ids as $id) if((string)$id!=''){
                $this->Delete((string)$id);
            }            
        } else if($ids!=''){
            $this->Sql="delete from `{$this->Provider->DBName}`.`".$this->GetName()."` where `id`=".$ids;
            $this->Provider->Query($this->Sql);
        }
        return $this;
    }
    /**
     * Копирование данных из одного ресурса в другой
     * Если ресурс не указан создается дубликат данных
     * 
     * @param \Core\SourceProvider
     * @return \Core\SourceProvider Description
     */
    public function Copy($source = null){
        return $this;
    }    
    /**
     * Экранирование данных
     * 
     * @param string $value
     * @return string
     */
    public function Escape($value){
        return $this->Provider->Escape($value);
    }
    /**
     * Очистьть временные данные
     * 
     * Обнулить значение фильтров, лимитов, сортировок, группировок
     * Не затрагивает настройки связей таблиц, имя таблицы и поля
     * @return \Core\SourceProvider
     */
    public function Clear(){
        $this->ClearWhere()->ClearLimit()->ClearSort()->ClearRelationship()->ClearFieldsAs();
        return $this;
    }
    /**
     * Задать фильтр запроса или добавить к существующему
     * 
     * Строка написанная по правилам SQL для WHERE.
     * Вставка слова WHERE не требуется.
     * 
     * @param string $where Условие
     * @param boolean $force Если False то добавляется через оператор AND к текущему значению $this->ValueWhere, в противном случае просто переопределяет его
     * @return \Core\SourceProvider
     */
    public function Where($where, $force=false){
        if($force && (string)$where == "") $this->ValueWhere = "";
        else if(((string)$this->ValueWhere == "" && (string)$where != "") || $force) $this->ValueWhere = " WHERE ({$where})";
        else if(!$force && (string)$where != "" && (string)$this->ValueWhere != "") $this->ValueWhere .= " AND ({$where})";
        return $this;
    }
    /**
     * Очистить фильтр данных
     * 
     * @return \Core\SourceProvider
     */
    public function ClearWhere(){
        $this->ValueWhere = "";
        return $this;
    }
    /**
     * Задать смещение и ограничение для запросов выборки данных
     * 
     * Строка написанная по правилам SQL для LIMIT.
     * Вставка слова LIMIT не требуется.
     * 
     * @param type $offset Смещение, порядковый номер записи с которой будет начат выбор данных, включительно
     * @param type $limit Максимальное количествозаписей в получаемом результате
     * @param type $forse Если False то значения $this->ValueOffset и $this->ValueLimit будут переназначены если входящие значения больше 0, иначе будут переназначены в любом случае
     * @return \Core\SourceProvider
     */
    public function Limit($offset=0, $limit=0, $force=true){
        if($offset>0 || $force) $this->ValueOffset = (int)abs($offset);
        if($limit>0 || $force) $this->ValueLimit = (int)abs($limit);
        return $this;
    }
    /**
     * Очистить смещение и ограничение
     * 
     * @return \Core\SourceProvider
     */
    public function ClearLimit(){
        $this->ValueLimit = 0;
        $this->ValueOffset = 0;
        return $this;
    }
    /**
     * Задать сортировку столбцов
     * 
     * Строка написанная по правилам SQL для ORDER BY.
     * Вставка слова ORDER BY не требуется.
     * Столбцы проверяются на существование в таблице, не существующие будут проигнорированы.
     * 
     * @param array $list Перечень, где ключь имя столбца, а значение тип сортировки 'ASC' или True по возрастанию, 'DESC' или False по убыванию, Null исключает столбец из сортировки, который был задан ранее
     * @param boolean $forse Если False то значения $this->ValueSort будут переназначен если не массив, иначе будут переназначен в любом случае
     * @return \Core\SourceProvider
     */
    public function Sort($list = null, $force=false){
        if(!is_array($list)) return $this;
        if(!is_array($this->ValueSort) || $force) $this->ClearSort();
        foreach ($list as $key => $value)
            if( $this->ExistsAllField($key)){
                if (mb_strtoupper($value)=='ASC' || $value===true) $this->ValueSort[$key] = 'ASC';
                else if(mb_strtoupper($value)=='DESC' || $value===false) $this->ValueSort[$key] = 'DESC';
                else if(array_key_exists($key, $this->ValueSort)) unset($this->ValueSort[$key]);
            }
        return $this;
    }
    /**
     * Очистить сортировку столбцов
     * 
     * @return \Core\SourceProvider
     */
    public function ClearSort(){
        $this->ValueSort = [];
        return $this;
    }
    /**
     * Фильтр по тексту поля
     * 
     * @param array $list Перечень, где ключь имя столбца, а значение искомая строка в значениях указанного столбца
     * @return \Core\SourceProvider
     */
    public function Like($list, $force=false){
        if(is_array($list)) foreach ($list as $key => $value) 
            if($this->ExistsAllField($key) && $value!=null && $value!=""){
                $this->Where("`{$key}` LIKE '%{$this->Escape($value)}%'");
            }
        return $this;
    }
    /**
     * Добавить связи между таблицами
     * 
     * @param array $relationships
     * @param boolean $forse Если False то значения $this->ValueRelationshipList будут переназначен если не \DataBase\TableRelationshipList, иначе будут переназначен в любом случае
     * @return \Core\SourceProvider
     */
    public function Relationship($relationships, $force=false){
        if(!($this->ValueRelationshipList instanceof \DataBase\TableRelationshipList) || $force)
            $this->ValueRelationshipList = new \DataBase\TableRelationshipList();
        $this->ValueRelationshipList->Add($relationships);
        if(is_array($relationships)) {
            foreach($relationships as $relationship) if($relationship instanceof \DataBase\TableRelationship){
                $this->FieldsAs($relationship->SourceFieldsAs, $relationship->SourceName);
            }
        } else if($relationships instanceof \DataBase\TableRelationship) $this->FieldsAs($relationships->SourceFieldsAs, $relationships->SourceName);
        $this->GetAllFields(true);
        return $this;
    }
    /**
     * Очистить связи
     * 
     * @return \Core\SourceProvider
     */
    public function ClearRelationship(){
        $this->ValueRelationshipList = new \DataBase\TableRelationshipList();
        return $this;
    }
    /**
     * Добавить в список переименовывания полей
     * 
     * @param array $list Перечень, где ключь имя столбца, а значение новое название столбца
     * @param type $forse Если False то значения $this->ValueFieldsAs будут переназначен если не массив, иначе будут переназначен в любом случае
     * @return \Core\SourceProvider
     */
    public function FieldsAs($list, $table_name=null, $force=false){
        if(!isset($table_name) || $table_name=='') $table_name = $this->GetName();
        if(!is_array($this->ValueFieldsAs) || $force) $this->ClearFieldsAs();
        if(!array_key_exists($table_name, $this->ValueFieldsAs)) $this->ValueFieldsAs[$table_name]=[];
        if(is_array($list)) foreach ($list as $key => $value) 
            if($this->ExistsAllField($key) && $value!=null && $value!=""){
                $this->ValueFieldsAs[$table_name][$key] = $value;
            }
        return $this;
    }
    /**
     * Очистить список переименовывания полей
     * 
     * @return \Core\SourceProvider
     */
    public function ClearFieldsAs(){
        $this->ValueFieldsAs = [$this->GetName()=>[]];
        return $this;
    }
    
    /**
     * Преобразовать $this->Sort в строку SQL
     * 
     * @return string
     */
    private function _sortToSql(){
        if(is_array($this->ValueSort) && count($this->ValueSort)>0){
            $result = '';
            $sep=' ORDER BY ';
            foreach ($this->ValueSort as $field => $sort) if(strtoupper($sort)=='ASC' || strtoupper($sort)=='DESC'){
                $result .= $sep.$field." ".strtoupper($sort);
                $sep=', ';
            }
            return $result;
        }
        return '';
    }
    /**
     * Преобразовать $this->ValueOffset и $this->ValueLimit в строку SQL
     * 
     * @return string
     */
    private function _limitToSql(){
        if(!isset($this->ValueLimit) || $this->ValueLimit<1) return '';
        if(!isset($this->ValueOffset) || $this->ValueOffset<1) return " LIMIT 0, {$this->ValueLimit}";
        else return " LIMIT {$this->ValueOffset}, {$this->ValueLimit}";
    }
    /**
     * Преобразовать $this->ValueGroup в строку SQL
     * 
     * @return string
     */
    private function _groupToSql(){
        return '';
    }
    /**
     * Преобразовать связи $this->ValueRelationshipList в строку SQL
     * 
     * @return string
     */
    private function _relationshipToSql(){
        $result = '';
        foreach ($this->ValueRelationshipList->GetItems() as $Relationship) if(isset($Relationship) && $Relationship instanceof \DataBase\TableRelationship){
            switch($Relationship->RelationshipType){
                case \DataBase\TableRelationshipList::ONE_TO_ONE: 
                    $result .= " JOIN `{$Relationship->SourceName}` ON `{$Relationship->SourceName}`.`{$Relationship->SourceIndex}`=`{$this->GetName()}`.`{$Relationship->DestinationIndex}`";
                    break;
                case \DataBase\TableRelationshipList::ONE_TO_MANY: 
                    $result .= " RIGHT JOIN `{$Relationship->SourceName}` ON `{$Relationship->SourceName}`.`{$Relationship->SourceIndex}`=`{$this->GetName()}`.`{$Relationship->DestinationIndex}`";
                    break;
                case \DataBase\TableRelationshipList::MANY_TO_ONE: 
                    $result .= " LEFT JOIN `{$Relationship->SourceName}` ON `{$Relationship->SourceName}`.`{$Relationship->SourceIndex}`=`{$this->GetName()}`.`{$Relationship->DestinationIndex}`";
                    break;
                case \DataBase\TableRelationshipList::MANY_TO_MANY:
                    break;
            }
        }
        return $result;
    }
    /**
     * Преобразовать переименовывание полей $this->ValueFieldsAs в строку SQL
     * 
     * @param string $table Название таблицы добавляемое к названию переименовываемого поля
     * @return string
     */
    private function _fieldsAsToSql(){
        $res = '';
        $sep = ' ';
        if(is_array($this->ValueFieldsAs) && count($this->ValueFieldsAs)>0) 
            foreach ($this->ValueFieldsAs as $table_name => $items) {
                if(is_array($items) && count($items)>0) {
                    foreach (self::FieldsSource($table_name, $this->Provider) as $key => $field) {
                        if(array_key_exists($field['name'], $items))
                            $res .= $sep."`{$this->Provider->DBName}`.`{$table_name}`.`{$field['name']}` AS `{$items[$field['name']]}`";
                        else $res .= $sep."`{$this->Provider->DBName}`.`{$table_name}`.`{$field['name']}`";
                        $sep = ', ';
                    }
                } else {
                    $res .= $sep."`{$this->Provider->DBName}`.{$table_name}.*";
                    $sep = ', ';
                }
            }
        return ($res==''?' *':$res);
    }

    private function _copy($id, $names, $name_prefix=null, $where = null){
        $fields = $this->GetFields();
        $head1 = "";
        $head2 = "";
        $sep = "";
        if(!isset($name_prefix)) $name_prefix=" (Копия)";
        foreach($fields as $field){
            if($field['name']!='id'){
                if(!in_array($field['name'], $names)){
                    $head1 .= $sep . "`{$field['name']}`";
                    $head2 .= $sep . "`{$field['name']}`";
                } else {
                    $head1 .= $sep . "`{$field['name']}`";
                    $head2 .= $sep . "CONCAT(`{$field['name']}`,'".$this->Escape($name_prefix)."') as `{$field['name']}`";
                }
                $sep = ",";
            }
        } 
        $ids = "`id`=".(int)$id;
        if(isset($where)) $ids .= " AND ({$where})";
        $sql = "INSERT INTO `".$this->GetName()."` ({$head1}) SELECT {$head2} FROM `".$this->GetName(). "` " . $this->Where($ids);
        $this->Provider->Query($sql);
    }

}