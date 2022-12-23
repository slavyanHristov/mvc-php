<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\Todo;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware([
            'getTodos',
            'createTodo',
            'markAsCompleted',
            'delete'
        ]));
    }

    public function getTodos(Request $request, Response $response)
    {
        $todo = new Todo();

        $viewData = $this->loadViewData();
        try {
            $viewData['todos'] = $todo->getTodos();
        } catch (\Exception $e) {
            Application::$app->auth->session->setFlash('error', 'Error getting user\'s todos: ' . $e->getMessage());
            $response->redirect('/');
            exit;
        }

        return $this->render('todos', $viewData);
    }

    public function createTodo(Request $request, Response $response)
    {
        $todo = new Todo();
        if ($request->isPostMethod()) {
            $todo->loadData($request->getPayload());
            if ($todo->validate() && $todo->createTodo()) {
                Application::$app->auth->session->setFlash('success', 'Todo added successfully!');
                $response->redirect('/todos');
                exit;
            }
        }

        return $this->render('create_todo', [
            'model' => $todo
        ]);
    }

    public function markAsCompleted(Request $request, Response $response)
    {
        $todoId = $_GET['todoId'];
        if (!isset($todoId)) {
            Application::$app->auth->session->setFlash('error', 'No ToDo id given!');
            $response->redirect('/todos');
            exit;
        }
        $todo = new Todo();
        if ($todo->markAsCompleted($todoId)) {
            Application::$app->auth->session->setFlash('success', 'Given todo marked as completed!');
            $response->redirect('/todos');
            exit;
        }

        Application::$app->auth->session->setFlash('error', 'Couldn\'t update the task!');
        $response->redirect('/todos');
        exit;
    }

    public function delete(Request $request, Response $response)
    {
        $todoId = $_GET['todoId'];
        if (!isset($todoId)) {
            Application::$app->auth->session->setFlash('error', 'No ToDo id given!');
            $response->redirect('/todos');
            exit;
        }
        $todo = new Todo();

        if ($todo->delete($todoId)) {
            Application::$app->auth->session->setFlash('success', 'Given todo marked as completed!');
            $response->redirect('/todos');
            exit;
        }

        Application::$app->auth->session->setFlash('error', 'Couldn\'t delete the task!');
        $response->redirect('/todos');
        exit;
    }
}
