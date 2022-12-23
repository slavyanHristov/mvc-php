<?php

namespace app\core;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Http;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;

class GraphHelper
{
    private static Client $tokenClient;
    private static string $clientId = '';
    private static string $tenantId = '';
    private static string $graphUserScopes = '';
    private static Graph $userClient;
    private static string $userToken;

    public static function initializeGraphForUserAuth(array $config): void
    {
        GraphHelper::$tokenClient = new Client();
        GraphHelper::$clientId = $config['client_id'];
        GraphHelper::$tenantId = $config['tenant_id'];
        GraphHelper::$graphUserScopes = $config['graph_user_scopes'];
        GraphHelper::$userClient = new Graph();
    }

    public static function getUserToken(): string
    {
        if (isset(GraphHelper::$userToken)) {
            return GraphHelper::$userToken;
        }

        $deviceCodeRequestUrl = 'https://login.microsoftonline.com/' . GraphHelper::$tenantId . '/oauth2/v2.0/devicecode';
        $tokenRequestUrl = 'https://login.microsoftonline.com/' . GraphHelper::$tenantId . '/oauth2/v2.0/token';

        $deviceCodeResponse = json_decode(GraphHelper::$tokenClient->post($deviceCodeRequestUrl, [
            'form_params' => [
                'client_id' => GraphHelper::$clientId,
                'scope' => GraphHelper::$graphUserScopes
            ]
        ])->getBody()->getContents());

        echo $deviceCodeResponse->message . PHP_EOL;

        $interval = (int)$deviceCodeResponse->interval;
        $device_code = $deviceCodeResponse->device_code;


        while (true) {
            sleep($interval);

            $tokenResponse = GraphHelper::$tokenClient->post($tokenRequestUrl, [
                'form_params' => [
                    'client_id' => GraphHelper::$clientId,
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                    'device_code' => $device_code
                ],
                'http_errors' => false,
                'curl' => [
                    CURLOPT_FAILONERROR => false
                ]
            ]);

            if ($tokenResponse->getStatusCode() == 200) {
                $responseBody = json_decode($tokenResponse->getBody()->getContents());

                GraphHelper::$userToken = $responseBody->access_token;

                return $responseBody->access_token;
            } else if ($tokenResponse->getStatusCode() == 400) {
                $responseBody = json_decode($tokenResponse->getBody()->getContents());
                if (isset($responseBody->error)) {
                    $error = $responseBody->error;

                    if (strcmp($error, 'authorization_pending') != 0) {
                        throw new \Exception('Token endpoint returned ' . $error, 100);
                    }
                }
            }
        }
    }

    public static function getUser(): Model\User
    {
        $token = GraphHelper::getUserToken();
        GraphHelper::$userClient->setAccessToken($token);   // sets the access_token to authorization header

        // MS Graph request to get authenticated user info
        return GraphHelper::$userClient->createRequest('GET', '/me?$select=displayName,mail,userPrincipalName')
            ->setReturnType(Model\User::class)
            ->execute();
    }
}
