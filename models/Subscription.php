<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['email'], 'required'],
            [['createddate', 'updateddate', 'status'], 'safe'],
            [['email'], 'string', 'max' => 255],
            //[['status'], 'string', 'max' => 3],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'status' => 'Status',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
        ];
    }

    public function add()
    {
        $this->status = 1;
        $this->createddate = date('Y-m-d H:i:s');
        $this->updateddate = date('Y-m-d H:i:s');
        $email = $this->email;
        $replyEmail ="info@healthbeautybank.com";
        if($this->save()){
            $subject = "Subscription Form";
            $body = "Thank you for your Subscription for healthbeautybank.com";
            Yii::$app->mailer->compose()
                    ->setTo($email)
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setReplyTo([$replyEmail => $replyEmail])
                    ->setSubject($subject)
                    ->setTextBody($body)
                    ->send();
            return true;
        }else{
            var_dump($this->getErrors());exit;
        }
    }
}
