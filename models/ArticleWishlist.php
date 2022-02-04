<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_wishlist".
 *
 * @property int $id
 * @property int|null $article_id
 * @property int|null $user_id
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class ArticleWishlist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_wishlist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id'], 'integer'],
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
            'article_id' => 'Article ID',
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
