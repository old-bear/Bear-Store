<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorite_order".
 *
 * @property integer $id
 * @property integer $group_order_id
 * @property integer $customer_id
 */
class FavoriteOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'group_order_id'], 'required'],
            [['id'], 'integer', 'min' => 1],
        ];
    }
  
    public static function existRecord($user_id, $group_order_id)
    {
        return FavoriteOrder::find()
            ->where(['customer_id' => $user_id, 'group_order_id' => $group_order_id])
            ->exists();
    }
}
