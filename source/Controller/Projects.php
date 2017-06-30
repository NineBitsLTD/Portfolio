<?php
namespace Controller;

class Projects extends \Core\Controller {
    public function getIndex() {
        $view = new \View\Base();
        $view->Content = new \View\Projects();
        $view->Content->List = (new\Model\Project())->Get("type")->GetResult()->Rows;
        $view->printContent();
    }
}

