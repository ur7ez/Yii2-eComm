<?php

namespace backend\controllers;

use backend\models\LoginForm;
use common\models\Order;
use common\models\OrderItem;
use common\models\User;
use backend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'forgot-password', 'reset-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $totalEarnings = Order::find()->paid()->sum('total_price');
        $totalOrders = Order::find()->paid()->count();
        $totalProducts = OrderItem::find()
            ->alias('oi')
            ->innerJoin(Order::tableName() . ' o', 'o.id = oi.order_id')
            ->andWhere(['o.status' => [Order::STATUS_PAID, Order::STATUS_COMPLETED]])
            ->sum('quantity');
        $totalUsers = User::find()->andWhere(['status' => User::STATUS_ACTIVE])->count();

        $orders = Order::findBySql("
                    SELECT
                        CAST(DATE_FORMAT(FROM_UNIXTIME(o.created_at), '%Y-%m-%d %H:%i:%s') as DATE) AS `date`
                        , SUM(o.total_price) AS `total_price`
                    FROM orders o
                    WHERE o.status IN (:status_paid, :status_completed)
                    GROUP BY CAST(DATE_FORMAT(FROM_UNIXTIME(o.created_at), '%Y-%m-%d %H:%i:%s') as DATE)
                    ORDER BY o.created_at;
            ", ['status_paid' => Order:: STATUS_PAID, 'status_completed' => Order::STATUS_COMPLETED])
            ->asArray()
            ->all();

        // line Chert data:
        $labels = $earningsData = [];
        if (!empty($orders)) {
            $orderByPriceMap = ArrayHelper::map($orders, 'date', 'total_price');
            $minDate = $orders[0]['date'];
            $dateFrom = new \DateTime($minDate);
            $dateTo = new \DateTime();
            while ($dateFrom->getTimestamp() < $dateTo->getTimestamp()) {
                $labels[] = $dateFrom->format('d.m.Y');
                $earningsData[] = (float)($orderByPriceMap[$dateFrom->format('Y-m-d')] ?? 0);
                $dateFrom->setTimestamp($dateFrom->getTimestamp() + 86400);
            }
        }

        // Pie Chart data:
        $ctyData = Order::findBySql("
                SELECT oa.country, SUM(o.total_price) AS `total_price`
                FROM orders o
                    JOIN order_addresses oa on o.id = oa.order_id
                WHERE o.status IN (:status_paid, :status_completed)
                GROUP BY oa.country
                ORDER BY oa.country;
        ", ['status_paid' => Order:: STATUS_PAID, 'status_completed' => Order::STATUS_COMPLETED])
            ->asArray()
            ->all();

        $countries = ArrayHelper::getColumn($ctyData, 'country');
        $countriesData = ArrayHelper::getColumn($ctyData, 'total_price');
        $colorOptions = [
            'bg' => ['#4e73df', '#1cc88a', '#36b9cc'],
            'hover' => ['#2e59d9', '#17a673', '#2c9faf'],
        ];
        $bgColors = $hoverBgColors = [];
        foreach ($countries as $i => $country) {
            $randColor = 'rgb(' . random_int(0, 255) . ', ' . random_int(0, 255) . ', ' . random_int(0, 255) . ')';
            $bgColors[] = $colorOptions['bg'][$i] ?? $randColor;
            $hoverBgColors[] = $colorOptions['hover'][$i] ?? $randColor;
        }
        return $this->render('index', compact(
            'totalEarnings', 'totalOrders', 'totalProducts', 'totalUsers',  // dashboard carts
            'earningsData', 'labels',  // dashboard line Chart
            'countries', 'countriesData', 'bgColors', 'hoverBgColors'  // dashboard Pie Chart
        ));
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!isGuest()) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        $this->layout = 'blank';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }
        return $this->render('forgot_password', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'blank';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
