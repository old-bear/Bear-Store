<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $open_id
 * @property string $captcha
 * @property integer $subscribe_time
 * @property string $refresh_token
 * @property string $nick_name
 * @property string $sexuality
 * @property string $head_img_path
 * @property double $balance
 * @property integer $bonus_points
 * @property string $phone
 * @property string $email
 * @property string $qrcode_ticket
 * @property datetime $qrcode_expire_time
 * @property string $qrcode_url
 */
class Customer extends \yii\db\ActiveRecord
{
    public $inputCaptcha;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
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
            [['open_id'], 'required'],
            [['phone', 'inputCaptcha'], 'required',
             'on' => 'register', 'message' => '请填写{attribute}'],
            ['inputCaptcha', 'compare', 'compareAttribute' => 'captcha',
             'enableClientValidation' => false, 'on' => 'register', 'message' => '验证码不正确'],
     
            [['open_id'], 'string', 'max' => 64],
            [['subscribe_time'], 'integer', 'min' => 0],
            [['refresh_token'], 'string', 'max' => 512],
            [['nick_name'], 'string', 'max' => 32],
            [['head_img_path'], 'string', 'max' => 255],
            [['balance'], 'double', 'min' => 0.0],
            [['bonus_points'], 'integer', 'min' => 0],

            [['phone'], 'string', 'length' => 11, 'notEqual' => '手机号格式不正确'],
            [['email'], 'email', 'message' => '邮箱格式不正确'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => '手机号',
            'inputCaptcha' => '验证码',
        ];
    }

    public function getOrders()
    {
        return $this
            ->hasMany(GroupOrder::className(), ['id' => 'group_order_id'])
            ->viaTable('customer_order', ['customer_id' => 'id'])
            ->orderBy('id DESC');
    }

    public function getAddresses()
    {
        return $this->hasMany(CustomerAddress::className(), ['customer_id' => 'id']);
    }
  
    public function getFavoriteItems()
    {
        return $this
            ->hasMany(Item::className(), ['id' => 'item_id'])
            ->viaTable('favorite_item', ['customer_id' => 'id'])
            ->orderBy('id DESC');
    }
  
    public function getFavoriteOrders()
    {
        return $this
            ->hasMany(GroupOrder::className(), ['id' => 'group_order_id'])
            ->viaTable('favorite_order', ['customer_id' => 'id'])
            ->orderBy('id DESC');
    }

    public function addressOptionArray()
    {
        $ret = [];
        foreach ($this->addresses as $addr) {
            $ret[$addr->id] = $addr->addressString();
        }
        return $ret;
    }
  
    public function fetchOrders($status)
    {
        return $this
            ->hasMany(GroupOrder::className(), ['id' => 'group_order_id'])
            ->viaTable('customer_order', ['customer_id' => 'id'])
            ->where(['status' => $status])
            ->all();
    }
  
    public function fetchCustomerOrder($groupOrderId)
    {
        return $this
            ->hasMany(CustomerOrder::className(), ['customer_id' => 'id'])
            ->where(['group_order_id' => $groupOrderId])
            ->all();
    }
  
    public function customerOrdersAreDelivered($groupOrderId)
    {
        $customerOrders = self::fetchCustomerOrder($groupOrderId);
        $ret = true;
        foreach ($customerOrders as $customerOrder) {
            if ($customerOrder->status !== 'delivered') {
                $ret = false;
                break;
            }
        }
        return $ret;
    }
  
    public function fetchFavoriteItem($item_id)
    {
        return $this
            ->hasMany(FavoriteItem::className(), ['customer_id' => 'id'])
            ->where(['item_id' => $item_id])
            ->limit(1)->one();
    }
  
    public function fetchFavoriteOrder($group_order_id)
    {
        return $this
            ->hasMany(FavoriteOrder::className(), ['customer_id' => 'id'])
            ->where(['group_order_id' => $group_order_id])
            ->limit(1)->one();
    }
  
    public function getQrTicket()
    {
        $ticket = $this->qrcode_ticket;
      
        if (!$ticket) {
            $ticket = self::_refreshQrTicket();
        } else {
            $expireDate = new \DateTime($this->qrcode_expire_time);
            $diff = $expireDate->getTimestamp() - time();
            if ($diff < 180) { 
                // Refresh QR code ticket when last one will expire in less than 3 minutes
                $ticket = self::_refreshQrTicket();
            }
        }
      
        return $ticket;
    }
  
    private function _refreshQrTicket()
    {
        $ak = AccessToken::getAccessToken();
        $resBody = Yii::$app->utils->fetchQrTicket($ak, $this->open_id)['body'];
        
        $expireDate = (new \DateTime())->add(
            new \DateInterval('PT' . $resBody['expire_seconds'] . 'S'));
      
        $this->qrcode_ticket = $resBody['ticket'];
        $this->qrcode_expire_time = $expireDate->format('Y-m-d H:i:s');
        $this->qrcode_url = $resBody['url'];
        $this->save();
      
        return $this->qrcode_ticket;
    }
}
