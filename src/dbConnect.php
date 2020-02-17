<?php

$dbInfo = parse_ini_file("init.ini");

$pdo = new PDO("mysql:host=" . $dbInfo['host'] . ";dbname=" . $dbInfo['db'], $dbInfo['user'], $dbInfo['pass']);

return $pdo; 