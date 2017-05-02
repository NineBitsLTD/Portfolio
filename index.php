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
$base = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";

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
<?php }
/**
 * Добавление поста в файл $filename
 * 
 * @param type $filename
 */
function faq_post($filename){
    if(array_key_exists('email', $_POST) && array_key_exists('message', $_POST) && $_POST['message']!=''){
        $post = [
            'firstname' => '',
            'email' => $_POST['email'],
            'message' => $_POST['message'],
        ];
        if(array_key_exists('email', $_POST)) $post['firstname'] = $_POST['firstname'];
        ob_start();
        echo "\n".'$faq["'.date("Y-m-d H:i:s").'"] = ';
        var_export($post);
        echo ";";
        $post = ob_get_contents();
        ob_end_clean();
        file_put_contents($filename, $post, FILE_APPEND);
    }
}

include 'source/model/menu.php';
include 'source/model/components.php';
if(array_key_exists('route', $_GET)) $route = $_GET['route'];
else $route='home';
$page='';
foreach ($menu as $pagename => $info) if(strtolower(substr($route, 0, strlen ($pagename)))==$pagename) $page = $pagename;
switch ($page){
    case 'contacts': include 'source/model/contacts.php'; break;
    case 'projects': include 'source/model/projects.php'; break;
    case 'faq': 
        faq_post('source/model/faq.php');
        include 'source/model/faq.php'; 
        break;
    default: break;
}
if($page!='') include "source/view/{$page}.php";