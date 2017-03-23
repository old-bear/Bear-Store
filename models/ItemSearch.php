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
    const DEVIATION = 0.1;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [];
        foreach (Item::rules() as $rule) {
            // Remove all required rules for search model
            if ($rule[1] != 'required') {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // Bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Item::find();

        // Add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'name', $this->name]);
        if ($this->price) {
            $query->andFilterWhere([
                'between', 'price',
                $this->price * (1 - self::DEVIATION),
                $this->price * (1 + self::DEVIATION),
            ]);
        }
        if ($this->amount) {
            $query->andFilterWhere([
                'between', 'amount',
                $this->amount * (1 - self::DEVIATION),
                $this->amount * (1 + self::DEVIATION),
            ]);
        }
        return $dataProvider;
    }
}
