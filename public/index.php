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
        return $c->view->render($response, '404.html')->withStatus(404);
    };
};

$getPreviewLinkFunction = new Twig_SimpleFunction('getPreviewLink', function(string $link, string $nameId){
    $extension = pathinfo($link, PATHINFO_EXTENSION);

    if ($extension != "png" && $extension != "gif" && $extension != "jpeg") {
        $extension = "jpeg";
    } 

    return "files/imgPreviews/$nameId.$extension"; 
});

$container->get('view')->getEnvironment()->addFunction($getPreviewLinkFunction);

$container['FilesTable'] = function($c){
    $db = $c->get('db');
    return new FilesTable($db);
};

$container['CommentsTable'] = function($c){
    $db = $c->get('db');
    return new CommentsTable($db);
};

$container['SphinxSearch'] = function($c){
    $db = $c->get('db');
    return new SphinxSearch();
};

$container['GearmanCLient'] = function($c){
    $db = $c->get('db');
    return new GearmanCLient();
};

$container['MainController'] = function($c) {
    $view = $c->get("view");
    $db = $c->get("db");
    $sphinxSearch = $c->get('SphinxSearch');
    $filesTable = $c->get('FilesTable');
    $gearmanCLient = $c->get('GearmanCLient');
    $helper = $c->get('Helper'); 

    return new MainController(
        $view, 
        $db, 
        $sphinxSearch, 
        $filesTable, 
        $gearmanCLient,
        $helper
    );
};

$container['FileController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    $filesTable = $c->get("FilesTable");
    $commentsTable = $c->get("CommentsTable");

    return new FileController($view, $db, $filesTable, $commentsTable);
};

$container['ListController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    $filesTable = $c->get('FilesTable');

    return new ListController($view, $db, $filesTable);
};

$container['SearchController'] = function($c){
    $view = $c->get("view");
    $db = $c->get("db");
    $sphinxSearch = $c->get('SphinxSearch');
    $filesTable = $c->get('FilesTable');

    return new SearchController($view, $db, $sphinxSearch, $filesTable);
};

$container['Helper'] = function($c){
    $db = $c->get("db");
    return new Helper($db);
};

$app->get('/[notify={notify}]', \MainController::class . ':printPage');
$app->get('/search', \SearchController::class . ':search');
$app->get('/list', \ListController::class . ':printPage');
$app->get('/file/{nameId}[/notify={notify}]', \FileController::class . ':printPage');
$app->post('/addComment', \FileController::class . ':addComment');
$app->post('/getCommentsList', \FileController::class . ":getCommentsList");
$app->post('/', \MainController::class . ':uploadFile');

$app->run();