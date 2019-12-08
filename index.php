<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App(["settings" => include('src/dbConfig.php')]);
$container = $app->getContainer();

$container['view'] = new \Slim\Views\PhpRenderer("src/Templates/");
$container['db'] = include ('src/dbConnect.php');


$app->get('/', function(Request $request, Response $response) {
	$mainController = new Main($this->view, $this->response);
    $mainController->printPage();
});

$app->get('/{fileId}', function(Request $request, Response $response, $args){
	$fileController = new File($this->view, $this->response);
	$fileController->printPage($args['fileId']);
});
 

$app->run();