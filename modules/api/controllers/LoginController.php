<?php

namespace app\modules\api\controllers;

use Yii;
use app\modules\api\models\ApiLoginForm;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\User;
class LoginController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'][] = 'login';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function beforeAction($action) { 
        //Yii::$app->controller->enableCsrfValidation = false; 
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        return true;
    }

    public function actionIndex()
    {
        $model = new ApiLoginForm();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->login()) {
            $user = Yii::$app->user->identity;
            return $user;
        } else { 
           throw new ForbiddenHttpException("Invalid Email or Password");
        }
    }

    public function actionLogout()
    {
        if(Yii::$app->user->logout()){
            return ["logout"=>true];
        }else{
            throw new ForbiddenHttpException("Something went wrong, Try again");
        }
    }

  
}
