<?php
namespace backend\models;

use common\models\User;

class ResetPasswordForm extends \common\models\ResetPasswordForm
{
    /**
     * @param string $token
     * @return User|null
     */
    public function findUser ($token)
    {
        if (!User::isPasswordResetTokenValid($token)) {
            return null;
        }
        return User::findOne([
            'password_reset_token' => $token,
            'status' => User::STATUS_ACTIVE,
            'is_admin' => 1,
        ]);
    }
}