<?php
function print_content(){ 
    \Helper\HTML::AddFAQ('source/Model/faq.php');
    include 'source/Model/faq.php'; 
    ?>
    <h1>FAQ</h1>
    <hr>
    <article class="">
        <h3>List of questions and answers:</h3>
    <?php krsort($faq); $i=0; foreach ($faq as $key => $value) { $i++; ?>
        <div class="form-group form-control">
            <label><?=$key?>: <b><?=$value['firstname']?></b> <i><?=$value['email']?></i></label>
            <hr>
            <?= htmlentities($value['message'])?>
            <?php if(array_key_exists('request', $value) && is_array($value['request'])) foreach ($value['request'] as $date => $request) { ?>
            <br><br>
            <div class="form-group form-control">
                <?=$date?>: <b><?=$request['firstname']?></b>
                <hr>
                <?=$request['message']?>
            </div>
            <?php } ?>
        </div>
    <?php if($i>=3) break; } ?>
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

include 'source/view/base.php' 

?>