<?php

namespace app\modules\mobileapi\controllers;

use Yii;
use app\modules\mobileapi\models\MobileLoginForm;
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
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actionIndex()
    {
        $model = new MobileLoginForm();
        $model->attributes = Yii::$app->request->post();
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
