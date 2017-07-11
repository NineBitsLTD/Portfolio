<?php

namespace Helper;

class HTML{
    /**
     * Печать атрибутов в тегах HTML
     * 
     * @param array $requirement Перечень имен требуемых атрибутов
     * @param array $attributes Асоциативный масив атрибутов, где ключ - название, а значение - содержание атрибута
     */
    static public function PrintAttributes(array $requirement, array $attributes){
        foreach ($attributes as $key => $attribute) if(in_array($key, $requirement)){
            if($key=='src' || $key=='href') echo " {$key}=\"".\Registry::$Data->BaseLink.$attribute."\"";
            else echo " {$key}=\"{$attribute}\"";
        }
    }
    /**
     * Печать подключаемых компонентов (скрипты стили)
     * 
     * @param array $components Многомерный асрциативный массив компонентов
     */
    static public function PrintComponents(array $components){
        foreach($components as $type=>$item) if(is_array($item)){
            switch($type){
                case 'css':
                    foreach($item as $component) if(is_array($component) && array_key_exists('href', $component)){ ?>
                        <link<?=self::PrintAttributes(['href', 'type', 'media', 'rel'], $component)?>>
                    <?php }
                break;
                case 'js':
                    foreach($item as $component) if(is_array($component) && array_key_exists('src', $component)){ ?>
                        <script<?=self::PrintAttributes(['src'], $component)?>></script>
                    <?php }
                break;
            }
        }
    }
    /**
     * Печать пунктов меню
     * 
     * @param array $menu Многомерный асрциативный массив - перечень пунктов меню
     */
    static public function PrintMenu(array $menu){ ?>
        <ul class="navbar-nav mr-auto">
            <?php foreach ($menu as $key => $item) if(is_array($item) && 
                (array_key_exists('icon',$item) || array_key_exists('text',$item)) && 
                ($key!='logout' || \Registry::$Session->IsLogged()) && 
                ($key!='login' || !\Registry::$Session->IsLogged()) &&
                ($key!='api')) { ?>
            <li<?=self::PrintAttributes(['class','title'], $item)?> data-key="item-<?=$key?>">
                <a class="nav-link"<?=self::PrintAttributes(['href'], $item)?>>
                    <?php if(array_key_exists('icon', $item)){ ?>
                    <i class="fa <?=$item['icon']?>"></i>
                    <?php } ?>
                    <?php if(array_key_exists('text', $item)){ ?>
                    <span><?=$item['text']?></span>
                    <?php } ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    <?php }
    /**
     * Кодирование данных для SQL запросов
     *  
     * @param mixed $data Данные в чистом виде
     * @return mixed Закодированные для SQL запросов
     */
    static public function EncodeSql($data){
        $result;
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $result[$this->EncodeSql($key)] = $this->EncodeSql($value);
            }
        } else {
            $result = str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $data);
        }
        return $result;
    }
}