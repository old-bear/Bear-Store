<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GroupOrder;

/**
 * GroupOrderSearch represents the model behind the search form about `app\models\GroupOrder`.
 */
class GroupOrderSearch extends GroupOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'leader_id', 'leader_amount', 'max_amount', 'delivery_address_id'], 'integer'],
            [['create_time', 'delivery_date', 'arrival_date', 'status', 'last_modified_time'], 'safe'],
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
        $query = GroupOrder::find();

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
            'leader_id' => $this->leader_id,
            'create_time' => $this->create_time,
            'delivery_date' => $this->delivery_date,
            'arrival_date' => $this->arrival_date,
            'leader_amount' => $this->leader_amount,
            'max_amount' => $this->max_amount,
            'delivery_address_id' => $this->delivery_address_id,
            'last_modified_time' => $this->last_modified_time,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
