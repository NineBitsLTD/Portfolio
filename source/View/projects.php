<?php
function print_content(){ 
    $projects = (new \Model\Project())->Get("","type")->Rows;
    ?>
    <h1>Projects</h1>
    <hr>
    <?php foreach ($projects as $type => $items) { ?>
    <article class="form-group">
        <h3><?=$type?></h3>
        <div class="list-group">
        <?php foreach ($items as $project) { ?>            
            <a target="_blank" href="<?=$project['href']?>" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?=$project['title']?></h5>
                    <small class="text-muted"><?=$project['year']?></small>
                </div>
                <p class="mb-1"><?=$project['description']?></p>
                <small class="text-muted"><b><?=$project['real']?'Real':'Demo'?>: </b><?=$project['demo']?></small>
            </a>
        <?php } ?>
        </div>
    </article>
    <?php } ?>
<?php } 

include 'source/view/base.php' 

?>