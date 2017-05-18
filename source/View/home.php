<?php
function print_content(){ ?>
    <h1>Welcome!</h1>
    <hr>
    <article>
        <h3>Used components:</h3>
        <?php foreach (\Registry::$Data->Components as $key => $value) { ?>
        <h4><?= strtoupper($key)?></h4>
        <ul>
            <?php foreach ($value as $index => $item) { ?>
            <li><a href="<?=$item['link']?>"><?=$item['title']?></a></li>
            <?php } ?>
        </ul>
        <?php } ?>
    </article>
    <article>
        <h3>License:</h3>
        <div class="form-control" style="overflow: auto; max-height: 200px;">
            <div style="max-width: 600px; margin: auto;">
                <?= str_replace("\n","<br>", htmlentities(file_get_contents('LICENSE')))?>
            </div>
        </div>
    </article>
<?php } 

include 'source/view/base.php' 

?>