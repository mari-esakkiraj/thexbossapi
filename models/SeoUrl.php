<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seo_url".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $url
 * @property int|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class SeoUrl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seo_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],
            [['title', 'description', 'url'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'url' => 'Url',
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
        if($this->save()){
            return true;
        }else{
            print_r($this->getErrors());
            exit;
        }
    }
}
