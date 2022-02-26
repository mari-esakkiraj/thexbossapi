<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $product_uuid
 * @property int $title_active
 * @property int $category_id
 * @property int|null $sub_category_id
 * @property string|null $desc
 * @property int $desc_active
 * @property string|null $aditional_info
 * @property int $aditional_info_active
 * @property string|null $filename
 * @property int|null $size_id
 * @property int $size_active
 * @property int|null $quantity
 * @property int $quantity_active
 * @property int $in_stock
 * @property int|null $color_id
 * @property int $color_active
 * @property int|null $status
 * @property string|null $discount
 * @property string|null $prize
 * @property int $discount_active
 * @property string|null $url
 * @property string|null $createddate
 * @property string|null $updateddate
 *
 * @property ProductColor $color
 * @property ProductSize $size
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
            [['title'], 'required'],
            [['title_active', 'desc_active', 'aditional_info_active', 'size_active', 'quantity_active', 'in_stock', 'color_active', 'status', 'discount_active'], 'integer'],
            [['createddate', 'updateddate','product_uuid','prize', 'quantity', 'size_id', 'color_id', 'category_id', 'sub_category_id'], 'safe'],
            [['title', 'filename', 'url'], 'string', 'max' => 255],
            [['desc', 'aditional_info', 'discount'], 'string'],
            [['color_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductColor::className(), 'targetAttribute' => ['color_id' => 'color_id']],
            [['size_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductSize::className(), 'targetAttribute' => ['size_id' => 'size_id']],
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
            'title_active' => 'Title Active',
            'category_id' => 'Category ID',
            'sub_category_id' => 'Sub Category ID',
            'desc' => 'Desc',
            'desc_active' => 'Desc Active',
            'aditional_info' => 'Aditional Info',
            'aditional_info_active' => 'Aditional Info Active',
            'filename' => 'Filename',
            'size_id' => 'Size ID',
            'size_active' => 'Size Active',
            'quantity' => 'Quantity',
            'quantity_active' => 'Quantity Active',
            'in_stock' => 'In Stock',
            'color_id' => 'Color ID',
            'color_active' => 'Color Active',
            'status' => 'Status',
            'discount' => 'Discount',
            'discount_active' => 'Discount Active',
            'url' => 'Url',
            'createddate' => 'Createddate',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * Gets query for [[Color]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(ProductColor::className(), ['color_id' => 'color_id']);
    }

    /**
     * Gets query for [[Size]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSize()
    {
        return $this->hasOne(ProductSize::className(), ['size_id' => 'size_id']);
    }

    public function add()
    {
        $this->status = 1;
        $this->createddate = date('Y-m-d H:i:s');
        $this->updateddate = date('Y-m-d H:i:s');
        $uid = $this->product_uuid;
        $title = $this->title;
        if($this->save()){
            $this->sendSunscriptionMail($uid,$title);
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
        return $this->hasMany(ProductImages::className(), ['product_id' => 'id'])->
        orderBy(['id' => SORT_DESC]);
    }

    public function getProductwish()
    {
        return $this->hasOne(ProductWishlist::className(), ['product_id' => 'id']);
    }

    public function sendSunscriptionMail($uid,$title) {

        $list = Subscription::find()->andWhere(['status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
        foreach ($list as $key => $value) {
            $email = $value['email'];
            $replyEmail ="info@healthbeautybank.com";
            $subject = "Healthbeautybank - New Product Added.";
            $body = "New Product added healthbeautybank.com/productview/".$uid."/".$title;
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
