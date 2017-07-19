<?php
namespace View;

class Login extends \Core\View {
    public $List=[];
    public $Error=null;
    
    public $TextLogin="E-mail";
    public $TextPassword="Password";
    public $BtnEnter="Enter";
    
    public function printContent() {
    ?>
    <h1>Authentification</h1>
    <hr>
    <?php if(isset($this->Error)){ ?>
    <div class="alert alert-danger"><?= $this->Error?></div>
    <?php }?>
    <article class="form-group">
        <form action="" method="post">
            <div class="form-group">
                <label class="control-label"><?= $this->TextLogin?></label>
                <input class="form-control" name="login">
            </div>
            <div class="form-group">
                <label class="control-label"><?= $this->TextPassword?></label>
                <input type = "password" class="form-control" name="password">
            </div>
            <input type="submit" value="<?= htmlentities($this->BtnEnter)?>">
        </form>
    </article>
<?php } 
}
?>