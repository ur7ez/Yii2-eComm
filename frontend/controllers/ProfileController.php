<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use yii\filters\AccessControl;
use frontend\base\Controller;
use yii\web\ForbiddenHttpException;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update-address', 'update-account'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $userAddress =  $user->getAddress();
        return $this->render('index', [
            'user' => $user,
            'userAddress' => $userAddress,
        ]);
    }

    public function actionUpdateAddress()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('You are only allowed to make ajax requests');
        }
        $user = Yii::$app->user->identity;
        $userAddress = $user->getAddress();
        $success = false;
        if ($userAddress->load(Yii::$app->request->post()) && $userAddress->save()) {
            $success = true;
        }
        return $this->renderAjax('user_address', [
            'userAddress' => $userAddress,
            'success' => $success,
        ]);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionUpdateAccount()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('You are only allowed to make ajax requests');
        }
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->scenario = User::SCENARIO_UPDATE;
        $success = false;
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            $success = true;
        }
        return $this->renderAjax('user_account', [
            'user' => $user,
            'success' => $success,
        ]);
    }
}