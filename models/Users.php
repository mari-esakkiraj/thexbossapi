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
            [['password', 'image', 'path', 'status'], 'string'],
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
            'image' => 'Image',
            'path' => 'Path',
            'company_id' => 'Company ID',
            'emp_id' => 'Emp ID',
            'themecolour' => 'Themecolour',
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
}
