<?php

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

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <style>
            body>nav ul {
                list-style: none;
            }
            body>nav li {
                float: left;
                margin-right: 15px;
            }
        </style>
    </head>
    <body>
        <nav>
            <ul>
                <?php foreach ($menu as $key => $value) { ?>
                <li title="<?=(array_key_exists('title', $value))?$value['title']:""?>">
                    <?php if(array_key_exists('icon', $value)){ ?>
                    <i class="<?=$value['icon']?>"></i>
                    <?php } ?>
                    <?php if(array_key_exists('text', $value)){ ?>
                    <span><?=$value['text']?></span>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </nav>
    </body>
</html>