<?php
namespace Controller;

class Contacts extends \Core\Controller {
    public function getIndex() {
        $view = new \View\Base();
        $view->Content = new \View\Contacts();
        $view->Content->List = (new \Model\Contact())->Get("title")->GetResult()->Rows;
        $view->printContent();
    }
}
