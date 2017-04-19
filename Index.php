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

$brand = "NineBits LTD";
$menu = [
    "home"=>[
        "icon"=>"fa-home",
        "title"=>"Main page - Home",
        "text"=>"Home",
    ],
    "projects"=>[
        "icon"=>"fa-file-o",
        "title"=>"Projects",
        "text"=>"Projects",
    ],
    "contacts"=>[
        "icon"=>"fa-phone",
        "title"=>"Phone",
        "text"=>"Phone",
    ],
];
$components = [
    "css"=>[
        [
            "title"=>"butstrap",
            "href"=>"https://v4-alpha.getbootstrap.com/components/navbar/",
        ]
    ]
];

?>
<!DOCTYPE html>
<html>
    <head>
        <title>NineBits - Portfolio</title>
        <link href="css/bootstrap.css" type="text/css" media="all" rel="stylesheet">
        <script src="js/jquery-3.1.1.js"></script>
        <script src="js/tether.min.js"></script>
        <script src="js/bootstrap.js"></script>
    </head>
    <body>
        <header class="">
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="#"><?=$brand?></a>
                <div id="navbarNav" class="collapse navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <?php foreach ($menu as $key => $value) { ?>
                        <li class="nav-item" title="<?=(array_key_exists('title', $value))?$value['title']:""?>">
                            <a class="nav-link">
                                <?php if(array_key_exists('icon', $value)){ ?>
                                <i class="<?=$value['icon']?>"></i>
                                <?php } ?>
                                <?php if(array_key_exists('text', $value)){ ?>
                                <span><?=$value['text']?></span>
                                <?php } ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="container-fluid">
            <article>
                <h3>Used components:</h3>
                <?php foreach ($components as $key => $value) { ?>
                <h4><?= strtoupper($key)?></h4>
                <ul>
                    <?php foreach ($value as $index => $item) { ?>
                    <li><a href="<?=$item['href']?>"><?=$item['title']?></a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </article>
        </div>
    </body>
</html>