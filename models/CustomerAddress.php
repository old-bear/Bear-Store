<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_address".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $district_id
 * @property string $address
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $note
 */
class CustomerAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'district_id', 'address', 'contact_name', 'contact_phone'],
             'required', 'message' => '请填写{attribute}'],

            [['customer_id'], 'integer', 'min' => 1],
            [['district_id'], 'integer', 'min' => 1, 'tooSmall' => '请选择地区'],
            [['address', 'note'], 'string', 'max' => 256],
            [['contact_name', 'contact_phone'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'district_id' => '地区',
            'address' => '详细地址',
            'contact_name' => '联系人',
            'contact_phone' => '联系电话',
        ];
    }

    public function getDistrict()
    { 
        return $this->hasOne(Area::className(), ['id' => 'district_id']);
    }

    public function addressString()
    {
        $ret = $this->district->toString() . ' ' . $this->address;
        if ($this->note) {
            $ret .= ' 【' . $this->note . '】';
        }
        return $ret;
    }
}
