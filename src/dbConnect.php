<?php

$dbInfo = parse_ini_file("init.ini");

$pdo = new PDO("mysql:host=" . $dbInfo['host'] . ";dbname=" . $dbInfo['db'] . ";charset=utf8", $dbInfo['user'], $dbInfo['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

return $pdo; 