<?php

namespace app\core;

use app\core\middlewares\Middleware;

class Controller
{
    private string $layout = 'main';

    protected array $middlewares = [];
    public string $action = '';


    protected function session()
    {
        return Application::$app->auth->session;
    }

    protected function getConfig()
    {
        return Application::$app->auth->msConfig;
    }

    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }
    public function getLayout(): string
    {
        return $this->layout;
    }

    public function loadViewData()
    {
        $viewData = [];

        if ($this->session()->getFlash('error')) {
            $viewData['error'] = $this->session()->getFlash('error');
            $viewData['errorDetail'] =  $this->session()->getFlash('errorDetail');
        }

        if ($this->session()->getSession('userName')) {
            $viewData['userName'] = $this->session()->getSession('userName');
            $viewData['userEmail'] = $this->session()->getSession('userEmail');
            $viewData['userTimeZone'] = $this->session()->getSession('userTimeZone');
            $viewData['name'] = $viewData['userName'];
        } else if (Application::$app->auth->user) {
            $dbUserName = Application::$app->auth->user->getDisplayName();
            $viewData['name'] = $dbUserName;
        } else {
            $viewData['name'] = 'Guest';
        }

        return $viewData;
    }

    public function getMiddlewares(): ?array
    {
        return $this->middlewares;
    }

    public function render($view, $params = []): string
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }
}
