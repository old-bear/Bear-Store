<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_order".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $customer_id
 * @property integer $group_order_id
 * @property datetime $create_time
 * @property integer $amount
 * @property double $price
 * @property string $status
 * @property datetime $expire_time
 */
class CustomerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'group_order_id', 'customer_id', 'amount'], 'required'],
            [['item_id', 'group_order_id', 'customer_id', 'amount'], 'integer', 'min' => 1],
            [['status'], 'in', 'range' => ['creating', 'created',
                                           'paid', 'delivered', 'refunded']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'item_id' => '商品',
            'group_order_id' => '团购订单',
            'customer_id' => '用户',
            'amount' => '数量',
            'price' => '金额',
            'status' => '状态',
            'create_time' => '创建时间',
            'prepay_id' => '预支付订单',
            'transaction_id' => '支付订单',
            'expire_time' => '支付过期时间',
            'last_modified_time' => '上次修改时间',
        ];
    }

    
    public function getGroupOrder() 
    {
        return $this->hasOne(GroupOrder::className(), ['id' => 'group_order_id']);
    }
  
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
  
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
