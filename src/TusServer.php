<?php

require '../vendor/autoload.php';

$server = new \TusPhp\Tus\Server();

$uploadPath = "../public/files/" . date("m_d_y");
$server->setUploadDir($uploadPath);

$response = $server->serve();
$response->send();

exit(0);
