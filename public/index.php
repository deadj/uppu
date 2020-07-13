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

$container['db'] = function($c){
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db["host"] . ";dbname=" . $db['dbname'] . ";charset=utf8",
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$container['MainController'] = function($c) {
    $view = $c->get("view");
    $db = $c->get("db");
    return new MainController($view, $db);
};

$container['FileController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    return new FileController($view, $db);
};

$container['ListController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    return new ListController($view, $db);
};

$container['SearchController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    return new SearchController($view, $db);
};

// $container['Helper'] = function($c){
//     return new Helper();
// };

// $container['TusServer'] = function($c){
//     return new TusServer();
// };


$app->get('/', \MainController::class . ':printPage');
$app->get('/search', \SearchController::class . ':search');
$app->get('/list', \ListController::class . ':printPage');
$app->get('/file/{nameId}', \FileController::class . ':printPage');
// $app->get('/tusServer', \Helper::class . ':startTusServer');
// $app->post('/tusServer', \Helper::class . ':startTusServer');
// $app->get('/tusServer', \TusServer::class . ':start');
// $app->post('/tusServer', \TusServer::class . ':start');

$app->get('/tusServer', function(Request $request, Response $response) {
    include 'tusServer.php';
});

$app->post('/tusServer', function(Request $request, Response $response) {
    include 'tusServer.php';
});


$app->post('/addComment', \FileController::class . ':addComment');
$app->post('/getCommentsList', \FileController::class . ":getCommentsList");
$app->post('/', \MainController::class . ':uploadFile');

$app->run();