<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_size".
 *
 * @property int $size_id
 * @property string $size_title
 * @property string $size_description
 * @property string|null $createddate
 * @property string|null $updateddate
 *
 * @property Product[] $products
 */
class ProductSize extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_size';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['size_title', 'size_description'], 'required'],
            [['createddate', 'updateddate'], 'safe'],
            [['size_title', 'size_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'size_id' => 'Size ID',
            'size_title' => 'Size Title',
            'size_description' => 'Size Description',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['size_id' => 'size_id']);
    }
}
