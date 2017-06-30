<?php
namespace View;

class Base extends \Core\View {
    /**
     *
     * @var \Core\View
     */
    public $Content='';
    
    public $TextHeader = 'Portfolio';
    
    public function printContent() {
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= \Registry::$Data->Brand?> - <?= $this->TextHeader?></title>
        <?= \Helper\HTML::PrintComponents(\Registry::$Data->Components)?>
    </head>
    <body>
        <header class="navbar navbar-toggleable-md navbar-light bg-faded">
            <nav class="container" style="min-width: 250px;">
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="<?= \Registry::$Data->BaseLink?>"><?= \Registry::$Data->Brand?></a>
                <div id="navbarNav" class="collapse navbar-collapse">
                    <?=\Helper\HTML::PrintMenu(\Registry::$Data->Menu)?>
                </div>
            </nav>
        </header>        
        <div class="container">
        <?= $this->Content->printContent()?>
        </div>
    </body>
</html>

<?php }
}
?>