<?php
namespace View;

class Contacts extends \Core\View {
    public $List;
    
    public function printContent() {
    ?>
    <h1>Contacts</h1>
    <hr>
    <article class="form-group">
        <h3>Main office:</h3>
        <ul class="list-group">
        <?php foreach ($this->List as $key => $value) { ?>
            <li class="list-group-item"><b><?=$key?></b>:&nbsp;<span><?=$value['value']?></span></li>    
        <?php } ?>
        </ul>
    </article>
<?php } 
}
?>