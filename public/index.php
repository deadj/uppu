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

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render($response, '404.html');
    };
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

$container['Helper'] = function($c){
    $db = $c->get("db");
    return new Helper($db);
};


$app->get('/', \MainController::class . ':printPage');
$app->get('/search', \SearchController::class . ':search');
$app->get('/list', \ListController::class . ':printPage');
$app->get('/file/{nameId}', \FileController::class . ':printPage');
$app->get('/deleteErrorFiles', \Helper::class . ':deleteErrorFiles');
$app->post('/addComment', \FileController::class . ':addComment');
$app->post('/getCommentsList', \FileController::class . ":getCommentsList");
$app->post('/', \MainController::class . ':uploadFile');

$app->run();