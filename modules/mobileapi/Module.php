<?php

namespace app\modules\mobileapi;
use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\mobileapi\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here

        Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\mobileapi\models\MobileUser',
            'enableAutoLogin' => false,
            'idParam' => 'id', //this is important !
        ]);
        Yii::$app->user->enableSession = false;
    }
}
