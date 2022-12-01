<?php

namespace app\core;

use app\core\db\Database;

class Application
{
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public string $layout = 'main';
    public static Application $app;
    private ?Controller $controller = null;
    public Database $db;
    public Authentication $auth;
    public View $view;

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = Database::getInstance($config['db']);
        $this->auth = new Authentication($config['userClass']);
        $this->view = new View();
    }

    public function getController(): ?Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        }
    }
}
