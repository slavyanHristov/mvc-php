<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\ContactForm;

class SiteController extends Controller
{

    public function home()
    {
        $viewData = $this->loadViewData();

        return $this->render('home', $viewData);
    }

    public function contact(Request $request, Response $response)
    {
        $contact = new ContactForm();
        if ($request->isPostMethod()) {
            $contact->loadData($request->getPayload());
            if ($contact->validate() && $contact->send()) {
                Application::$app->auth->session->setFlash('success', 'Thanks for contacting us. We will get back to you soon!');
                return $response->redirect('/contact');
            }
        }
        return $this->render('contact', [
            'model' => $contact
        ]);
    }
    public function handleContact(Request $request)
    {
        $body = $request->getPayload();
        echo '<pre>';
        var_dump($body);
        echo '</pre>';
        exit;
    }
}
