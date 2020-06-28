<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

$gearmanWorker = new GearmanWorker();
$gearmanWorker->addServer();

$gearmanWorker->addFunction('convertVideo', 'convertVideo');

while (1) {
    $gearmanWorker->work();
    if ($gearmanWorker->returnCode() != GEARMAN_SUCCESS) {
	    break;
  	} 
}

// function convertVideo($job)
// {
// 	$link = "public/" . $job->workload();
// 	$newLink = preg_replace('/[.]\\w*/', '.mp4', $link);

// 	$process = new Process(['ffmpeg', '-i', $link, '-q:v', '1', '-c:v', 'h264', $newLink]);
//     $process->run();

//     unlink($link);
// }

function convertVideo($job)
{
    $db = include('src/dbConfig.php');
    $db = $db['db'];
    $pdo = new PDO("mysql:host=" . $db["host"] . ";dbname=" . $db['dbname'] . ";charset=utf8", $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $workload = $job->workload();
    $nameId = preg_replace('~files\\/\\d{2}_\\d{2}_\\d{2}\\/~ui',"", $workload);
    $nameId = preg_replace('/[.]\\S*/', "", $nameId);
    $linkForConvert = "public/" . $workload;
    $newLinkForConvert = preg_replace('/[.]\\w*/', '.mp4', $linkForConvert);

    $process = new Process(['ffmpeg', '-i', $linkForConvert, '-q:v', '1', '-c:v', 'h264', $newLinkForConvert]);
    $process->run();

    unlink($linkForConvert);

    $metadata = MediaInfo::getMetadata("video", $newLinkForConvert);
    $size = MediaInfo::getSize($newLinkForConvert);

    $filesTable = new FilesTable($pdo);
    $filesTable->updateMetadata($nameId, $metadata, $size);
}