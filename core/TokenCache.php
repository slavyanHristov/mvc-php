<?php

namespace app\core;

class TokenCache
{
    public function session()
    {
        return Application::$app->auth->session;
    }
    public function storeTokens($accessToken, $user)
    {
        $this->session()->setSession([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
            'userName' => $user->getDisplayName(),
            'userEmail' => $user->getMail() !== null ? $user->getMail() : $user->getUserPrincipalName(),
            'userTimeZone' => $user->getMailboxSettings()->getTimeZone()
        ]);
    }

    public function clearTokens()
    {
        $this->session()->removeSession('accessToken');
        $this->session()->removeSession('refreshToken');
        $this->session()->removeSession('tokenExpires');
        $this->session()->removeSession('userName');
        $this->session()->removeSession('userEmail');
        $this->session()->removeSession('userTimeZone');
    }

    public function getAccessToken()
    {
        // Check if tokens exist
        if (
            empty($this->session()->getSession('accessToken')) ||
            empty($this->session()->getSession('refreshToken')) ||
            empty($this->session()->getSession('tokenExpires'))
        ) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($this->session()->getSession('tokenExpires') <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = GraphHelper::initOAuthClient();

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $this->session()->getSession('refreshToken')
                ]);

                // Store the new values
                $this->updateTokens($newToken);

                return $newToken->getToken();
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        }

        // Token is still valid, just return it
        return $this->session()->getSession('accessToken');
    }

    public function updateTokens($accessToken)
    {
        $this->session()->setSession([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires()
        ]);
    }
}
