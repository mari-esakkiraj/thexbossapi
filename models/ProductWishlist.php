<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_wishlist".
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $user_id
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class ProductWishlist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_wishlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'user_id'], 'integer'],
            [['createddate', 'updateddate', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'user_id' => 'User ID',
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
