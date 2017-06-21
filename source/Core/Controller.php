<?php

namespace Core;
/**
 * Базовый шаблон контроллера, формирует логику раздела или страницы сайта
 * 
 * @uses \Registry
 * @uses \Core\Object
 * @uses \Core\Action
 * @uses \Helper
 * @uses \Helper\Str
 */
class Controller extends \Core\Object {
    /**
     * Название раздела или страницы сайта
     * 
     * Данное название как правило соответствует части пути адресной строки и является алиасом $this->Action->PathShort
     * 
     * @var string
     */
    public $Page = "";
    /**
     * Действие запустившее контроллер
     * 
     * @var \Core\Action
     */
    public $Action = null;
    
    /**
     * Конструктор контроллера
     * 
     * @param \Core\Action $action
     */
    public function __construct($action = null) {
        $this->Action = $action;
        if($this->Action != null){
            $this->Page = $this->Action->PathShort;
        }
        parent::__construct();
    }
    /**
     * Воспроизведение шаблона страницы или блока
     * 
     * Воспроизведение шаблона $view
     * 
     * @param \Core\View $view
     * @return string
     */
    public function Render(& $view){
        $this->setData($view);
        return $view->Render();
    }
    
    /**
     * Генерация стандартных данных
     * 
     * Добавление в параметры для шаблонов страниц общих данных.
     * 
     * @param \Core\View $view
     */
    protected function setData(& $view){
        $view->Ajax = \Registry::IsAjax();
        $view->Base = \Registry::Link();
        $view->Link = \Registry::Link($this->Action->Path);
        $view->Page = $this->Page;
    }
    
}

