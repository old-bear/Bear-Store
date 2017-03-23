<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorite_item".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $customer_id
 */
class FavoriteItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'customer_id'], 'required'],
            [['id'], 'integer', 'min' => 1],
        ];
    }
  
    public static function existRecord($user_id, $item_id)
    {
        return FavoriteItem::find()
            ->where(['customer_id' => $user_id, 'item_id' => $item_id])
            ->exists();
    }
}
