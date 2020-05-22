<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';

$app = new \Slim\App(["settings" => include('../src/dbConfig.php')]);
$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../src/Templates/', [
            'cache' => false
    ]);

    return $view;
};

$container['db'] = include('../src/dbConnect.php');

$app->get('/search', function(Request $request, Response $response){
    $searchController = new SearchController($this->view, $request, $response, $this->db);
    $searchController->search();
});

$app->get('/', function(Request $request, Response $response) {
    $mainController = new MainController($this->view, $request, $response, $this->db);
    $mainController->printPage();
});

$app->get('/list', function(Request $request, Response $response){
    $listController = new ListController($this->view, $request, $response, $this->db);
    $listController->printPage();
});

$app->get('/{nameId}', function(Request $request, Response $response, $args){
    $fileController = new FileController($this->view, $request, $response, $this->db);
    $fileController->printPage($args['nameId']);
});


$app->post('/addComment', function (Request $request, Response $response){
    $data = $request->getParsedBody();
    
    $fileController = new FileController($this->view, $request, $response, $this->db);
    $fileController->addComment($data['fileId'], $data['comment']);

    return "done";
});

$app->post('/getCommentsList', function (Request $request, Response $response){
    $data = $request->getParsedBody();

    $fileController = new FileController($this->view, $request, $response, $this->db);

    return $response->withJson($fileController->getCommentsList($data['fileId']));
});

//Перенаправление можно сделать из Slim (withRedirect)
$app->post('/', function(Request $request, Response $response){
    $mainController = new MainController($this->view, $request, $response, $this->db);
    // return $mainController->uploadFile();
    $response->getBody()->write($mainController->uploadFile());

    return $response;
});




$app->run();
