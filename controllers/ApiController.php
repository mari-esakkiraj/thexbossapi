<?php 

namespace app\controllers;

use yii\rest\ActiveController;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\api\models\ApiLoginForm;
use yii\web\ForbiddenHttpException;

class ApiController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionApilogin()
    {
        $model = new ApiLoginForm();
        //$model->attributes = Yii::$app->request->post();
        $request = Yii::$app->request;
        $params = $request->bodyParams;
        $model->attributes = $params;
        var_dump($_request);
        if ($model->login()) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'id' => 1
            ];
        } else {
            throw new ForbiddenHttpException("Invalid Email or Password");
        }
    }
}

?>