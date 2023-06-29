<?php
namespace backend\models;
use common\models\User;

/**
 * Login form for backend user
 */
class LoginForm extends \common\models\LoginForm
{
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne([
                'username' => $this->username,
                'status' => User::STATUS_ACTIVE,
                'is_admin' => 1,
            ]);
        }

        return $this->_user;
    }
}
