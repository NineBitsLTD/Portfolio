<?php
namespace Controller;

class Logout extends \Core\Controller {
    public function getIndex() {
        (new \User\Controller())->getLogout();
    }
}