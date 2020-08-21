<?php

require_once 'vendor/autoload.php';

$db = include('src/dbConfig.php');
$db = $db['db'];
$pdo = new PDO("mysql:host=" . $db["host"] . ";dbname=" . $db['dbname'] . ";charset=utf8", $db['user'], $db['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$filesTable = new FilesTable($pdo);
$filesList = $filesTable->getErrorFilesList();

$commentsTable = new CommentsTable($pdo);

foreach ($filesList as $file) {
    $commentsTable->deleteListForFile($file->getId());
    unlink('public/' . $file->getLink());
    $filesTable->deleteFile($file->getNameId());
}