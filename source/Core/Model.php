<?php
namespace Core;

class Model{
    /**
     * Table name
     * 
     * @var string
     */
    public $TableName;
    /**
     * Get data from database table
     * 
     * @param string $where Parts "WHERE" for sql
     * @param string $index The column name for creating the result key, if the key is not unique, will group the result data.
     * @param int $offset Offset for sql parts "LIMIT"
     * @param int $limit Limit for sql parts "LIMIT"
     * @param array $sort Parts "ORDER BY" for sql. Example: ['id'=>'acs', 'name'=>'desc']
     * @return \Core\DataBase\ActiveRecord
     */
    public function Get($where = "", $index=null, $offset=0, $limit=0, $sort=[]){
        return \Registry::$DB->Query("SELECT * FROM {$this->TableName}".($where!=''?" WHERE {$where}":'').$this->sort($sort), [], $index);
    }
    
    private function sort($sort){
        $result = "";
        if(is_array($sort) && count($sort)>0){
            $result = "ORDER BY";
            $sep = '';
            foreach ($sort as $key => $value) {
                $result .= $sep."`".$key."` ".$value;
                $sep = ", ";
            }// ORDER BY `id` asc, `name` desc
        }
        return $result;
    }
}
