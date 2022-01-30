<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string|null $title
 * @property int $category_id
 * @property int $sub_category_id
 * @property string|null $filename
 * @property string $content
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'content', 'sub_category_id'], 'required'],
            [['category_id','status'], 'integer'],
            [['content'], 'string'],
            [['createddate', 'updateddate'], 'safe'],
            [['title', 'filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'category_id' => 'Category ID',
            'filename' => 'Filename',
            'content' => 'Content',
            'status' => 'Status',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
        ];
    }

    public function add()
    {
        //$this->status = 1;
        $this->createddate = date('Y-m-d H:i:s');
        $this->updateddate = date('Y-m-d H:i:s');
        if($this->save()){
            $this->sendSunscriptionMail();
            return true;
        }else{
            print_r($this->getErrors());
            exit;
        }
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function sendSunscriptionMail() {
        $list = Subscription::find()->andWhere(['status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
        foreach ($list as $key => $value) {
            $email = $value['email'];
            $replyEmail ="info@healthbeautybank.com";
            $subject = "Healthbeautybank - New Post Added.";
            $body = "New post added healthbeautybank.com/article";

            Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setReplyTo([$replyEmail => $replyEmail])
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
        }
    }
}
