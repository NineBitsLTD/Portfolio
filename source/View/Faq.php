<?php
namespace View;

class Faq extends \Core\View {
    public $List=[];
    public $Offset=0;
    public $Limit=0;

    public function printContent() {
    ?>
    <h1>FAQ</h1>
    <hr>
    <article class="">
        <h3>List of questions and answers:</h3>
    <?php foreach ($this->List as $value) { ?>
        <div class="form-group form-control">
            <label><?=$value['created_at']?>: <b><?=$value['firstname']?></b> <i><?=$value['email']?></i></label>
            <hr>
            <?= htmlentities($value['message'])?>
            <?php if(array_key_exists('request', $value) && is_array($value['request']))
                    foreach ($value['request'] as $request) { ?>
            <br><br>
            <div class="form-group form-control">
                <?=$request['created_at']?>: <b><?=$request['firstname']?></b>
                <hr>
                <?=$request['message']?>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
        <div class="form-group">
            <?php if(($this->Offset-$this->Limit)>=0){?>    
            <a class="btn btn-secondary" href="<?=\Registry::$Data->BaseLink?>/<?=\Registry::$Data->Page?>?offset=<?=$this->Offset-$this->Limit?>">Prev</a>
            <?php } ?>
            <?php if($this->Total>($this->Offset+$this->Limit)){?>
            <a class="btn btn-secondary" href="<?=\Registry::$Data->BaseLink?>/<?=\Registry::$Data->Page?>?offset=<?=$this->Offset+$this->Limit?>">Next</a>
            <?php } ?>
        </div>
    </article>
    <article class="form-group">
        <h3>Write us:</h3>
        <div class="form-control">
            <form action="<?=$GLOBALS['base']?>faq/" method="POST">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input name="firstname" type="text" class="form-control" id="firstname" placeholder="Enter your First Name">
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    <label for="textarea">Message</label>
                    <textarea name="message" class="form-control" id="textarea" rows="3" style="margin-top: 0px; margin-bottom: 0px; height: 108px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </article>
<?php } 
}
?>