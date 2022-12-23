<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\Mail;

class MailController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['inbox', 'sendMail']));
    }

    public function inbox(Request $request, Response $response)
    {
        $mail = new Mail();
        $viewData = $this->loadViewData();

        try {
            $viewData['mails'] = $mail->getMails();
        } catch (\Exception $e) {
            Application::$app->auth->session->setFlash('error', 'Error getting user\'s inbox: ' . $e->getMessage());
            $response->redirect('/');
            exit;
        }

        return $this->render('mail', $viewData);
    }

    public function sendMail(Request $request, Response $response)
    {
        $mail = new Mail();
        if ($request->isPostMethod()) {
            $mail->loadData($request->getPayload());
            if ($mail->validate() && $mail->sendMail()) {
                Application::$app->auth->session->setFlash('success', 'Mail sent successfully!');
                $response->redirect('/');
                exit;
            }
        }
        return $this->render('send_mail', [
            'model' => $mail
        ]);
    }
}
