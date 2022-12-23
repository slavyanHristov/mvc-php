<?php

use app\controllers\SiteController;
use app\controllers\AuthController;
use app\controllers\MailController;
use app\controllers\TodoController;
use app\core\Application;

require_once __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(['DB_DSN', 'DB_USER', 'DB_PASSWORD', 'CLIENT_ID', 'TENANT_ID', 'GRAPH_USER_SCOPES']);


$config = [
    'userClass' =>  \app\models\User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
    'msGraph' => [
        'clientId' => $_ENV['OAUTH_APP_ID'],
        'clientSecret' => $_ENV['OAUTH_APP_SECRET'],
        'redirectUri' => $_ENV['OAUTH_REDIRECT_URI'],
        'urlAuthorize' => $_ENV['OAUTH_AUTHORITY'] . $_ENV['OAUTH_AUTHORIZE_ENDPOINT'],
        'urlAccessToken' => $_ENV['OAUTH_AUTHORITY'] . $_ENV['OAUTH_TOKEN_ENDPOINT'],
        'scopes' => $_ENV['OAUTH_SCOPES'],
    ]
];

$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);

$app->router->get('/contact', [SiteController::class, 'contact']);
$app->router->post('/contact', [SiteController::class, 'contact']);

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);

$app->router->get('/ms-login', [AuthController::class, 'msLogin']);
$app->router->get('/callback', [AuthController::class, 'callback']);


$app->router->get('/logout', [AuthController::class, 'logout']);
$app->router->get('/profile', [AuthController::class, 'profile']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);

$app->router->get('/mails', [MailController::class, 'inbox']);

$app->router->get('/mails/send', [MailController::class, 'sendMail']);
$app->router->post('/mails/send', [MailController::class, 'sendMail']);

$app->router->get('/todos', [TodoController::class, 'getTodos']);

$app->router->get('/todos/create', [TodoController::class, 'createTodo']);
$app->router->post('/todos/create', [TodoController::class, 'createTodo']);

$app->router->get('/todos/update', [TodoController::class, 'markAsCompleted']);
$app->router->get('/todos/delete', [TodoController::class, 'delete']);


$app->run();
