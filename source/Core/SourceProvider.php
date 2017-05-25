<?php

namespace Core;
interface SourceProvider {
    /**
     * Проверка на существование ресурса с именем $name
     *
     * @param type $name
     */
    public static function ExistsSource($name);
    /**
     * Создание ресурса с именем $name
     *
     * @param type $name
     * @param type $params
     */
    public static function CreateSource($name, $params);
    /**
     * Удаление ресурса с именем $name
     *
     * @param type $name
     */
    public static function DeleteSource($name);
    /**
     * Получить данные ресурса в виде асоциативного массива
     * Пример: $source->Where("user_id=1")->Get()
     *
     * @return SourceProvider
     */
    public function Get();
    /**
     * Сохранить данные ресурса
     *
     * @param array $params Данные для сохранения в виде асоциативного массива
     * @return SourceProvider
     */
    public function Set(array $params);
    /**
     * Копирование данных полученных с помощью Get в ресурс $source
     * Пример: $source1->Where("user_id=1")->Get()->Copy($source2)
     *
     * @param type $source
     * @return SourceProvider
     */
    public function Copy($source);
    /**
     * Удаление данных ресурса
     * Пример: $source->Where("user_id=1")->Delete()
     *
     * @param array $params
     * @return SourceProvider
     */
    public function Delete();
}