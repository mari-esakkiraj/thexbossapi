<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "home_banner".
 *
 * @property int $id
 * @property string|null $filename
 * @property int|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class HomeBanner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'home_banner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],
            [['filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'status' => 'Status',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
        ];
    }
}
