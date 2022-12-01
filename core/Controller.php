<?php

namespace app\core;

use app\core\middlewares\Middleware;

class Controller
{
    private string $layout = 'main';

    protected array $middlewares = [];
    public string $action = '';

    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }
    public function getLayout(): string
    {
        return $this->layout;
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
