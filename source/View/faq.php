<?php
function print_content(){ 
    $modelFaq = new \Model\Faq();
    if(array_key_exists('email', $_POST) && $_POST['email']!='' && array_key_exists('message', $_POST) && $_POST['message']!=''){
        $post = [
            'firstname' => '',
            'email' => $_POST['email'],
            'message' => $_POST['message'],
        ];
        if(array_key_exists('firstname', $_POST)) $post['firstname'] = $_POST['firstname'];
        $modelFaq->Set($post);
    }
    $offset = 0;
    $limit = 2;
    if(isset($_GET['offset'])) $offset = $_GET['offset'];
    if(isset($_GET['limit'])) $limit = $_GET['limit'];
    $total = $modelFaq->Clear()->Where("`parent_id` IS NULL")->GetTotal();
    $faq = $modelFaq->Limit($offset,$limit)->Sort(['created_at'=>'desc'])->Get('id')->ClearLimit()->GetResult()->Rows;
    foreach ($faq as $id=>$value) {
        $faq[$id]['request'] = $modelFaq->ClearWhere()->Where("`parent_id`='{$value['id']}'")->Get('id')->GetResult()->Rows;
    }
    ?>
    <h1>FAQ</h1>
    <hr>
    <article class="">
        <h3>List of questions and answers:</h3>
    <?php foreach ($faq as $value) { ?>
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
            <?php if(($offset-$limit)>=0){?>    
            <a class="btn btn-secondary" href="<?=\Registry::$Data->BaseLink?>/<?=\Registry::$Data->Page?>?offset=<?=$offset-$limit?>">Prev</a>
            <?php } ?>
            <?php if($total>($offset+$limit)){?>
            <a class="btn btn-secondary" href="<?=\Registry::$Data->BaseLink?>/<?=\Registry::$Data->Page?>?offset=<?=$offset+$limit?>">Next</a>
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

include 'source/view/base.php' 

?>