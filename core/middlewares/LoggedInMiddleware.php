<?php

namespace app\core\middlewares;

use app\core\Application;

class LoggedInMiddleware extends Middleware
{
    public array $actions = [];

    public function __construct()
    {
        $this->actions = ['login', 'register'];
    }

    public function execute()
    {
        // if the user is NOT authorized
        if (Application::$app->auth::isAuthenticated()) {
            // if the given action exists among the action the middleware handles...
            if (empty($this->actions) || in_array(Application::$app->getController()->action, $this->actions)) {
                header('Location: ' . '/');
                exit;
            }
        }
    }
}
