<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\models\UserModel;

class AuthController extends Controller
{
    public function login()
    {
        $this->setLayout('auth');
        return $this->render('login');
    }
    public function register(Request $request)
    {
        $userModel = new UserModel();
        if ($request->isPostMethod()) {
            $userModel->loadData($request->getPayload());

            if ($userModel->validate() && $userModel->register()) {
                return 'Success';
            }

            return $this->render('register', [
                'model' => $userModel
            ]);
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $userModel
        ]);
    }
}
