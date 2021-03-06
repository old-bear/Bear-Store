<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Item;

/**
 * ItemSearch represents the model behind the search form about `app\models\Item`.
 */
class ItemSearch extends Item
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_i', 'category_ii', 'amount', 'threshold', 'delivery_address_id', 'delivery_duration'], 'integer'],
            [['name', 'specification', 'description', 'due_date', 'delivery_date_start', 'delivery_date_end'], 'safe'],
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
        $query = Item::find();

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
            'category_i' => $this->category_i,
            'category_ii' => $this->category_ii,
            'price' => $this->price,
            'amount' => $this->amount,
            'threshold' => $this->threshold,
            'due_date' => $this->due_date,
            'delivery_address_id' => $this->delivery_address_id,
            'delivery_duration' => $this->delivery_duration,
            'delivery_date_start' => $this->delivery_date_start,
            'delivery_date_end' => $this->delivery_date_end,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'specification', $this->specification])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
