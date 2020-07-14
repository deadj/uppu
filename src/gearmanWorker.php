<?php

require_once 'vendor/autoload.php';

$gearmanWorker = new GearmanWorker();
$gearmanWorker->addServer();

$gearmanWorker->addFunction('convertVideo', 'convertVideo');

while (1) {
    $gearmanWorker->work();
    if ($gearmanWorker->returnCode() != GEARMAN_SUCCESS) {
	    break;
  	} 
}

function convertVideo($job)
{   
    $db = include('src/dbConfig.php');
    $db = $db['db'];
    $pdo = new PDO("mysql:host=" . $db["host"] . ";dbname=" . $db['dbname'] . ";charset=utf8", $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $workload = $job->workload();

    $converter = new Converter($pdo);
    $converter->convert($workload);
}