<?php
/**
* Режим отладки
* 
* Если равен 1, при переводе текста выполняется проверка 
* отсутствующих переводов и их запись в базу
*/
define("DEBUG_MODE", 1);
/**
 * Вывод текста ошибок и исключений при выполнении кода когда DEBUG_MODE = 1
 */
if(DEBUG_MODE){
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
} else {
    error_reporting();
    ini_set('display_errors', 0);
}
if(!isset($root)) $root = "";
$brand = "NineBits LTD";
$baseLink = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";
$menu = [
    "home"=>[
        "icon"=>"fa-home",
        "title"=>"Main page - Home",
        "text"=>"Home",
        "href"=>$baseLink,
        "class"=>"nav-item",
    ],
    "projects"=>[
        "icon"=>"fa-file-code-o",
        "title"=>"Projects",
        "text"=>"Projects",
        "href"=>$baseLink."projects/",
        "class"=>"nav-item",
    ],
    "contacts"=>[
        "icon"=>"fa-phone",
        "title"=>"Contacts",
        "text"=>"Contacts",
        "href"=>$baseLink."contacts/",
        "class"=>"nav-item",
    ],
];
$components = [
    "css"=>[
        [
            "title"=>"Bootstrap",
            "link"=>"https://v4-alpha.getbootstrap.com/components/navbar/",
            "href"=>$root."css/bootstrap.css",
            "type"=>"text/css",
            "media"=>"all",
            "rel"=>"stylesheet",
        ],
        [
            "title"=>"Font Awesome",
            "link"=>"http://fontawesome.io/icons/",
            "href"=>$root."css/font-awesome.css",
            "type"=>"text/css",
            "media"=>"all",
            "rel"=>"stylesheet",
        ],
    ],
    "js"=>[
        [
            "title"=>"JQuery",
            "link"=>"http://jquery.com/",
            "src"=>$root."js/jquery-3.1.1.js",
        ],
        [
            "title"=>"Tether",
            "link"=>"http://tether.io/",
            "src"=>$root."js/tether.min.js",
        ],
        [
            "title"=>"Bootstrap",
            "link"=>"https://v4-alpha.getbootstrap.com/components/navbar/",
            "src"=>$root."js/bootstrap.js",
        ],
    ]
];
/**
 * Печать атрибутов в тегах HTML
 * 
 * @param array $requirement Перечень имен требуемых атрибутов
 * @param array $attributes Асоциативный масив атрибутов, где ключ - название, а значение - содержание атрибута
 */
function print_attributes(array $requirement, array $attributes){
    foreach ($attributes as $key => $attribute) if(in_array($key, $requirement)){
        echo " {$key}=\"{$attribute}\"";
    }
}
/**
 * Печать подключаемых компонентов (скрипты стили)
 * 
 * @param array $components Многомерный асрциативный массив компонентов
 */
function print_components(array $components){
    foreach($components as $type=>$item) if(is_array($item)){
        switch($type){
            case 'css':
                foreach($item as $component) if(is_array($component) && array_key_exists('href', $component)){ ?>
                    <link<?=print_attributes(['href', 'type', 'media', 'rel'], $component)?>>
                <?php }
            break;
            case 'js':
                foreach($item as $component) if(is_array($component) && array_key_exists('src', $component)){ ?>
                    <script<?=print_attributes(['src'], $component)?>></script>
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
function print_menu(array $menu){ ?>
    <ul class="navbar-nav mr-auto">
        <?php foreach ($menu as $key => $item) if(is_array($item) && (array_key_exists('icon',$item) || array_key_exists('text',$item))) { ?>
        <li<?=print_attributes(['class','title'], $item)?>>
            <a class="nav-link"<?=print_attributes(['href'], $item)?>>
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
<?php } ?>
