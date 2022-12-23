<?php

namespace app\models;

use app\core\GraphHelper;
use app\core\Model;

class Todo extends Model
{
    public string $title = '';

    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED]
        ];
    }

    public function labels(): array
    {
        return [
            'title' => 'ToDo Title'
        ];
    }

    public function getTodos()
    {
        $graph = GraphHelper::getGraph();
        // $queryParams = array(
        //     '$top' => 25
        // );

        $todosUrl = '/me/todo/lists/' . $_ENV['TODO_TASK_LIST_ID'] . '/tasks';
        // . http_build_query($queryParams);


        $todos = $graph->createRequest('GET', $todosUrl)
            ->setReturnType(\Microsoft\Graph\Model\TodoTask::class)
            ->execute();

        return $todos;
    }

    public function createTodo(): bool
    {
        $graph = GraphHelper::getGraph();
        $todosUrl = '/me/todo/lists/' . $_ENV['TODO_TASK_LIST_ID'] . '/tasks';

        $createTodoBody = array(
            'title' => $this->title
        );

        $graph->createRequest('POST', $todosUrl)
            ->attachBody($createTodoBody)
            ->execute();

        return true;
    }

    public function markAsCompleted($todoId): bool
    {
        $graph = GraphHelper::getGraph();

        $todosUrl = '/me/todo/lists/' . $_ENV['TODO_TASK_LIST_ID'] . '/tasks/' . $todoId;

        $createTodoBody = array(
            'status' => 'completed'
        );

        $graph->createRequest('PATCH', $todosUrl)
            ->attachBody($createTodoBody)
            ->execute();

        return true;
    }

    public function delete($todoId): bool
    {
        $graph = GraphHelper::getGraph();

        $todosUrl = '/me/todo/lists/' . $_ENV['TODO_TASK_LIST_ID'] . '/tasks/' . $todoId;
        try {
            $graph->createRequest('DELETE', $todosUrl)
                ->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }
}
