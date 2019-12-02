<?php

$dbInfo = parse_ini_file('init.ini');

return function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $dbInfo['host'] . ";dbname=" . $dbInfo['db'], $dbInfo['user'], $dbInfo['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo;	
};