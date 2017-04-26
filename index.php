<?php

include 'source/config/main.php';

function print_content(){ ?>
    <h1>Welcome!</h1>
    <article>
        <h3>Used components:</h3>
        <?php foreach ($GLOBALS['components'] as $key => $value) { ?>
        <h4><?= strtoupper($key)?></h4>
        <ul>
            <?php foreach ($value as $index => $item) { ?>
            <li><a href="<?=$item['link']?>"><?=$item['title']?></a></li>
            <?php } ?>
        </ul>
        <?php } ?>
    </article>
<?php } 

include 'source/view/main.php' 

?>