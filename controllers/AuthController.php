<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\LoginUser;
use app\models\User;

class AuthController extends Controller
{
    public function login(Request $request, Response $response)
    {
        $loginUser = new LoginUser();
        if ($request->isPostMethod()) {
            $loginUser->loadData($request->getPayload());
            if ($loginUser->validate() && $loginUser->login()) {
                $response->redirect("/");
                return;
            }
        }
        $this->setLayout('auth');
        return $this->render('login', [
            "model" => $loginUser
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        Application::$app->auth->logout();
        $response->redirect("/");
    }

    public function register(Request $request, Response $response)
    {
        $user = new User();
        if ($request->isPostMethod()) {
            $user->loadData($request->getPayload());

            if ($user->validate() && $user->save()) {
                Application::$app->auth->session->setFlash('success', 'Thanks for registering!');
                $response->redirect('/');
                exit;
            }

            return $this->render('register', [
                'model' => $user
            ]);
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $user
        ]);
    }
}
