<?php

require 'examples/MySqli.php';

$db = new \cMySQLi('localhost', 'root', '', 'test');

$users = $db->query('SELECT * FROM `user`');


