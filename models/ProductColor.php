<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_color".
 *
 * @property int $color_id
 * @property string $product_title
 * @property string $product_code
 * @property string|null $createddate
 * @property string|null $updateddate
 *
 * @property Product[] $products
 */
class ProductColor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_title', 'product_code'], 'required'],
            [['createddate', 'updateddate'], 'safe'],
            [['product_title', 'product_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'color_id' => 'Color ID',
            'product_title' => 'Product Title',
            'product_code' => 'Product Code',
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
        return $this->hasMany(Product::className(), ['color_id' => 'color_id']);
    }
}
