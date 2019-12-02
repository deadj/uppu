<?php

$dbInfo = parse_ini_file('init.ini');

$config = array();

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = $dbInfo['host'];
$config['db']['user']   = $dbInfo['user'];
$config['db']['pass']   = $dbInfo['pass'];
$config['db']['dbname'] = $dbInfo['db'];

return $config;

