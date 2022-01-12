<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string|null $title
 * @property int $category_id
 * @property string|null $filename
 * @property string $content
 * @property string $content_new
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'content'], 'required'],
            [['category_id','status'], 'integer'],
            [['content'], 'string'],
            [['createddate', 'updateddate','price','content_new'], 'safe'],
            [['title', 'filename'], 'string', 'max' => 255],
            //[['status'], 'string', 'max' => 3],
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
            'content_new' => 'Content',
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

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    public function getImages()
    {
        return $this->hasMany(ProductImages::className(), ['product_id' => 'id']);
    }
}
