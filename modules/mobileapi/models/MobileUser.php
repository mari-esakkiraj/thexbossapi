<?php

namespace app\modules\mobileapi\models;

use app\models\Users;


class MobileUser extends Users implements \yii\web\IdentityInterface {

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status'=>'1']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => '1']);
    }

    /**
     * Finds user by email
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByUsername($email)
    {
        return self::find()->where(['email'=>$email])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function findByEmail($email)
    {
        return static::find()
            ->where(['email' => $email, 'status'=>'1'])
            ->one();
    }

    public function setAccestoken()
    {
        $this->access_token = substr(md5(date(strtotime('now')).rand()), 0,30);
        $this->save();
        return $this->access_token;
    }

    public function getAccestoken()
    {
        return $this->access_token;
    }

}
