<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorite".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $resource_type
 * @property integer $resource_id
 */
class Favorite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'uri', 'resource_type', 'resource_id'], 'required'],
            [['id', 'customer_id', 'resource_id'], 'integer', 'min' => 1],
            [['resource_type'], 'in', 'range' => ['item', 'group-order']],
        ];
    }

    public function getItem()
    {
        if ($this->resource_type == 'item') {
            return $this->hasOne(Item::className(), ['id' => 'resource_id']);
        } else if ($this->resource_type == 'group-order') {
            return $this
                ->hasOne(Item::className(), ['id' => 'item_id'])
                ->viaTable('group_order', ['id' => 'resource_id']);
        }
    }

    public function getGroupOrder()
    {
        if ($this->resource_type == 'group-order') {
            return $this->hasOne(GroupOrder::className(), ['id' => 'resource_id']);
        }
        return null;
    }

    public function getUri()
    {
        return '/' . $this->resource_type . '/view?id=' . $this->resource_id;
    }

    public function duplicate()
    {
        return self::findOne(['customer_id' => $this->customer_id,
                              'resource_type' => $this->resource_type,
                              'resource_id' => $this->resource_id]) != null;
    }
}
