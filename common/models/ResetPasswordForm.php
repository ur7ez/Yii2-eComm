<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var User
     */
    protected $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(Yii::t('app', 'Password reset token cannot be blank.'));
        }
        $this->_user = $this->findUser($token);
        if (!$this->_user) {
            throw new InvalidArgumentException(Yii::t('app', 'Wrong password reset token.'));
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => param('user.passwordMinLength')],
        ];
    }

    /**
     * Resets password.
     * @return bool if password was reset.
     * @throws Exception
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        // $user->generateAuthKey();
        return $user->save(false);
    }

    protected function findUser($token)
    {
        return User::findByPasswordResetToken($token);
    }
}