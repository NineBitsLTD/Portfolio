<?php
namespace View;

class Export extends \Core\View {    
    public $Components=[];
    
    public $TextNotPermission='Not permission';

    public function printContent() {
        if(\Registry::$Session->IsLogged()) { ?>
    <h1>Export</h1>
    <hr>
    <article>
        <h3>Table list:</h3>
        <?php foreach ($this->Components as $key => $item) { ?>
        <ul>
            <li><a href="<?=$item['link']?>"><?=$item['title']?></a></li>
        </ul>
        <?php } ?>
    </article>
    <?php } else { ?>
        <h1><?= $this->TextNotPermission?></h1>
        <hr>
    <?php }    
    } 
}
?>