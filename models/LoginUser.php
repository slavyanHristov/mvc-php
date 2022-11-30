<?php

namespace app\models;

use app\core\Application;
use app\core\Model;

class LoginUser extends Model
{
    public string $email = '';
    public string $password = '';

    public function login(): bool
    {
        $user = User::findOne(['email' => $this->email]);
        if (!$user) {
            $this->addError('email', "User doesn't exist with this email");
            return false;
        }
        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', "Password is incorrect");
            return false;
        }
        return Application::$app->auth->login($user);
    }

    public function labels(): array
    {
        return [
            'email' => 'Email Address',
            'password' => 'Password',
        ];
    }

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED]
        ];
    }
}
