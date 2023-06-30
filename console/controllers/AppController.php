<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;
use yii\helpers\Console;

class AppController extends Controller
{
    /**
     * @param string $username
     * @param string|null $password
     * @return void
     * @throws \yii\base\Exception
     */
    public function actionCreateAdminUser (string $username, string $password = null): void
    {
        $user = new User();
        $user->username = $username;
        $user->firstname = $username;
        $user->lastname = $username;
        $user->email = $username . '@example.com';
        $password = $password ?: \Yii::$app->security->generateRandomString(8);
        $user->setPassword($password);
        $user->status = User::STATUS_ACTIVE;
        $user->is_admin = 1;
        if ($user->save()) {
            Console::output("Admin user has been successfully created\nUsername: $username\nPassword: $password");
        } else {
            Console::error("User '$username' was not created");
            var_dump($user->errors);
        }
    }
}