<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_customer".
 *
 * @property integer $order_id
 * @property integer $customer_id
 * @property integer $amount
 */
class ItemPhoto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'photo_path'], 'required'],
            [['item_id'], 'integer', 'min' => 1],
            [['photo_path'], 'string', 'max' => 256],
        ];
    }
}
