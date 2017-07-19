<?php
namespace View;

class Home extends \Core\View {
    public $Components=[];
    
    public $TextWelcome='Welcome';


    public function printContent() {
    ?>
    <h1><?=\Registry::Trans($this->TextWelcome)?>
        <?php if(\Registry::$Session->IsLogged()) { ?>
        <span class="text-primary"><?= \Registry::$Session->User->Data['firstname']?> <?= \Registry::$Session->User->Data['lastname']?></span>
        <?php } ?>!
    </h1>
    <hr>
    <article>
        <h3>Used components:</h3>
        <?php foreach ($this->Components as $key => $value) { ?>
        <h4><?= strtoupper($key)?></h4>
        <ul>
            <?php foreach ($value as $index => $item) { ?>
            <li><a href="<?=$item['link']?>"><?=$item['title']?></a></li>
            <?php } ?>
        </ul>
        <?php } ?>
    </article>
    <article style="text-align: center; color: #fff;">
        <a href="<?=\Registry::$Data->BaseLink?>payment" class="btn btn-success btn-lg">Donate</a>
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
}
?>