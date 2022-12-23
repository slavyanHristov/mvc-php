<?php

namespace app\core;

class Authentication
{
    public string $userClass;
    public array $msConfig;
    private static Authentication $auth;
    public ?UserModel $user = null;
    public Session $session;

    public function __construct(string $userClass, array $msGraph)
    {
        self::$auth = $this;
        $this->userClass = $userClass;
        $this->session = new Session();

        if (class_exists($this->userClass)) {
            $primaryValue = $this->session->getSession('user');
            if ($primaryValue) {
                $primaryKey = $this->userClass::primaryKey();
                $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
            }
        }

        if (isset($msGraph)) {
            $this->msConfig = $msGraph;
        }
    }

    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->setSession('user', $primaryValue);

        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->removeSession('user');
        $tokenCache = new TokenCache();
        $tokenCache->clearTokens();
    }

    public static function isGuest()
    {
        return !self::$auth->user && empty(self::$auth->session->getSession('userName'));
    }

    public static function isAuthenticated()
    {
        return isset(self::$auth->user) || self::$auth->session->getSession('userName');
    }
}
