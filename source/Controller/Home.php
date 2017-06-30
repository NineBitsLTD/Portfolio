<?php
namespace Controller;

class Home extends \Core\Controller {
    public function getIndex() {
        $view = new \View\Base();
        $view->Content = new \View\Home();
        $view->Content->Components=\Registry::$Data->Components;
        $view->printContent();
    }
}

