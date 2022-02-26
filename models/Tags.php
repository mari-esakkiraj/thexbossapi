<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tags".
 *
 * @property int $id
 * @property string|null $script
 * @property string|null $script_tag
 * @property string|null $status
 * @property string|null $createddate
 * @property string|null $updateddate
 */
class Tags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['createddate', 'updateddate','status','script','script_tag','title'], 'safe'],
            //[['script'], 'string', 'max' => 255],
            //[['script_tag'], 'string', 'max' => 255],
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
            'script' => 'Script',
            'script_tag' => 'Script Tag',
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
            Yii::$app->db->createCommand("UPDATE tags SET title=:title WHERE id=:id")
            ->bindValue(':id', $this->id)
            ->bindValue(':title', '{{tags'.$this->id.'}}')
            ->execute();
            return true;
        }else{
            print_r($this->getErrors());
            exit;
        }
    }
}
