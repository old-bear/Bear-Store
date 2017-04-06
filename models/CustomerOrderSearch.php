<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerOrder;

/**
 * CustomerOrderSearch represents the model behind the search form about `app\models\CustomerOrder`.
 */
class CustomerOrderSearch extends CustomerOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'customer_id', 'group_order_id', 'amount'], 'integer'],
            [['create_time', 'prepay_id', 'transaction_id', 'status', 'expire_time', 'last_modified_time'], 'safe'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CustomerOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'item_id' => $this->item_id,
            'customer_id' => $this->customer_id,
            'group_order_id' => $this->group_order_id,
            'create_time' => $this->create_time,
            'amount' => $this->amount,
            'price' => $this->price,
            'expire_time' => $this->expire_time,
            'last_modified_time' => $this->last_modified_time,
        ]);

        $query->andFilterWhere(['like', 'prepay_id', $this->prepay_id])
            ->andFilterWhere(['like', 'transaction_id', $this->transaction_id])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
