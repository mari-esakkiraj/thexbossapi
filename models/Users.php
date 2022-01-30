<?php

namespace app\models;
use app\webmodels\AudittrailModel;

use Yii;

/**
 * This is the model class for table "{{%users}}".
 *
 * @property integer $id
 * @property string $fullname
 * @property string $email
 * @property string $password
 * @property string $image
 * @property string $path
 * @property integer $company_id
 * @property integer $emp_id
 * @property string $themecolour
 * @property string $status
 * @property string $mobile
 * @property string $access_token
 * @property string $createddate
 * @property string $updateddate
 * @property integer $createdby
 * @property integer $updatedby
 */
class Users extends \yii\db\ActiveRecord
{
    public static $userRole = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'status', 'mobile'], 'safe'],
            [[ 'createdby', 'updatedby'], 'integer'],
            [['createddate', 'updateddate','role'], 'safe'],
            [['fullname', 'email'], 'string', 'max' => 100],
            [['access_token'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullname' => 'Fullname',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'role',
            'mobile' => 'mobile',
            'status' => 'Status',
            'access_token' => 'Access Token',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
            'createdby' => 'Createdby',
            'updatedby' => 'Updatedby',
        ];
    }

    public function getAuthname()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id'])->with(['itemName']);
    }

    public function getAudittrail()
    {
        return $this->hasOne(AudittrailModel::className(),['user_id'=>'id']);
    }

    public function getAuthAssignment()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public static function getUserRole($id)
    {
        if(empty(self::$userRole) || !isset(self::$userRole[$id])){
            $userRoleId = Yii::$app->authManager->getRolesByUser($id);
            self::$userRole[$id] = key($userRoleId);
        }
        return self::$userRole[$id];
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function add()
    {
        //$this->status = 1;
        $this->createddate = date('Y-m-d H:i:s');
        $this->updateddate = date('Y-m-d H:i:s');
        $this->createdby = 1;
        $this->updatedby = 1;
        $this->status = 1;
        $this->role = "User";
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->access_token = Yii::$app->security->generateRandomString();
        $email = $this->email;
        $replyEmail ="info@healthbeautybank.com";;
        if($this->save()){
            $subject = "Signup Form";
            $body = "Thank you for your Signup for healthbeautybank.com";
            Yii::$app->mailer->compose()
                    ->setTo($email)
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setReplyTo([$replyEmail => $replyEmail])
                    ->setSubject($subject)
                    ->setTextBody($body)
                    ->send();
            return true;
        }else{
            print_r($this->getErrors());
            exit;
        }
    }
}
