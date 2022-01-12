<?php

namespace app\modules\api;
use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here

        Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\api\models\ApiUser',
            'enableAutoLogin' => false,
            'idParam' => 'id', //this is important !
        ]);
        Yii::$app->user->enableSession = false;
    }
}
