<?php
namespace View;

class FaqEdit extends \Core\View {
    public $ID=0;
    public $ParentID=0;
    public $Item=[];
    
    public $TextColumns=[
        'id'=>'ID',
        'parent_id'=>'ID parent',
        'firstname'=>'Name',
        'lastname'=>'Surname',
        'email'=>'E-mail',
    ];
    public $TextNotPermission='Not permission';
    public $TextAddAnswer='Add user answer Parent ID';
    public $TextEditQuestion='Edit user question ID';
    public $BtnCancel='Cancel';
    public $BtnSave='Save';

    public function printContent() {
        if(\Registry::$Session->IsLogged() && ($this->ID>0 || $this->ParentID>0)) { ?>
        <form action="<?= \Registry::$Data->BaseLink?>faq/save" method="post">
        <div class="btn-group pull-right" role="group">
            <a href="<?= \Registry::$Data->BaseLink?>faq" class="btn btn-sm btn-secondary" title="<?= htmlentities($this->BtnCancel)?>"><i class="fa fa-reply"></i></a>
            <button type="submit" href="" class="btn btn-sm btn-primary" title="<?= htmlentities($this->BtnSave)?>"><i class="fa fa-save"></i></button>
        </div>
        <?php if($this->ID>0){ ?>
        <h1><?= $this->TextEditQuestion?>: <?= $this->ID?></h1>
        <?php } else { ?>
        <h1><?= $this->TextAddAnswer?>: <?= $this->ParentID?></h1>
        <?php } ?>
        <article class="">
            <?php foreach ($this->Item as $key => $value) { ?>
            <div class="form-group">
                <label class="control-label"><?= (isset($this->TextColumns[$key]))?$this->TextColumns[$key]:$key ?></label>
                <input class="form-control" name="item[<?= $key ?>]" value="<?= htmlentities($value) ?>">
            </div>        
            <?php } ?>
        </article>
        </form>
    <?php } else { ?>
        <h1><?= $this->TextNotPermission?></h1>
        <hr>
    <?php }
    } 
}
?>