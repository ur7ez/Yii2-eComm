<?php

namespace backend\models;

use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends \common\models\PasswordResetRequestForm
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE, 'is_admin' => 1],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    protected function findUser(): ?User
    {
        return User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
            'is_admin' => 1,
        ]);
    }
}