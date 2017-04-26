<!DOCTYPE html>
<html>
    <head>
        <title>NineBits - Portfolio</title>
        <?=print_components($components)?>
    </head>
    <body>
        <header class="navbar navbar-toggleable-md navbar-light bg-faded">
            <nav class="container" style="min-width: 250px;">
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="#"><?=$brand?></a>
                <div id="navbarNav" class="collapse navbar-collapse">
                    <?=print_menu($menu)?>
                </div>
            </nav>
        </header>        
        <div class="container">
        <?=print_content()?>
        </div>
    </body>
</html>

