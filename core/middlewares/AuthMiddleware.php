<?php

namespace app\core\middlewares;

use app\core\Application;
use app\core\exceptions\ForbiddenException;

class AuthMiddleware extends Middleware
{
    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        // if the user is NOT authorized
        if (Application::$app->auth::isGuest()) {
            // if the given action exists among the action the middleware handles...
            if (empty($this->actions) || in_array(Application::$app->getController()->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}
