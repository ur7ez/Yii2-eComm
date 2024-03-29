<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


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
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t('app', 'There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        $user = $this->findUser();
        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([param('supportEmail') => Yii::$app->name . ' ' . Yii::t('app', 'robot')])
            ->setTo($this->email)
            ->setSubject(Yii::t('app', 'Password reset for') . ' ' . Yii::$app->name)
            ->send();
    }

    /**
     * @return User|null
     */
    protected function findUser(): ?User
    {
        return User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
    }
}