<?php

namespace app\core;

class Router
{
    // all routes in the app
    protected array $routes = [];
    public Request $request;
    public Response $response;
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    // Creates a route which accepts requests with GET method on specified path
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    // Creates a route which accepts requests with POST method on specified path
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    // On entered URL this method executes
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();


        /**
         *  The '??' operator returns the first operand if it exists
         *  and is not NULL; otherwise it returns it's second operand
         *  The equivalent to $callback = $this->routes[$method][$path] ?? false; is:
         *  $callback = isset($this->routes[$method][$path]) ? $this->routes[$method][$path] : false;
         */
        $callback = $this->routes[$method][$path] ?? false;

        /**
         * If callback is equal to FALSE => render the _404.php page
         *  and set the response's status code to 404
         */
        if (!$callback) {
            $this->response->setStatusCode(404);
            return $this->renderView("_404");
        }

        /**
         * if the callback variable is a string
         * that means that the argument given references page name
         * if so, render the page
         */
        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        /**
         * if the callback variable is array
         * that means that the argument is a method within a Controller class
         * which executes specific logic
         */
        if (is_array($callback)) {
            // create instance of the controller passed in the array
            $controller = new $callback[0];
            Application::$app->controller = $controller;

            // Set the first element of callback to be object instead of string
            $callback[0] = $controller;
        }

        /**
         * call_user_func() => calls the callback given by the first parameter 
         * and passes the remaining parameters as arguments.
         * We are using this function because we don't know explicitly
         *  what function and from which class would be called
         */
        return call_user_func($callback, $this->request);   // $callback => [Class, 'methodName']
    }

    // replaces the placeholder({{content}}) of the layout with the given view content
    public function renderView($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }


    // Gets the content of the given view
    public function renderViewOnly($view, $params = [])
    {
        return Application::$app->view->renderViewOnly($view, $params);
    }
}
