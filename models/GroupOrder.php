<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "group_order".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $leader_id
 * @property datetime $create_time
 * @property date $delivery_date
 * @property date $arrival_date
 * @property integer $leader_amount
 * @property integer $max_amount
 * @property integer $delivery_address_id
 * @property string $status
 * @property datetime $last_modified_time
 */
class GroupOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_order';
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
            [['item_id', 'leader_id', 'status'], 'required'],
            [['delivery_date', 'arrival_date', 'delivery_address_id', 'leader_amount'],
             'required', 'on' => 'submit', 'message' => '请填写{attribute}'],
            
            [['item_id', 'leader_id', 'delivery_address_id', 'max_amount'],
             'integer', 'min' => 1],
            [['leader_amount'], 'integer', 'min' => 0],
            [['delivery_date', 'arrival_date'], 'date', 'format' => 'php:Y-m-d'],
            [['status'], 'in', 'range' => ['creating', 'created',
                                           'delivering', 'delivered',
                                           'completed', 'cancelled']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'arrival_date' => '到货日期',
            'delivery_address_id' => '送货地址',
        ];
    }

    public function setArrivalDate(string $rawDate, int $duration)
    {
        if ($rawDate) {
            $date = str_replace(['年', '月', '日'], '', $rawDate);
            $date = new \DateTime($date);
            $this->arrival_date = $date->format('Y-m-d'); 
            $date->sub(new \DateInterval('P' . $duration . 'D'));
            $this->delivery_date = $date->format('Y-m-d');
        } else {
            $this->arrival_date = null;
            $this->delivery_date = null;
        }
    }
    
    public function setDeliveryAddress(string $addressID)
    {
        if ($addressID) {
            $this->delivery_address_id = intval($addressID);
        } else {
            $this->delivery_address_id = null;
        }
    }
    
    public function getLeader()
    {
        return $this->hasOne(Customer::className(), ['id' => 'leader_id']);
    }

    public function getMembers()
    {
        return $this
            ->hasMany(Customer::className(), ['id' => 'customer_id'])
            ->viaTable('customer_order', ['group_order_id' => 'id']);
    }

    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
  
    public function getCustomerOrders()
    {
        return $this
            ->hasMany(CustomerOrder::className(), ['group_order_id' => 'id']);
    }

    public function getDeliveryAddress()
    {
        return $this->hasOne(CustomerAddress::className(), ['id' => 'delivery_address_id']);
    }

    public function deliveryAddressString()
    {
        return $this->deliveryAddress->addressString();
    }
  
    public function hasMember(string $qrcode_url)
    {
        return $this
            ->hasMany(Customer::className(), ['id' => 'customer_id'])
            ->viaTable('customer_order', ['group_order_id' => 'id'])
            ->where(['qrcode_url' => $qrcode_url])
            ->distinct()->count();
    }
  
    public function fetchCustomer(string $qrcode_url)
    {
         return $this
            ->hasMany(Customer::className(), ['id' => 'customer_id'])
            ->viaTable('customer_order', ['group_order_id' => 'id'])
            ->where(['qrcode_url' => $qrcode_url])
            ->distinct()->limit(1)->one();
    }
  
    public function fetchCustomerOrderID(string $user_id)
    {
        $customer_orders = $this
            ->hasMany(CustomerOrder::className(), ['group_order_id' => 'id'])
            ->where(['customer_id' => (int)$user_id])
            ->distinct()->all();
        
        $transaction_id_list = array();
        foreach ($customer_orders as $order) {
            array_push($transaction_id_list, $order->pay_id);
        }
        return $transaction_id_list;
    }
  
    public function isCompleted()
    {
        $customerOrders = $this->customerOrders;
        foreach ($customerOrders as $order) {
            if ($order->status !== 'delivered')
                return false;
        }
        return true;
    }
}
