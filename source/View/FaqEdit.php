<?php
namespace View;

class FaqEdit extends \Core\View {
    public $ID=0;
    public $Item=[];
    
    public $TextNotPermission='Not permission';
    public $TextEditQuestion='Edit user question ID';
    public $BtnCancel='Cancel';
    public $BtnSave='Save';

    public function printContent() {
        if(\Registry::$Session->IsLogged()) { ?>
        <h1><?= $this->TextEditQuestion?>: <?= $this->ID?></h1>
        <article class="">
            <h3></h3>
            <div class="form-group">
            </div>        
        </article>
    <?php } else { ?>
        <h1><?= $this->TextNotPermission?></h1>
        <hr>
    <?php }
    } 
}
?>