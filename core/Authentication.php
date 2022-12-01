<?php

namespace app\core;


class Authentication
{
    public string $userClass;
    private static Authentication $auth;
    public ?UserModel $user = null;
    public Session $session;

    public function __construct(string $userClass)
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
    }

    public static function isGuest()
    {
        return !self::$auth->user;
    }
}
