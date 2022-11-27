<?php

namespace app\core;

class View
{
    public string $title = '';

    public function renderView($view, array $params)
    {
        // get the default layout from app class
        $layoutName = Application::$app->layout;

        // if the endpoint is handled by controller method
        if (Application::$app->controller) {
            // set the layoutName to the layout of the given controller
            $layoutName = Application::$app->controller->layout;
        }
        $viewContent = $this->renderViewOnly($view, $params);
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layoutName.php";
        $layoutContent = ob_get_clean();
        // Replaces all occurances of '{{content}} with $viewContent inside $layoutContent'
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderViewOnly($view, array $params)
    {
        extract($params);
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}
