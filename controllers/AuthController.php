<?php

namespace app\controllers;

use app\models\User;
use app\core\Request;
use app\core\Response;
use app\core\Controller;
use app\core\TokenCache;
use app\core\Application;
use app\core\GraphHelper;
use app\models\LoginUser;
use Microsoft\Graph\Graph;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\LoggedInMiddleware;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
        $this->registerMiddleware(new LoggedInMiddleware());
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

    /**
     * Redirects the user to /authorize endpoint, so
     * they sign-in and grant consent for the permissions that
     * the app uses.
     */
    public function msLogin()
    {
        $oauthClient = GraphHelper::initOAuthClient();

        $authUrl = $oauthClient->getAuthorizationUrl();

        // The getState method returns a state parameter which helps 
        // to detect CSRF attacks against the client
        $this->session()->setSession('oauthState', $oauthClient->getState());

        header('Location: ' . $authUrl);
        return;
    }

    /**
     * After the user signs-in and the consents are granted
     * the Microsoft identity platform returns authorization_code
     * with which we can request access_token and with that access_token
     * we can request resources from the Microsoft Graph API
     */
    public function callback(Request $request, Response $response)
    {
        // Validations performed on the state parameter saved in the session
        $expectedState = $this->session()->getSession('oauthState');
        $this->session()->removeSession('oauthState');
        $providedState = $_GET['state'];

        // if state is not existant return from the function and redirect to root url
        if (!isset($expectedState)) {
            $response->redirect('/');
            return;
        }

        // if state parameter is not present as a query parameter
        // or it doesn't match the state saved in the session
        // return error messages and redirect to root url
        // (this means that an CSRF attack is detected)
        if (!isset($providedState) || $expectedState != $providedState) {
            $this->session()->setFlash('error', 'Invalid auth state');
            $this->session()->setFlash('errorDetail', 'The provided auth state did not match the expected value');
            $response->redirect('/');
            return;
        }

        $authCode = $_GET['code'];

        if (isset($authCode)) {
            $oauthClient = GraphHelper::initOAuthClient();
        }

        try {
            $accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);
            $graph = new Graph();
            $graph->setAccessToken($accessToken->getToken());

            $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
                ->setReturnType(\Microsoft\Graph\Model\User::class)
                ->execute();
            $tokenCache = new TokenCache();
            $tokenCache->storeTokens($accessToken, $user);
            $this->session()->setFlash('success', 'Successfully logged in with your Microsoft account!');
            $response->redirect('/');
            return;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            $this->session()->setFlash('error', 'Error requesting access token');
            $this->session()->setFlash('errorDetail', json_encode($e->getResponseBody()));
            $response->redirect('/');
            return;
        }

        $this->session()->setFlash('error', $_GET['error']);
        $this->session()->setFlash('errorDetail', $_GET['error_description']);
        $response->redirect('/');
        return;
    }

    public function profile()
    {
        return $this->render('profile');
    }
}
