<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property integer $id
 * @property integer $category_i
 * @property integer $category_ii
 * @property string $name
 * @property double $price
 * @property integer $amount
 * @property integer $threshold
 * @property string $specification
 * @property string $description
 * @property integer $delivery_id
 * @property integer $delivery_city
 * @property integer $delivery_district
 */
class Item extends \yii\db\ActiveRecord
{
    public $imageFiles;
        
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    /// /**
    ///  * @inheritdoc
    ///  */
    /// public function scenarios()
    /// {
    ///     return [
    ///         'creating' => ['item_id'],
    ///     ];
    /// }

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'amount'], 'required'],
            
            [['name'], 'string', 'min' => 1, 'max' => 64],
            [['price'], 'double', 'min' => 0.0],
            [['amount'], 'integer', 'min' => 0],
            [['threshold'], 'integer', 'min' => 0],
            [['specification'], 'string', 'min' => 1, 'max' => 64],
            [['description'], 'string', 'min' => 1, 'max' => 255],

            [['imageFiles'], 'image', 'maxFiles' => 5, 'maxSize' => 2048*1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'category_i' => '一级类别',
            'category_ii' => '二级类别',
            'name' => '名称',
            'price' => '价格',
            'amount' => '数量',
            'threshold' => '最低成团数量',
            'specification' => '规格',
            'description' => '详情',
            'due_date' => '截团日期',
            'delivery_address_id' => '送货范围',
            'delivery_duration' => '预计运输耗时',
            'delivery_date_start' => '送货开始日期',
            'delivery_date_end' => '送货截止日期',
        ];
    }

    public function uploadImages()
    {
        if ($this->validate()) {
            $path = 'images/item-' . $this->id;
            if (!mkdir($path, 0775, true)) {
                return false;
            }
            foreach ($this->imageFiles as $file) {
                $file->saveAs($path . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getImages()
    {
        return $this->hasMany(ItemPhoto::className(), ['item_id' => 'id']);
    }

    public function getDeliveryAddress()
    {
        return $this->hasOne(Area::className(), ['id' => 'delivery_address_id']);
    }
}
