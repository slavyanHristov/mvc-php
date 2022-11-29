<?php

namespace app\core;

class Controller
{
    private string $layout = 'main';

    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }
    public function getLayout(): string
    {
        return $this->layout;
    }

    public function render($view, $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }
}
