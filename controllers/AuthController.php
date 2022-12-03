<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\LoginUser;
use app\models\User;
use app\core\middlewares\AuthMiddleware;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

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
                /**
                 *  On successful registration create a session of type flashMessages, 
                 *  which notifies for successful registration on the next page (redirect)
                 */
                Application::$app->auth->session->setFlash('success', 'Thanks for registering!');
                $response->redirect('/');
                // exit calls the destructor 
                exit;
            }
            // render the given view and pass data with key of model
            return $this->render('register', [
                'model' => $user
            ]);
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $user
        ]);
    }

    public function profile()
    {
        return $this->render('profile');
    }
}
