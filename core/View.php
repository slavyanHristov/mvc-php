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
        if (Application::$app->getController()) {
            // set the layoutName to the layout of the given controller
            $layoutName = Application::$app->getController()->getLayout();
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
        /**
         * extract($array) => turns key/value pairs from an array into variables.
         * ex. $arr = ["name" => "Stoyan", "age" => 15]
         * extract($arr) => $name = "Stoyan"; $age = 15;
         */
        extract($params); // When the array is extracted, we can use the variables in the included file.

        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}
