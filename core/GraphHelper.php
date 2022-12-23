<?php

namespace app\core;

use app\core\TokenCache;
use Microsoft\Graph\Graph;

class GraphHelper
{
    public static function initOAuthClient()
    {
        $msConfig = Application::$app->auth->msConfig;

        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $msConfig['clientId'],
            'clientSecret' => $msConfig['clientSecret'],
            'redirectUri' => $msConfig['redirectUri'],
            'urlAuthorize' => $msConfig['urlAuthorize'],
            'urlAccessToken' => $msConfig['urlAccessToken'],
            'urlResourceOwnerDetails' => '',
            'scopes' => $msConfig['scopes']
        ]);
    }

    public static function getGraph(): Graph
    {

        // Get access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        return $graph;
    }
}
