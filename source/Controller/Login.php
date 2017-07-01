<?php
namespace Controller;

class Login extends \Core\Controller {
    public function getIndex() {
        (new \User\Controller())->getIndex();
    }
}